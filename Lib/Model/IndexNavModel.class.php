<?php
/**
 * 首页导航模型类
 */

class IndexNavModel extends Model
{
    // 首页导航id
    public $index_nav_id;
   
    /**
     * 构造函数
     * @author zlf
     * @param $index_nav_id 首页导航ID
     * @return void
     * @todo 初始化首页导航id
     */
    public function IndexNavModel($index_nav_id)
    {
        parent::__construct('index_nav');

        if ($index_nav_id = intval($index_nav_id))
		{
            $this->index_nav_id = $index_nav_id;
		}
    }

    /**
     * 获取首页导航信息
     * @author zlf
     * @param int $index_nav_id 首页导航id
     * @param string $fields 要获取的字段名
     * @return array 首页导航基本信息
     * @todo 根据where查询条件查找首页导航表中的相关数据并返回
     */
    public function getIndexNavInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改首页导航信息
     * @author zlf
     * @param array $arr 首页导航信息数组
     * @return boolean 操作结果
     * @todo 修改首页导航信息
     */
    public function editIndexNav($arr)
    {
        return $this->where('index_nav_id = ' . $this->index_nav_id)->save($arr);
    }

    /**
     * 添加首页导航
     * @author zlf
     * @param array $arr 首页导航信息数组
     * @return boolean 操作结果
     * @todo 添加首页导航
     */
    public function addIndexNav($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除首页导航
     * @author zlf
     * @param int $index_nav_id 首页导航ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delIndexNav($index_nav_id)
    {
        if (!is_numeric($index_nav_id)) return false;
		return $this->where('index_nav_id = ' . $index_nav_id)->delete();
    }

    /**
     * 根据where子句获取首页导航数量
     * @author zlf
     * @param string|array $where where子句
     * @return int 满足条件的首页导航数量
     * @todo 根据where子句获取首页导航数量
     */
    public function getIndexNavNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询首页导航信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 首页导航基本信息
     * @todo 根据SQL查询字句查询首页导航信息
     */
    public function getIndexNavList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取首页导航列表页数据信息列表
     * @author zlf
     * @param array $index_nav_list
     * @return array $index_nav_list
     * @todo 根据传入的$index_nav_list获取更详细的首页导航列表页数据信息列表
     */
    public function getListData($index_nav_list)
    {
		foreach ($index_nav_list AS $k => $v)
		{
            $index_nav_info = $this->getIndexNavInfo('index_nav_id = ' . $v['index_nav_id'],'size_style');
            $style_name = '';
            if ($index_nav_info['size_style'] == 0)
            {
                $style_name = '全幅大图';
            }
            elseif ($index_nav_info['size_style'] == 1)
            {
                $style_name = '半幅中图';
            }
            elseif ($index_nav_info['size_style'] == 2)
            {
                $style_name = '1/3幅小图';
            }
            $index_nav_list[$k]['style_name'] = $style_name;
			//产品名称
			/*$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic');
			$index_nav_list[$k]['item_name'] = $item_info['item_name'];
			$index_nav_list[$k]['mall_price'] = $item_info['mall_price'];
			$index_nav_list[$k]['small_pic'] = $item_info['base_pic'];

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$index_nav_list[$k]['status'] = $status;*/
		}

		return $index_nav_list;
    }
}
