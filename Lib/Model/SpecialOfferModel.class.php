<?php
/**
 * 抵用券模型类
 */

class SpecialOfferModel extends PromoBaseModel
{
    // 抵用券id
    public $special_offer_id;
   
    /**
     * 构造函数
     * @author wsq
     * @param $special_offer_id 抵用券ID
     * @return void
     * @todo 初始化抵用券id
     */
    public function SpecialOfferModel($special_offer_id)
    {
        parent::__construct('special_offer');

        if ($special_offer_id = intval($special_offer_id))
		{
            $this->special_offer_id = $special_offer_id;
		}
    }

    /**
     * 获取抵用券信息
     * @author wsq
     * @param int $special_offer_id 抵用券id
     * @param string $fields 要获取的字段名
     * @return array 抵用券基本信息
     * @todo 根据where查询条件查找抵用券表中的相关数据并返回
     */
    public function getSpecialOfferInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改抵用券信息
     * @author wsq
     * @param array $arr 抵用券信息数组
     * @return boolean 操作结果
     * @todo 修改抵用券信息
     */
    public function editSpecialOffer($arr)
    {
        return $this->where('special_offer_id = ' . $this->special_offer_id)->save($arr);
    }

    /**
     * 添加抵用券
     * @author wsq
     * @param array $arr 抵用券信息数组
     * @return boolean 操作结果
     * @todo 添加抵用券
     */
    public function addSpecialOffer($arr)
    {
        if (!is_array($arr)) return false;

	$arr['addtime'] = time();

	//获取old_price
	$item_obj = new ItemModel();
	$item_info = $item_obj->getItemInfo('item_id = ' . $arr['item_id'], 'mall_price');
	$arr['old_price'] = $item_info['mall_price'];

        return $this->add($arr);
    }

