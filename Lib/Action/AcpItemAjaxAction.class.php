<?php
/**
 * 商品管理ajax类
 */
class AcpItemAjaxAction extends AcpAction {

    // 商品模型对象
    protected $Item;

    /**
     * 初始化
     * @author 张勇
     * @return void
     * @todo 初始化方法
     */
    function _initialize() {
        parent::_initialize();

        // 实例化商品模型类
        $this->Item = D('Item');
    }

    /**
     * 通过ajax对单个商品进行的操作
     * @author 张勇
     * @return string 操作结果
     * @todo 通过ajax对单个商品进行的操作
     */
    public function single_action() {
        $id     = I('id', 0);
        $action = I('action', '');

        switch ($action) {
            // 删除商品
            case 'del_item':
                $res = $this->Item->delItem($id);
                break;

            // 还原已删除商品到出售中
            case 'restore_to_sale':
                $res = $this->Item->restoreToSale($id);
                break;

            // 还原已删除商品到仓库中
            case 'restore_to_store':
                $res = $this->Item->restoreToStore($id);
                break;

            // 彻底删除商品 (只保留商品的基本信息)
            case 'deep_del_item':
                $res = $this->deep_del_item($id);
                break;

            // 下架商品
            case 'store_item':
                $res = $this->Item->moveStorage($id);
                break;

            // 上架商品
            case 'display_item':
                $res = $this->Item->displayItem($id);
                break;
        }

        if ($res === false) {
            $this->ajaxReturn('对不起，操作失败！');
        } else {
            $this->ajaxReturn('恭喜您，操作成功！');
        }
    }

    /**
     * 通过ajax对多个商品进行的批量操作
     * @author 张勇
     * @return string 操作结果
     * @todo 通过ajax对多个商品进行的批量操作
     */
    public function batch_action() {
        $arr_id = I('arr_id', array());
        $action = I('post.action', '');

        switch ($action) {
            // 批量下架商品
            case 'batch_store_item':
                $res = $this->Item->batchMoveStorage($arr_id);
                break;

            // 批量上架商品
            case 'batch_display_item':
                $res = $this->Item->batchDisplayItem($arr_id);
                break;

            // 批量删除商品
            case 'batch_del_item':
                $res = $this->Item->batchDelItem($arr_id);
                break;

            // 批量彻底删除商品
            case 'batch_deep_del':
                $res = $this->batch_deep_del($arr_id);
                break;

            // 批量还原删除商品到出售中
            case 'batch_to_sale':
                $res = $this->Item->batchToSale($arr_id);
                break;

            // 批量还原删除商品到仓库中
            case 'batch_to_store':
                $res = $this->Item->batchToStore($arr_id);
                break;
        }

        if ($res === false) {
            $this->ajaxReturn('对不起，批量操作失败！');
        } else {
            $this->ajaxReturn('恭喜您，批量操作成功！');
        }
    }

    /**
     * 彻底删除一件商品
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo 只保留一件商品的基本信息
     */
    protected function deep_del_item($item_id) {
        // 1. 将商品表该商品的is_del字段设为2
        $res = $this->Item->deepDelItem($item_id);

        // 2. 删除关联的标签信息
        $ItemTag = D('ItemVirtualTypeDetail');
        $ItemTag->delItemTag($item_id);

        // 3. 删除关联的扩展属性信息
        $ItemVirtualTypeDetail = D('ItemExtendProperty');
        $ItemVirtualTypeDetail->delItemProperty($item_id);

        // 4. 删除主图（保留默认图片）
        $ItemPhoto = D('ItemPhoto');
        $ItemPhoto->delPhotos($item_id);

        // 5. 删除关联的分销商等级价格信息
        $ItemPriceRank = D('ItemPriceRank');
        $ItemPriceRank->delItemAgentRankPrice($item_id);

        // 6. 删除关联的SKU信息
        $ItemSku = D('ItemSku');
        $ItemSku->delItemSku($item_id);

        // 7. 删除关联的商品描述
        $ItemTxt = D('ItemTxt');
        $ItemTxt->delItemTxt($item_id);

        // 8. 删除关联的商品描述图片
        $ItemTxtPhoto = D('ItemTxtPhoto');
        $ItemTxtPhoto->delItemTxtPhoto($item_id);

        return $res;
    }

    /**
     * 批量彻底删除商品
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo 只保留一件商品的基本信息
     */
    protected function batch_deep_del($arr_id) {
        foreach ($arr_id as $id) {
            $this->deep_del_item($id);
        }

        return true;
    }

    /**
     * 通过ajax上传图片
     * @author 张勇
     */
    public function upload_img() {
        $pic_info = uploadImg('userfile');
        exit(json_encode($pic_info));
    }

    /**
     * 通过ajax删除图片
     * @author 张勇
     */
    public function del_img() {
        @unlink(APP_PATH . I('post.img'));
    }

    /**
     * 通过ajax添加商品属性值
     * @author 张勇
     */
    public function add_prop_value() {
        $prop_id    = I('post.prop_id');
        $prop_value = I('post.prop_value');
        $serial     = I('post.serial', 0);
        $id = 0;
        if ($prop_id && $prop_value) {
            $data = array(
                'property_id'    => $prop_id,
                'property_value' => $prop_value,
                'serial'         => $serial,
                'isuse'          => 1
            );

            $id = M('PropertyValue')->add($data);
        }
        $this->ajaxReturn($id);
    }

    /**
     * 通过ajax添加扩展属性
     * @author 张勇
     */
    public function add_extend_prop() {
        $p_type = I('post.p_type');
        $p_name = I('post.p_name');
        $p_serial = I('post.p_serial');
        $p_value = I('post.p_value');

        $data_return = false;
        if ($p_type && ($p_name !== '') && ($p_serial !== '') && ($p_value !== '')) {
            $data = array(
                'item_type_id' => $p_type,
                'is_extended_property' => 1,
                'property_name' => $p_name,
                'serial' => $p_serial,
                'isuse' => 1
            );

            $property_id = M('Property')->add($data);

            $PropertyValue = D('PropertyValue');
            if ($property_id) {
                $prop_value = explode(',', $p_value);
                $data = $data_value = array();
                foreach ($prop_value as $v) {
                    // 过滤重复的值
                    if (in_array($v, $data_value)) continue;

                    $data_value[] = $v;
                    $data[] = array(
                        'property_id' => $property_id,
                        'property_value' => $v,
                        'isuse' => 1
                    );
                }
                $PropertyValue->addAll($data);

                $data_return = array('prop_id' => $property_id);
                $arr_prop_value = $PropertyValue->getPropertyValueList($property_id);
                foreach ($arr_prop_value as $v) {
                    $data_return['prop_value'][] = array(
                        'id'     => $v['property_value_id'],
                        'name'   => $v['property_value'],
                        'serial' => $v['serial']
                    );
                }
            }
        }

        $this->ajaxReturn($data_return);
    }

    /**
     * 根据商品类型获取相应的属性信息
     * @author 张勇
     */
    public function get_type_prop() {
        $data_return = false;
        $type_id = I('post.type_id', 0);
        
        if ($type_id) {
            require_cache('Common/func_item.php');
            $data_return = array(
                'extend' => get_type_extend_prop($type_id),
                'sku'    => get_type_sku($type_id),
            	
            );
        }
        $this->ajaxReturn($data_return);
    }
}
?>
