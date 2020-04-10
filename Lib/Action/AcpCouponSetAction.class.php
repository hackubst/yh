<?php
/**
 * acp后台优惠券设置类
 */
class AcpCouponSetAction extends AcpAction
{

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
    }

    /**
     * 接收搜索表单数据，组织返回where子句
     * @author wsq
     * @param void
     * @return void
     * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
     */
    public function get_search_condition()
    {
        $where = "";

        //添加时间范围起始时间
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        $this->assign('start_time', $start_time);
        if ($start_time) {
            $where .= ' AND addtime >= ' . $start_time;
            $this->assign('start_time', $start_time);
        }

        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time) {
            $where .= ' AND addtime <= ' . $end_time;
            $this->assign('end_time', $end_time);
        }

        return $where;
    }

    //获取满减信息
    //@author wsq
    public function get_coupon_set_list()
    {
        $where        = "1" . $this->get_search_condition();
        $discount_obj = D('CouponSet');
        $count        = $discount_obj->getCouponSetNum($where);
        $sort         = $this->get_and_set_sort_info(array(
            'amount_limit', 'num', 'use_time',
        ));

        $sort = $sort ? $sort : ' addtime DESC';

        import('ORG.Util.Pagelist');
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $discount_obj->setStart($Page->firstRow);
        $discount_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
        $this->assign('show', $show);

        $coupon_set_list = $discount_obj->getCouponSetList('', $where, $sort);
        $coupon_set_list = $discount_obj->getListData($coupon_set_list);
        #dump($coupon_set_list);exit;

        $this->assign('coupon_set_list', $coupon_set_list);

        $this->assign('opt', $opt);
        $this->assign('head_title', '领券列表');
        $this->display();
    }

    //获取满减明细
    //@author wsq
    public function get_coupon_set_detail()
    {
        $where        = "1" . $this->get_search_condition();
        $discount_obj = D('CouponSet');
        $count        = $discount_obj->getCouponSetNum($where);
        $sort         = $this->get_and_set_sort_info(array(
            'order_amount', 'num',
        ));

        $sort = $sort ? $sort : ' addtime DESC';

        import('ORG.Util.Pagelist');
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $discount_obj->setStart($Page->firstRow);
        $discount_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
        $this->assign('show', $show);

        $coupon_set_list = $discount_obj->getCouponSetList('', $where, $sort);
        // $coupon_set_list = $discounht_obj->getListData($coupon_set_list);

        $this->assign('coupon_set_list', $coupon_set_list);

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display();
    }

    /**
     * 添加新人优惠券
     * @author tale
     */
    public function add_coupon_set()
    {
        $action = I('post.action');
        if ($action == 'add') {
            $post         = I('post.');
            $title        = trim($post['title']);
            $type_num     = intval($post['type_num']);
            $vouchers_ids = $post['vouchers_ids'];
            $start_time   = $this->_request('start_time');
            $start_time   = str_replace('+', ' ', $start_time);
            $start_time   = strtotime($start_time);
            $end_time     = $this->_request('end_time');
            $end_time     = str_replace('+', ' ', $end_time);
            $end_time     = strtotime($end_time);
            $bg_pic       = $post['bg_pic'];
            $isuse        = intval($post['isuse']);

            if (!$title) {
                $this->error('对不起，请填写领券标题');
            }

            if (!$type_num || !in_array($type_num, array(1, 2, 3, 4))) {
                $this->error('对不起，请选择领券类型');
            }

            if (!$vouchers_ids || !is_array($vouchers_ids)) {
                $this->error('对不起，请选择已有优惠券');
            }

            if ($type_num == 1 && !$bg_pic) {
                $this->error('对不起，新人优惠券需上传背景图片');
            }

            if ($type_num == 2 && count($vouchers_ids) > 1) {
                $this->error('对不起，注册领券只能选择一个优惠券');
            }

            if (!$start_time || !$end_time) {
                $this->error('对不起，请选择使用期限');
            }

            $coupon_set_obj = new CouponSetModel;

            $where           = 'type_num = ' . $type_num . ' AND (start_time < ' . $end_time . ' OR end_time > ' . $start_time . ')';
            $coupon_set_info = $coupon_set_obj->where($where)->find();
            if ($coupon_set_info) {
                $this->error('对不起，同一时间段，不能存在相同类型的领券！');
            }

            $data = array(
                'title'        => $title,
                'type_num'     => $type_num,
                'vouchers_ids' => implode($vouchers_ids, ','),
                'start_time'   => $start_time,
                'end_time'     => $end_time,
                'isuse'        => $isuse,
            );

            if ($type_num == 1) {
                $data['bg_pic'] = $bg_pic;
            }
            $result = $coupon_set_obj->addCouponSet($data);

            if ($result) {
                $this->success('添加成功!', '/AcpCouponSet/get_coupon_set_list');
            } else {
                $this->error('添加失败！');
            }
        }

        // 优惠券图片
        $this->assign('bg_pic_data', array(
            'name'  => 'bg_pic',
            'title' => '优惠券图片',
            'help'  => '图片尺寸750*200，暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片',
        ));

        //获取优惠券列表
        $vouchers_obj  = new VouchersModel;
        $vouchers_list = $vouchers_obj->getVouchersList('vouchers_id,num,amount_limit,title', 'isuse = 1');
        $this->assign('vouchers_list', $vouchers_list);
        // dump($vouchers_list);exit;

        $this->assign('action', 'add');
        $this->assign("head_title", "添加新人优惠券");

        $this->display();
    }

    //@author
    //修改优惠券
    public function edit_coupon_set()
    {
        $coupon_set_id = intval(I('request.coupon_set_id'));
        if (!$coupon_set_id) {
            $this->error('对不起，访问出错！');
        }

        $coupon_set_obj  = new CouponSetModel($coupon_set_id);
        $coupon_set_info = $coupon_set_obj->where('coupon_set_id = ' . $coupon_set_id)->find();
        if (!$coupon_set_info) {
            $this->error('对不起，访问错误！');
        }

        $action = I('post.action');

        if ($action == 'edit') {
            $post = I('post.');
            // dump($post);exit;
            $title        = trim($post['title']);
            $type_num     = intval($post['type_num']);
            $vouchers_ids = $post['vouchers_ids'];
            $start_time   = $this->_request('start_time');
            $start_time   = str_replace('+', ' ', $start_time);
            $start_time   = strtotime($start_time);
            $end_time     = $this->_request('end_time');
            $end_time     = str_replace('+', ' ', $end_time);
            $end_time     = strtotime($end_time);
            $bg_pic       = $post['bg_pic'];
            $isuse        = intval($post['isuse']);

            if (!$title) {
                $this->error('对不起，请填写领券标题');
            }

            if (!$type_num || !in_array($type_num, array(1, 2, 3, 4))) {
                $this->error('对不起，请选择领券类型');
            }

            if (!$vouchers_ids || !is_array($vouchers_ids)) {
                $this->error('对不起，请选择已有优惠券');
            }

            if ($type_num == 1 && !$bg_pic) {
                $this->error('对不起，新人优惠券需上传背景图片');
            }

            if ($type_num == 2 && count($vouchers_ids) > 1) {
                $this->error('对不起，注册领券只能选择一个优惠券');
            }

            if (!$start_time || !$end_time) {
                $this->error('对不起，请选择使用期限');
            }

            $where           = 'type_num = ' . $type_num . ' AND (start_time < ' . $end_time . ' OR end_time > ' . $start_time . ') AND coupon_set_id != ' . $coupon_set_id;
            $coupon_set_info = $coupon_set_obj->where($where)->find();
            if ($coupon_set_info) {
                $this->error('对不起，同一时间段，不能存在相同类型的领券！');
            }

            $data = array(
                'title'        => $title,
                'type_num'     => $type_num,
                'vouchers_ids' => implode($vouchers_ids, ','),
                'start_time'   => $start_time,
                'end_time'     => $end_time,
                'isuse'        => $isuse,
            );

            if ($type_num == 1) {
                $data['bg_pic'] = $bg_pic;
            }

            $result = $coupon_set_obj->editCouponSet($data);

            if ($result) {
                $this->success('修改成功!');
            } else {
                $this->error('修改失败！');
            }
        }

        // 优惠券图片
        $this->assign('bg_pic_data', array(
            'name'  => 'bg_pic',
            'url'   => $coupon_set_info['bg_pic'],
            'title' => '优惠券图片',
            'help'  => '图片尺寸750*200，暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片',
        ));

        // 优惠券ids
        $this->assign('vouchers_ids', explode(',', $coupon_set_info['vouchers_ids']));

        //获取优惠券列表
        $vouchers_obj  = new VouchersModel;
        $vouchers_list = $vouchers_obj->getVouchersList('vouchers_id,num,amount_limit,title', 'isuse = 1');
        $this->assign('vouchers_list', $vouchers_list);
        // dump($vouchers_list);exit;

        $this->assign('coupon_set_info', $coupon_set_info);
        $this->assign('action', 'edit');
        $this->assign("head_title", "编辑新人优惠券");

        $this->display('add_coupon_set');
    }

    /**
     * 删除领券
     * @author tale
     */
    public function delete_coupon_set()
    {
        $coupon_set_id = I('post.coupon_set_id', 0, 'int');

        if ($coupon_set_id) {
            $where  = 'coupon_set_id = ' . $coupon_set_id;
            $status = D('CouponSet')->where($where)->delete();
            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }

    /**
     * 批量删除领券
     * @author tale
     */
    public function batch_delete_coupon_set()
    {
        $coupon_set_ids = I('post.coupon_set_ids');

        if ($coupon_set_ids) {
            $coupon_set_array = explode(',', $coupon_set_ids);
            $success_num      = 0;
            $coupon_set_obj   = D('CouponSet');

            foreach ($coupon_set_array as $coupon_set_id) {
                $status = $coupon_set_obj->where('coupon_set_id =' . $coupon_set_id)->delete();
                $success_num += $status ? 1 : 0;

            }
            exit($success_num ? 'success' : 'failure');

        } else {
            exit('failure');

        }
    }

    /**
     * 设置领券状态
     * @author tale
     */
    public function set_enable()
    {
        $coupon_set_id = I('post.coupon_set_id', 0, 'int');
        $opt           = I('post.opt');

        if ($coupon_set_id && is_numeric($opt)) {
            $status = D('CouponSet')->where('coupon_set_id =' . $coupon_set_id)->setField('isuse', $opt);
            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }

    /**
     * 批量设置领券状态
     * @author tale
     */
    public function batch_set_enable()
    {
        $coupon_set_ids = I('post.coupon_set_ids');
        $opt            = I('post.opt');

        if ($coupon_set_ids && is_numeric($opt)) {
            $coupon_set_array = explode(',', $coupon_set_ids);
            $success_num      = 0;
            $coupon_set_obj   = D('CouponSet');

            foreach ($coupon_set_array as $coupon_set_id) {
                $status = $coupon_set_obj->where('coupon_set_id =' . $coupon_set_id)->setField('isuse', $opt);
                $success_num += $status ? 1 : 0;

            }
            exit($success_num ? 'success' : 'failure');

        } else {
            exit('failure');

        }
    }
}
