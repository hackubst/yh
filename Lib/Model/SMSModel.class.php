<?php
class SMSModel extends Model
{

    const VERIFY_CODE = 1;
    const LOGIN = 2;
     public function __construct(){
		$this->db(0);
		$this->tableName = C('DB_PREFIX') . 'sms_log';
     }
     
    /** 
     * @access public
     * @desc 用实际数据替换格式化的消息格式 （参考tp_sms_set表中sms_text字段）
     * @param string $sms_text	预处理的格式化短信内容
     * @param string $type 类型。3种值：'verify_code'
     * @param array $data 对应类型的数据。比如：如果$send_name为order_create即与订单有关，那么这里的$data就是订单的数据（数据通过订单模型在控制器中获取）
     * @return string $new_text 返回替换好的新短信内容               
     * 
     */
    public function replaceSMSSettingParams($sms_text='', $type='', $data=array())
    {
        if(!$sms_text || !$type)
        {
        	return false;
        }

        switch ($type)          //对于跟多的文字模版支持，这里再行添加
        {
            case 'verify_code':
                $sms_text = str_replace('#verify_code#', $data['verify_code'], $sms_text);	 //订单编号
                break;
            default :
                break;
        }
        return $sms_text;
    }

	/**
	 * 极速：发送短信
	 * @author 姜伟
	 * @param string $mobile 手机号
	 * @param string $text 发送内容
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
    /*public function sendSMS($mobile, $text)
    {
		//发送短信
        $key = D('ConfigBase')->getConfig('sms_key');
		$key = '00ea59a4b3c7b4a7';
		$url = 'http://api.jisuapi.com/sms/send?appkey=' . $key . '&mobile=' . $mobile . '&content=' . $text;
		$result = json_decode(file_get_contents($url), true);

		//数据
		$data = array(
			'send_mobile_list'	=> $mobile,
			'send_text'			=> $text,
			'sms_send_time'		=> time(),
			'sms_send_state'	=> $result['status'],
		);
		//写日志
		if (isset($result['status']) && $result['status'] == 0)
		{
			//发送成功，保存到发送记录中
			$this->add($data);
		}
		else
		{
			//发送失败，保存到日志中
			log_file(json_encode($data) . "\n" . json_encode($result), 'sms_error_log', true);
		}

		return isset($result['status']) ? $result['status'] : -1;
	}*/

	/**
	 * 网建：发送短信
	 * @author 姜伟
	 * @param string $mobile 手机号
	 * @param string $text 发送内容
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
    public function sendSMS($mobile, $text)
    {
		//发送短信
        $uid = D('ConfigBase')->getConfig('sms_uid');
        $key = D('ConfigBase')->getConfig('sms_key');
        $uid = $uid ? $uid : "达利2015";
        $key = $key ? $key : "e5d272fbac61f0e59878";

        $url = 'http://utf8.sms.webchinese.cn/?Uid='.$uid.'&Key='.$key
            .'&smsMob=' . $mobile . '&smsText=' . $text;
		$send_state = file_get_contents($url);
        log_file('wj='.json_encode($send_state), 'sms');
		//写日志
		$data = array(
			'send_mobile_list'	=> $mobile,
			'send_text'			=> $text,
			'sms_send_time'		=> time(),
			'sms_send_state'	=> $send_state,
		);
		$this->add($data);

		return $send_state;
    }

    //极速短信
    public function sendJsSMS($mobile, $text)
    {
        //发送短信
        $key = 'aa5a85c439d26556';
        $url = 'http://api.jisuapi.com/sms/send?appkey='.$key.'&mobile=' . $mobile . '&content=' . $text;
        $send_state = json_decode(file_get_contents($url), true);
        log_file('js='.json_encode($send_state), 'sms');

        //写日志
        $data = array(
            'send_mobile_list'  => $mobile,
            'send_text'         => $text,
            'sms_send_time'     => time(),
            'sms_send_state'    => $send_state['status'],
        );
        $this->add($data);

        return $send_state;
    }
   
    /** 
     * @access public
     * @desc 获取短信发送历史记录
     * @param string $where  
     * @param string $limit  
     * @param string $order
     * @return bool 返回查询结果
     */
    public function getSMSSendingList($where='', $limit='0,20', $order='sms_send_time DESC')
    {
        $data = $this->where($where)->order($order)->limit($limit)->select();
        return $data;
    }
   
