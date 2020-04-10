<?php
/**
 * 商品详情模型类
 */

class ItemTxtModel extends Model
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
     * 添加商品详情
     * @author 张勇
     * @param array $arr_item_txt 商品详情信息
     * @return boolean 操作结果
     * @todo 添加商品详情
     */
    public function addItemTxt($arr_item_txt)
    {
        if (!is_array($arr_item_txt)) return false;
        return $this->add($arr_item_txt);
    }

    /**
     * 删除商品详情
     * @author 张勇
     * @param string $item_id 商品ID
     * @return boolean 操作结果
     * @todo 删除商品详情
     */
    public function delItemTxt($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->delete();
    }

    /**
     * 更改商品详情信息
     * @author 张勇
     * @param int $item_id 商品ID
     * @param string $content 商品详情描述
     * @return boolean 操作结果
     * @todo 更改商品详情信息
     */
    public function setItemTxt($item_id, $content)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('contents' => $content));
    }

    /**
     * 获取商品详情信息
     * @author 张勇
     * @param int $item_id 商品详情ID
     * @return array 商品详情信息
     * @todo 获取商品详情信息
     */
    public function getItemTxt($item_id)
    {
        if (!is_numeric($item_id))   return false;
        return $this->where('item_id = ' . $item_id)->getField('contents');
    }
}
