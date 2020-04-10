<?php
/**
 * acp后台商品套餐类
 */
class AcpItemPackageAction extends AcpAction {

    /**
     * 初始化
     * @author cc
     * @return void
     * @todo 初始化方法
     */
    function _initialize()
    {
        parent::_initialize();

        // 引入商品公共函数库
        require_cache('Common/func_item.php');

        $this->assign('ITEM_NAME', '套餐');
    }

    /**
     * 接收搜索表单数据，组织返回where子句
     * @author cc
     * @param void
     * @return void
     * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
     */
    function get_search_condition()
    {
        //初始化查询条件
        $where = '';

        //商品名称
        $item_name = $this->_request('item_name');
        if ($item_name)
        {
            $where .= ' AND item_name LIKE "%' . $item_name . '%"';
        }

        //商品货号
        $item_sn = $this->_request('item_sn');
        if ($item_sn)
        {
            $where .= ' AND item_sn = "' . $item_sn . '"';
        }

        //分类
        $category_id = $this->_request('category_id');
        if ($category_id)
        {
            $arr_category = explode('.', $category_id);
            if ($arr_category[0] == 1)
            {
                $where .= ' AND class_id = ' . $arr_category[1];
            }
            elseif ($arr_category[0] == 2)
            {
                $where .= ' AND sort_id = ' . $arr_category[1];
            }
        }

        // 商品状态
        $item_status = $this->_request('item_status');
        if ($item_status) {
            if ($item_status == 'onsale') {
                // $condition['_string'] = 'stock > 0 AND stock > stock_alarm';
                $where .= ' AND isuse = 1';
            }
            elseif ($item_status == 'outofstock')
            {
                $where .= ' AND isuse = 0';
            }
        }

        //添加时间范围起始时间
        $start_date = $this->_request('start_date');
        $start_date = str_replace('+', ' ', $start_date);
        $start_date = strtotime($start_date);
        if ($start_date)
        {
            $where .= ' AND addtime >= ' . $start_date;
        }

        //添加时间范围结束时间
        $end_date = $this->_request('end_date');
        $end_date = str_replace('+', ' ', $end_date);
        $end_date = strtotime($end_date);
        if ($end_date)
        {
            $where .= ' AND addtime <= ' . $end_date;
        }

        //销售量范围起点
        $start_sales_num = $this->_request('start_sales_num');
        if ($start_sales_num != -1 && $start_sales_num != '')
        {
            $where .= ' AND sales_num >= ' . intval($start_sales_num);
        }

        //销售量范围结束点
        $end_sales_num = $this->_request('end_sales_num');
        if ($end_sales_num != -1 && $end_sales_num != '')
        {
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

    public function get_item_package_list()
    {
        $where = 'isuse < 2';
        $where .= $this->get_search_condition();

        $item_obj = new ItemPackageModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $item_obj->getItemPackageNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $item_obj->setStart($Page->firstRow);
        $item_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
        $this->assign('show', $show);

        $item_list = $item_obj->getItemPackageList('', $where, ' addtime DESC');
        $item_list = $item_obj->getListData($item_list);

        $this->assign('item_list', $item_list);
        #echo "<pre>";
        #print_r($item_list);
        #echo "</pre>";
        #echo $item_obj->getLastSql();
        
        // // 获取所有启用的分类
        // $this->arr_category = get_category_tree();
        // $this->assign('arr_category', $this->arr_category);

        // 商品状态
        $arr_item_status =  array(
            'onsale'   => '出售中',
            'outofstock'   => '仓库中',
            // 'alarm'    => '库存报警',
            // 'outstock' => '缺货中'
        );
        $this->assign('arr_item_status', $arr_item_status);

        $this->assign('head_title', $title . '列表');
        $this->display();
    }

    public function add_item_package()
    {
        $act = $this->_post('act');
        if($act == 'add')           //执行添加操作时
        {
            $package_obj = new ItemPackageModel();
            
            $item_id_list       = $this->_post('choose');           //商品id列表
            $item_number_list   = $this->_post('choose_number');    //商品数量列表
            $item_name          = $this->_post('item_name');        //名称：
            $item_sn            = $this->_post('item_sn');          //货号：
            $mall_price         = $this->_post('mall_price');       //本店价：
            $market_price         = $this->_post('market_price');       //本店价：
            $integral_exchange_rate = $this->_post('integral_exchange_rate'); //积分抵扣百分比：
            $isuse              = $this->_post('isuse');            //是否启用
            $item_desc          = $this->_post('item_desc');        //是否启用
            $redirect           = $this->_get('redirect');
            $redirect           = $redirect?url_jiemi($redirect):'/AcpItemPackage/get_item_package_list/mod_id/2';
            
            // dump($_POST);die;
            if(!$item_name || !$mall_price || !$market_price || !$item_sn)
            {
                $this->error('请完善页面必填信息',get_url());
            }

            if (!$item_id_list || !$item_number_list) {
                $this->error('请向套餐添加商品',get_url());
            }
            
            $package_data = array(
                    'item_name'                 => $item_name,
                    'item_sn'                   => $item_sn,
                    'mall_price'                => $mall_price,
                    'market_price'                => $market_price,
                    'integral_exchange_rate'    => $integral_exchange_rate,
                    'isuse'                     => ($isuse==1)?1:0,
                    'item_desc'                 => $item_desc,
                    'addtime'                   => time(),
                    'serial'        => I('serial'),
            );
            // dump($package_data);die;

            // 商品图片
            $data_photo = array();
            $arr_pic = I('post.pic', array());
            foreach ($arr_pic as $k => $pic) {
                if ($k == 0) {
                    $package_data['base_pic'] = $pic;
                    $data_photo[$k]['is_default'] = 1;
                } else {
                    $data_photo[$k]['is_default'] = 0;
                }
                $data_photo[$k]['base_pic'] = $pic;
            }

            // 添加商品
            $item_package_id = $package_obj->addItemPackage($package_data);     //执行添加

            if($item_package_id)
            {
                // 添加图片
                foreach ($data_photo as $k => $photo)
                {
                    $data_photo[$k]['item_id'] = $item_package_id;

                    // 图片压缩加水印
                    $this->_resizeImg(APP_PATH . ltrim($photo['base_pic'], '/'));
                }
                D('ItemPackagePhoto')->addAll($data_photo);

                // 添加商品
                $package_detail_obj = D('ItemPackageDetail');
                
                $item_list = array();
                foreach ($item_id_list as $key => $value) {
                    if ($item_number_list[$key] != 0) {
                        $item_list[$key]['item_id'] = $value;
                        $item_list[$key]['number'] = $item_number_list[$key];
                    }
                }
                // dump($item_list);die;
                if($item_list && !empty($item_list))
                {
                    if(!$package_detail_obj->addItemListToPackage($item_package_id, $item_list)) //设置参加套餐的商品
                    {
                        $this->error('对不起1,套餐未能成功添加,您可以再尝试一次!',get_url());
                    }
                }

                $this->success('恭喜你，套餐添加成功', '/AcpItemPackage/get_item_package_list/mod_id/2');
            }
            else
            {
                $this->error('对不起3,套餐未能成功添加,您可以在尝试一次!',get_url());
            }
            exit;
        }

        // 获取所有启用的分类
        $arr_category = get_category_tree();
        $this->assign('arr_category', $arr_category);
        
        // 获取所有启用的品牌
        $arr_brand = get_brand();
        $this->assign('arr_brand', $arr_brand);

        // 自动生成的套餐货号
        $last_id = intval(D('ItemPackage')->order('item_package_id DESC')->getField('item_package_id'));
        // $item_sn = $this->system_config['SN_PREFIX'] . sprintf('%0' . $this->system_config['SN_LENGTH'] . 's', $last_id + 1);
        $item_sn = 't' . sprintf('%0' . $this->system_config['SN_LENGTH'] . 's', $last_id + 1);
        $this->assign('item_sn', $item_sn);

        $this->assign('action_title', $action_title);
        $this->assign('action_src', $action_src);
        $this->assign('head_title', '添加套餐商品');
        
        $this->display();
    }

    public function edit_item_package()
    {
        $redirect = U('/AcpItemPackage/get_item_package_list/mod_id/2');
        $item_package_id = intval($this->_get('item_package_id'));
        if (!$item_package_id)
        {
            $this->error('对不起，非法访问！', $redirect);
        }
        $item_package_obj = new ItemPackageModel($item_package_id);
        $item_package_info = $item_package_obj->getItemPackageInfo('item_package_id = ' . $item_package_id);
        if (!$item_package_info)
        {
            $this->error('对不起，不存在相关奖品！', $redirect);
        }

        $act = $this->_post('act');
        // 修改商品
        if ($act == 'edit')
        {
            // 商品分类
            $arr = array(
                'item_name'     => I('item_name'),
                'item_sn'       => I('item_sn'),
                'mall_price'    => I('mall_price'),
                'market_price'    => I('market_price'),
                'integral_exchange_rate'    => I('integral_exchange_rate'),
                'isuse'         => I('isuse'),
                'item_desc'     => I('item_desc'),
                'serial'        => I('serial'),
            );
            #echo "<pre>";
            #print_r($_POST);
            #echo "</pre>";
            #die;

            $item_id_list       = $this->_post('choose');           //商品id列表
            $item_number_list   = $this->_post('choose_number');    //商品数量列表

            $item_list = array();
            foreach ($item_id_list as $key => $value) {
                if ($item_number_list[$key] != 0) {
                    $item_list[$key]['item_id'] = $value;
                    $item_list[$key]['number'] = $item_number_list[$key];
                }
            }

            // 商品图片
            $Photo   = D('ItemPackagePhoto');
            $arr_pic = I('pic', array());
            $old_pic = explode('|', I('old_photo'));

            // 需要删除的图片
            $arr_diff = array_diff($old_pic, $arr_pic);
            // dump($arr_diff);
            // echo "<pre>";
            // print_r($arr_pic);
            // print_r($old_pic);
            // echo "</pre>";
            // die;
            foreach ($arr_diff as $diff) {
                if ($diff) {
                    if ($Photo->where('item_id = ' . $item_package_id . " AND base_pic = '" . $diff . "'")->delete() !== false) {
                        // 删除原图及其大、中、小图
                        @unlink($diff);
                        @unlink(big_img($diff));
                        @unlink(middle_img($diff));
                        @unlink(small_img($diff));
                    }
                }
            }

            $data = array();
            foreach ($arr_pic as $k => $pic) {
                if ($k == 0)
                {
                    $arr['base_pic'] = $pic;
                }
                $is_default = $k == 0 ? 1 : 0;

                if (in_array($pic, $old_pic)) {
                    $Photo->where('item_id = ' . $item_package_id . " AND base_pic = '" . $pic . "'")->save(array('is_default' => $is_default, 'serial' => $k));
                } else {
                    $data[] = array(
                        'item_id'    => $item_package_id,
                        'is_default' => $is_default,
                        'base_pic'   => $pic,
                        // 'serial'     => $k
                    );

                    // 图片压缩加水印
                    $this->_resizeImg(APP_PATH . ltrim($pic, '/'));
                }
            }
            // dump($data);
            if($data){
                $Photo->addAll($data);
            }
            // echo $Photo->getLastSql();die;

            //将商品基本信息保存到数据库
            $package_success = $item_package_obj->editItemPackage($arr);

            $package_detail_obj = new ItemPackageDetailModel($item_package_id);
            if($item_list && !empty($item_list))
            {
                $package_detail_obj->deleteAllItem();
                $item_success = $package_detail_obj->addItemListToPackage($item_package_id, $item_list);
                
            }
            if($package_success || $item_success)
            {
                $this->success('恭喜你，套餐修改成功', '/AcpItemPackage/get_item_package_list/mod_id/2');
            }
            else
            {
                $this->error('对不起，修改失败!',get_url());
            }

        }

        // 获取所有启用的分类
        $arr_category = get_category_tree();
        $this->assign('arr_category', $arr_category);
        
        // 获取所有启用的品牌
        $arr_brand = get_brand();
        $this->assign('arr_brand', $arr_brand);

        // 商品图片
        $arr_photo = array();
        $old_photo = '';
        // $ItemTxt = D('ItemTxt');
        // $ItemTxtPhoto = D('ItemTxtPhoto');
        $res = D('ItemPackagePhoto')->getPhotos($item_package_id);
        // dump($res);die;
        if ($res) {
            $arr_photo = $res;
            $old_photo = implode('|', $res);
        }
        $this->assign('arr_photo', $arr_photo);
        $this->assign('old_photo', $old_photo);

        $item_list = D('ItemPackageDetail')->getPackageItemList('', 'item_package_id = ' . $item_package_id);
        // dump($item_list);
        $this->assign('item_list', $item_list);

        $this->assign('item_package', $item_package_info);
        // dump($item_package_info);
        $this->display();
    }

    public function del_item_package()
    {
        $item_package_id = intval(I('id'));
        // echo $item_package_id;die;

        $res = D('ItemPackage')->delItemPackage($item_package_id);

        if ($res === false) {
            $this->ajaxReturn('对不起，操作失败！');
        } else {
            $this->ajaxReturn('恭喜您，操作成功！');
        }
    }

    /**
     * 图片压缩加水印
     * @param string $base_img 原图地址(绝对路径)
     * @return array 生成的图片信息
     */
    protected function _resizeImg($base_img) {
        import('ORG.Util.Image');
        $Image = new Image();

        $arr_img = array();

        if (!is_file($base_img)) return $arr_img;

        $base_img_info = pathinfo($base_img);
        $img_path = $base_img_info['dirname'] . '/';
        $img_extension = $base_img_info['extension'];
        $img_name = str_replace('.' . $img_extension, '', $base_img_info['basename']);

        /***** 等比缩放 开始 *****/

        // 生成大图
        $big_img_path = $img_path . $img_name . C('BIG_IMG_SUFFIX') . '.' . $img_extension;
        $big_img = $Image->thumb($base_img, $big_img_path, $img_extension, C('BIG_IMG_SIZE'), C('BIG_IMG_SIZE'));

        // 生成中图
        $middle_img_path = $img_path . $img_name . C('MIDDLE_IMG_SUFFIX') . '.' . $img_extension;
        $middle_img = $Image->thumb($base_img, $middle_img_path, $img_extension, C('MIDDLE_IMG_SIZE'), C('MIDDLE_IMG_SIZE'));

        // 生成小图
        $small_img_path = $img_path . $img_name . C('SMALL_IMG_SUFFIX') . '.' . $img_extension;
        $small_img = $Image->thumb($base_img, $small_img_path, $img_extension, C('SMALL_IMG_WIDTH'), C('SMALL_IMG_HEIGHT'));
        /***** 等比缩放 结束 *****/

        $arr_img['big_img']    = $big_img;
        $arr_img['middle_img'] = $middle_img;
        $arr_img['small_img']  = $small_img;

        return $arr_img;
    }

    /**
     *  @access private
     *  @todo 获取商品列表（搜索） 
     *  @return array 返回查询结果，同时分配分页变量数据到smarty模板
     */
    private function list_item()
    {
        $this->Item = D('Item');
        
        $title       = $this->_request('title');
        $category_id = $this->_request('category');
        $brand_id    = $this->_request('brand');
        $mall_price  = $this->_request('mall_price');
        
        import('ORG.Util.Pagelist');
        
        /***** 组织查询条件 开始 *****/
        $condition = array(
                'isuse'      => 1,
                'is_del'     => 0,
                'is_gift'    => 0
        );
        if ($title){
            $condition['item_name'] = array('LIKE', "%$title%");
        }
        if ($brand_id){
            $condition['brand_id']  = $brand_id;
        }
        // 分类
        if ($category_id) {
            $arr_category = explode('.', $category_id);
            if ($arr_category[0] == 1) {
                $condition['class_id'] = $arr_category[1];
            } elseif ($arr_category[0] == 2) {
                $condition['sort_id'] = $arr_category[1];
            } elseif ($arr_category[0] == 3) {
                $condition['class_genre_id'] = $arr_category[1];
            }
        }
        
        if($mall_price){
            $condition['mall_price'] = array('EGT',$mall_price);
        }
        /***** 组织查询条件 结束 *****/
        
        // 符合条件的商品总数
        $count = $this->Item->getItemNum($condition);
        
        //分页开始
        $Page = new Pagelist($count, 5);
        //获取查询参数传递
        $map['title']       = $title;
        $map['category']    = $category_id;
        $map['brand']       = $brand_id;
        $map['mall_price']  = $mall_price;
        $map['mod_id']      = 4;
        //      $map['total'] = $total;
        
        foreach($map as $k=>$v){
            $Page->parameter.= "$k=".$v.'&';    //为分页添加搜索条件
        }
        $page_str = $Page->show();                      //分页输出
        $this->assign('page_str',$page_str);

        $this->Item->setStart($Page->firstRow);
        $this->Item->setLimit($Page->listRows);
        $arr_item = $this->Item->listItem($condition, 'addtime DESC');      //分页取数据
        //分页结束
        #echo $this->Item->getLastSql();echo '<hr/>';
        foreach ($arr_item as $k => $item) {
            $arr_item[$k]['small_img'] = $item['base_pic'] ? small_img($item['base_pic']) : '';
            $arr_item[$k]['item_sn']   = $item['item_sn'] ? $item['item_sn'] : '--';
        }
        
        return $arr_item;
        
//      $this->assign('item_list', $arr_item);
//      $this->display();
    }
    
    
    /**
     * @access public
     * @type AJAX
     * @todo ajax异步请求获取商品的分页数据
     * 
     */
    public function get_items_ajax()
    {
        $item_arr = $this->list_item();
        $this->assign('item_list',$item_arr);
        $this->display('list_item');
    }

}
?>