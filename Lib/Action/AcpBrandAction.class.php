<?php
/**
 * acp后台品牌类
 */
class AcpBrandAction extends AcpAction {

    // 品牌模型对象
    protected $Brand;

    /**
     * 初始化
     * @author cc
     * @return void
     * @todo 初始化方法
     */
    function _initialize() {
        parent::_initialize();
        $this->assign('action_src', U('/AcpBrand/list_brand'));
        // 实例化模型类
        $this->Brand      = D('Brand');
    }

    /**
     * 获取品牌列表
     * @author cc
     * @return void
     * @todo 获取品牌列表
     */
    function list_brand() {
        $brand_obj = new BrandModel();
        $where = '';
        //数据总量
        $total = $brand_obj->getBrandNum($where);

        //处理分页
        import('ORG.Util.Pagelist');
        $per_page_num = C('PER_PAGE_NUM');
        $Page = new Pagelist($total, $per_page_num);
        $brand_obj->setStart($Page->firstRow);
    $brand_obj->setLimit($Page->listRows);

        $page_str = $Page->show();
        $this->assign('page_str',$page_str);

        $brand_list = $brand_obj->getBrandList();
    
        $this->assign('question_brand_list', $brand_list);
        $this->display();
    }

    //添加品牌
    function add_brand() {

        $act = $this->_post('act');
        if ($act == 'add') {
            $_post = $this->_post();
            $brand_name = $_post['brand_name'];
            $brand_desc = $_post['brand_desc'];
            $brand_logo = $_post['pic'];
            $serial     = $_post['serial'];
            $isuse      = $_post['isuse'];
            
            //表单验证
            if (!$brand_name) {
                $this->error('请填写分类名称！');
            }

            if (!ctype_digit($serial)) {
                $this->error('请填写排序号！');
            }

            if (!ctype_digit($isuse)) {
                $this->error('请选择是否有效！');
            }

            $arr = array(
                'brand_name'    => $brand_name,
                'brand_desc'    => $brand_desc,
                'brand_logo'    => $brand_logo,
                'serial'        => $serial,
                'isuse'         => $isuse,
            );

            $brand_obj = new BrandModel();
            $success = $brand_obj->addBrand($arr);

            if ($success) {
                $this->success('恭喜您，品牌添加成功！', '/AcpBrand/add_brand');
            } else {
                $this->error('抱歉，品牌添加失败！', '/AcpBrand/add_brand');
            }
        }

        $this->assign('head_title', '添加品牌');
        $this->display();
    }

    //修改问题
    function edit_brand () {
        $redirect = U('/AcpBrand/get_brand');
        $brand_id = intval($this->_get('brand_id'));
        if (!$brand_id) {
            $this->error('对不起，非法访问！', $redirect);
        }

        $brand_obj = new BrandModel();
        $brand_info = $brand_obj->getBrand($brand_id);
        // dump($brand_info);die;

        if (!$brand_info) {
            $this->error('对不起，不存在相关分类！', $redirect);
        }

        $act = $this->_post('act');

        if($act == 'edit') {
            $_post = $this->_post();
            $brand_name = $_post['brand_name'];
            $brand_desc = $_post['brand_desc'];
            $brand_logo = $_post['pic'];
            $serial     = $_post['serial'];
            $isuse      = $_post['isuse'];
            
            //表单验证
            if(!$brand_name) {
                $this->error('请填写分类名称！');
            }

            if(!ctype_digit($serial)) {
                $this->error('请填写排序号！');
            }

            if(!ctype_digit($isuse)) {
                $this->error('请选择是否有效！');
            }

            $arr = array(
                'brand_name'    => $brand_name,
                'brand_desc'    => $brand_desc,
                'brand_logo'    => $brand_logo,
                'serial'        => $serial,
                'isuse'         => $isuse,
            );

            $brand_obj = new BrandModel($brand_id);
      $url = '/AcpBrand/edit_brand/brand_id/' . $brand_id;

            if ($brand_obj->setBrand($brand_id,$arr)) {
                $this->success('恭喜您，分类修改成功！', $url) ;
            } else {
                $this->error('抱歉，分类修改失败！', $url);
            }

        }
        // dump($brand_info);die;

        if ($brand_info['brand_logo']) {
            $this->assign('pic', $brand_info['brand_logo']);
            $this->assign('pic_img_path', APP_PATH . $brand_info['brand_logo']);
        }

        $this->assign('brand_info', $brand_info);
        $this->assign('head_title', '修改一级分类');
        $this->display();
    }

    //删除
    public function delete_brand() {
        $brand_id = intval($this->_post('brand_id'));
        if ($brand_id) {
            $question_brand_obj = new BrandModel();
            $item_obj           = new ItemModel();
            $num                = $item_obj->where('brand_id = ' . $brand_id)->count();
            if ($num) exit('failure');
            $success = $question_brand_obj->delBrand($brand_id);
            echo $success ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }

    //批量删除
    function batch_delete_brand () {
  
        $question_ids = $this->_post('question_brand_ids');

        if ($question_ids) {
            $question_id_ary = explode(',', $question_ids);
            $success_num = 0;
            $question_obj = new BrandModel();
            $item_obj           = new ItemModel();
            foreach ($question_id_ary AS $question_id)
            {
                $num = $item_obj->where('brand_id = ' . $question_id)->count();
                if ($num) continue;
                $success_num += $question_obj->delIndustry($question_id);
            }
            echo $success_num ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }
}