<?php
class ApiModel extends Model
{
	//请求方式
	private $method = 'request';
	public $key = 'N7&7WY8m6%q@J4*AjvB^A96s9_p+z-h=';

	/**
	 * 验证必要参数合法性
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 验证必要参数合法性
	 */
	function getRequiredParams()
	{
		$params = array(
			array(
				'field'		=> 'appid', 
				'type'		=> 'string', 
				'required'	=> true, 
				'miss_code'	=> 41008, 
				'empty_code'=> 44008, 
				'type_code'	=> 45008, 
				'func'		=> 'checkAppid', 
				'func_code'	=> 47008,
			),
			array(
				'field'		=> 'api_name', 
				'required'	=> true, 
				'miss_code'	=> 41009, 
				'empty_code'=> 44009, 
				'type_code'	=> 45009, 
				'func'		=> 'checkApiName', 
				'func_code'	=> 47009,
			),
			array(
				'field'		=> 'token', 
				'min_len'	=> 32,
				'max_len'	=> 32, 
				'type'		=> 'string', 
				'required'	=> true, 
				'len_code'	=> 40010, 
				'miss_code'	=> 41010, 
				'empty_code'=> 44010, 
				'type_code'	=> 45010, 
			),
			array(
				'field'		=> 'PHPSESSID', 
				'type'		=> 'string', 
				#'required'	=> true,
				#'miss_code'	=> 41011,
				'type_code'	=> 45011, 
				'func'		=> 'checkPHPSESSID', 
				'func_code'	=> 40011, 
			),
		);

		return $params;
	}

	/**
	 * 获取sign
	 * @author 姜伟
	 * @param array $params 请求参数列表
	 * @return string sign
	 * @todo 排序，组成请求字符串后，连接key进行MD5加密，返回
	 */
	function generateSign($data)
	{
		ksort($data);
	
		$sign = '';
		foreach($data as $key => $val)
		{
            if ($key == 'api_name' || $key == 'appid' || $key == 'timestamp' ) {
                $sign .= "&$key=$val";
            }
		}

		$sign = md5(substr($sign, 1) . $this->key);
		return $sign;
	}

