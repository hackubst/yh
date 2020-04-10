<?php
/**
 * 商品三级分类模型类
 */

class GenreModel extends BaseModel
{
    /**
     * 构造函数
     * @author 张勇
     * @todo 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加三级分类
     * @author 张勇
     * @param array $arr_genre 三级分类数组
     * @return boolean 操作结果
     * @todo 添加三级分类
     */
    public function addGenre($arr_genre)
    {
        if (!is_array($arr_genre)) return false;
        return $this->add($arr_genre);
    }

    /**
     * 删除三级分类
     * @author 张勇
     * @param string $genre_id 三级分类ID
     * @return boolean 操作结果
     * @todo 删除三级分类
     */
    public function delGenre($genre_id)
    {
        if (!is_numeric($genre_id)) return false;
        return $this->where('genre_id = ' . $genre_id)->delete();
    }

    /**
     * 删除二级分类下的所有三级分类
     * @author 张勇
     * @param string $sort_id 二级分类ID
     * @return boolean 操作结果
     * @todo 删除三级分类
     */
    public function delSortGenre($sort_id)
    {
        if (!is_numeric($sort_id)) return false;
        return $this->where('sort_id = ' . $sort_id)->delete();
    }

    /**
     * 更改三级分类
     * @author 张勇
     * @param int $genre_id 三级分类ID
     * @param array $arr_genre 三级分类数组
     * @return boolean 操作结果
     * @todo 更改三级分类
     */
    public function setGenre($genre_id, $arr_genre)
    {
        if (!is_numeric($genre_id) || !is_array($arr_genre)) return false;
        return $this->where('genre_id = ' . $genre_id)->save($arr_genre);
    }

    /**
     * 获取三级分类
     * @author 张勇
     * @param int $genre_id 三级分类ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 三级分类
     * @todo 获取三级分类
     */
    public function getGenre($genre_id, $fields = null)
    {
        if (!is_numeric($genre_id))   return false;
        return $this->field($fields)->where('genre_id = ' . $genre_id)->find();
    }

    /**
     * 获取三级分类某个字段的信息
     * @author 张勇
     * @param int $genre_id 三级分类ID
     * @param string $field 查询的字段名
     * @return array 三级分类
     * @todo 获取三级分类某个字段的信息
     */
    public function getGenreField($genre_id, $field)
    {
        if (!is_numeric($genre_id))   return false;
        return $this->where('genre_id = ' . $genre_id)->getField($field);
    }

    /**
     * 获取所有三级分类列表
     * @author 张勇
     * @param string $where where子句
     * @return array 三级分类列表
     * @todo 获取所有三级分类列表
     */
    public function getGenreList($where = null,$fields='')
    {
        return $this->field($fields)->where($where)->order('serial')->select();
    }

    /**
     * 获取二级分类下所有启用的三级分类列表
     * @author 张勇
     * @param int $sort_id 二级分类ID
     * @return array 三级分类列表
     * @todo 获取二级分类下所有isuse=1的三级分类列表
     */
    public function getSortGenreList($sort_id,$fields='')
    {
        if (!is_numeric($sort_id))   return false;
        $result = $this->field($fields)->where('sort_id = ' . $sort_id . ' AND isuse = 1')->order('serial')->select();
		return $result;
    }
}