    /**
     * 删除抵用券
     * @author wsq
     * @param int $special_offer_id 抵用券ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delSpecialOffer($special_offer_id)
    {
        if (!is_numeric($special_offer_id)) return false;
		return $this->where('special_offer_id = ' . $special_offer_id)->delete();
    }

    /**
     * 根据where子句获取抵用券数量
     * @author wsq
     * @param string|array $where where子句
     * @return int 满足条件的抵用券数量
     * @todo 根据where子句获取抵用券数量
     */
    public function getSpecialOfferNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据merchant_id查询是否已抵用券
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果 
     * @todo 查询是否已抵用券
     */
    public function getMerchantIsSpecialOffer($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询抵用券信息
     * @author wsq
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 抵用券基本信息
     * @todo 根据SQL查询字句查询抵用券信息
     */
    public function getSpecialOfferList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取抵用券列表页数据信息列表
     * @author wsq
     * @param array $discount_minus_list
     * @return array $discount_minus_list
     * @todo 根据传入的$discount_minus_list获取更详细的抵用券列表页数据信息列表
     */
    public function getListData($list)
    {
        $merchant_obj = D('Merchant');
        $item_obj     = D('Item');
        foreach ($list AS $k => $v) {
            //商户信息
            if ($v['merchant_id']) {
                $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])->getField('shop_name');
                $list[$k]['shop_name'] = $shop_name? $shop_name : '未设置';
            }

            //用户名称
            if ($v['user_id']) {
                $user_obj  = new UserModel($v['user_id']);
                $user_info = $user_obj->getUserInfo('nickname, realname');
                $username  = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
                $list[$k]['username'] = $username? $username: '未设置';
            }
            //商品信息
            if ($v['item_id']) {
                $item_info = $item_obj->where('item_id = ' . $v['item_id'])->find();
                $list[$k]['mall_price'] = $item_info['mall_price'];
                $list[$k]['item_name'] = $item_info['item_name'] ? : '未设置';
            }

            $list[$k]['status'] = $v['isuse'] ? '进行中':'未启用';

            $is_adv_display = intval($v['is_adv_display']);
            $is_display = '';
            switch($is_adv_display){
            	case 3:
            		$is_display = '申请被拒';break;
            	case 2:
            		$is_display = '是';break;
            	case 1:
            		$is_display = '申请中';break;
            	case 0:
            		$is_display = '否';break;
            }
            $list[$k]['is_adv_display'] = $is_adv_display;
            $list[$k]['adv_display_stat'] = $is_display;

            $list[$k]['scope_desc'] = $v['scope'] == PromoBaseModel::SCOPE_ALL ? '全部' : ($v['scope'] == PromoBaseModel::SCOPE_WX ? '仅微信' : '仅APP');
        }
        return $list;
    }

    /**
     * 获取前台抵用券列表页数据信息列表
     * @author wsq
     * @param array $special_offer_list
     * @return array $special_offer_list
     * @todo 根据传入的$special_offer_list获取更详细的特价促销列表页数据信息列表
     */
    public function getFrontListData($list)
    {
        $merchant_obj = D('Merchant');
        $item_obj     = D('Item');
		foreach ($list AS $k => $v)
		{
            //商户信息
			if ($v['merchant_id'])
			{
                $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])->getField('shop_name');
                $list[$k]['shop_name'] = $shop_name? $shop_name : '未设置';
            }

            //商品信息
            if ($v['item_id']) {
                $item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, base_pic, mall_price, sales_num, stock');
				if ($item_info)
				{
					$list[$k] = array_merge($list[$k], $item_info);
				}
            }
        }
        return $list;
    }

    /**
     * 获取有效的特价促销活动列表
     * @author jw
     * @param int $merchant_id
     * @param int $user_id
     * @return array $special_offer_list
     * @todo 获取有效的特价促销活动列表
     */
    public function getValidSpecialOfferList($merchant_id, $user_id = 0, $is_adv_display = false, $item_id = 0)
    {
		$user_id = $user_id ? $user_id : intval(session('user_id'));
		//时间在有效期内
		$where = 'isuse = 1';
		$where .= ' AND start_time <= ' . time() . ' AND end_time > ' . time();
		//是否特价促销列表页商品
		$where .= $is_adv_display ? ' AND is_adv_display = 2' : '';
		//商品ID
		$where .= $item_id ? ' AND item_id = ' . $item_id : '';
		//当前商家
		$where .= $merchant_id ? ' AND merchant_id = ' . $merchant_id : '';
		$where .= ' AND (scope = ' . PromoBaseModel::SCOPE_ALL . ' OR scope = ' . PromoBaseModel::SCOPE_APP . ')';

		//字段
		$fields = 'special_offer_id, merchant_id, item_id, start_time, end_time, old_price, promotion_price, use_time, title';

		$special_offer_obj = new SpecialOfferModel();
		$special_offer_list = $special_offer_obj->getSpecialOfferList($fields, $where, 'serial ASC,addtime DESC');
		$item_obj = new ItemModel();
		foreach ($special_offer_list AS $k => $v)
		{
			$this->special_offer_id = $v['special_offer_id'];
			//判断是否超出使用/参与次数限制
			$used_time = $this->getUsedTime($user_id);
			if ($used_time >= $v['use_time'] && !$is_adv_display)
			{
				unset($special_offer_list[$k]);
				continue;
			}
			elseif ($used_time < $v['use_time'])
			{
				$special_offer_list[$k]['num'] = $v['use_time'] - $used_time;
			}

			//获取商品信息
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, base_pic, item_desc, sales_num, stock');
			$special_offer_list[$k]['item_info'] = $item_info ? $item_info : '';
            $special_offer_list[$k]['item_info']['small_img'] = $item_info['base_pic'] ? small_img($item_info['base_pic']) : '';
		}

		return $special_offer_list;
	}

    /**
     * 获取某用户某活动参与次数
     * @author 姜伟
     * @param int $user_id
     * @return boolean
     * @todo 获取某用户某活动参与次数
     */
	function getUsedTime($user_id = 0)
	{
		$user_id = $user_id ? $user_id : intval(session('user_id'));
		$where = 'user_id = ' . $user_id . ' AND special_offer_id = ' . $this->special_offer_id;
		$user_special_offer_obj = new UserSpecialOfferModel();
		$num = $user_special_offer_obj->where($where)->sum('used_time');

		return $num ? $num : 0;
	}

    /**
     * 获取前台列表页的where字句
     * @author 姜伟
     * @param int $user_id
     * @return boolean
     * @todo 获取前台列表页的where字句
     */
	function getListWhere()
	{
		$where = 'isuse = 1';
		$where .= ' AND start_time <= ' . time() . ' AND end_time > ' . time();
		//是否特价促销列表页商品
		$where .= $is_adv_display ? ' AND is_adv_display = 2' : '';
		//商品ID
		$where .= $item_id ? ' AND item_id = ' . $item_id : '';
		//当前商家
		$where .= $merchant_id ? ' AND merchant_id = ' . $merchant_id : '';
		$where .= ' AND (scope = ' . PromoBaseModel::SCOPE_ALL . ' OR scope = ' . PromoBaseModel::SCOPE_WX . ')';

		return $where;
	}
}
