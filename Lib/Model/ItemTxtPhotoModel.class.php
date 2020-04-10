<?php
/**
 * 商品详情图片信息模型类
 */

class ItemTxtPhotoModel extends Model
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
     * 添加商品详情图片信息
     * @author 张勇
     * @param array $arr_item_txt_photo 商品详情图片信息
     * @return boolean 操作结果
     * @todo 添加商品详情图片信息
     */
    public function addItemTxtPhoto($arr_item_txt_photo)
    {
        if (!is_array($arr_item_txt_photo)) return false;
        return $this->add($arr_item_txt_photo);
    }

    /**
     * 删除某个商品的详情图片信息
     * @author 张勇
     * @param string $item_id 商品ID
     * @return boolean 操作结果
     * @todo 删除某个商品的详情图片信息（包含图片文件）
     */
    public function delItemTxtPhoto($item_id)
    {
        if (!is_numeric($item_id)) return false;

        // 删除图片文件
        $arr_photo = $this->getItemTxtPhotoList($item_id);
        foreach ($arr_photo as $photo) {
            @unlink($photo['path_img']);
        }

        return $this->where('item_id = ' . $item_id)->delete();
    }

    /**
     * 获取某个商品的详情图片信息
     * @author 张勇
     * @param int $item_id 商品详情图片信息ID
     * @return array 商品详情图片信息
     * @todo 获取某个商品的详情图片信息
     */
    public function getItemTxtPhotoList($item_id)
    {
        if (!is_numeric($item_id))   return false;
        return $this->where('item_id = ' . $item_id)->getField('path_img', true);
    }
}
