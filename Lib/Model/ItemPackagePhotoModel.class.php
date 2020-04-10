<?php
/**
 * 商品图片模型类
 */

class ItemPackagePhotoModel extends Model
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
     * 通过商品ID获取商品主图列表
     * @author 张勇
     * @param string $item_id 商品id
     * @return array 商品主图列表
     * @todo 通过商品ID获取商品主图列表
     */
    public function getPhotos($item_id) {
        if (!is_numeric($item_id))  return false;
        return $this->where('item_id = ' . $item_id)->order('is_default DESC')->getField('base_pic', true);
    }

    /**
     * 根据where子句查询商品图片信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @return array 商品图片基本信息
     * @todo 根据SQL查询字句查询商品信息
     */
    public function getItemPhotoList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 删除某个商品的主图
     * @author 张勇
     * @param string $item_id 商品ID
     * @return boolean 操作结果
     * @todo 删除某个商品的主图（包含图片文件，保留默认图片）
     */
    public function delPhotos($item_id)
    {
        if (!is_numeric($item_id)) return false;

        // 删除图片文件（保留默认图片）
        require_cache('Common/func_item.php');
        $arr_photo = $this->getPhotos($item_id);
        foreach ($arr_photo as $k => $photo) {
            if ($k == 0)   continue;
            // 删除原图及其大、中、小图
            @unlink($photo);
            @unlink(big_img($photo));
            @unlink(middle_img($photo));
            @unlink(small_img($photo));
        }

        return $this->where('item_id = ' . $item_id)->delete();
    }
}
