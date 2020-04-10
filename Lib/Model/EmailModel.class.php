<?php

//  邮件管理
//  @ File Name : EmailModel.class.php
//  @ Date 2014-02-20
//  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc

require_once('frame/Lib/Core/Model.class.php');
require_once('Lib/Model/ConfigBaseModel.class.php');
/** */
class EmailModel extends Model 
{
    protected $tableName = 'email_log';
    function _initialize() {
        parent::_initialize();
        
    }
    
    /**
     * @access public 
     * @todo  发送邮件
     * @param array $smtpemailto    收件人邮箱地址，必须
     * @param string $mailsubject   邮件主题，必须
     * @param string $mailbody      邮件内容，必须
     * @param string $mailtype      邮件格式（HTML/TXT）,TXT为文本邮件，默认发送text文本格式的邮件
     * @param int  $smtpserverport  smtp邮件服务端口。默认25 (非必要情况下不要传递该参数)
     * @return bool 成功返回TRUE 失败返回FALSE 如果关闭了SMTP则返回字符串（这里在实际开发中在定夺）
     */
    public function sendEmail($smtpemailto, $mailsubject, $mailbody, $mailtype='text',$smtpserverport = 25 )
    {
/*      $ConfigModel = new ConfigBaseModel();                                //用来获取当前系统的配置信息
        $smtp_email_open = $ConfigModel->getConfig('smtp_email_open');       //SMTP邮件发送是否开启
        if(!$smtp_email_open)
        {
            return '您关闭了smtp邮件服务功能';
        }
        $smtpserver      = $ConfigModel->getConfig('smtp_email_host');       //SMTP服务器 
        $smtpusermail    = $ConfigModel->getConfig('smtp_email_reply');      //SMTP服务器的用户邮箱 (SMTP用来发送邮件的邮箱地址也即发件人地址)
        $smtpuser        = $ConfigModel->getConfig('smtp_email_account');    //SMTP服务器的用户帐号 (登录SMTP服务器的账号)
        $smtppass        = $ConfigModel->getConfig('smtp_email_password');   //SMTP服务器的用户密码 (登录SMTP服务器的密码)
  */     
        /** 测试用数据开始 **/
        #$smtpserver = 'smtp.qq.com';
        #$smtpusermail = '1285686679@qq.com';
        #$smtpuser = '1285686679@qq.com';
        #$smtppass = '123456';
        $smtpserver = 'smtp.163.com';
        $smtpusermail = '15158131315@163.com';
        $smtpuser = '15158131315@163.com';
        $smtppass = 'tengfei113';
        /**测试用数据结束**/
        
        //导入邮件处理类
        vendor('Email.email');      //类中有使用方法介绍
        $smtp  = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug = false;               //是否显示发送的调试信息 
        #################### 发送操作 ###################### 
        
        if($smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype))
        {
            return TRUE;
        }else{
            return FALSE;
        }
        
    }
       
    /**
     * @access public
     * @todo 记录发送消息的日志
     * @param $data array 数据组成的关联数组 元素的键值固定格式，具体参考数据表的字段 
     *         例如 array(
     *                  'send_email_account'=> 'zhoutao@360shop.cc',
     *                  'email_recipients'  => '123@qq.com,456@sina.com',
     *                  'subject'           => '测试邮件标题',
     *                  'send_time'         =>  1355799616,
     *                  'state'             =>  1
     *                  )
     *  @return int $email_log_id 返回插入的email_log_id
     */
    public function addEmaiSendlLog($data = array())
    {
        if(empty($data))
        {
            return false;
        }
        $email_log_id = $this->add($data);
        if($email_log_id)
        {
            return $email_log_id;
        }else{
            return false;
        }
    }
    
    /**
     * @access public
     * @todo  查询获取发送邮件的历史记录：支持对时间段的查询,以及对邮件发送状态的查询
     * @param int $start 查询条件limit的第一个参数  
     * @param int $limit 查询条件limit的第二个参数
     * @param int $send_time1 查询条件中的起始时间戳    默认为空，表示该项无条件
     * @param int $send_time2 查询条件中的截止时间戳    默认为空，表示该项无条件
     * @param int $state      查询条件中的发送状态      默认为空，表示该项无条件
     * @return array $result  查询结果
     */
    public function getEmailSendingList($start=0,$limit=15,$send_time1 = '',$send_time2 = '',$state = '')
    {
        $result = array();
        $condition = '';        //组装查询条件
        if($send_time1)
        {
            $condition .= 'send_time >= '.$send_time1;
            $condition .= ($send_time2 || $state)?' AND ':'';
        }
        if($send_time2)
        {
            $condition .= 'send_time <= '.$send_time2;
            $condition .= ($state)?' AND ':'';
        }
        if($state){
            $condition .= 'state = '.$state;
        }
        $result = $this->where($condition)->limit($start,$limit)->select();
        return $result;
       // return $EmailLog->getLastSql();
    }
    
    /** 
     * 管理邮件的配置信息
     * @param array $data 键值对应的关联数组，元素键值表示配置项的名称，元素值代表该项配置的具体的值
     * @return bool
     * 调用举例  setEmailConfig(array('smtp_email_account'=>'zhoutao@360shop.cc'))
     */
    public function setEmailConfig($data=array())
    {
        if(empty($data))
        {
            return false;
        }
        foreach($data as $k=>$v)
        {
            $result = $this->where($k.' = "'.$v.'"');
        }
        return true;
    }
    
    
    
    
}
