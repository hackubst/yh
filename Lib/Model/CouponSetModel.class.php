<?php
/**
 * 优惠券设置模型类
 */

class CouponSetModel extends BaseModel
{
    // 优惠券设置id
    public $coupon_set_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $coupon_set_id 优惠券设置ID
     * @return void
     * @todo 初始化优惠券设置id
     */
    public function CouponSetModel($coupon_set_id)
    {
        parent::__construct('coupon_set');

        if ($coupon_set_id = intval($coupon_set_id))
		{
            $this->coupon_set_id = $coupon_set_id;
		}
    }

    /**
     * 获取优惠券设置信息
     * @author 姜伟
     * @param int $coupon_set_id 优惠券设置id
     * @param string $fields 要获取的字段名
     * @return array 优惠券设置基本信息
     * @todo 根据where查询条件查找优惠券设置表中的相关数据并返回
     */
    public function getCouponSetInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改优惠券设置信息
     * @author 姜伟
     * @param array $arr 优惠券设置信息数组
     * @return boolean 操作结果
     * @todo 修改优惠券设置信息
     */
    public function editCouponSet($arr)
    {
        return $this->where('coupon_set_id = ' . $this->coupon_set_id)->save($arr);
    }

    /**
     * 添加优惠券设置
     * @author 姜伟
     * @param array $arr 优惠券设置信息数组
     * @return boolean 操作结果
     * @todo 添加优惠券设置
     */
    public function addCouponSet($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除优惠券设置
     * @author 姜伟
     * @param int $coupon_set_id 优惠券设置ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delCouponSet($coupon_set_id)
    {
        if (!is_numeric($coupon_set_id)) return false;
		return $this->where('coupon_set_id = ' . $coupon_set_id)->delete();
    }

    /**
     * 根据where子句获取优惠券设置数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的优惠券设置数量
     * @todo 根据where子句获取优惠券设置数量
     */
    public function getCouponSetNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据merchant_id查询是否已优惠券设置
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果
     * @todo 查询是否已优惠券设置
     */
    public function getMerchantIsCouponSet($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询优惠券设置信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 优惠券设置基本信息
     * @todo 根据SQL查询字句查询优惠券设置信息
     */
    public function getCouponSetList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取优惠券设置列表页数据信息列表
     * @author 姜伟
     * @param array $coupon_set_list
     * @return array $coupon_set_list
     * @todo 根据传入的$coupon_set_list获取更详细的优惠券设置列表页数据信息列表
     */
    public function getListData($coupon_set_list)
    {
		foreach ($coupon_set_list AS $k => $v)
		{
		}

		return $coupon_set_list;
    }
    //@author wsq
    public function getListDataForAPI($coupon_set_list)
    {
		foreach ($coupon_set_list AS $k => $v)
		{
			//商户信息
			$merchant_obj = new MerchantModel($v['merchant_id']);
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $v['merchant_id'], 'shop_name, isuse, make_time_avg, on_time_rate, score_avg, logo, longitude, latitude, trading_area_id, class_id');
			//获取商户类型
			$class_obj = new ClassModel();
			$class_name = $class_obj->getClassField($merchant_info['class_id'], 'class_name');
			//获取商户商圈
			$area_obj = new TradingAreaModel();
			$area_name = $area_obj->getTradingAreaInfo('trading_area_id =' . $merchant_info['trading_area_id'], 'trading_area_name');
			$coupon_set_list[$k]['shop_name'] = $merchant_info['shop_name']?$merchant_info['shop_name']:'';
			$coupon_set_list[$k]['make_time_avg'] = $merchant_info['make_time_avg']?$merchant_info['make_time_avg']:0;
			$coupon_set_list[$k]['score_avg'] = $merchant_info['score_avg']?$merchant_info['score_avg']:0;
			$coupon_set_list[$k]['on_time_rate'] = $merchant_info['on_time_rate']?$merchant_info['on_time_rate']:0;
			$coupon_set_list[$k]['logo'] = $merchant_info['logo']?$merchant_info['logo']:'';
			$coupon_set_list[$k]['class_name'] = $class_name?$class_name:'';
			$coupon_set_list[$k]['area_name'] = $area_name['trading_area_name']?$area_name['trading_area_name']:'';

			$status = '';
			if ($merchant_info['isuse'] == 0)
			{
				$status = '已失效';
			}
			elseif ($merchant_info['isuse'] == 1)
			{
				$status = '线上商户';
			}

			//用户名称
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, realname');
			$username = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
			$coupon_set_list[$k]['username'] = $username;
		}

		return $coupon_set_list;
    }

    /**
     * 获取优惠券使用条件描述
     * @author 姜伟
     * @param float $amount_limit
     * @param int $class_id
     * @param int $sort_id
     * @param int $genre_id
     * @return array $coupon_set_list
     * @todo 获取优惠券使用条件描述
     */
    public static function getUseLimitDesc($amount_limit, $class_id = 0, $sort_id = 0, $genre_id = 0)
    {
		$desc = '';
		if ($genre_id)
		{
			$genre_obj = new GenreModel();
			$genre_info = $genre_obj->getGenre($genre_id, 'genre_name');
			$desc = '购买【' . $genre_info['genre_name'] . '】类商品';
		}
		/*elseif ($sort_id)
		{
			$sort_obj = new SortModel();
			$sort_info = $sort_obj->getSort($sort_id, 'sort_name');
			$desc = '购买【' . $sort_info['sort_name'] . '】类商品';
		}
		elseif ($class_id)
		{
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClass($class_id, 'class_name');
			$desc = '购买【' . $class_info['class_name'] . '】类商品';
		}*/

		if ($amount_limit)
		{
			$desc .= $desc ? '且' : '';
			$desc .= '满' . $amount_limit . '元时可用';
		}
		else
		{
			if ($desc)
			{
				$desc .= '时可用';
			}
		}

		return $desc ? $desc : '全场通用';
	}
}
