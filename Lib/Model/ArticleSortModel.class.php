<?php
/**
 * 文章分类模型类
 */

class ArticleSortModel extends Model
{
    // 文章分类id
    public $article_sort_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $article_sort_id 文章分类ID
     * @return void
     * @todo 初始化文章分类id
     */
    public function ArticleSortModel($article_sort_id)
    {
        parent::__construct('article_sort');

        if ($article_sort_id = intval($article_sort_id))
        {
            $this->article_sort_id = $article_sort_id;
        }
    }

    /**
     * 获取文章分类信息
     * @author 姜伟
     * @param int $article_sort_id 文章分类id
     * @param string $fields 要获取的字段名
     * @return array 文章分类基本信息
     * @todo 根据where查询条件查找文章分类表中的相关数据并返回
     */
    public function getArticleSortInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改文章分类信息
     * @author 姜伟
     * @param array $arr 文章分类信息数组
     * @return boolean 操作结果
     * @todo 修改文章分类信息
     */
    public function editArticleSort($arr)
    {
        return $this->where('article_sort_id = ' . $this->article_sort_id)->save($arr);
    }

    /**
     * 添加文章分类
     * @author 姜伟
     * @param array $arr 文章分类信息数组
     * @return boolean 操作结果
     * @todo 添加文章分类
     */
    public function addArticleSort($arr)
    {
        if (!is_array($arr)) return false;

        return $this->add($arr);
    }

    /**
     * 删除文章分类
     * @author 姜伟
     * @param int $article_sort_id 文章分类ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delArticleSort($article_sort_id)
    {
        if (!is_numeric($article_sort_id)) return false;
        return $this->where('article_sort_id = ' . $article_sort_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取文章分类数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的文章分类数量
     * @todo 根据where子句获取文章分类数量
     */
    public function getArticleSortNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询文章分类信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 文章分类基本信息
     * @todo 根据SQL查询字句查询文章分类信息
     */
    public function getArticleSortList($fields = '', $where = '', $orderby = '', $limit = null)
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取文章分类列表页数据信息列表
     * @author 姜伟
     * @param array $article_sort_list
     * @return array $article_sort_list
     * @todo 根据传入的$article_sort_list获取更详细的文章分类列表页数据信息列表
     */
    public function getListData($article_sort_list)
    {
        require_cache('Common/func_item.php');

        foreach ($article_sort_list AS $k => $v)
        {
            //产品名称
            $item_obj = new ItemModel($v['item_id']);
            $item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic, is_gift');
            $article_sort_list[$k]['item_name']  = $item_info['item_name'];
            $article_sort_list[$k]['mall_price'] = $item_info['mall_price'];
            $article_sort_list[$k]['is_gift']    = $item_info['is_gift'];
            $article_sort_list[$k]['small_pic']  = small_img($item_info['base_pic']);

            $status = '';
            if ($item_info['isuse'] == 0)
            {
                $status = '已下架';
            }
            elseif ($item_info['isuse'] == 1)
            {
                $status = $item_info['stock'] ? '上架中' : '缺货';
            }
            $article_sort_list[$k]['status'] = $status;
        }

        return $article_sort_list;
    }

    // 猜你喜欢列表内容
    // 暂时先根据文章分类列表获取同类型的商品，若没有同类型的商品，则获取热门推荐里面同类型的商品
    //@author wangsongqing
    public function getGuessMyLikeList($user_id)
    {

        $item_obj = D('Item');
        $where    = 'isuse = 1 AND is_gift = 0';

        $article_sort_obj  = new ArticleSortModel();
        $article_sort_list = $article_sort_obj->getArticleSortList('item_id', 'user_id = ' . $user_id, 'addtime DESC');

        $class_ids_array = array();
        if ($article_sort_list) {
            foreach ($article_sort_list AS $k => $v) {
                $class_id = $item_obj->where('item_id = %d', $v['item_id'])->getField('class_id'); 
                $class_id ? array_push($class_ids_array, $class_id) : 0;
            }
        } 

        if (count($class_ids_array)) {
            $where .= ' AND class_id IN (' . implode(',', $class_ids_array) . ')';
        }

        $item_obj->setLimit(20);
        $hot_item_list = $item_obj->getItemList(
            'item_name, item_id, base_pic, mall_price, market_price', 
            $where,
            'sales_num DESC'
        );

        shuffle($hot_item_list);
        $result = array_slice($hot_item_list, 0, 4);

        if (count($result) < 4) {
            $item_obj->setLimit(20);
            $extra_item_list = $item_obj->getItemList(
                'item_name, item_id, base_pic, mall_price, market_price', 
                'isuse = 1 AND is_gift = 0',
                'sales_num DESC'
            );
            $left_item = array_slice($extra_item_list, 0, 4 - count($result));
            $result = array_merge($result, $left_item);
        }

        return $result;
    }
}