	/**
	 *
	 * @author 姜伟
	 * @param 
	 * @return 
	 * @todo 
	 */
	function checkPriv()
	{
		$data = $_REQUEST;
		unset($data['_URL_']);
		ksort($data);
	
		$sign = '';
		foreach($data as $key => $val)
		{
			if($key != 'token')
			{
				$sign .= "&$key=$val";
			}
		}
		#echo substr($sign,1);
		#die;

		if($data['token'] != md5(substr($sign,1) . $this->key))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 *
	 * @author 姜伟
	 * @param 
	 * @return 
	 * @todo 
	 */
	function permissionDeny()
	{
		self::returnResult(40001, null, '访问令牌无效，key不正确或加密方式不对');
	}

	/**
	 *
	 * @author 姜伟
	 * @param int $code 返回码
	 * @param array $data 返回结果
	 * @param string $error_msg 错误说明
	 * @return 
	 * @todo 
	 */
	static function returnResult($code = 0, $data = array(), $error_msg = '')
	{
		if ($code)
		{
			$return_arr = array(
				'code'		=> $code,
				'error_msg'	=> $error_msg
			);

			log_file('error_result: ' . json_encode($return_arr), 'api');
			if (strtolower(PHP_OS) == 'linux')
			{
				exit(json_encode($return_arr));
			}
			else
			{
				echo "<pre>";
				print_r($return_arr);
				exit;
			}
		}

		$return_arr = array(
			'code'	=> 0,
			'data'	=> $data,
		);
		exit(json_encode($return_arr));	//上线后开启
		print_r($return_arr);	//调试时开启
		exit;
	}

	/**
	 * 验证用户名合法性
	 * @author 姜伟
	 * @param string $username 用户名
	 * @param int $func_code 返回码
	 * @return 成功返回true，失败退出返回错误码
	 * @todo 
	 */
	static function checkUserName($username, $func_code)
	{
		if(!preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9._@]{5,31}$/", $username))
		{
			self::returnResult($func_code, null, 'username只能包含数字、大小写字母和_@，且必须为大小写字母开头');
		}
	}

	/**
	 * 验证mobile合法性
	 * @author 姜伟
	 * @param string $mobile 接口名
	 * @param int $func_code 返回码
	 * @return 成功返回true，失败退出返回错误码
	 * @todo 验证mobile合法性
	 */
	static function checkMobile($mobile, $func_code)
	{
		if(!preg_match("/^1[3456789][0-9]{9}$/", $mobile))
		{
			self::returnResult($func_code, null, '参数mobile格式不正确');
		}
	}

	/**
	 * 验证api_name合法性
	 * @author 姜伟
	 * @param string $api_name 接口名
	 * @param int $func_code 返回码
	 * @return 成功返回true，失败退出返回错误码
	 * @todo 验证api_name合法性
	 */
	static function checkApiName($api_name, $func_code)
	{
		if(!preg_match("/^[a-zA-Z]{3,10}\.[a-zA-Z]{3,16}\.[a-zA-Z]{4,32}$/", $api_name))
		{
			self::returnResult($func_code, null, '参数api_name格式不正确');
		}
	}

	/**
	 * 验证PHPSESSID合法性
	 * @author 姜伟
	 * @param string $PHPSESSID 接口名
	 * @param int $func_code 返回码
	 * @return 成功返回true，失败退出返回错误码
	 * @todo 验证PHPSESSID合法性
	 */
	static function checkPHPSESSID($PHPSESSID, $func_code)
	{
        $api_name = explode('.', I('request.api_name'));
        $api_name = $api_name[2];
        $api_name_arr = array(
            'checkUpdating',//检查更新接口
            'login',
            'register',
            'checkMobileRegistered',
            'sendVerifyCode',
            'findPassword',
            'findPasswordBySms',
            'checkVerifyCodeValid',
            'signin',
            'signup',
            'getPayInfoByOrderId',
            'androidUpdate',
            'frontUpdate',
            'getMerchantClassList',
            'getMerchantIntro',
            'getShopinfo',
            'merchantShopItem',
            'homePage',
            'resetPassword',
            'getImgCode',
            'activityList',
            'noticeList',
            'AgentList',
            'giftCardInfo',
            'rankList',
            'noticeDetail',
            'getGameTypeList',
            'getArticleInfo',
            'getQiniuToken',
            'getSysqq',
            'getSessionid',
        );

        if (!in_array($api_name, $api_name_arr)) {
            if (!session('user_id')) {
                log_file('user_id=' . session('user_id'), 'debug_session');
                log_file('session_id=' . session_id(), 'debug_session');
                self::returnResult($func_code, null, '无效的PHPSESSID');
            }

        }


    }

	/**
	 * 验证appid合法性
	 * @author 姜伟
	 * @param string $appid
	 * @param int $func_code 返回码
	 * @return 成功返回true，失败退出返回错误码
	 * @todo 验证appid合法性
	 */
	static function checkAppid($appid, $func_code)
	{
		$arr = array('cheqishiandroidappid@U*NDd8vK1^2pKh', 'cheqishiiosappid@u8ms@nsN2G8M2', '1');
		if (!in_array($appid, $arr))
		{
			self::returnResult($func_code, null, 'appid错误');
		}
	}

	/**
	 * 校验字段 fieldName 的值$value非空
	 *
	 **/
	public static function checkNotNull($value,$fieldName)
	{
		if(self::checkEmpty($value)){
			throw new Exception("client-check-error:Missing Required Arguments: " .$fieldName , 40);
		}
	}

	/**
	 * 检验字段fieldName的值value 的长度
	 *
	 **/
	public static function checkMaxLength($value,$maxLength,$fieldName)
	{
		if(!self::checkEmpty($value) && mb_strlen($value , "UTF-8") > $maxLength){
			throw new Exception("client-check-error:Invalid Arguments:the length of " .$fieldName . " can not be larger than " . $maxLength . "." , 41);
		}
	}

	/**
	 * 检验字段fieldName的值value的最大列表长度
	 *
	 **/
	public static function checkMaxListSize($value,$maxSize,$fieldName)
	{
		if(self::checkEmpty($value))
			return ;

		$list=preg_split("/,/",$value);
		if(count($list) > $maxSize){
				throw new Exception("client-check-error:Invalid Arguments:the listsize(the string split by \",\") of ". $fieldName . " must be less than " . $maxSize . " ." , 41);
		}
	}

	/**
	 * 检验字段fieldName的值value 的最大值
	 *
	 **/
	public static function checkMaxValue($value,$maxValue,$fieldName)
	{
		if(self::checkEmpty($value))
			return ;

		self::checkNumeric($value,$fieldName);

		if($value > $maxValue){
				throw new Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be larger than " . $maxValue ." ." , 41);
		}
	}

	/**
	 * 检验字段fieldName的值value 的最小值
	 *
	 **/
	public static function checkMinValue($value,$minValue,$fieldName)
	{
		if(self::checkEmpty($value))
			return ;

		self::checkNumeric($value,$fieldName);
		
		if($value < $minValue){
				throw new Exception("client-check-error:Invalid Arguments:the value of " . $fieldName . " can not be less than " . $minValue . " ." , 41);
		}
	}

	/**
	 * 检验字段fieldName的值value是否是number
	 *
	 **/
	public static function checkNumeric($value,$fieldName)
	{
		if(!is_numeric($value))
		{
			return false;
		}

		return true;
	}

	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *	if is null , return true;
	 *	
	 *
	 **/
	public static function checkEmpty($value)
	{
		if(!isset($value))
			return true ;
		if($value === null )
			return true;
		if(trim($value) === "")
			return true;
		
		return false;
	}
}
