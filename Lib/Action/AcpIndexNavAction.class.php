<?php
/**
 * 首页导航类
 */

class AcpIndexNavAction extends AcpAction
{
    public function AcpIndexNavAction()
    {
        parent::_initialize();
    }

    private function get_search_condition()
    {
        //初始化SQL查询的where子句
        $where = '';

        //标题
        $title = $this->_request('title');
        if ($title) {
            $where .= ' AND title LIKE "%' . $title . '%"';
        }

        //状态
        $isuse = $this->_request('isuse');
        if (is_numeric($isuse) && $isuse != -1) {
            $where .= ' AND isuse LIKE "%' . $isuse . '%"';
        }

        #echo $where;
        //重新赋值到表单
        $this->assign('title', $title);
        $this->assign('isuse', $isuse);

        /*重定向页面地址begin*/
        $redirect = $_SERVER['PATH_INFO'];
        $redirect .= $title ? '/title/' . $title : '';
        $redirect .= $isuse ? '/isuse/' . $isuse : '';

        $this->assign('redirect', url_jiami($redirect));
        /*重定向页面地址end*/

        return $where;
    }

    /**
     * 获取首页导航列表，公共方法
     * @author zlf
     * @param string $where
     * @param string $head_title
     * @param string $opt    引入的操作模板文件
     * @todo 获取首页导航列表，公共方法
     */
    public function index_nav_list($where, $head_title, $opt)
    {
        $where .= $this->get_search_condition();
        $index_nav_obj = new IndexNavModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $index_nav_obj->getIndexNavNum($where);
        $Page  = new Pagelist($count, C('PER_PAGE_NUM'));
        $index_nav_obj->setStart($Page->firstRow);
        $index_nav_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $index_nav_list = $index_nav_obj->getIndexNavList('', $where, ' serial ASC');
        $index_nav_list = $index_nav_obj->getListData($index_nav_list);
        $this->assign('index_nav_list', $index_nav_list);
        #echo "<pre>"; print_r($index_nav_list);die;
        #echo "<pre>";
        #print_r($index_nav_list);
        #echo "</pre>";
        #echo $index_nav_obj->getLastSql();

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        #$this->display(APP_PATH . 'Tpl/AcpIndexNav/get_index_nav_list.html');
    }

    public function get_index_nav_list()
    {
        $this->index_nav_list('1 = 1', '首页首页导航列表');
        $this->display();

    }

    /**
     * @access public
     * @todo 添加首页导航
     *
     */
    public function add_index_nav()
    {
        $submit = $this->_post('submit');
        if ($submit == 'submit') //执行添加操作
        {
            $title      = $this->_post('title');
            $link       = $this->_post('link');
            $serial     = $this->_post('serial');
            $pic        = $this->_post('pic');
            $isuse      = $this->_post('isuse');
            $size_style = $this->_post('size_style');

            if (!is_numeric($serial)) {
                $this->error('对不起，排序号必须为0-255的整数，请重新输入');
            }

            if (!$pic) {
                $this->error('对不起，请上传图片');
            }

            $data = array(
                'title'      => $title,
                'link'       => $link,
                'serial'     => $serial,
                'pic'        => $pic,
                'isuse'      => $isuse,
                'size_style' => $size_style,
            );
            $this->_resizeImg(APP_PATH . ltrim($pic, '/'));

            $index_nav_obj = new IndexNavModel();
            $index_nav_id  = $index_nav_obj->addIndexNav($data, 1);
            #echo "<pre>";
            #print_r($data);
            #die;
            if ($index_nav_id) {
                $this->success('恭喜您，首页导航添加成功', '/AcpIndexNav/get_index_nav_list');
            } else {
                $this->error('抱歉，添加失败');
            }
        }

        // 分类图标
        $this->assign('pic_data', array(
            'name'  => 'pic',
            'title' => '图标',
            'help'  => '<span style="color:red;">图片尺寸：100*100像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片',
        ));

        $this->assign('head_title', '添加首页导航');
        $this->display();
    }

