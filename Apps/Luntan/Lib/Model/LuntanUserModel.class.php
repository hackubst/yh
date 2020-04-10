<?php
/**
 * 论坛模型类
 */
class LuntanUserModel extends Model
{
    // 论坛id
    public $luntan_user_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $luntan_user_id 论坛ID
     * @return void
     * @todo 初始化论坛id
     */
    public function LuntanUserModel($luntan_user_id)
    {
        // parent::__construct('luntan_user');
        if ($luntan_user_id = intval($luntan_user_id))
		{
            $this->luntan_user_id = $luntan_user_id;
		}
    }

    public function testLuntanUser(){
    	return '哈哈哈哈哈哈哈哈哈或或或或或或或或或或或或或或或或或或或或或或';
    }
}