    /** 
     * @access public
     * @desc 获取剩余短信数  (通过$this->ConfigBaseMode->getConfig('sms_total')进行获取)
     */
    public function getSMSLeftNumber()
    {
		$url = 'http://sms.webchinese.cn/web_api/SMS/?Action=SMS_Num&Uid=jjedw&Key=e646f760d00cd4e69102';
		$sms_num = file_get_contents($url);

		return $sms_num;
    }
   
    /**
     * @access public
     * @todo 发送验证码
     * @param int $mobile 接收者手机号。必须
     * @param string $verify_code 验证码
     * @return 成功返回1，失败返回其它数字
     */
    public function sendVerifyCode($mobile, $verify_code,$type)
    {
        if(!$mobile || !$verify_code)
        {
            return 0;
        }

        //$row = $this->getSMSSettingByTag('verify_code');	//根据发送标记（事件）获取相应配置信息
        //if(!$row['state'])		//如果该项短信服务已经关闭
        //{
        //    log_file('come in','sms');
        //	return -1;
        //}
        //$data = array(			//组装数据
        //	'verify_code'	=>	$verify_code
        //);
        //$sms_text = $row['sms_text'] ? $row['sms_text'] : $row['default_sms_text'];
        //$message =  $this->replaceSMSSettingParams($row['sms_text'], 'verify_code', $data);		//短信内容
        //$send_state = $this->sendSMS($mobile,$message);
        //log_file('send_state'.$send_state,'sms');
        $send_state = $this->sendAliSms($mobile, $verify_code, $type);

        return $send_state;
    }
 
    /** 
     * @access public
     * @desc 根据发送标记获得相应设置信息
     */
    public function getSMSSettingByTag($send_name)
    {
		$sms_set_obj = new SMSSetModel();
		return $sms_set_obj->getSMSSettingByTag($send_name);
    }

    /** 
     * @access public
     * @desc    获取短信配置项
     */
    public function getSMSSettingList()
    {
        $this->trueTableName = C('DB_PREFIX').'sms_set';
        $row = $this->select();
        if($row)
        {
            return $row;
        }
        else
        {
            return FALSE;
        }
    }
  
    /** 
     * @access public
     * @desc 设置短信配置项
     * @param string $send_name 要设置的项目 为where条件所用
     * @data array  $data 数组，参数值 如：array('state'=>1,'to_admin'=>1)（格式参考TP框架CURD操作中更新部分）
     * @return bool 成功返回TRUE 否则返回FALSE
     */
    public function setSMSSetting($send_name,$data=array())
    {
        if(!$send_name || empty($data))
        {
            return FALSE;
        }
      #  $this->trueTableName = C('DB_PREFIX').'sms_set';           //操作tp_sms_set表
        $sms_set = M('sms_set');
        if($sms_set->where('send_name = "'.$send_name.'"')->save($data))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * 先用极速短信发送，若极速短信发送失败，则再用网建短信发送
     * @param string mobile 手机号， string text 短信内容，不带签名
     * @return 
     */
    public function sendBtSMS($mobile, $text){

        $js_r = $this->sendJsSMS($mobile, $text.'【Sama】');
        if($js_r['status'] == 0 && isset($js_r['status'])){
            return true;
        }
        $wj_r = $this->sendSMS($mobile, $text);
        return $wj_r == 1 ? true : false;
        
    }

    function sendAliSms($mobile, $code, $type) {
        $params = array ();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAImwfV885WRQhZ";
        $accessKeySecret = "BEVjwEbyiwp2vk2kCNs52EcVvF6LHT";
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = "".$mobile;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "开开乐";
        $TemplateCode = array(
            self::LOGIN =>'SMS_164065773',  //'登录'
            self::VERIFY_CODE =>'SMS_164065771',  //'注册',
        );
        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $TemplateCode[$type];

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => "".$code,
//            "order_sn"=> "".$code,
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        Vendor('aliSMS.SignatureHelper');
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        $data = array(
            'send_mobile_list'	=> $mobile,
            'send_text'			=> $code,
            'sms_send_time'		=> time(),
            'sms_send_state'	=> $content->Code,
        );
        //写日志
        $this->add($data);
        if($content->Code == 'OK') {
            return 1;
        } else return 0;
    }


}
