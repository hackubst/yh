<?php
class SMSSetModel extends Model
{
     public function __construct(){
		$this->db(0);
		$this->tableName = C('DB_PREFIX') . 'sms_set';
     }
 
    /** 
     * @access public
     * @desc 根据发送标记获得相应设置信息
     */
    public function getSMSSettingByTag($send_name)
    {
        if($send_name)
        {
            $row = $this->where('send_name = "'.$send_name.'"')->find();
            if($row)
            {
                return $row;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }
 
    /** 
     * @access public
     * @desc 修改短信模板
     */
    public function setSMSSettingByTag($send_name, $sms_text)
    {
        if($send_name)
        {
			$arr = array(
				'sms_text'	=> $sms_text
			);
			return $this->where('send_name = "' . $send_name . '"')->save($arr);
        }

		return false;
    }
}
