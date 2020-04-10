<?php

//商品批发价格模型
class ItemWholesalePriceModel extends Model{

	/**
	 * 添加商品批发价，若商品已存在批发价则先删除原有的在添加
	 * @param array $data 二维数组
	 */
	public function addAllPrice($item_id, $data){
		$r = $this->getItemPrice($item_id);
		if($r){
			$this->delItemPrice($item_id);
		}
		//不设置批发价
		if(count($data) == 0){
			return true;
		}
		return $this->addAll($data);
	}

	/**
	 * 获取商品批发价
	 * @param  int $item_id 商品id
	 * @return array       商品批发价数组
	 */
	public function getItemPrice($item_id, $order='min_num asc'){
		return $this->where('item_id ='.$item_id)->order($order)->select();
	}

	/**
	 * 删除商品批发价
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delItemPrice($item_id){
		return $this->where('item_id ='.$item_id)->delete();
	}

}