    /**
     * @access public
     * @todo 修改首页导航
     *
     */
    public function edit_index_nav()
    {
        $redirect       = $this->_get('redirect');
        $index_nav_id   = intval($this->_get('index_nav_id'));
        $index_nav_obj  = new IndexNavModel();
        $index_nav_info = $index_nav_obj->getIndexNavInfo('index_nav_id = ' . $index_nav_id, '');
        #echo $index_nav_obj->getLastSql();
        #echo "<pre>";
        #print_r($index_nav_info);
        #die;

        if (!$index_nav_info) {
            $this->error('抱歉，轮播图位不存在', U('/AcpIndexNav/get_index_nav_list'));
        }

        $submit = $this->_post('submit');
        if ($submit == 'submit') //执行添加操作
        {
            $title  = $this->_post('title');
            $link   = $this->_post('link');
            $serial = $this->_post('serial');
            $pic    = $this->_post('pic');
            // echo $pic;die;
            $isuse      = $this->_post('isuse');
            $size_style = $this->_post('size_style');

            if (!is_numeric($serial)) {
                $this->error('对不起，排序号必须为0-255的整数，请重新输入');
            }

            if (!$pic) {
                $this->error('对不起，请上传图片');
            }

            if (!is_numeric($isuse)) {
                $this->error('对不起，请选择是否显示');
            }

            $data = array(
                'title'      => $title,
                'link'       => $link,
                'serial'     => $serial,
                'pic'        => $pic,
                'isuse'      => $isuse,
                'size_style' => $size_style,
            );
            // dump($data);die;
            if ($index_nav_info['pic'] != $pic) {
                $arr_img = $this->_resizeImg(APP_PATH . ltrim($pic, '/'));
                // dump($arr_img);
            }
            // die;

            $index_nav_obj = new IndexNavModel($index_nav_id);
            $success       = $index_nav_obj->editIndexNav($data);
            // echo $index_nav_obj->getLastSql();die;
            if ($success) {
                $this->success('恭喜您，首页导航修改成功');
            } else {
                $this->error('抱歉，修改失败');
            }
        }
        // dump($index_nav_info);die;
        foreach ($index_nav_info as $k => $v) {
            if ($k == 'pic') {
                $this->assign('pic_img_path', APP_PATH . $v);
            }
            if ($k == 'link') {
                $this->assign('index_nav_link', $v);
            }
            $this->assign($k, $v);
        }

        // 分类图标
        $this->assign('pic_data', array(
            'name'  => 'pic',
            'url'   => $index_nav_info['pic'],
            'title' => '图标',
            'help'  => '<span style="color:red;">图片尺寸：100*100像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片',
        ));

        $this->assign('head_title', '修改首页导航');
        $this->display('add_index_nav');
    }

