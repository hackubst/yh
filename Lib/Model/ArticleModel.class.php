<?php
/**
 * 文章模型类
 * table_name = tp_article
 * py_key = article_id
 */

class ArticleModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化文章id
     */
    public function ArticleModel()
    {
        parent::__construct('article');
    }

    /**
     * 获取文章信息
     * @author 姜伟
     * @param int $article_id 文章id
     * @param string $fields 要获取的字段名
     * @return array 文章基本信息
     * @todo 根据where查询条件查找文章表中的相关数据并返回
     */
    public function getArticleInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改文章信息
     * @author 姜伟
     * @param array $arr 文章信息数组
     * @return boolean 操作结果
     * @todo 修改文章信息
     */
    public function editArticle($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加文章
     * @author 姜伟
     * @param array $arr 文章信息数组
     * @return boolean 操作结果
     * @todo 添加文章
     */
    public function addArticle($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除文章
     * @author 姜伟
     * @param int $article_id 文章ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delArticle($article_id,$opt = false)
    {
        if (!is_numeric($article_id)) return false;
        if($opt)
        {
            return $this->where('article_id = ' . $article_id)->delete();
        }else{
           return $this->where('article_id = ' . $article_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取文章数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的文章数量
     * @todo 根据where子句获取文章数量
     */
    public function getArticleNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询文章信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 文章基本信息
     * @todo 根据SQL查询字句查询文章信息
     */
    public function getArticleList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getArticleField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取文章列表页数据信息列表
     * @author 姜伟
     * @param array $Article_list
     * @return array $Article_list
     * @todo 根据传入的$Article_list获取更详细的文章列表页数据信息列表
     */
    public function getListData($Article_list)
    {
        
    }

}
