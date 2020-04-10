<?php
/**
 * 礼品模型类
 */

class VouchersModel extends PromoBaseModel
{
    // 礼品id
    public $vouchers_id;

    //动态验证规则
    //@author wsq
    //Model::EXISTS_VALIDATE 或者0 存在字段就验证（默认）
    //Model::MUST_VALIDATE 或者1 必须验证
    //Model::VALUE_VALIDATE或者2 值不为空的时候验证
    protected $_validate = array(
        array('merchant_id','require','请选择商家!'), 
        array('amount_limit','require','排序号必填!'), 
        array('num','require','排序号必填!'), 
        array('start_time','require','请选择开始时间!'), 
        array('end_time','require','请选择结束时间!'), 
        array('start_time,end_time', 'check_time', '日期不合法', Model::MUST_VALIDATE, 'callback'),
    );

    //自动完成规则1
    //Model:: MODEL_INSERT或者1 新增数据的时候处理（默认）
    //Model:: MODEL_UPDATE或者2更新数据的时候处理
    //Model:: MODEL_BOTH或者3所有情况都进行处理
    protected $_auto = array(
        array('merchant_id', 'intval', Model::MODEL_BOTH, 'function'),
        array('end_time','strtotime', Model::MODEL_BOTH, 'function'),
        array('start_time','strtotime',Model::MODEL_BOTH, 'function'),
        array('amount_limit','intval',Model::MODEL_BOTH, 'function'),
        array('num','intval',Model::MODEL_BOTH, 'function'),
        array('use_time','intval',Model::MODEL_BOTH, 'function'),
    );
   
    protected function check_time($param) 
    {
        $start_time  = strtotime($param['start_time']);
        $end_time    = strtotime($param['end_time']);

        if ($start_time > $end_time) return false;
        if ($end_time  < time())     return false;
        return true;
    }

    /**
     * 构造函数
     * @author wsq
     * @param $vouchers_id 礼品ID
     * @return void
     * @todo 初始化礼品id
     */
    public function VouchersModel($vouchers_id)
    {
        parent::__construct('vouchers');

        if ($vouchers_id = intval($vouchers_id))
		{
            $this->vouchers_id = $vouchers_id;
		}
    }

    /**
     * 获取礼品信息
     * @author wsq
     * @param int $vouchers_id 礼品id
     * @param string $fields 要获取的字段名
     * @return array 礼品基本信息
     * @todo 根据where查询条件查找礼品表中的相关数据并返回
     */
    public function getVouchersInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改礼品信息
     * @author wsq
     * @param array $arr 礼品信息数组
     * @return boolean 操作结果
     * @todo 修改礼品信息
     */
    public function editVouchers($arr)
    {
        return $this->where('vouchers_id = ' . $this->vouchers_id)->save($arr);
    }

    /**
     * 添加礼品
     * @author wsq
     * @param array $arr 礼品信息数组
     * @return boolean 操作结果
     * @todo 添加礼品
     */
    public function addVouchers($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除礼品
     * @author wsq
     * @param int $vouchers_id 礼品ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delVouchers($vouchers_id)
    {
        if (!is_numeric($vouchers_id)) return false;
		return $this->where('vouchers_id = ' . $vouchers_id)->delete();
    }

    /**
     * 根据where子句获取礼品数量
     * @author wsq
     * @param string|array $where where子句
     * @return int 满足条件的礼品数量
     * @todo 根据where子句获取礼品数量
     */
    public function getVouchersNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据merchant_id查询是否已礼品
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果 
     * @todo 查询是否已礼品
     */
    public function getMerchantIsVouchers($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询礼品信息
     * @author wsq
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 礼品基本信息
     * @todo 根据SQL查询字句查询礼品信息
     */
    public function getVouchersList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取礼品列表页数据信息列表
     * @author wsq
     * @param array $vouchers_list
     * @return array $vouchers_list
     * @todo 根据传入的$vouchers_list获取更详细的礼品列表页数据信息列表
     */
    public function getListData($list)
    {
        if (!count($list)) return NULL;
        $merchant_obj = D('Merchant');

        foreach ($list AS $k  => $v) {
            if ($v['merchant_id'] == 0) {
                $list[$k]['shop_name'] = "系统平台";

            } else if ($v['merchant_id']) {
                $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])
                    ->getField('shop_name');
                $list[$k]['shop_name'] = $shop_name ? $shop_name : '未设置';
            }

            $list[$k]['status'] = $v['isuse'] ? '上架中':'未上架';
            $list[$k]['scope_desc'] = $v['scope'] == PromoBaseModel::SCOPE_ALL ? '全部' : ($v['scope'] == PromoBaseModel::SCOPE_WX ? '仅微信' : '仅APP');
        }

		return $list;
    }
}
