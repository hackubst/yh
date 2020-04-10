<?php
/**
 * 文章内容模型类
 */

class ArticleTxtModel extends Model
{
    // 文章内容idarticle_txt
    public $article_txt_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $article_txt_id 文章内容ID
     * @return void
     * @todo 初始化文章内容id
     */
    public function ArticleTxtModel($article_txt_id)
    {
        parent::__construct('article_txt');

        if ($article_txt_id = intval($article_txt_id))
        {
            $this->article_txt_id = $article_txt_id;
        }
    }

    /**
     * 获取文章内容信息
     * @author 姜伟
     * @param int $article_txt_id 文章内容id
     * @param string $fields 要获取的字段名
     * @return array 文章内容基本信息
     * @todo 根据where查询条件查找文章内容表中的相关数据并返回
     */
    public function getArticleTxtInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改文章内容信息
     * @author 姜伟
     * @param array $arr 文章内容信息数组
     * @return boolean 操作结果
     * @todo 修改文章内容信息
     */
    public function editArticleTxt($arr)
    {
        return $this->where('article_txt_id = ' . $this->article_txt_id)->save($arr);
    }


        /**
     * 通过文章id修改文章内容信息
     * @author 姜伟
     * @param array $arr 文章内容信息数组
     * @return boolean 操作结果
     * @todo 修改文章内容信息
     */
    public function editArticleTxtByArticleId($article_id,$arr)
    {
        //dump($arr);die('123');
        return $this->where('article_id = '.$article_id)->save($arr);
        //echo $this->getLastSql();die('321312312312');
    }

    /**
     * 添加文章内容
     * @author 姜伟
     * @param array $arr 文章内容信息数组
     * @return boolean 操作结果
     * @todo 添加文章内容
     */
    public function addArticleTxt($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除文章内容
     * @author 姜伟
     * @param int $article_txt_id 文章内容ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delArticleTxt($article_txt_id)
    {
        if (!is_numeric($article_txt_id)) return false;
        return $this->where('article_txt_id = ' . $article_txt_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取文章内容数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的文章内容数量
     * @todo 根据where子句获取文章内容数量
     */
    public function getArticleTxtNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询文章内容信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 文章内容基本信息
     * @todo 根据SQL查询字句查询文章内容信息
     */
    public function getArticleTxtList($fields = '', $where = '', $orderby = '', $limit = null)
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

}
