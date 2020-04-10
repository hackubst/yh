<?php
/**
 * 套餐模型类
 */

class ItemPackageModel extends Model
{
    // 套餐id
    public $item_package_id;
    
    // 套餐信息数组
    public $item_package_info = array();

    /**
     * 构造函数
     * @author cc
     * @param $item_package_id 套餐ID
     * @return void
     * @todo 初始化套餐id
     */
    public function __construct($item_package_id)
    {
        parent::__construct();
        require_cache('Common/func_item.php');

        if ($item_package_id = intval($item_package_id))
            $this->item_package_id = $item_package_id;
    }

    /**
     * 添加套餐
     * @author cc
     * @param array $arr_item_package_info 套餐信息数组
     * @return boolean 操作结果
     * @todo 添加套餐
     */
    public function addItemPackage($arr_item_package_info)
    {
        if (!is_array($arr_item_package_info)) return false;

        $data = $arr_item_package_info;
        return $this->add($data);
    }

    /**
     * 删除套餐
     * @author cc
     * @param int $item_package_id 套餐ID
     * @return boolean 操作结果
     * @todo isuse设为2
     */
    public function delItemPackage($item_package_id)
    {
        if (!is_numeric($item_package_id)) return false;
        return $this->where('item_package_id = ' . $item_package_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取套餐数量
     * @author cc
     * @param string|array $where where子句
     * @return int 满足条件的套餐数量
     * @todo 根据where子句获取套餐数量
     */
    public function getItemPackageNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询套餐信息
     * @author cc
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @return array 套餐基本信息
     * @todo 根据SQL查询字句查询套餐信息
     */
    public function getItemPackageList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取套餐列表页数据信息列表
     * @author zlf
     * @param array $item_list
     * @return array $item_list
     * @todo 根据传入的$item_list获取更详细的套餐列表页数据信息列表
     */
    public function getListData($item_list)
    {
        $user_id = intval(session('user_id'));
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . $user_id);
        $user_rank_obj = new UserRankModel;
        foreach ($item_list AS $k => $v)
        {
            $item_list[$k]['link_item'] = U('/FrontMall/detail/item_id/' . $v['item_id']);
            $item_list[$k]['small_img'] = $v['base_pic'] ? small_img($v['base_pic']) : '';
            $item_list[$k]['middle_img'] = $v['base_pic'] ? middle_img($v['base_pic']) : '';
            $status = '';
            if ($v['isuse'] == 0)
            {
                $status = '仓库中';
            }
            else
            {
                $status = '出售中';
            }
            $item_list[$k]['status'] = $status;
             //vip价
            $user_rank = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_info['user_rank_id'], 'discount');
            $item_list[$k]['vip_price'] = round($user_rank['discount'] * $v['mall_price'] / 100, 2);
        }

        return $item_list;
    }

    /**
     * 获取套餐信息
     * @author cc
     * @param string $fields 要获取的字段名
     * @return array 套餐基本信息
     * @todo 根据where查询条件查找套餐表中的相关数据并返回
     */
    public function getItemPackageInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改套餐信息
     * @author cc
     * @param array $arr 套餐信息数组
     * @return boolean 操作结果
     * @todo 修改套餐信息
     */
    public function editItemPackage($arr)
    {
        return $this->where('item_package_id = ' . $this->item_package_id)->save($arr);
    }
}