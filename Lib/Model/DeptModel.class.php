<?php
class DeptModel extends Model
{

    // 常量定义
    const TABLE_NAME  = 'dept';
    const PRIMARY_KEY = 'dept_id';
    
    /**
     * 构造函数
     * @author wsq
     * @param $id 
     * @return void
     * @todo 初始化主键
     */
    public function DeptModel()
    {
        parent::__construct(self::TABLE_NAME);
    }

    // auto complete
    protected $_auto = array(
        array('isuse','0', Model::MODEL_BOTH),
        //array('addtime','time', Model::MODEL_BOTH, 'function'),
    );

    // validate
    protected $_validate = array(
        //array('password','checkPwd','密码格式不正确',0,'function'),
    );


    // map
    /*
     CardCode 卡号
     MemberName 会员名字
     CardLevel 卡级别
     IsIntegral 是否有积分功能，1是 0否
     IsDeposited 是否有储值功能，1是 0否，为1时能做为支付对象
     CurrentIntegral 当前积分
     PeriodOfValidity 卡有效期，格式YYYYMMDD
     CurrentBalance 当前余额
     */
    //
    protected $_map = array(
        'NodeCode' => 'node_code',
        'NodeName' => 'node_name',
        'Address' => 'address',
        'PhoneNum' => 'phone_number',
        'NodeType' => 'node_type',
    );

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

    // 获取门店列表
    // @author wsq
    public function getDeptListFromERP($dept_code='')
    {
        $data = array('NodeCode' => $dept_code);
        $data = json_encode($data);
        // 数据进行json 格式转换
        // 通过接口发送数据
        $status = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_GET_DEPT_INFO,
                'msg' => $data,
            )
        );

        if ($status['Sucess'] && $status['Compress']) {
            $ret_data = ConnectionModel::decodeCompressData($status['CompressData']);
            return $ret_data;
        } else {
            return NULL;
        }
    }

    // 同步ERP门店信息
    // @author wsq
    public function syncData()
    {
        $data = $this->getDeptListFromERP();
        foreach ($data AS $k => $v) {
            $id = $this->where('node_code="%s"', $v['NodeCode'])->getField(self::PRIMARY_KEY);
            $this->editRecord($v, $id);
        }
    }

    // 获取配送信息
    // @author wsq
    public function getSendWayInfo($id)
    {
        $result = NULL;
        if ($id == OrderModel::MAIN_FACTORY || !$id) {
            return $result;
        }

        $info = $this->where( "node_code=%d", intval($id))->find();

        if ($info) {
            $result .= $info['node_name'] ? $info['node_name']."," : "";
            $result .= $info['address'] ? '地址:'.$info['address'] : "";
            $result .= $info['phone_number'] ? '，联系电话'.$info['phone_number'] : "";
        } 

        return $result;

    }
}
