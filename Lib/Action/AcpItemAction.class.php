<?php
/**
 * acp后台商品类
 */
class AcpItemAction extends AcpAction
{

    // 商品模型对象
    protected $Item;

    // 所有启用的分类
    protected $arr_category;

    /**
     * 初始化
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();

        // 引入商品公共函数库
        require_cache('Common/func_item.php');

        // 实例化商品模型类
        $this->Item = D('Item');
        $this->assign('ITEM_NAME', C('ITEM_NAME'));
    }

    /**
     * 接收搜索表单数据，组织返回where子句
     * @author 姜伟
     * @param void
     * @return void
     * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
     */
    public function get_search_condition()
    {
        //初始化查询条件
        $where = '';

        //商品名称
        $item_name = $this->_request('item_name');
        if ($item_name) {
            $where .= ' AND item_name LIKE "%' . $item_name . '%"';
        }

        //商品货号
        $item_sn = $this->_request('item_sn');
        if ($item_sn) {
            $where .= ' AND item_sn = "' . $item_sn . '"';
        }

        //分类
        $category_id = $this->_request('category_id');
        if ($category_id) {
            $arr_category = explode('.', $category_id);
            if ($arr_category[0] == 1) {
                $where .= ' AND class_id = ' . $arr_category[1];
            } elseif ($arr_category[0] == 2) {
                $where .= ' AND sort_id = ' . $arr_category[1];
            }
        }

        // 商品状态
        $item_status = $this->_request('item_status');
        if ($item_status) {
            if ($item_status == 'onsale') {
                $condition['_string'] = 'stock > 0 AND stock > stock_alarm';
                $where .= ' AND stock > 0 AND stock > stock_alarm';
            } elseif ($item_status == 'alarm') {
                $where .= ' AND stock <= stock_alarm';
            } elseif ($item_status == 'outstock') {
                $where .= ' AND stock < 1';
            }
        }

        //添加时间范围起始时间
        $start_date = $this->_request('start_date');
        $start_date = str_replace('+', ' ', $start_date);
        $start_date = strtotime($start_date);
        if ($start_date) {
            $where .= ' AND addtime >= ' . $start_date;
        }

        //添加时间范围结束时间
        $end_date = $this->_request('end_date');
        $end_date = str_replace('+', ' ', $end_date);
        $end_date = strtotime($end_date);
        if ($end_date) {
            $where .= ' AND addtime <= ' . $end_date;
        }

        //销售量范围起点
        $start_sales_num = $this->_request('start_sales_num');
        if ($start_sales_num != -1 && $start_sales_num != '') {
            $where .= ' AND sales_num >= ' . intval($start_sales_num);
        }

        //销售量范围结束点
        $end_sales_num = $this->_request('end_sales_num');
        if ($end_sales_num != -1 && $end_sales_num != '') {
            $where .= ' AND sales_num <= ' . intval($end_sales_num);
        }

        //重新赋值到表单
        $this->assign('item_name', $item_name);
        $this->assign('item_sn', $item_sn);
        $this->assign('start_sales_num', $start_sales_num);
        $this->assign('end_sales_num', $end_sales_num);
        $this->assign('start_date', $start_date ? $start_date : '');
        $this->assign('end_date', $end_date ? $end_date : '');
        $this->assign('category_id', $category_id);
        $this->assign('item_status', $item_status);

        return $where;
    }

