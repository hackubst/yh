<?php
/**
 * 论坛模型类
 */
class LuntanModel extends Model
{
    // 论坛id
    public $luntan_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $luntan_id 论坛ID
     * @return void
     * @todo 初始化论坛id
     */
    public function LuntanModel($luntan_id)
    {
        // parent::__construct('luntan');
        if ($luntan_id = intval($luntan_id))
		{
            $this->luntan_id = $luntan_id;
		}
    }

    public function testLuntan(){
    	return '呵呵哈哈哈或';
    }
}
