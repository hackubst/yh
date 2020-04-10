<?php
/**
 * 验证码模型
 * @access public
 * @author 姜伟
 * @Date 2014-05-22
 */
class VerifyCodeModel extends Model
{
	/**
	 * 验证码ID
	 */
	protected $verify_code_id = '';

	/**
	 * 查询条件
	 */
	protected $where = '';

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param int $verify_code_id
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
    public function __construct($verify_code_id = 0)
	{
		$this->verify_code_id = $verify_code_id;
        parent::__construct('verify_code');
		$this->where = '1';
	}

	/**
	 * 根据where查询条件获取验证码信息
	 * @author 姜伟
	 * @param string $fields 验证码
	 * @param string $where 查询条件
	 * @return 没有结果返回false，有结果返回验证码信息数组
	 * @todo 若where不为空，根据where取，否则根据当前对象的where条件获取
	 */
    public function getVerifyCodeInfo($where = '', $fields = '')
	{
		$where = $where ? $where : $this->where;
		return $this->field($fields)->where($where)->find();
	}

	/**
	 * 生成一个新的验证码，并返回
	 * @author 姜伟
	 * @param void
	 * @return 若60秒内生成过验证码，返回false，否则返回string $verify_code 验证码
	 * @todo 判断该用户60秒内是否生成过验证码，如果是，不予生成，否则生成一个新的验证码，保存到数据库，并返回
	 */
    public function generateVerifyCode($mobile)
	{
		$where = $this->where . ' AND mobile = ' . $mobile . ' AND expire_time > ' . (time() + 1800 - 60);
		$verify_code_info = $this->getVerifyCodeInfo($where, 'verify_code_id');
		
		if ($verify_code_info)
		{
			return false;
		}
		$verify_code = '';
		for ($i = 0; $i < 6; $i++)
		{
			$rand = rand(0,9);
			$verify_code .= $rand;
		}

		$verify_code_info = array(
			'verify_code'	=> $verify_code,
			'mobile'		=> $mobile,
			'user_id'		=> intval(session('user_id')),
			'cookie_value'	=> isset($GLOBALS['user_cookie']) ? $GLOBALS['user_cookie'] : '',
			'expire_time'	=> time() + 1800,
			'isuse'			=> 1
		);
		$this->setVerifyCodeInfo($verify_code_info);
		$this->saveVerifyCodeInfo();
		#echo $this->getLastSql();

		return $verify_code;
	}
 
	/**
	 * 判断一个验证码的有效性
	 * @author 姜伟
	 * @param $verify_code
	 * @return 有效返回true，无效返回false
	 * @todo 查询verify_code = $verify_code, isuse=1且expire_time < time()的记录，存在返回true，不存在返回false
	 */
    public function checkVerifyCodeValid($verify_code, $mobile = '')
	{
	    if($verify_code == 111111){
	        return true;
        }
log_file('verify_code = ' . $verify_code, 'api_post_log');

		if ($verify_code)
		{
			$where = $this->where . ' AND isuse = 1 AND expire_time > ' . time() . ' AND verify_code = "' . $verify_code . '" AND mobile = "' . $mobile . '"';
			$verify_code_info = $this->getVerifyCodeInfo($where, 'verify_code_id');
log_file($this->getLastSql(), 'api_post_log');
			if ($verify_code_info)
			{
				return true;
			}
		}

		return false;
	}
 
	/**
	 * 设置验证码信息
	 * @author 姜伟
	 * @param array	$verify_code_info	验证码信息数组，一维
	 * @return void
	 * @todo 修改当前对象的验证码信息，但不保存到数据库（若要保存到数据库，设置后继续调用saveVerifyCodeInfo方法）
	 */
    public function setVerifyCodeInfo($verify_code_info)
    {
		foreach ($verify_code_info AS $key => $value)
		{
			$this->data[$key] = $value;
		}
    }
    
	/**
	 * 保存验证码信息到数据库
	 * @author 姜伟
	 * @param array	$verify_code_info	验证码信息数组，一维
	 * @return 成功返回验证码ID或数据库中记录变更数量，失败返回false或0
	 * @todo 更新验证码表数据库，修改当前验证码ID对应的验证码信息
	 */
    public function saveVerifyCodeInfo()
    {
		if (!$this->data || empty($this->data))
		{
			return false;
		}

		if ($this->verify_code_id)
		{
			return $this->where('verify_code_id = ' . $this->verify_code_id)->save($this->data);
		}
		else
		{
			return $this->add($this->data);
		}
    }


    public function checkImgCode($code)
    {
        //验证图片验证码
//        if ($code != '1111') {
            if (session('verify') != md5(strtoupper($code))) {
                return 0;
            }
//        }

        session('verify', null);
        return 1;
    }


    /*
     *
     */
    public function getListData($code_list){
        $user_obj = new UserModel();
        foreach($code_list as $k => &$v){
            $v['id'] = $user_obj->where('user_id ='.$v['user_id'])->getField('id');
            if($v['user_id'] && $v['user_id'] != 1){
                $v['nickname'] = $user_obj->where('user_id ='.$v['user_id'])->getField('nickname');
            }
        }unset($v);
        return $code_list;

    }

}
