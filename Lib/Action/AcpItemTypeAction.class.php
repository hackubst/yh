<?php
/**
 * acp后台商品类型类
 */
class AcpItemTypeAction extends AcpAction {

    // 商品类型模型对象
    protected $Type;

    /**
     * 初始化
     * @author 张勇
     * @return void
     * @todo 初始化方法
     */
    function _initialize() {
        parent::_initialize();

        $this->assign('action_title', '分类与属性');
        $this->assign('action_src', U('/AcpItemType/list_type'));

        // 实例化模型类
        $this->Type = D('ItemType');

        // 引入商品公共函数库
        require_cache('Common/func_item.php');
    }

	/**
     * 商品类型列表
     * @author 张勇
     * @return void
     * @todo 显示商品类型列表,相关表：item_type
     */
	public function list_type()
	{
        $this->assign('head_title', '商品类型列表');

        import('ORG.Util.Pagelist');

        $count = $this->Type->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $this->assign('page', $Page);

        $arr_type = $this->Type->limit($Page->firstRow, $Page->listRows)->order('item_type_id')->select();
        $this->assign('arr_type', $arr_type);

        $this->display();
	}

    /**
     * 添加商品类型
     * @author 张勇
     * @return void
     * @todo 将商品类型的基本信息入库，相关表：item_type
     */
    public function add_type()
    {
        $this->assign('head_title', '添加商品类型');

        $action = I('post.action');

        if ($action == 'add') {
            if ($this->Type->create()) {
                $this->Type->isuse = 1;
                $item_type_id = $this->Type->add();
                if ($item_type_id) {
                    // 品牌关联信息
                    $TypeBrand = D('ItemTypeBrand');
                    #$arr_brand  = I('brand', array());
                    $data = array();
                    #foreach ($arr_brand as $brand) {
                        #$data[] = array('item_type_id' => $item_type_id, 'brand_id' => $brand);
                    #}
                    $TypeBrand->addAll($data);

                    $Property      = D('Property');
                    $PropertyValue = D('PropertyValue');

                    // 扩展属性信息
                    $arr_prop_name   = I('prop_name', array());
                    $arr_prop_values  = I('prop_value', array());                    
                    $arr_prop_serial = I('prop_serial', array());

                    foreach ($arr_prop_name as $k => $prop_name) {
                        $data = array(
                            'item_type_id' => $item_type_id,
                            'is_extended_property' => 1,
                            'property_name' => $prop_name,
                            'serial' => $arr_prop_serial[$k],
                            'isuse' => 1
                        );

                        $property_id = $Property->add($data);

                        if ($property_id) {
                 			$arr_prop_value[]  = str_replace("，", ",", $arr_prop_values[$k]);
                            $prop_value = explode(',', $arr_prop_value[$k]);
                            
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
                        }
                    }

                    // 规格属性信息
                    $arr_sku_name   = I('sku_name', array());
                    $arr_sku_values  = I('sku_value', array());
                    
 
                    $arr_sku_serial = I('sku_serial', array());

                    foreach ($arr_sku_name as $k => $sku_name) {
                        $data = array(
                            'item_type_id' => $item_type_id,
                            'is_extended_property' => 2,
                            'property_name' => $sku_name,
                            'serial' => $arr_sku_serial[$k],
                            'isuse' => 1
                        );

                        $property_id = $Property->add($data);

                        if ($property_id) {
                        	$arr_sku_value[]  = str_replace("，", ",", $arr_sku_values[$k]);
                            $sku_value = explode(',', $arr_sku_value[$k]);
                      		
                            $data = $data_value = array();
                            foreach ($sku_value as $v) {
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
                        }
                    }
                   $this->success('恭喜您，商品类型添加成功！', U('/AcpItemType/list_type'));
                } else {
                    $this->error('对不起，商品类型添加失败！');
                }
            } else {
                $this->error($this->Type->getError());
            }
        }

        #$arr_brand = get_brand();
        #$this->assign('arr_brand', $arr_brand);

        $this->display();
    }
	
	/**
     * 编辑商品类型
     * @author 张勇
     * @return void
     * @todo 编辑商品类型的基本信息，相关表：item_type
     */
	public function edit_type()
	{
        $this->assign('head_title', '编辑商品类型');

        $id = I('id', 0);
        if (!$id)   $this->error('对不起，参数错误！');

        $TypeBrand     = D('ItemTypeBrand');
        $Property      = D('Property');
        $PropertyValue = D('PropertyValue');

        $action = I('post.action');
        if ($action == 'edit') {
            if ($this->Type->create()) {
                // 类型品牌关联
                $relate_brand = I('relate_brand');
                $arr_relate = explode(',', $relate_brand);
                #$arr_brand = I('brand', array());

                // 删除取消关联的
                $arr_diff = array_diff($arr_relate, $arr_brand);
                foreach ($arr_diff as $diff) {
                    $TypeBrand->where('item_type_id = ' . $id . ' AND brand_id = ' . $diff)->delete();
                }

                // 添加新增关联的
                $arr_diff = array_diff($arr_brand, $arr_relate);
                foreach ($arr_diff as $diff) {
                    $TypeBrand->add(array('item_type_id' => $id, 'brand_id' => $diff));
                }

                $this->Type->save();
                $this->success('恭喜您，商品类型修改成功!');
            } else {
                $this->error($this->Type->getError());
            }
        } elseif ($action == 'edit_prop') {
            // 编辑前的属性
            $old_prop_ids = I('old_prop_ids');
            
            $arr_old_prop = explode(',', $old_prop_ids);
         

            // 编辑后的属性
            $arr_prop_id = I('prop_id', array());

            // 需要删除的属性
            $arr_diff = array_diff($arr_old_prop, $arr_prop_id);
            foreach ($arr_diff as $diff) {
                $Property->where('property_id = ' . $diff)->delete();
                $PropertyValue->where('property_id = ' . $diff)->delete();
            }

            $arr_prop_name   = I('prop_name', array());
            $arr_prop_values  = I('prop_value', array());
            
            $arr_prop_serial = I('prop_serial', array());

            foreach ($arr_prop_id as $k => $prop_id) {
                // 新增的属性
                if ($prop_id == 0) {
                    $data = array(
                        'item_type_id' => $id,
                        'is_extended_property' => 1,
                        'property_name' => $arr_prop_name[$k],
                        'serial' => $arr_prop_serial[$k],
                        'isuse' => 1
                    );

                    $property_id = $Property->add($data);

                    if ($property_id) {
                    	$arr_prop_value[]    = str_replace("，", ",", $arr_prop_values[$k]);
                        $prop_value = explode(',', $arr_prop_value[$k]);
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
                    }
                } else {
                    $data = array(
                        'property_name' => $arr_prop_name[$k],
                        'serial' => $arr_prop_serial[$k]
                    );

                    $Property->where('property_id = ' . $prop_id)->save($data);

                    // 编辑后的属性值
                    $arr_prop_value[]    = str_replace("，", ",", $arr_prop_values[$k]);
                    $arr_value = explode(',', $arr_prop_value[$k]);

                    // 编辑前的属性值
                    $old_values = array();
                    $arr_old_value = $PropertyValue->getPropertyValueList($prop_id);
                    foreach ($arr_old_value as $old_value) {
                        $old_values[] = $old_value['property_value'];
                    }

                    // 需要删除的属性值
                    $arr_diff = array_diff($old_values, $arr_value);
                    foreach ($arr_diff as $diff) {
                        $PropertyValue->where('property_id = ' . $prop_id . " AND property_value = '" . $diff . "'")->delete();
                    }

                    $data = $data_value = array();
                    foreach ($arr_value as $k => $v) {
                        if (($key = array_search($v, $old_values)) !== false) {
                            $PropertyValue->where('property_value_id = ' . $arr_old_value[$key]['property_value_id'])->save(array('serial' => $k));
                        } else {
                            // 过滤重复的值
                            if (in_array($v, $data_value)) continue;

                            $data_value[] = $v;
                            $data[] = array(
                                'property_id'    => $prop_id,
                                'property_value' => $v,
                                'serial'         => $k,
                                'isuse'          => 1
                            );
                        }
                    }
                    $PropertyValue->addAll($data);
                }
            }
           $this->success('恭喜您，扩展属性编辑成功！');
        } elseif ($action == 'edit_sku') {
            // 编辑前的规格
            $old_sku_ids = I('old_sku_ids');
            
            $arr_old_sku = explode(',', $old_sku_ids);

            // 编辑后的规格
            $arr_sku_id = I('sku_id', array());

            // 需要删除的规格
            $arr_diff = array_diff($arr_old_sku, $arr_sku_id);
            foreach ($arr_diff as $diff) {
                $Property->where('property_id = ' . $diff)->delete();
                $PropertyValue->where('property_id = ' . $diff)->delete();
            }

            $arr_sku_name   = I('sku_name', array());
            $arr_sku_values  = I('sku_value', array());     
            $arr_sku_serial = I('sku_serial', array());

            foreach ($arr_sku_id as $k => $sku_id) {
                // 新增的规格
                if ($sku_id == 0) {
                    $data = array(
                        'item_type_id' => $id,
                        'is_extended_property' => 2,
                        'property_name' => $arr_sku_name[$k],
                        'serial' => $arr_sku_serial[$k],
                        'isuse' => 1
                    );

                    $property_id = $Property->add($data);

                    if ($property_id) {
                    	$arr_sku_value[]   = str_replace("，", ",", $arr_sku_values[$k]);
                        $sku_value = explode(',', $arr_sku_value[$k]);
      
                        
                        $data = $data_value = array();
                        foreach ($sku_value as $v) {
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
                    }
                } else {
                    $data = array(
                        'property_name' => $arr_sku_name[$k],
                        'serial' => $arr_sku_serial[$k]
                    );

                    $Property->where('property_id = ' . $sku_id)->save($data);

                    // 编辑后的规格值
                    $arr_sku_value[]   = str_replace("，", ",", $arr_sku_values[$k]);
                    $arr_value = explode(',', $arr_sku_value[$k]);
						            
                    // 编辑前的规格值
                    $old_values = array();
                    $arr_old_value = $PropertyValue->getPropertyValueList($sku_id);
                    foreach ($arr_old_value as $old_value) {
                        $old_values[] = $old_value['property_value'];
                    }

                    // 需要删除的规格值
                    $arr_diff = array_diff($old_values, $arr_value);
                    foreach ($arr_diff as $diff) {
                        $PropertyValue->where('property_id = ' . $sku_id . " AND property_value = '" . $diff . "'")->delete();
                    }

                    $data = $data_value = array();
                    foreach ($arr_value as $k => $v) {
                        if (($key = array_search($v, $old_values)) !== false) {
                            $PropertyValue->where('property_value_id = ' . $arr_old_value[$key]['property_value_id'])->save(array('serial' => $k));
                        } else {
                            // 过滤重复的值
                            if (in_array($v, $data_value)) continue;

                            $data_value[] = $v;
                            $data[] = array(
                                'property_id'    => $sku_id,
                                'property_value' => $v,
                                'serial'         => $k,
                                'isuse'          => 1
                            );
                        }
                    }
                    $PropertyValue->addAll($data);
                }
            }
           $this->success('恭喜您，规格属性编辑成功！');

        }

        if ($type = $this->Type->getItemType($id)) {
            $this->assign('type', $type);
        } else {
            $this->error('对不起，没有找到相关信息！');
        }

        /***** 获取关联的商品品牌 开始 *****/
        /*$arr_brand = get_brand();
        $arr_relate_brand = $TypeBrand->getBrandListByTypeId($id);

        $relate_brand = '';
        if ($arr_relate_brand) {
            foreach ($arr_brand as $k => $brand) {
                if (in_array($brand['brand_id'], $arr_relate_brand)) {
                    $arr_brand[$k]['is_relate'] = 1;
                }
            }

            $relate_brand = implode(',', $arr_relate_brand);
        }

        $this->assign('relate_brand', $relate_brand);
		$this->assign('arr_brand', $arr_brand);*/
        /***** 获取关联的商品品牌 结束 *****/

        /***** 获取扩展属性 开始 *****/
        $arr_prop = $Property->where('item_type_id = ' . $id . ' AND is_extended_property = 1')->order('serial')->select();
        $old_prop_ids = '';
        foreach ($arr_prop as $k => $prop) {
            $arr_prop_value = $PropertyValue->where('property_id = ' . $prop['property_id'])->order('serial, property_value_id')->getField('property_value', true);
            $arr_prop[$k]['prop_value'] = implode(',', $arr_prop_value);
            $old_prop_ids .= $prop['property_id'] . ',';
        }
        $this->assign('arr_prop', $arr_prop);
        $this->assign('old_prop_ids', substr($old_prop_ids, 0, -1));
        /***** 获取扩展属性 结束 *****/

        /***** 获取规格属性 开始 *****/
        $arr_sku = $Property->where('item_type_id = ' . $id . ' AND is_extended_property = 2')->order('serial')->select();
        $old_sku_ids = '';
        foreach ($arr_sku as $k => $sku) {
            $arr_sku_value = $PropertyValue->where('property_id = ' . $sku['property_id'])->order('serial ASC, property_value_id ASC')->getField('property_value', true);
            $arr_sku[$k]['prop_value'] = implode(',', $arr_sku_value);
            $old_sku_ids .= $sku['property_id'] . ',';
        }
        $this->assign('arr_sku', $arr_sku);
        $this->assign('old_sku_ids', substr($old_sku_ids, 0, -1));
        /***** 获取规格属性 结束 *****/

        $this->display();
	}

    /**
     * 通过ajax删除商品类型
     * @author 张勇
     * @return void
     * @todo 通过ajax删除商品类型
     */
    public function ajax_del_type()
    {
        $id = I('id', 0);

        if ($this->del_type($id) === false) {
            $this->ajaxReturn('对不起，商品类型删除失败！请确认不存在该类型的商品。');
        } else {
            $this->ajaxReturn('恭喜您，商品类型删除成功！');
        }
    }

    /**
     * 通过ajax批量删除商品类型
     * @author 张勇
     * @return void
     * @todo 通过ajax批量删除商品类型
     */
    public function ajax_batch_del_type()
    {
        $arr_id = I('arr_id', array());

        foreach ($arr_id as $id) {
            if ($this->del_type($id) === false) {
                $this->ajaxReturn('对不起，商品类型删除失败！请确认不存在该类型的商品。');
            }
        }

        $this->ajaxReturn('恭喜您，商品类型删除成功！');
    }

    /**
     * 删除商品类型
     * @author 张勇
     * @param int $id 类型ID
     * @return voi
     * @todo 删除商品类型，包括对应的扩展属性和规格，相关表：item_type、item_type_brand、property、property_value
     */
    protected function del_type($id)
    {
        // 判断是否存在该类型的商品
        $count = M('Item')->where('item_type_id = ' . $id)->count();

        if ($count) {
            $res = false;
        } else {
            // 删除类型信息
            $res = $this->Type->where('item_type_id = ' . $id)->delete();

            // 删除关联信息
            if ($res !== false) {
                // 删除品牌关联信息
                // $TypeBrand = D('ItemTypeBrand');
                // $TypeBrand->delTypeBrand($id);

                // // 删除规格属性关联信息
                // $Property = D('Property');
                // $PropertyValue = D('PropertyValue');
                // $arr_prop = $Property->getItemTypePropertyList($id);
                // foreach ($arr_prop as $prop) {
                //     $PropertyValue->delPropertyValue($prop['property_id']);
                // }
                // $Property->delTypeProperty($id);
                // 
                $property_obj = new PropertyModel();
                $pro_arr = $property_obj->getPropertyIds($id);
                $property_obj->delTypeProperty($id);
                $pro_val_obj = new PropertyValueModel();
                foreach ($pro_arr as $k => $v) {
                    $r = $pro_val_obj->delPropertyValue($v);
                }
            }
        }

        return $res;
    }

    /**
     * 规格属性列表
     * @author 张勇
     * @return void
     * @todo 显示某种商品类型的规格属性列表,相关表：property、property_value
     */
    public function list_property()
    {

        $this->display();
    }

    /**
     * 添加规格属性
     * @author 张勇
     * @return void
     * @todo 将规格属性的信息入库，相关表：property、property_value
     */
    public function add_property()
    {

        $this->display();
    }

    /**
     * 编辑规格属性
     * @author 张勇
     * @return void
     * @todo 编辑规格属性的信息，相关表：property、property_value
     */
    public function edit_property()
    {

        $this->display();
    }

    /**
     * 删除商品规格属性
     * @author 张勇
     * @return void
     * @todo 删除商品规格属性，相关表：property、property_value
     */
    public function del_property()
    {

    }

    /**
     * 保存规格属性排序
     * @author 张勇
     * @return void
     * @todo 保存规格属性排序，相关表：item_property
     */
    public function save_property_order()
    {

    }

}
?>
