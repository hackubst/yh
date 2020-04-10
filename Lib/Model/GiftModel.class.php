<?php
/**
 * 礼品模型类
 */

class GiftModel extends BaseModel
{
    // 礼品id
    public $gift_id;

    //动态验证规则
    //@author wsq
    protected $_validate = array(
        array('merchant_id','require','请选择商家!'), 
        array('gift_name','require','奖品名称必填'), 
        array('serial','require','排序号必填!'), 
        array('pic','require','请上传图片!'), 
    );

    //自动完成规则1
    protected $_auto = array(
        array('serial', 'intval', Model::MODEL_BOTH, 'function'),
        array('merchant_id', 'intval', Model::MODEL_BOTH, 'function'),
        array('gift_name', 'htmlspecialchars', Model::MODEL_BOTH, 'function'),
        array('desc', 'htmlspecialchars', Model::MODEL_BOTH, 'function'),
        array('addtime', 'time', Model::MODEL_INSERT, 'function'),
    );
   
    /**
     * 构造函数
     * @author wsq
     * @param $gift_id 礼品ID
     * @return void
     * @todo 初始化礼品id
     */
    public function GiftModel($gift_id)
    {
        parent::__construct('gift');

        if ($gift_id = intval($gift_id))
		{
            $this->gift_id = $gift_id;
		}
    }

    /**
     * 获取礼品信息
     * @author wsq
     * @param int $gift_id 礼品id
     * @param string $fields 要获取的字段名
     * @return array 礼品基本信息
     * @todo 根据where查询条件查找礼品表中的相关数据并返回
     */
    public function getGiftInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 获取所有礼品信息
     * @author cc
     * @param string $fields 要获取的字段名
     * @return array 二维数组
     * @todo 根据where查询条件查找礼品表中的相关数据并返回
     */
    public function getAllGiftInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->select();
    }



    /**
     * 修改礼品信息
     * @author wsq
     * @param array $arr 礼品信息数组
     * @return boolean 操作结果
     * @todo 修改礼品信息
     */
    public function editGift($arr)
    {
        return $this->where('gift_id = ' . $this->gift_id)->save($arr);
    }

    /**
     * 添加礼品
     * @author wsq
     * @param array $arr 礼品信息数组
     * @return boolean 操作结果
     * @todo 添加礼品
     */
    public function addGift($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除礼品
     * @author wsq
     * @param int $gift_id 礼品ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delGift($gift_id)
    {
        if (!is_numeric($gift_id)) return false;
		return $this->where('gift_id = ' . $gift_id)->delete();
    }

    /**
     * 根据where子句获取礼品数量
     * @author wsq
     * @param string|array $where where子句
     * @return int 满足条件的礼品数量
     * @todo 根据where子句获取礼品数量
     */
    public function getGiftNum($where = '')
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
    public function getMerchantIsGift($merchant_id)
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
    public function getGiftList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取礼品列表页数据信息列表
     * @author wsq
     * @param array $gift_list
     * @return array $gift_list
     * @todo 根据传入的$gift_list获取更详细的礼品列表页数据信息列表
     */
    public function getListData($gift_list)
    {
        if (!count($gift_list)) return NULL;
        $merchant_obj = D('Merchant');

        foreach ($gift_list AS $k  => $v) {
            if ($v['merchant_id']) {
                $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])
                    ->getField('shop_name');
                $gift_list[$k]['shop_name'] = $shop_name ? $shop_name : '未设置';
            }

            $gift_list[$k]['status'] = $v['isuse'] ? '上架中':'未上架';
            $gift_list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
        }

		return $gift_list;
    }
}
