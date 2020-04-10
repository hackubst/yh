<?php
/**
 * 套餐商品模型
 * @access public
 * @author cc
 * @Date 2015-04-30
 */
class ItemPackageDetailModel extends Model
{
	/**
	 * 套餐ID
	 */
    public $packageId = 0;

	/**
	 * 构造函数
	 * @author cc
	 * @return void
	 * @todo 初构造函数
	 */
	public function __construct($item_package_id = 0)
    {
        parent::__construct();
		$this->packageId = $item_package_id;
    }
 
	/**
	 * 获取套餐关联的商品列表
	 * @author cc
	 * @param string $fields 字段 
	 * @param string $where 查询条件
	 * @param string $order 排序条件
	 * @return array	$item_info	商品信息数组，二维
	 * @todo 根据当前套餐号查找tp_order_item表中关联的商品数据，并返回
	 */
    public function getPackageItemList($fields = '', $where = '', $order = '')
    {
		//检验套餐号合法性
		if (!$where)
		{
			$this->checkItemPackageIdValid();
		}

		//从数据库中查询
		$where = $where ? $where : 'item_package_id = ' . $this->packageId;
		$item_list = $this->field('item_id, number')->where($where)->order($order)->select();
		foreach ($item_list as $key => $value) {
			$item_info = D('Item')->field($fields)->where('item_id = ' . $value['item_id'])->find();
            $item_list[$key] = array_merge($item_list[$key], $item_info);
		}
        // dump($item_list);die;
		// echo D('Item')->getLastSql();
		return $item_list;
    }
    
	/**
     * 向套餐添加商品
     * @author cc
     * @param array $data
     * @return  boolean 操作结果
     * @todo 向套餐添加商品
     */
    public function addItemToPackage($data)
    {
        if (!is_array($data)) return false;
        return $this->add($data);
    }

    /**
     * 向套餐添加商品列表
     * @author cc
     * @param array $data
     * @return  boolean 操作结果
     * @todo 向套餐添加商品列表
     */
    public function addItemListToPackage($item_package_id, $item_list)
    {
        if (!is_array($item_list)) return false;

        foreach ($item_list as $key => $value) {
            $item_package_info = D('ItemPackage')->field('item_package_id')->where('item_package_id = '. $item_package_id .' AND item_id = ' . $value['item_id'])->find();
            if (!$item_package_info) {
                $data[$key] = array(
                    'item_package_id' => $item_package_id,
                    'item_id' => $value['item_id'],
                    'number' => $value['number'],
                );
            }
        }

        return $this->addAll($data);
    }
    
    /**
     * 删除套餐中的一个商品
     * @author cc
     * @param int   $item_id  商品ID
     * @return  void
     * @todo 删除套餐中的一个商品
     */
    public function deleteItemFromPackage($item_id)
    {
        //检验分类号合法性
        $this->checkItemPackageIdValid();

        $this->where('item_package_id = ' . $this->packageId . ' AND item_id = ' . $item_id)->delete();
    }
 
	/**
	 * 删除套餐中的所有商品
	 * @author cc
	 * @param void
	 * @return 	void
	 * @todo 从套餐商品关联表中删除该套餐中的所有商品
	 */
    public function deleteAllItem()
    {
		//检验套餐号合法性
		$this->checkItemPackageIdValid();

		$this->where('item_package_id = ' . $this->packageId)->delete();
    }

	/**
	 * 检查当前套餐号合法性
	 * @author cc
	 * @param void
	 * @return boolean
	 * @todo 若不合法，抛出异常，合法返回true
	 */
	public function checkItemPackageIdValid()
	{
		if (!$this->packageId)
		{
			throw new Exception('套餐ID不存在');
		}

		return true;
	}

}
