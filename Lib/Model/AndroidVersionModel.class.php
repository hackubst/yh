<?php
/**
 * 频道栏目模型类
 */

class AndroidVersionModel extends Model
{
    private $android_version_id;
    /**
     * 构造函数
     * @author 姜伟
     * @todo 构造函数
     */
    public function __construct($android_version_id=0)
    {
        parent::__construct();
        $this->android_version_id = $android_version_id;
    }

    public function getAndroidVersionNum($where='') {
        return $this->where($where)->count();
    }

    /**
     * 添加视频分类
     * @author 姜伟
     * @param array $arr_class 视频分类数组
     * @return boolean 操作结果
     * @todo 添加视频分类
     */
    public function addAndroidVersion($arr)
    {
        if (!is_array($arr)) return false;
        $arr['addtime'] = time();
        return $this->add($arr);
    }

    /**
     * 删除视频分类
     * @author 姜伟
     * @param string $android_version_id 视频分类ID
     * @return boolean 操作结果
     * @todo 删除视频分类
     */
    public function delAndroidVersion()
    {
        if (!is_numeric($this->android_version_id)) return false;
        return $this->where('android_version_id = ' . $this->android_version_id)->delete();
    }

    /**
     * 更改视频分类
     * @author 姜伟
     * @param int $android_version_id 视频分类ID
     * @param array $arr_class 视频分类数组
     * @return boolean 操作结果
     * @todo 更改视频分类
     */
    public function editAndroidVersion($android_version_id, $arr_class)
    {
        if (!is_numeric($android_version_id) || !is_array($arr_class)) return false;
        return $this->where('android_version_id = ' . $android_version_id)->save($arr_class);
    }

    /**
     * 获取视频分类
     * @author 姜伟
     * @param int $android_version_id 视频分类ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 视频分类
     * @todo 获取视频分类
     */
    public function getAndroidVersion($android_version_id, $fields = null)
    {
        if (!is_numeric($android_version_id))   return false;
        return $this->field($fields)->where('android_version_id = ' . $android_version_id)->find();
    }

    /**
     * 获取视频分类某个字段的信息
     * @author 姜伟
     * @param int $android_version_id 视频分类ID
     * @param string $field 查询的字段名
     * @return array 视频分类
     * @todo 获取视频分类某个字段的信息
     */
    public function getAndroidVersionField($android_version_id, $field)
    {
        if (!is_numeric($android_version_id))   return false;
        return $this->where('android_version_id = ' . $android_version_id)->getField($field);
    }

    /**
     * 获取所有视频分类列表
     * @author 姜伟
     * @param string $where where子句
     * @return array 视频分类列表
     * @todo 获取所有视频分类列表
     */
    public function getAndroidVersionList($where = null)
    {
        return $this->where($where)->order('addtime desc')->limit()->select();
    }

    public function getValidAndroidVersionList()
    {
        return $this->where('isuse = 1')->order('serial')->limit(1000000)->select();
    }

    /**
     * 获取分类信息
     * @author 姜伟
     * @param string $where where子句
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getAndroidVersionInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }


    public function getListData($list){
        foreach ($list as $k => $v) {
            $list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
        }
        return $list;
    }


    public function getRemarkByVersion($version){
        return $this->where('version = "'.$version.'"')->getField('remark');
    }


    public function getInfoyVersion($version){
        return $this->where('version = "'.$version.'"')->find();
    }
}