    /**
     * 获取商品列表，公共方法
     * @author 姜伟
     * @param string $where
     * @param string $head_title
     * @param string $opt    引入的操作模板文件
     * @return void
     * @todo 获取商品列表，公共方法
     */
    public function item_list($where, $head_title, $opt)
    {
        $where .= $this->get_search_condition();
        $item_obj = new ItemModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $item_obj->getItemNum($where);
        $Page  = new Pagelist($count, C('PER_PAGE_NUM'));
        $item_obj->setStart($Page->firstRow);
        $item_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
        $this->assign('show', $show);

        $item_list = $item_obj->getItemList('', $where, ' addtime DESC');
        $item_list = $item_obj->getListData($item_list);

        $this->assign('item_list', $item_list);
        #echo "<pre>";
        #print_r($item_list);
        #echo "</pre>";
        #echo $item_obj->getLastSql();

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);
        // 商品状态
        $arr_item_status = array(
            'onsale'   => '出售中',
            'alarm'    => '库存报警',
            'outstock' => '缺货中',
        );
        $this->assign('arr_item_status', $arr_item_status);

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display(APP_PATH . 'Tpl/AcpItem/get_item_list.html');
    }

    /**
     * 出售中的商品列表
     * @author 姜伟
     * @param void
     * @return void
     * @todo 获取出售中的商品列表
     */
    public function get_onsale_item_list()
    {
        $this->item_list('isuse = 1 AND stock > 0', '出售中的' . C('ITEM_NAME') . '列表', 'onsale');
    }

    /**
     * 仓库中的商品列表
     * @author 姜伟
     * @param void
     * @return void
     * @todo 获取仓库中的商品列表
     */
    public function get_store_item_list()
    {
        $this->item_list('isuse = 0', '仓库中的' . C('ITEM_NAME') . '列表', 'store');
    }

    /**
     * 库存警报的商品列表
     * @author 姜伟
     * @param void
     * @return void
     * @todo 获取库存警报的商品列表
     */
    public function get_alarm_item_list()
    {
        $this->item_list('stock <= stock_alarm AND stock > 0', '库存警报的' . C('ITEM_NAME') . '列表', 'alarm');
    }

    /**
     * 售罄的商品列表
     * @author 姜伟
     * @param void
     * @return void
     * @todo 获取售罄的商品列表
     */
    public function get_sold_out_item_list()
    {
        $this->item_list('stock <= 0', '售罄的' . C('ITEM_NAME') . '列表', 'sold_out');
    }

    /**
     * 添加商品
     * @author 姜伟
     * @return void
     * @todo 上传新商品
     */
    public function add_item()
    {
        $this->assign('head_title', '添加' . C('ITEM_NAME'));

        $action = I('post.action');

        $Item        = D('Item');
        $is_integral = I('is_integral');
        // 添加商品
        if ($action == 'add') {
            if ($Item->create()) {
                // 商品分类
                $arr_category   = explode('.', I('category_id'));
                $Item->class_id = $arr_category[0];
                $Item->sort_id  = $arr_category[1];
                #echo "<pre>";
                #print_r($_POST);
                #echo "</pre>";
                #die;

                // 商品图片
                $data_photo = array();
                $arr_pic    = I('post.pic', array());
                foreach ($arr_pic as $k => $pic) {
                    if ($k == 0) {
                        $Item->base_pic               = $pic;
                        $data_photo[$k]['is_default'] = 1;
                    } else {
                        $data_photo[$k]['is_default'] = 0;
                    }
                    $data_photo[$k]['base_pic'] = $pic;
                }

                $Item->addtime = time();

                // 添加商品
                $item_id = $Item->add();

                if ($item_id) {
                    // 添加商品图片
                    foreach ($data_photo as $k => $photo) {
                        $data_photo[$k]['item_id'] = $item_id;

                        // 图片压缩加水印
                        $this->_resizeImg(ltrim($photo['base_pic'], '/'));
                    }
                    D('ItemPhoto')->addAll($data_photo);

                    /**** 添加商品属性 开始 *****/
                    $ItemExtendProperty = D('ItemExtendProperty');
                    $ItemSku            = D('ItemSku');

                    // 添加商品扩展属性
                    $data             = array();
                    $arr_extend_value = I('extend_prop_value', array());
                    foreach ($arr_extend_value as $value) {
                        $data[] = array(
                            'item_id'           => $item_id,
                            'property_value_id' => $value,
                        );
                    }
                    $ItemExtendProperty->addAll($data);

                    // 添加商品规格属性
                    $has_sku = I('has_sku', 0);
                    $Item->where('item_id = ' . $item_id)->save(array('has_sku' => $has_sku));
                    if ($has_sku) {
                        //如果开启sku
                        $new_skus      = $this->_post('new_sku', array()); //    为该商品添加的sku信息
                        $total_stock   = 0; // 商品库存
                        $ItemPriceRank = D('ItemPriceRank');
                        //新添加的sku信息
                        if (!empty($new_skus)) {
                            $data_value = array(); //用来过滤重复的sku数据
                            foreach ($new_skus as $key => $value) //按照前台页面的规定，此处的$value代表一条新的sku临时标记（整数且大于等于1，开始,唯一的）
                            {
                                $data          = array(); //要添加的数据
                                $new_sku0_val  = $this->_post('new_J_sku0_' . $value);
                                $new_sku1_val  = $this->_post('new_J_sku1_' . $value);
                                $new_sku1_val = intval($new_sku1_val);
                                $new_sku_sn    = $this->_post('new_sku_sn' . $value);
                                $new_sku_stock = $this->_post('new_sku_stock' . $value);
                                $new_sku_price = $this->_post('new_sku_price' . $value);
                                // 过滤重复数据
                                if (in_array($new_sku0_val . ',' . $new_sku1_val, $data_value)) {
                                    continue;
                                } else {
                                    $data_value[] = $new_sku0_val . ',' . $new_sku1_val;
                                }

                                $data = array(
                                    'item_sn'         => $new_sku_sn,
                                    'sku_price'       => $new_sku_price,
                                    'sku_stock'       => $new_sku_stock,
                                    'property_value1' => $new_sku0_val,
                                    'property_value2' => $new_sku1_val,
                                    'isuse'           => 1,
                                );
                                $new_sku_id = $ItemSku->addSku($item_id, $data); //执行添加
                                if (!$new_sku_id) {
                                    continue;
                                }
                                $total_stock += $new_sku_stock; //库存
                                //新的sku为每一个会员级别所设置的价格
                                $new_sku_agent_rank_ids   = $this->_post('new_sku_rank_id' . $value); //这又是一个数组，代表着所有的用户的级别ID
                                $new_sku_agent_rank_price = $this->_post('new_sku_rank_price' . $value); //此也是一个数组，代表着该sku商品的每一个用户级别的价格。该数组的值是与上面的级别数组一一对应的
                                #myprint($new_sku_agent_rank_ids);
                                foreach ($new_sku_agent_rank_ids as $key0 => $value0) {
                                    if (!$new_sku_agent_rank_price[$key0] || $new_sku_agent_rank_price[$key0] <= 0) //该sku上没有设置本等级的价格
                                    {
                                        continue;
                                    }
                                    $sku_rank_price = array(
                                        'item_sku_price_id' => $new_sku_id,
                                        'agent_rank_id'     => $value0,
                                        'price'             => sprintf('%0.2f', $new_sku_agent_rank_price[$key0]),
                                    );
                                    $ItemPriceRank->addSkuRankPrice($item_id, $sku_rank_price); //一一添加
                                }
                            }
                        }
                        // 修改库存数量
                        $Item->where('item_id = ' . $item_id)->save(array('stock' => $total_stock));
                    }
                    /**** 添加商品属性 结束 *****/

                    /**** 添加商品详情 开始 *****/
                    $ItemTxt      = D('ItemTxt');
                    $ItemTxtPhoto = D('ItemTxtPhoto');

                    $ItemTxt->add(array('item_id' => $item_id, 'contents' => I('contents')));

                    $arr_txt_photo = I('item_txt_photo', array());
                    $data          = array();
                    foreach ($arr_txt_photo as $photo) {
                        $data[] = array(
                            'item_id'  => $item_id,
                            'path_img' => $photo,
                        );
                    }
                    $ItemTxtPhoto->addAll($data);
                    /**** 添加商品详情 结束 *****/
                    //所属分类下是否有积分商品
                    $class_obj = new ClassModel();
                    $class_obj->updateClassIntegral($arr_category[0], $is_integral);

                    if (I('isuse', 0)) {
                        $link = U('/AcpItem/get_onsale_item_list');
                    } else {
                        $link = U('/AcpItem/get_store_item_list');
                    }
                    $this->success('恭喜您，' . C('ITEM_NAME') . '添加成功！', $link);
                } else {
                    $this->error('对不起，' . C('ITEM_NAME') . '基本信息添加失败！');
                }
            } else {
                $this->error($Item->getError());
            }
        }

        $this->assign('pic_data', array(
            'batch' => true,
            'name'  => 'pic',
            'help'  => '图片800x800像素的效果最佳，建议使用4张以内图片',
        ));

        // 自动生成的商品货号
        $last_id = intval($Item->order('item_id DESC')->getField('item_id'));
        $item_sn = $this->system_config['SN_PREFIX'] . sprintf('%0' . $this->system_config['SN_LENGTH'] . 's', $last_id + 1);
        $this->assign('item_sn', $item_sn);

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

        // 获取所有启用的商品类型
        $this->arr_type = get_item_type();
        $this->assign('arr_type', $this->arr_type);

        // 获取所有启用的品牌
        $brand_list = get_brand();
        $this->assign('brand_list', $brand_list);

        $this->display();
    }

    /**
     * 修改商品
     * @author 姜伟
     * @return void
     * @todo 修改商品
     */
    public function edit_item()
    {
        $redirect = U('/AcpItem/get_onsale_item_list');
        $item_id  = intval($this->_get('item_id'));
        if (!$item_id) {
            $this->error('对不起，非法访问！', $redirect);
        }
        $item_obj  = new ItemModel($item_id);
        $item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
        if (!$item_info) {
            $this->error('对不起，不存在相关奖品！', $redirect);
        }

        $act = $this->_post('act');
        // 修改商品
        if ($act == 'edit') {
            //dump($_POST);die;
            // 商品分类
            $arr_category = explode('.', I('category_id'));
            $arr          = array(
                'class_id'               => $arr_category[0],
                'sort_id'                => $arr_category[1],
                'is_gift'                => I('is_gift'),
                'brand_id'               => I('brand_id'),
                'item_name'              => I('item_name'),
                'item_sn'                => I('item_sn'),
                'serial'                 => I('serial'),
                'mall_price'             => I('mall_price'),
                'market_price'           => I('market_price'),
                'integral_exchange_rate' => I('integral_exchange_rate'),
                'stock'                  => I('stock'),
                'stock_alarm'            => I('stock_alarm'),
                'weight'                 => I('weight'),
                'isuse'                  => I('isuse'),
                'is_recommend'           => I('is_recommend'),
                'item_type_id'           => I('item_type_id'),
                'item_desc'              => I('item_desc'),
                'unit'                   => I('unit'),
                'integral_exchange'      => I('integral_exchange'),
                'is_integral'            => I('is_integral'),
            );
            #echo "<pre>";
            #print_r($_POST);
            #echo "</pre>";
            #die;

            // 商品图片
            $Photo   = D('ItemPhoto');
            $arr_pic = I('pic', array());
            $old_pic = explode('|', I('old_photo'));

            // 需要删除的图片
            // $arr_diff = array_diff($old_pic, $arr_pic);
            $arr_diff = $old_pic;
            #echo "<pre>";
            #print_r($arr_pic);
            #print_r($old_pic);
            #echo "</pre>";
            #die;
            foreach ($arr_diff as $diff) {
                if ($Photo->where('item_id = ' . $item_id . " AND base_pic = '" . $diff . "'")->delete() !== false) {
                    // 删除原图及其大、中、小图
                    // @unlink($diff);
                    // @unlink(big_img($diff));
                    // @unlink(middle_img($diff));
                    // @unlink(small_img($diff));
                }
            }
            // exit;

            $data = array();
            foreach ($arr_pic as $k => $pic) {
                if ($k == 0) {
                    $arr['base_pic'] = $pic;
                }
                $is_default = $k == 0 ? 1 : 0;

                // if (in_array($pic, $old_pic)) {
                //     $Photo->where('item_id = ' . $item_id . " AND base_pic = '" . $pic . "'")->save(array('is_default' => $is_default, 'serial' => $k));
                // } else {
                    $data[] = array(
                        'item_id'    => $item_id,
                        'is_default' => $is_default,
                        'base_pic'   => $pic,
                        'serial'     => $k,
                    );

                    // 图片压缩加水印
                     $this->_resizeImg(ltrim($pic, '/'));
                // }
            }
            if ($data) {
                $Photo->addAll($data);
            }

            /***** 商品属性 开始 *****/
            // 编辑商品扩展属性
            $ItemExtendProperty = D('ItemExtendProperty');
            $ItemExtendProperty->delItemProperty($item_id);
            $data             = array();
            $arr_extend_value = I('extend_prop_value', array());
            foreach ($arr_extend_value as $value) {
                $data[] = array(
                    'item_id'           => $item_id,
                    'property_value_id' => $value,
                );
            }
            $ItemExtendProperty->addAll($data);

            // 编辑商品规格属性
            $has_sku        = I('has_sku', 0);
            $arr['has_sku'] = $has_sku; //是否开启sku
            if ($has_sku) {
                //如果开启sku
                $sku_ids    = $this->_post('sku_ids'); //要编辑的sku信息，这是一个数组，由商品sku的ID组成，这个数组的长度与其它提交的表单的长度始终是一样长的，且一一对应
                $data_value = array(); //用来过滤重复的sku数据

                $sku0_arr      = $this->_post('J_sku0', array()); //系统规定了每一件商品最多只有2个规格属性。数组长度与$sku_ids等同
                $sku1_arr      = $this->_post('J_sku1', array()); //同上，这是第二个规格属性（如果存在的话）
                $sku_sn_arr    = $this->_post('sku_sn', array()); //该sku的商品编号
                $sku_stock_arr = $this->_post('sku_stock', array()); //该sku的商品库存
                $sku_price_arr = $this->_post('sku_price', array()); //该sku的商品价格

                $new_skus    = $this->_post('new_sku', array()); //    该商品新添加的sku信息
                $total_stock = 0; // 商品库存

                $ItemSku      = D('ItemSku');
                $arr_item_sku = $ItemSku->itemSkuInfo($item_id); //获取当前的商品sku信息
                $old_sku_id   = array();
                foreach ($arr_item_sku as $k => $v) {
                    if (!in_array($v['item_sku_price_id'], $sku_ids)) //此条件成立说明某一条原先的sku被删除了（删除操作很危险，因为涉及到购物车还有订单）
                    {
                        $ItemSku->delSku($v['item_sku_price_id']); //这里执行删除sku原始信息
                    } else {
                        $old_sku_id[] = $v['item_sku_price_id']; //这里存的是剩下的、以前设置过的sku的ID，即这些sku本次要执行编辑
                    }
                }
                #myprint($old_sku_id);
                foreach ($sku_ids as $k0 => $v0) //循环取数据。(这里的$k0 很重要，用来标示每一条sku对应的一组数据)
                {
                    $data      = array();
                    $sku_id    = $v0; //    编辑的sku  ID
                    $sku0_val  = $sku0_arr[$k0]; //  商品该sku的第一个规格属性值ID。(这里就体现出了$k0这个参数的重要性)
                    $sku1_val  = $sku1_arr[$k0]; //  商品该sku的第二个规格属性值ID
                    $sku1_val = intval($sku1_val);
                    $sku_sn    = $sku_sn_arr[$k0]; //  商品该sku的货号
                    $sku_stock = $sku_stock_arr[$k0]; //  商品该sku的库存
                    $sku_price = $sku_price_arr[$k0]; //    商品该sku的价格
                    // 过滤重复数据
                    if (in_array($sku0_val . ',' . $sku1_val, $data_value)) {
                        continue;
                    } else {
                        $data_value[] = $sku0_val . ',' . $sku1_val;
                    }

                    $data = array(
                        'item_sn'         => $sku_sn,
                        'sku_price'       => $sku_price,
                        'sku_stock'       => $sku_stock,
                        'property_value1' => $sku0_val,
                        'property_value2' => $sku1_val,
                        'isuse'           => 1,
                    );
                    if (in_array($sku_id, $old_sku_id)) //如果本条sku已经存在于旧的sku信息中，那么本次执行更新
                    {
                        $ItemSku->setSku($sku_id, $data); //执行更新
                    } else //否则新增一条sku记录
                    {
                        $ItemSku->addSku($item_id, $data); //执行添加
                    }

                    $sku_rank_price     = array(); // 该商品的本条sku的级别价格的设置
                    $sku_rank_id_arr    = $this->_post('sku_rank_id_' . $sku_id); //级别ID
                    $sku_rank_price_arr = $this->_post('sku_rank_price_' . $sku_id); //级别价格，与级别ID一一对应

                    //删除该商品当前sku条件下所有设置的等级价格(如此操作，逻辑简单)
                    //$ItemSku->delItemSkuRankPrice($item_id,$sku_id);     //由于前面执行了$ItemPriceRank->delItemAgentRankPrice($item_id) 所以每一次执行商品编辑操作，所有的等级价格都清空了，所以这里不再执行
                    foreach ($sku_rank_id_arr as $k1 => $v1) {
                        if (!$sku_rank_price_arr[$k1] || $sku_rank_price_arr[$k1] <= 0) //该sku上没有设置本等级的价格
                        {
                            continue;
                        }
                        $sku_rank_price = array(
                            'item_sku_price_id' => $sku_id,
                            'agent_rank_id'     => $v1,
                            'price'             => sprintf('%0.2f', $sku_rank_price_arr[$k1]),
                        );
                        $ItemPriceRank->addSkuRankPrice($item_id, $sku_rank_price); //重新一一添加
                    }

                    $total_stock += $sku_stock; //库存加上
                }

                //新添加的sku信息
                if (!empty($new_skus)) {
                    foreach ($new_skus as $key => $value) //按照前台页面的规定，此处的$value代表一条新的sku临时标记（整数且大于等于1，开始,唯一的）
                    {
                        $data          = array(); //要添加的数据
                        $new_sku0_val  = $this->_post('new_J_sku0_' . $value);
                        $new_sku1_val  = $this->_post('new_J_sku1_' . $value);
                        $new_sku1_val  = intval($new_sku1_val);
                        $new_sku_sn    = $this->_post('new_sku_sn' . $value);
                        $new_sku_stock = $this->_post('new_sku_stock' . $value);
                        $new_sku_price = $this->_post('new_sku_price' . $value);
                        // 过滤重复数据
                        if (in_array($new_sku0_val . ',' . $new_sku1_val, $data_value)) {
                            continue;
                        } else {
                            $data_value[] = $new_sku0_val . ',' . $new_sku1_val;
                        }
                        $data = array(
                            'item_sn'         => $new_sku_sn,
                            'sku_price'       => $new_sku_price,
                            'sku_stock'       => $new_sku_stock,
                            'property_value1' => $new_sku0_val,
                            'property_value2' => $new_sku1_val ? $new_sku1_val : 0,
                            'isuse'           => 1,
                        );
                        $new_sku_id = $ItemSku->addSku($item_id, $data); //执行添加
                        if (!$new_sku_id) {
                            continue;
                        }
                        $total_stock += $new_sku_stock;
                        //新的sku为每一个会员级别所设置的价格
                        $new_sku_agent_rank_ids   = $this->_post('new_sku_rank_id' . $value); //这又是一个数组，代表着所有的用户的级别ID
                        $new_sku_agent_rank_price = $this->_post('new_sku_rank_price' . $value); //此也是一个数组，代表着该sku商品的每一个用户级别的价格。该数组的值是与上面的级别数组一一对应的
                        #myprint($new_sku_agent_rank_ids);
                        foreach ($new_sku_agent_rank_ids as $key0 => $value0) {
                            if (!$new_sku_agent_rank_price[$key0] || $new_sku_agent_rank_price[$key0] <= 0) //该sku上没有设置本等级的价格
                            {
                                continue;
                            }
                            $sku_rank_price = array(
                                'item_sku_price_id' => $new_sku_id,
                                'agent_rank_id'     => $value0,
                                'price'             => sprintf('%0.2f', $new_sku_agent_rank_price[$key0]),
                            );
                            $ItemPriceRank->addSkuRankPrice($item_id, $sku_rank_price); //重新一一添加
                        }
                    }
                }
                // 修改库存数量
                $arr['stock'] = $total_stock;
            }

            /***** 商品属性 结束 *****/

            /***** 详细描述 开始 *****/
            $ItemTxt      = D('ItemTxt');
            $ItemTxtPhoto = D('ItemTxtPhoto');
            $ItemTxt->delItemTxt($item_id);
            $ItemTxt->add(array('item_id' => $item_id, 'contents' => I('contents')));

            $ItemTxtPhoto->delItemTxtPhoto($item_id);
            $arr_txt_photo = I('item_txt_photo', array());
            $data          = array();
            foreach ($arr_txt_photo as $photo) {
                $data[] = array(
                    'item_id'  => $item_id,
                    'path_img' => $photo,
                );
            }
            $ItemTxtPhoto->addAll($data);
            /***** 详细描述 结束 *****/

            //将商品基本信息保存到数据库
            //所属分类下是否有积分商品

            $item_obj->editItem($arr);
            $class_obj = new ClassModel();
            $class_obj->updateClassIntegral($arr_category[0], I('is_integral'));
            $this->success('恭喜您，' . C('ITEM_NAME') . '编辑成功！');
        }

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

        // 商品图片
        $arr_photo    = array();
        $old_photo    = '';
        $ItemTxt      = D('ItemTxt');
        $ItemTxtPhoto = D('ItemTxtPhoto');
        $ItemPhoto    = D('ItemPhoto');
        $res          = $ItemPhoto->getPhotos($item_id);
        if ($res) {
            $arr_photo = $res;
            $old_photo = implode('|', $res);
        }
        $this->assign('arr_photo', $arr_photo);
        $this->assign('old_photo', $old_photo);

        // 商品图片
        $this->assign('pic_data', array(
            'batch'   => true,
            'name'    => 'pic',
            'pic_arr' => $arr_photo,
            'help'    => '图片800x800像素的效果最佳，建议使用4张以内图片',
        ));
        // dump($res);exit;

        // 商品描述
        $this->assign('contents', $ItemTxt->getItemTxt($item_id));

        // 商品描述图片
        $this->assign('arr_txt_photo', $ItemTxtPhoto->getItemTxtPhotoList($item_id));

        $this->assign('item', $item_info);

        // 获取所有启用的商品类型
        $this->arr_type = get_item_type();
        $this->assign('arr_type', $this->arr_type);

        // 获取所有启用的品牌
        $brand_list = get_brand();
        $this->assign('brand_list', $brand_list);

        // 获取商品类型
        $type_id = $item_info['item_type_id'];
        $this->assign('type_id', $type_id);

        /***** 获取商品扩展属性 开始 *****/
        $ItemExtendProperty = D('ItemExtendProperty');
        $arr_extend_prop    = get_type_extend_prop($type_id);
        $arr_prop_list      = $ItemExtendProperty->getPropertyListByItemId($item_id);
        foreach ($arr_extend_prop as $k1 => $prop) {
            foreach ($prop['prop_value'] as $k2 => $v) {
                if (in_array($v['property_value_id'], $arr_prop_list)) {
                    $arr_extend_prop[$k1]['checked']                     = 1;
                    $arr_extend_prop[$k1]['prop_value'][$k2]['selected'] = 1;
                    continue 2;
                }
            }
        }
        $this->assign('arr_extend_prop', $arr_extend_prop);
        /***** 获取商品扩展属性 结束 *****/

        /***** 获取商品规格属性 开始 *****/
        $arr_sku = get_type_sku($type_id);
        $this->assign('arr_sku', $arr_sku);
        $this->assign('sku_num', count($arr_sku));
        $ItemSku      = D('ItemSku');
        $arr_item_sku = $ItemSku->itemSkuInfo($item_id);
        $this->assign('arr_item_sku', $arr_item_sku);

        /***** 获取商品规格属性 结束 *****/

        $this->assign('head_title', '修改' . C('ITEM_NAME'));
        $this->display();
    }

    /**
     * 商品设置列表，公共方法
     * @author cc
     * @param string $where
     * @param string $title
     * @return void
     * @todo 商品设置列表，公共方法
     */
    public function item_set_list($where, $title, $action_name)
    {
        if ($where != 'no_item') {
            $where .= $this->get_search_condition();
            $item_obj = new ItemModel();

            //分页处理
            import('ORG.Util.Pagelist');
            $count = $item_obj->getItemNum($where);
            $Page  = new Pagelist($count, C('PER_PAGE_NUM'));
            $item_obj->setStart($Page->firstRow);
            $item_obj->setLimit($Page->listRows);
            $show = $Page->show();
            $this->assign('page', $Page);
            $this->assign('show', $show);

            $item_list = $item_obj->getItemList('', $where, ' addtime DESC');
            $item_list = $item_obj->getListData($item_list);
            if (ACTION_NAME == 'first_buy_item_list') {
                foreach ($item_list as $key => $value) {
                    $item_list[$key]['number'] = M('FirstBuyItem')->where('item_id = ' . $value['item_id'])->getField('number');
                }
            }

            $this->assign('item_list', $item_list);
            #echo "<pre>";
            #print_r($item_list);
            #echo "</pre>";
            #echo $item_obj->getLastSql();
        }

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

        // 商品状态
        $arr_item_status = array(
            'onsale'   => '出售中',
            'alarm'    => '库存报警',
            'outstock' => '缺货中',
        );
        $this->assign('arr_item_status', $arr_item_status);

        $this->assign('head_title', $title . '列表');
        $this->assign('button_name', $title);
        $this->assign('action_name', $action_name);
        $this->display(APP_PATH . 'Tpl/AcpItem/item_set_list.html');
    }

    //新品列表
    public function new_item_list()
    {
        $where         = '';
        $new_item_obj  = M('NewItem');
        $new_item_list = $new_item_obj->field('item_id')->select();
        // dump($new_item_list);die;
        if ($new_item_list) {
            $item_ids = '';
            foreach ($new_item_list as $key => $value) {
                $item_ids .= $value['item_id'] . ',';
            }
            $last_str = strlen(rtrim($item_ids)) - 1;
            if ($item_ids[$last_str] == ',') {
                $item_ids = substr($item_ids, 0, $last_str);
            }
            $where .= 'item_id IN (' . $item_ids . ')';
        } else {
            $where = 'no_item';
        }
        $this->item_set_list($where, '新品', 'new_item');
    }

    //快速订单商品列表
    public function quick_item_list()
    {
        $where           = '';
        $quick_item_obj  = M('QuickItem');
        $quick_item_list = $quick_item_obj->field('item_id')->select();
        // dump($quick_item_list);die;
        if ($quick_item_list) {
            $item_ids = '';
            foreach ($quick_item_list as $key => $value) {
                $item_ids .= $value['item_id'] . ',';
            }
            $last_str = strlen(rtrim($item_ids)) - 1;
            if ($item_ids[$last_str] == ',') {
                $item_ids = substr($item_ids, 0, $last_str);
            }
            $where .= 'item_id IN (' . $item_ids . ')';
        } else {
            $where = 'no_item';
        }
        $this->item_set_list($where, '快速订单商品', 'quick_item');
    }

    //首批订单商品列表
    public function first_buy_item_list()
    {
        $where               = '';
        $first_buy_item_obj  = M('FirstBuyItem');
        $first_buy_item_list = $first_buy_item_obj->field('item_id')->select();
        // dump($first_buy_item_list);die;
        if ($first_buy_item_list) {
            $item_ids = '';
            foreach ($first_buy_item_list as $key => $value) {
                $item_ids .= $value['item_id'] . ',';
            }
            $last_str = strlen(rtrim($item_ids)) - 1;
            if ($item_ids[$last_str] == ',') {
                $item_ids = substr($item_ids, 0, $last_str);
            }
            $where .= 'item_id IN (' . $item_ids . ')';
        } else {
            $where = 'no_item';
        }
        $this->item_set_list($where, '首批订单商品', 'first_buy_item');
    }

    //开业装修商品列表
    public function new_shop_item_list()
    {
        $where              = '';
        $new_shop_item_obj  = M('NewShopItem');
        $new_shop_item_list = $new_shop_item_obj->field('item_id')->select();
        // dump($new_shop_item_list);die;
        if ($new_shop_item_list) {
            $item_ids = '';
            foreach ($new_shop_item_list as $key => $value) {
                $item_ids .= $value['item_id'] . ',';
            }
            $last_str = strlen(rtrim($item_ids)) - 1;
            if ($item_ids[$last_str] == ',') {
                $item_ids = substr($item_ids, 0, $last_str);
            }
            $where .= 'item_id IN (' . $item_ids . ')';
        } else {
            $where = 'no_item';
        }
        $this->item_set_list($where, '开业装修商品', 'new_shop_item');
    }

    //今日推荐列表
    public function recommend_item_list()
    {
        $where               = '';
        $recommend_item_obj  = M('RecommendItem');
        $recommend_item_list = $recommend_item_obj->field('item_id')->select();
        // dump($recommend_item_list);die;
        if ($recommend_item_list) {
            $item_ids = '';
            foreach ($recommend_item_list as $key => $value) {
                $item_ids .= $value['item_id'] . ',';
            }
            $last_str = strlen(rtrim($item_ids)) - 1;
            if ($item_ids[$last_str] == ',') {
                $item_ids = substr($item_ids, 0, $last_str);
            }
            $where .= 'item_id IN (' . $item_ids . ')';
        } else {
            $where = 'no_item';
        }
        $this->item_set_list($where, '今日推荐', 'recommend_item');
    }

    /**
     * 通用商品设置
     * @author cc
     * @return void
     * @todo 通用商品设置
     */
    public function item_set($action_title = '', $action_src = '', $head_title = '')
    {
        // 获取所有启用的分类
        $arr_category = get_category_tree();
        // dump($arr_category);die;
        $this->assign('arr_category', $arr_category);

        // 获取所有启用的品牌
        $arr_brand = get_brand();
        $this->assign('arr_brand', $arr_brand);

        // 获取所有启用的商品类型
        $arr_type = get_item_type();
        $this->assign('arr_type', $arr_type);

        $this->assign('action_title', $action_title);
        $this->assign('action_src', $action_src);
        $this->assign('head_title', $head_title ? $head_title : $action_title);

        $this->display('item_set');
    }

    /**
     * 设置新品
     * @author cc
     * @return void
     * @todo 设置新品
     */
    public function new_item_set()
    {
        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $new_item_obj = M('NewItem');

            $item_list = $this->_post('choose'); //哪些商品参加活动
            $redirect  = $this->_get('redirect');
            $redirect  = $redirect ? url_jiemi($redirect) : '/AcpItem/new_item_list/mod_id/2';

            #   myprint($_POST);

            foreach ($item_list as $key => $value) {
                $new_item_info = $new_item_obj->field('new_item_id')->where('item_id = ' . $value)->find();
                if (!$new_item_info) {
                    $data[$key] = array(
                        'item_id' => $value,
                    );
                }
            }
            if (!$data) {
                $this->error('请勿重复设置!', get_url());
            }
            // dump($data);die;
            $success = $new_item_obj->addAll($data); //执行添加

            if ($success) {
                $this->success('恭喜你，设置成功', '/AcpItem/new_item_list/mod_id/2');
            } else {
                $this->error('对不起，设置失败!', get_url());
            }
            exit;
        }

        $this->item_set('设置新品');
    }

    /**
     * 设置快速订单商品
     * @author cc
     * @return void
     * @todo 设置快速订单商品
     */
    public function quick_item_set()
    {
        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $quick_item_obj = M('QuickItem');

            $item_list = $this->_post('choose'); //哪些商品参加活动
            $redirect  = $this->_get('redirect');
            $redirect  = $redirect ? url_jiemi($redirect) : '/AcpItem/quick_item_list/mod_id/2';

            #   myprint($_POST);

            foreach ($item_list as $key => $value) {
                $quick_item_info = $quick_item_obj->field('quick_item_id')->where('item_id = ' . $value)->find();
                if (!$quick_item_info) {
                    $data[$key] = array(
                        'item_id' => $value,
                    );
                }
            }
            if (!$data) {
                $this->error('请勿重复设置!', get_url());
            }
            // dump($data);die;
            $success = $quick_item_obj->addAll($data); //执行添加

            if ($success) {
                $this->success('恭喜你，设置成功', '/AcpItem/quick_item_list/mod_id/2');
            } else {
                $this->error('对不起，设置失败!', get_url());
            }
            exit;
        }

        $this->item_set('设置快速订单商品');
    }

    /**
     * 设置首批订单商品
     * @author cc
     * @return void
     * @todo 设置首批订单商品
     */
    public function first_buy_item_set()
    {
        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $first_buy_item_obj = M('FirstBuyItem');

            $item_list        = $this->_post('choose'); //哪些商品参加活动
            $item_number_list = $this->_post('choose_number'); //商品数量列表
            $redirect         = $this->_get('redirect');
            $redirect         = $redirect ? url_jiemi($redirect) : '/AcpItem/first_buy_item_list/mod_id/2';

            #   myprint($_POST);

            foreach ($item_list as $key => $value) {
                $first_buy_item_info = $first_buy_item_obj->field('first_buy_item_id')->where('item_id = ' . $value)->find();
                if (!$first_buy_item_info) {
                    $data[$key] = array(
                        'item_id' => $value,
                        'number'  => $item_number_list[$key],
                    );
                }
            }
            if (!$data) {
                $this->error('请勿重复设置!', get_url());
            }
            // dump($data);die;
            $success = $first_buy_item_obj->addAll($data); //执行添加

            if ($success) {
                $this->success('恭喜你，设置成功', '/AcpItem/first_buy_item_list/mod_id/2');
            } else {
                $this->error('对不起，设置失败!', get_url());
            }
            exit;
        }

        $this->item_set('设置快速订单商品');
    }

    /**
     * 设置开业装修商品
     * @author cc
     * @return void
     * @todo 设置开业装修商品
     */
    public function new_shop_item_set()
    {
        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $new_shop_item_obj = M('NewShopItem');

            $item_list = $this->_post('choose'); //哪些商品参加活动
            $redirect  = $this->_get('redirect');
            $redirect  = $redirect ? url_jiemi($redirect) : '/AcpItem/new_shop_item_list/mod_id/2';

            #   myprint($_POST);

            foreach ($item_list as $key => $value) {
                $new_shop_item_info = $new_shop_item_obj->field('new_shop_item_id')->where('item_id = ' . $value)->find();
                if (!$new_shop_item_info) {
                    $data[$key] = array(
                        'item_id' => $value,
                    );
                }
            }
            if (!$data) {
                $this->error('请勿重复设置!', get_url());
            }
            // dump($data);die;
            $success = $new_shop_item_obj->addAll($data); //执行添加

            if ($success) {
                $this->success('恭喜你，设置成功', '/AcpItem/new_shop_item_list/mod_id/2');
            } else {
                $this->error('对不起，设置失败!', get_url());
            }
            exit;
        }

        $this->item_set('设置快速订单商品');
    }

    /**
     * 修改首批订单商品数量
     * @author cc
     * @return void
     * @todo 修改首批订单商品数量
     */
    public function first_buy_item_number_set()
    {
        $item_id = intval(I('item_id'));
        $number  = intval(I('number'));
        // dump($_POST);die;

        M('FirstBuyItem')->where('item_id = ' . $item_id)->save(array('number' => $number));
        // echo M('FirstBuyItem')->getLastSql();
    }

    /**
     * 设置今日推荐
     * @author zlf
     * @return void
     * @todo 设置今日推荐
     */
    public function recommend_item_set()
    {
        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $recommend_item_obj = M('RecommendItem');

            $item_list = $this->_post('choose'); //哪些商品参加活动
            $redirect  = $this->_get('redirect');
            $redirect  = $redirect ? url_jiemi($redirect) : '/AcpItem/recommend_item_list/mod_id/2';

            #   myprint($_POST);

            foreach ($item_list as $key => $value) {
                $recommend_item_info = $recommend_item_obj->field('recommend_item_id')->where('item_id = ' . $value)->find();
                if (!$recommend_item_info) {
                    $data[$key] = array(
                        'item_id' => $value,
                    );
                }
            }
            if (!$data) {
                $this->error('请勿重复设置!', get_url());
            }
            // dump($data);die;
            $success = $recommend_item_obj->addAll($data); //执行添加

            if ($success) {
                $this->success('恭喜你，设置成功', '/AcpItem/recommend_item_list/mod_id/2');
            } else {
                $this->error('对不起，设置失败!', get_url());
            }
            exit;
        }

        $this->item_set('设置今日推荐');
    }

    /**
     * 删除商品设置关联
     * @author cc
     * @return void
     * @todo 删除商品设置关联
     */
    public function del_item_set()
    {
        $item_id     = intval(I('id'));
        $action_name = I('action');
        // echo $action_name;die;
        // echo $item_id;die;

        switch ($action_name) {
            case 'new_item':
                $new_item_obj = M('NewItem');
                $res          = $new_item_obj->where('item_id = ' . $item_id)->delete();
                break;
            case 'quick_item':
                $quick_item_obj = M('QuickItem');
                $res            = $quick_item_obj->where('item_id = ' . $item_id)->delete();
                break;
            case 'first_buy_item':
                $first_buy_item_obj = M('FirstBuyItem');
                $res                = $first_buy_item_obj->where('item_id = ' . $item_id)->delete();
                break;
            case 'new_shop_item':
                $new_shop_item_obj = M('NewShopItem');
                $res               = $new_shop_item_obj->where('item_id = ' . $item_id)->delete();
                break;
            case 'recommend_item':
                $recommend_item_obj = M('RecommendItem');
                $res                = $recommend_item_obj->where('item_id = ' . $item_id)->delete();
                break;

            default:
                # code...
                break;
        }

        if ($res === false) {
            $this->ajaxReturn('对不起，操作失败！');
        } else {
            $this->ajaxReturn('恭喜您，操作成功！');
        }
    }

    /**
     * 商品详情图片上传
     * @author 姜伟
     */
    public function upload_desc_pic()
    {
        import("@.Common.EditorUpload");
        $conf = array(
            'imageDomain' => 'http://' . $_SERVER['HTTP_HOST'],
            'rootPath'    => APP_PATH . 'Uploads',
            'rootDir'     => $GLOBALS['install_info']['dir_name'] . '/item_txt_photo',
        );
        $eUpload = new EditorUpload($conf);
        $eUpload->save($_FILES['imgFile']);
    }

    /**
     * 图片压缩加水印
     * @param string $base_img 原图地址(绝对路径)
     * @return array 生成的图片信息
     */
    protected function _resizeImg($base_img)
    {   
        $base_img = strstr($base_img, '/Uploads/');
        $base_img = APP_PATH.ltrim($base_img, '/');
        //dump($base_img);die;
        import('ORG.Util.Image');
        $Image = new Image();

        $arr_img = array();

        if (!is_file($base_img)) {
            return $arr_img;
        }

        $base_img_info = pathinfo($base_img);
        $img_path      = $base_img_info['dirname'] . '/';
        $img_extension = $base_img_info['extension'];
        $img_name      = str_replace('.' . $img_extension, '', $base_img_info['basename']);

        /***** 等比缩放 开始 *****/

        // 生成大图
        $big_img_path = $img_path . $img_name . C('BIG_IMG_SUFFIX') . '.' . $img_extension;
        $big_img      = $Image->thumb($base_img, $big_img_path, $img_extension, C('BIG_IMG_SIZE'), C('BIG_IMG_SIZE'));

        // 生成中图
        $middle_img_path = $img_path . $img_name . C('MIDDLE_IMG_SUFFIX') . '.' . $img_extension;
        $middle_img      = $Image->thumb($base_img, $middle_img_path, $img_extension, C('MIDDLE_IMG_SIZE'), C('MIDDLE_IMG_SIZE'));

        // 生成小图
        $small_img_path = $img_path . $img_name . C('SMALL_IMG_SUFFIX') . '.' . $img_extension;
        $small_img      = $Image->thumb($base_img, $small_img_path, $img_extension, C('SMALL_IMG_WIDTH'), C('SMALL_IMG_HEIGHT'));
        /***** 等比缩放 结束 *****/

        $arr_img['big_img']    = $big_img;
        $arr_img['middle_img'] = $middle_img;
        $arr_img['small_img']  = $small_img;

        /***** 图片加水印 开始 *****/
        // 判断水印功能是否开启
        /*if ($this->system_config['WATER_PRINT_OPEN'] && file_exists(APP_PATH . $this->system_config['WATER_PRINT_IMG'])) {
        // 水印图片
        $water_img = APP_PATH . $this->system_config['WATER_PRINT_IMG'];

        // 水印透明度
        $alpha = intval($this->system_config['WATER_PRINT_TRANSPARENCY']);

        //水印位置
        $position = intval($this->system_config['WATER_PRINT_IMG_POSITION']);

        // 大图加水印
        if ($big_img) {
        $Image->water($big_img, $water_img, '', $alpha, $position);
        }

        // 中图加水印
        if ($middle_img) {
        $Image->water($middle_img, $water_img, '', $alpha, $position);
        }

        // 小图尺寸太小，不建议添加水印
        }*/
        /***** 图片加水印 结束 *****/

        return $arr_img;
    }

    /**
     * 获取要被统计的单品；
     * @author wsq
     * @param void
     * @return void
     * @todo 接收表单提交的参数，过滤合法性
     */
    public function item_sales_num_stat()
    {

        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $items = $this->_post('choose');
            if (count($items) > 1) {
                $this->error('对不起,最多选择一项商品', U('/AcpItem/item_sales_num_stat'));
            }
            if (count($items) == 0) {
                $this->error('对不起,最选择需要统计的商品', U('/AcpItem/item_sales_num_stat'));
            }

            $year       = $year ? $year : date('Y');
            $month      = $month ? $month : date('m');
            $start_time = 0;
            $end_time   = 0;
            $date       = '';

            if ($year && $month) {
                $this->assign('year', $year);
                $this->assign('month', $month);
                $start_time = mktime(0, 0, 0, $month, 1, $year);
                if ($month == 12) {
                    $year++;
                    $month = 1;
                } else {
                    $month++;
                }

                $end_time = mktime(0, 0, 0, $month, 1, $year) - 1;
                $date     = $year . '-' . date('m');
            }

            $start_time_last_month = strtotime("-1 month", $start_time);
            $end_time_last_month   = strtotime("-1 month", $end_time);

            $where            = 'addtime >= ' . $start_time . ' AND addtime <= ' . $end_time . ' AND items.item_id  = ' . $items[0];
            $where_last_month = 'addtime >= ' . $start_time_last_month . ' AND addtime <= ' . $end_time_last_month . ' AND items.item_id  = ' . $items[0];

            //获取订单统计数据
            $order_obj = new OrderModel();

            $order_stat_list            = $order_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%d") AS day, number, real_price')->where($where)->join('tp_order_item AS items ON items.order_id = tp_order.order_id')->group('day')->order('addtime DESC')->select();
            $order_stat_list_last_month = $order_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%d") AS day, number, real_price')->where($where_last_month)->join('tp_order_item AS items ON items.order_id = tp_order.order_id')->group('day')->order('addtime DESC')->select();

            $new_order_sum_list   = array();
            $new_order_money_list = array();

            $new_order_sum_list_last_month   = array();
            $new_order_money_list_last_month = array();

            for ($i = 0; $i <= 30; $i++) {
                $new_order_sum_list[$i]   = 0;
                $new_order_money_list[$i] = 0;

                $new_order_sum_list_last_month[$i]   = 0;
                $new_order_money_list_last_month[$i] = 0;
            }

            //组成数组
            foreach ($order_stat_list as $key => $val) {
                $new_order_sum_list[intval($val['day'])]   = $val['number'];
                $new_order_money_list[intval($val['day'])] = $val['number'] * $val['real_price'];
            }

            foreach ($order_stat_list_last_month as $key => $val) {
                $new_order_sum_list_last_month[intval($val['day'])]   = $val['number'];
                $new_order_money_list_last_month[intval($val['day'])] = $val['number'] * $val['real_price'];
            }

            $this->assign('order_amount_stat_list', $new_order_sum_list);
            $this->assign('cost_amount_stat_list', $new_order_money_list);

            $this->assign('order_amount_stat_list_last', $new_order_sum_list_last_month);
            $this->assign('cost_amount_stat_list_last', $new_order_money_list_last_month);

            $this->assign('date', $date);
            $this->assign('day_num', date('d', mktime(0, 0, 0, $month + 1, 0, $year)));

            //TITLE中的页面标题
            $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
            $this->assign('head_title', '单品销量统计');

            $this->display();
            exit;
        }
        $this->item_set('统计单品销量');
    }

    /**
     * 获取要被统计的单品；
     * @author wsq
     * @param void
     * @return void
     * @todo 接收表单提交的参数，过滤合法性
     */
    public function item_export()
    {

        $act = $this->_post('act');
        if ($act == 'add') //执行添加操作时
        {
            $items = $this->_post('choose');

            if (count($items) == 0) {
                $this->error('对不起,最少选择一项商品', U('/AcpItem/item_sales_num_stat'));
            }

            $date_last_moth = date("Y-m", strtotime("-1 month")) . "-01 00:00:00 ";
            $date_this_moth = date("Y-m") . "-01 00:00:00 ";

            $order_obj      = new OrderModel();
            $order_item_obj = new OrderItemModel();
            $item_obj       = new ItemModel();

            $date_where = ' AND addtime >= ' . strtotime($date_last_moth);
            //$date_where     = '';

            # 统计总销量
            $summary_array = array();

            foreach ($items as $item) {
                $where     = 'items.item_id = ' . $item . $date_where;
                $item_info = $item_obj->getItemInfo('item_id = ' . $item);

                if (!$item_info) {
                    continue;
                }

                $info = $order_obj->join('tp_order_item AS items ON items.order_id = tp_order.order_id')->where($where)->select();

                $sum_total  = 0;
                $sale_total = 0;

                foreach ($info as $key => $value) {
                    $sum_total += $value['number'];
                    $sale_total += $value['number'] * $value['real_price'];
                }

                $detail               = array();
                $detail['item_name']  = $item_info['item_name'];
                $detail['item_sn']    = $item_info['item_sn'];
                $detail['sum_total']  = $sum_total;
                $detail['sale_total'] = $sale_total;

                $summary_array[$item] = $detail;
            }

            if (count($summary_array) > 0) {
                //引入PHPExcel类库
                vendor('Excel.PHPExcel');
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("Da")
                    ->setLastModifiedBy("Da")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX,generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");

                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->getActiveSheet(0)->setTitle('商品单品销量'); //标题
                $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15); //单元格宽度
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
                $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); //设置字体大小

                $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '商品名称');
                $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '货号');
                $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '月份');
                $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '销量');
                $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '销售额');

                //每行数据的内容
                foreach ($summary_array as $k => $value) {
                    static $i = 2;
                    //商品
                    $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $value['item_name']);
                    //货号
                    $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['item_sn']);
                    //月份
                    $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, date('Ym', strtotime('-1 month')) . '~' . date('m'));
                    //销量
                    $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['sum_total']);
                    //销售额
                    $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $value['sale_total']);
                    $i++;
                }

                //直接输出文件到浏览器下载
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . '单品销量导出_' . date("YmdHis") . '.xls"');
                header('Cache-Control: max-age=0');
                ob_clean(); //关键
                flush(); //关键
                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
            }
        }

        $this->item_set('导出单品销量');
    }

    //积分商品列表
    public function get_integral_item_list()
    {
        $this->item_list('isuse = 1 AND stock > 0 AND is_integral = 1', '积分' . C('ITEM_NAME') . '列表', 'onsale');
    }

    //设置批发价
    public function set_wholesale_price()
    {
        $item_id   = I('item_id');
        $item_obj  = new ItemModel();
        $item_info = $item_obj->getItemInfo('item_id =' . $item_id . ' and is_integral != 1');
        if (!$item_info) {
            $this->error('商品不存在');
        }
        //设置批发价
        $act = I('act');
        if ($act == 'set') {
            $min_arr   = $_POST['min_num'];
            $price_arr = $_POST['price'];
            #dump($_POST);
            foreach ($min_arr as $v) {
                if (!ctype_digit($v)) {
                    $this->error('数量必须为大于0的整数');
                }
            }
            foreach ($price_arr as $v) {
                if (!is_numeric($v)) {
                    $this->error('批发优惠必须为大于0的数字');
                }
            }
            $min_len   = count($min_arr);
            $price_len = count($price_arr);
            if ($min_len != $price_len) {
                $this->error('修改失败');
            }
            $data = array();
            for ($i = 0; $i < $min_len; $i++) {
                $data[] = array(
                    'item_id' => $item_id,
                    'min_num' => $min_arr[$i],
                    'price'   => $price_arr[$i],
                );
            }
            $price_obj = new ItemWholesalePriceModel();
            if ($price_obj->addAllPrice($item_id, $data)) {
                $this->success('商品批发价设置成功');
            }
            $this->error('商品批发价设置失败');
        }

        //获取商品批发价
        $price_obj  = new ItemWholesalePriceModel();
        $price_list = $price_obj->getItemPrice($item_id);
        $this->assign('price_list', $price_list);

        $this->assign('item_id', $item_id);
        //$this->assign('max_wholesale',$item_info['max_wholesale']);
        $this->assign('num', count($price_list));
        $this->assign('head_title', '设置批发价');
        $this->display();
    }
}
