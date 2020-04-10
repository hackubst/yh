<?php
/**
 * 记录模型
 */

class CardPayLogModel extends Model
{
    // 常量定义
    const TABLE_NAME  = 'card_pay_log';
    const PRIMARY_KEY = 'card_pay_log_id';
    // 参考类型
    
    // auto complete
    protected $_auto = array(
        //array('isuse','1', Model::MODEL_BOTH),
        array('addtime','time', Model::MODEL_BOTH, 'function'),
    );

    // validate
    protected $_validate = array(
        //array('password','checkPwd','密码格式不正确',0,'function'),
    );

    /**
     * 构造函数
     * @author wsq
     * @param $id 
     * @return void
     * @todo 初始化主键
     */
    public function CardPayLogModel()
    {
        parent::__construct(self::TABLE_NAME);
    }

    /**
     * 修改
     * @author wsq
     * @param array $arr 信息数组
     * @return boolean 操作结果
     * @todo 修改信息
     */
    public function editRecord($arr, $id=false)
    {
        $opt = $id ? Model::MODEL_UPDATE : Model::MODEL_INSERT;

        if (!is_array($arr)) {return false;} //参数不合法
        if ($this->create($arr, $opt)) return ($id ? $this->where(self::PRIMARY_KEY . ' = %d ', $id)->save() : $this->add()); 
        else return false; //数据获取出错
    }


    /**
     * 删除
     * @author wsq
     * @param int $id 
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delRecord($id, $delete=true)
    {
        if (!is_numeric($id)) return false;
        if ($delete) return $this->where(self::PRIMARY_KEY . ' = %d', $id)->delete();  //真删除
        else return $this->where(self::PRIMARY_KEY . ' = %d', $id)->save(array('isuse' => 2)); //假删除
    }

    /**
     * 获取列表页数据信息列表
     * @author wsq
     * @param array $list
     * @return array $list
     * @todo 根据传入的$friend_list获取更详细的列表页数据信息列表
     */
    public function getListData($list)
    {
        foreach ($list AS $k => $v) {
        }
        return $list;
    }
}
