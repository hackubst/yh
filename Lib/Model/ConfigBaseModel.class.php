<?php
/**
 * 配置表Model
 *
 * @author zhengzhen
 * @date 2014/2/21
 *
 */
class ConfigBaseModel extends Model
{
	protected $tableName = 'config';
	
	
    /**
	 * 设置某个配置项
	 *
	 * @param string $name 配置项参数英文名称
	 * @param string $value 配置项参数新设置值
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_config中config_name为$value
	 *
	 */
    public function setConfig($name, $value)
    {
		$status = $this->where('config_name="' . $name . '"')->setField('config_value', $value);
        if (!$status) {
            $status = $this->add(array(
                'config_name'   => $name,
                'config_value'  => $value,
            ));
        }
        return $status;
    }
    
    /**
     * @access public
     * @todo 批量设置配置项
     * @param $arr 索引数组，键名为配置项名，元素值为配置项值。必须
     */
    public function setConfigs($arr)
    {
    	if(!$arr || empty($arr))
    	{
    		return FALSE;
    	}
    	foreach($arr as $k=>$v)
    	{
    		$this->setConfig($k, $v);
    	}
    	return TRUE;
    }
    
    
    /**
	 * 读取某个配置项
	 *
	 * @param string $name 配置项参数英文名称
	 * @return mixed 成功返回配置项值，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_config中config_name为$name的值config_value
	 *
	 */
    public function getConfig($name)
    {
		return $this->where('config_name= "' . $name . '"')->getField('config_value');
    }
    
    /**
	 * 获取配置项列表
	 *
	 * @return mixed 成功返回配置项列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_config中所有记录
	 *
	 */
    public function getConfigList()
    {
		return $this->select();
    }
    
     /**
	 * 获取顶部菜单列表
	 *
	 * @return mixed 成功返回顶部菜单表表表数组，否则返回false
	 * @author 陆宇峰
	 * @todo 获取tp_menu表中所有数据
	 *
	 */
    public function getMenuList($isuse = false)
    {
    	$menu = M('menu');
   		if ($isuse == true)
   		{
   			$menu->where('isuse = 1');
   		}
    	$menu->order('serial asc');
    	return $menu->select();
    }
    
     /**
	 * 获取一个顶部菜单
	 *
	 * @return array(),
	 * @author 陆宇峰
	 * @todo 根据id获取tp_menu表对应的数据
	 *
	 */
    public function getMenu($id, $fields = null)
    {
    	if (!is_numeric($id))   return false;
    	$menu = M('menu');
    	return $menu->field($fields)->where('id = ' . $id)->find();
    }
    
    /**
	 * 添加顶部菜单
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author 陆宇峰
	 * @todo 向表tp_menu表插入一条记录
	 *
	 */
    public function addMenu(array $data)
    {
    	$menu = M('menu');
    	$menu->create($data);
		return $menu->add();
    }
    
    /**
	 * 修改顶部菜单
	 * 
	 * @param int $id 菜单ID
	 * @param array $data 修改数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author 陆宇峰
	 * @todo 修改tp_menu表的记录
	 *
	 */
    public function editMenu($id, array $data)
    {
    	if($id < 0)
		{
			return false;
		}
		$menu = M('menu');
		$status = $menu->where('id=' . $id)->setField($data);
		return $status;
    }
    
    /**
	 * 删除顶部菜单
	 *
	 * @param int $id 菜单ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author 陆宇峰
	 * @todo 删除tp_menu表的记录
	 *
	 */
    public function delMenu($id)
    {
    	if($id < 0)
		{
			return false;
		}
		$menu = M('menu');
		return $menu->where('id=' . $id)->delete();
    }
    
    /**
	 * 获取菜单类型列表
	 *
	 * @return array() 菜单类型
	 * @author 陆宇峰
	 * @todo 从常量表取出3种类型
	 *
	 */
    public function getMenuType()
    {
    	return array(
    	MENU_TYPE_ITEMS_CLASS	=> '商品分类',
    	MENU_TYPE_ARTICLE_CLASS => '文章分类'	,
    	MENU_TYPE_OUT_LINK 		=> '自定义链接',
    	);
    }
    
     /**
	 * 获取菜单的最大排序号
	 *
	 * @return int 排序号
	 * @author 陆宇峰
	 * @todo 从tp_menu表取max series
	 *
	 */
    public function getMaxMenuSerial()
    {
    	$menu = M('menu');
		return $menu->max('serial');
    }
    
	/**
	 * 获取指定ID头部菜单启用状态
	 *
	 * @param int $id 头部菜单ID
	 * @return mixed 成功返回菜单启用状态，否则返回false
	 * @author 陆宇峰
	 * @todo 获取表tp_menu表中id为$id的isuse值
	 *
	 */
	public function getMenuIsuse($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		$menu = M('menu');
		return $menu->where('id=' . $id)->getField('isuse');
	}
	
	
    /**
	 * 设置菜单的排序
	 *
	 * @param int $id
	 * @param int $serial
	 * @return bool
	 * @author lyf
	 *
	 */
	public function setMenuSerial($id, $serial)
	{
		return $this->editMenu($id, array('serial' => $serial));
	}
	
	
    /**
	 * 设置菜单的状态为关闭或开启
	 *
	 * @param int $id
	 * @param int $serial
	 * @return bool
	 * @author lyf
	 *
	 */
	public function setMenuIsuse($id, $isuse)
	{
		return $this->editMenu($id, array('isuse' => $isuse));
	}
}
?>
