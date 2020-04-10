<?php
/**
 * 团购模型类
 */

class GroupBuyModel extends Model
{
    private $group_buy_id;
    /**
     * 构造函数
     * @author 姜伟
     * @todo 构造函数
     */
    public function __construct($group_buy_id=0)
    {
        parent::__construct();
        $this->group_buy_id = $group_buy_id;
    }

    //自动验证
    protected $_validate = array(
        array('group_name','require','请填写团购标题！', 3), //默认情况下用正则进行验证
        array('item_id','require','请选择商品!', 3), //默认情况下用正则进行验证
        array('start_time','require','请填写团购开始时间！', 3), // 在新增的时候验证name字段是否唯一
        array('end_time','require','请填写团购结束时间！', 3), // 在新增的时候验证name字段是否唯一
        array('group_price','require','请填写团购价格！', 3), // 在新增的时候验证name字段是否唯一
        array('people_limit','require','请填写团购人数！', 3), // 在新增的时候验证name字段是否唯一
        array('serial','/^\d+$/','请填写正确的排序号！', 3), // 在新增的时候验证name字段是否唯一
    );

    //自动完成
    protected $_auto = array(
        array('start_time','to_time',3,'callback'), 
        array('end_time','to_time',3,'callback'), 
    );

    //转为时间截
    function to_time($data)
    {
        $u_time = str_replace('+', ' ', $data);
		$u_time = strtotime($data);
        return $u_time;
    }



    public function getGroupBuyNum($where='') {
      return $this->where($where)->count();
    }

    /**
     * 添加团购
     * @author 姜伟
     * @param array $arr_group_buy 团购数组
     * @return boolean 操作结果
     * @todo 添加团购
     */
    public function addGroupBuy($arr_group_buy)
    {
        if (!is_array($arr_group_buy)) return false;
        return $this->add($arr_group_buy);
    }

    /**
     * 删除团购
     * @author 姜伟
     * @param string $group_buy_id 团购ID
     * @return boolean 操作结果
     * @todo 删除团购
     */
    public function delGroupBuy()
    {
        if (!is_numeric($this->group_buy_id)) return false;
        return $this->where('group_buy_id = ' . $this->group_buy_id)->delete();
    }

    /**
     * 更改团购
     * @author 姜伟
     * @param int $group_buy_id 团购ID
     * @param array $arr_group_buy 团购数组
     * @return boolean 操作结果
     * @todo 更改团购
     */
    public function setGroupBuy($group_buy_id, $arr_group_buy)
    {
        if (!is_numeric($group_buy_id) || !is_array($arr_group_buy)) return false;
        return $this->where('group_buy_id = ' . $group_buy_id)->save($arr_group_buy);
    }

    /**
     * 获取团购
     * @author 姜伟
     * @param int $group_buy_id 团购ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 团购
     * @todo 获取团购
     */
    public function getGroupBuy($group_buy_id, $fields = null)
    {
        if (!is_numeric($group_buy_id))   return false;
        return $this->field($fields)->where('group_buy_id = ' . $group_buy_id)->find();
    }

    /**
     * 获取团购某个字段的信息
     * @author 姜伟
     * @param int $group_buy_id 团购ID
     * @param string $field 查询的字段名
     * @return array 团购
     * @todo 获取团购某个字段的信息
     */
    public function getGroupBuyField($group_buy_id, $field)
    {
        if (!is_numeric($group_buy_id))   return false;
        return $this->where('group_buy_id = ' . $group_buy_id)->getField($field);
    }

    /**
     * 获取所有团购列表
     * @author 姜伟
     * @param string $where where子句
     * @return array 团购列表
     * @todo 获取所有团购列表
     */
    public function getGroupBuyList($field, $where = null, $orderby)
    {
        return $this->field($field)->where($where)->order($orderby)->limit()->select();
    }
 
    /**
     * 获取分类信息
     * @author 姜伟
     * @param string $where where子句
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getGroupBuyInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 数据处理
     * @author wzg
     */
    public function getListData($list)
    {
        $item_obj = new ItemModel();

        foreach($list AS $k => $v)
        {
            $item_info = $item_obj->field('item_name, base_pic')->where('item_id = ' . $v['item_id'])->find();
            $list[$k]['item_name'] = $item_info['item_name'];
            $list[$k]['pic'] = $item_info['base_pic'];
            $list[$k]['link_item'] = U('/FrontMall/item_detail/item_id/'.$v['item_id']);

            //已参团人数
            $list[$k]['num'] = M('GroupBuyUser')->where('group_buy_id = ' . $v['group_buy_id'])->count();
        }

        return $list;
    }

    /**
     * 验证团购是否可行
     * @param $group_buy_id 
     * @author wzg
     */
    public function checkGroupBuy($group_buy_id)
    {
        if (!intval($group_buy_id)) return false;

        $info = $this->getGroupBuyInfo('isuse = 1 AND group_buy_id = ' . $group_buy_id . ' AND start_time <= ' . NOW_TIME . ' AND end_time >= ' . NOW_TIME);
        if (!$info) return false;

        //$num = M('GroupBuyUser')->where('group_buy_id = ' . $group_buy_id)->count();
        //if ($info['people_limit'] == $num) return false;

        return true;
    }


    /**
     * 设置团购状态
     * $status 
     * param $group_buy_id
     */
    public function setGroupBuyStatus()
    {
        if (!$this->group_buy_id) return false;       

        //先从团购用户表中找出已参团数量
        $where = 'group_buy_id = ' . $this->group_buy_id;
        $num = M('GroupBuyUser')->where($where)->count();
        $limit = $this->where($where)->getField('people_limit');

        if ($num == $limit) {
            $this->where($where)->setField('status', 2);
        } else if ($num == 1) {
            $this->where($where)->setField('status', 1);
        }

    }
}