    //删除轮播图
    public function del_index_nav()
    {
        $id = intval($this->_post('id'));
        if ($id) {

            $index_nav_obj = new IndexNavModel($id);
            $success       = $index_nav_obj->delIndexNav($id);

            echo $success ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }

    /**
     * 获取广告图片列表，公共方法
     * @author zlf
     * @param string $where
     * @param string $head_title
     * @param string $opt    引入的操作模板文件
     * @todo 获取广告图片列表，公共方法
     */
    public function adv_list($where = '', $head_title = '', $opt = '')
    {
        $where .= $this->get_search_condition();
        $adv_obj = D('Adv');

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $adv_obj->getAdvNum($where);
        $Page  = new Pagelist($count, C('PER_PAGE_NUM'));
        $adv_obj->setStart($Page->firstRow);
        $adv_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $adv_list = $adv_obj->getAdvList('', $where, ' serial ASC');

        $this->assign('adv_list', $adv_list);
        // echo "<pre>";
        // print_r($adv_list);
        // echo "</pre>";
        // echo $adv_obj->getLastSql();die;

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
    }

    //顶部广告位
    public function top_adv_list()
    {
        $this->adv_list('adv_type = 1', '顶部广告位');
        $this->display('get_adv_list');
    }

    /**
     * @access public
     * @todo 添加首页导航
     *
     */
    public function add_adv()
    {
        $submit = $this->_post('submit');
        if ($submit == 'submit') //执行添加操作
        {
            $title  = $this->_post('title');
            $link   = $this->_post('link');
            $serial = $this->_post('serial');
            $pic    = $this->_post('pic');
            $isuse  = $this->_post('isuse');

            if (!is_numeric($serial)) {
                $this->error('对不起，排序号必须为0-255的整数，请重新输入');
            }

            if (!$pic || !file_exists(APP_PATH . $pic)) {
                $this->error('对不起，请上传图片');
            }

            $data = array(
                'title'    => $title,
                'link'     => $link,
                'serial'   => $serial,
                'pic'      => $pic,
                'isuse'    => $isuse,
                'adv_type' => 1,
            );
            $this->_resizeImg(APP_PATH . ltrim($pic, '/'));

            $adv_obj = new AdvModel();
            $adv_id  = $adv_obj->addAdv($data, 2);
            #echo "<pre>";
            #print_r($data);
            #die;
            if ($adv_id) {
                $this->success('恭喜您，图片添加成功', '/AcpIndexNav/top_adv_list');
            } else {
                $this->success('抱歉，添加失败');
            }
        }

        $this->assign('head_title', '添加首页导航');
        $this->display();
    }

    /**
     * @access public
     * @todo 修改首页导航
     *
     */
    public function edit_adv()
    {
        $redirect = $this->_get('redirect');
        $adv_id   = intval($this->_get('adv_id'));
        $adv_obj  = new AdvModel();
        $adv_info = $adv_obj->getAdvInfo('adv_id = ' . $adv_id, '');
        #echo $adv_obj->getLastSql();
        #echo "<pre>";
        #print_r($adv_info);
        #die;

        if (!$adv_info) {
            $this->error('抱歉，广告图位不存在', U('/AcpIndexNav/top_adv_list'));
        }

        $submit = $this->_post('submit');
        if ($submit == 'submit') //执行添加操作
        {
            $title  = $this->_post('title');
            $link   = $this->_post('link');
            $serial = $this->_post('serial');
            $pic    = $this->_post('pic');
            $isuse  = $this->_post('isuse');

            if (!is_numeric($serial)) {
                $this->error('对不起，排序号必须为0-255的整数，请重新输入');
            }

            if (!$pic || !file_exists(APP_PATH . $pic)) {
                $this->error('对不起，请上传图片');
            }

            if (!is_numeric($isuse)) {
                $this->error('对不起，请选择是否显示');
            }

            $data = array(
                'title'  => $title,
                'link'   => $link,
                'serial' => $serial,
                'pic'    => $pic,
                'isuse'  => $isuse,
            );
            if ($adv_info['pic'] != $pic) {
                $this->_resizeImg(APP_PATH . ltrim($pic, '/'));
            }

            $adv_obj = new AdvModel($adv_id);
            $success = $adv_obj->editAdv($data);
            if ($success) {
                $this->success('恭喜您，图片修改成功');
            } else {
                $this->success('抱歉，修改失败');
            }
        }

        foreach ($adv_info as $k => $v) {
            if ($k == 'pic') {
                $this->assign('pic_img_path', APP_PATH . $v);
            }
            if ($k == 'link') {
                $this->assign('adv_link', $v);
            }
            $this->assign($k, $v);
        }

        $this->assign('head_title', '修改广告图片');
        $this->display();
    }

    //删除轮播图
    public function del_adv()
    {
        $id = intval($this->_post('id'));
        if ($id) {

            $adv_obj = new AdvModel($id);
            $success = $adv_obj->delAdv($id);

            echo $success ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }

    /**
     * 图片压缩加水印
     * @param string $base_img 原图地址(绝对路径)
     * @return array 生成的图片信息
     */
    protected function _resizeImg($base_img)
    {
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
}
