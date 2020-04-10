<?php
/**
 * 系统设置
 *
 *
 */
class AcpConfigAction extends AcpAction
{

    /**
     * 构造函数
     * @return void
     * @todo
     */
    public function AcpConfigAction()
    {
        parent::_initialize();
        #require_once('Common/func_sms.php');
    }

    /**
     * @access public
     * @todo 高级设置
     * @return void
     * @author 姜伟
     */
    public function advanced_setting()
    {
        $act = $this->_post('act');
        if ($act == 'save') //提交保存时
        {
            $planter_online_time            = $this->_post('planter_online_time');
            $planter_visit_time_interval    = $this->_post('planter_visit_time_interval');
            $item_num_per_sort              = $this->_post('item_num_per_sort');
            $item_num_per_page              = $this->_post('item_num_per_page');
            $item_num_per_my_plant_page     = $this->_post('item_num_per_my_plant_page');
            $item_num_per_collect_page      = $this->_post('item_num_per_collect_page');
            $item_num_per_recharge_log_page = $this->_post('item_num_per_recharge_log_page');

            if (!$planter_online_time || $planter_online_time == '') {
                $this->error('对不起, 种植机在线定义不能为空，请填写！', get_url());
            }

            if (!ctype_digit($planter_online_time)) {
                $this->error('对不起, 种植机在线定义必须为整数，请重新填写！', get_url());
            }

            if (!$planter_visit_time_interval || $planter_visit_time_interval == '') {
                $this->error('对不起, 种植机请求时间间隔不能为空，请填写！', get_url());
            }

            if (!ctype_digit($planter_visit_time_interval) || !($planter_visit_time_interval <= 9999 && $planter_visit_time_interval >= 500)) {
                $this->error('对不起, 种植机请求时间间隔必须为整数，且在500-9999范围内，请重新填写！', get_url());
            }

            if (!$item_num_per_sort || $item_num_per_sort == '') {
                $this->error('对不起, 商城首页每分类显示商品数不能为空，请填写！', get_url());
            }

            if (!ctype_digit($item_num_per_sort)) {
                $this->error('对不起, 商城首页每分类显示商品数必须为整数，请重新填写！', get_url());
            }

            if (!$item_num_per_page || $item_num_per_page == '') {
                $this->error('对不起, 商城列表页每页显示商品数不能为空，请填写！', get_url());
            }

            if (!ctype_digit($item_num_per_page)) {
                $this->error('对不起, 商城列表页每页显示商品数必须为整数，请重新填写！', get_url());
            }

            if (!$item_num_per_my_plant_page || $item_num_per_my_plant_page == '') {
                $this->error('对不起, 我种植的植物每页记录数不能为空，请填写！', get_url());
            }

            if (!ctype_digit($item_num_per_my_plant_page)) {
                $this->error('对不起, 我种植的植物每页记录数必须为整数，请重新填写！', get_url());
            }

            if (!$item_num_per_collect_page || $item_num_per_collect_page == '') {
                $this->error('对不起, 我的收藏每页显示记录数不能为空，请填写！', get_url());
            }

            if (!ctype_digit($item_num_per_collect_page)) {
                $this->error('对不起, 我的收藏每页显示记录数必须为整数，请重新填写！', get_url());
            }

            if (!$item_num_per_recharge_log_page || $item_num_per_recharge_log_page == '') {
                $this->error('对不起, 充值/财务明细每页记录不能为空，请填写！', get_url());
            }

            if (!ctype_digit($item_num_per_recharge_log_page)) {
                $this->error('对不起, 充值/财务明细每页记录必须为整数，请重新填写！', get_url());
            }

            $data = array(
                'planter_online_time'            => $planter_online_time,
                'planter_visit_time_interval'    => $planter_visit_time_interval,
                'item_num_per_sort'              => $item_num_per_sort,
                'item_num_per_page'              => $item_num_per_page,
                'item_num_per_my_plant_page'     => $item_num_per_my_plant_page,
                'item_num_per_collect_page'      => $item_num_per_collect_page,
                'item_num_per_recharge_log_page' => $item_num_per_recharge_log_page,
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            if ($planter_visit_time_interval != $GLOBALS['config_info']['PLANTER_VISIT_TIME_INTERVAL']) {
                //如果修改了种植机访问时间间隔，更新内存中的command
                PlanterModel::flushKey('time_interval', sprintf('%04d', $planter_visit_time_interval));
            }
            $this->success('恭喜你，设置成功!', '/AcpConfig/advanced_setting');
        }

        $this->assign('head_title', '高级设置');
        $this->assign('config', $this->system_config);
        $this->display();
    }

    /**
     * 有效流水配置
     * @author yzp
     * @Date:  2019/6/24
     * @Time:  9:53
     */
    public function valid_flow_config()
    {
        $act = $this->_post('act');
        if ($act == 'save') //提交保存时
        {
            $double_flow = $this->_post('double_flow');
            $valid_flow = $this->_post('valid_flow');
            $return_double_flow = $this->_post('return_double_flow');
            $self_flow_rate = $this->_post('self_flow_rate');

            if (!ctype_digit($double_flow)) {
                $this->error('对不起, 有效流水倍数必须为整数，请重新填写！', get_url());
            }
            if (!ctype_digit($valid_flow)) {
                $this->error('对不起, 有效流水必须为整数，请重新填写！', get_url());
            }
            $data = array(
                'double_flow' => $double_flow,
                'valid_flow' => $valid_flow,
                'return_double_flow' => $return_double_flow,
                'self_flow_rate' => $self_flow_rate,
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            $this->success('恭喜你，参数设置成功了!', '/AcpConfig/valid_flow_config/mod_id/0');
        }

        $this->assign('head_title', '有效流水配置');
        $this->assign('config', $this->system_config);
        $this->display();
    }

    /**
     * @access public
     * @todo 基础资料设置 tp_config表中需要设置的参数
     * @return void
     * @author zhoutao@360shop.cc  zhoutao0928@sina.com
     */
    public function base_config()
    {
        $act = $this->_post('act');
        if ($act == 'save') //提交保存时
        {
            $red_limit_money = $this->_post('red_limit_money');
            $red_expire_time = $this->_post('red_expire_time');
            $invite_award = $this->_post('invite_award');
            $red_point = $this->_post('red_point');
            $recharge_rebate = $this->_post('recharge_rebate');
            $rank1 = $this->_post('rank1');
            $rank2 = $this->_post('rank2');
            $rank3 = $this->_post('rank3');
            $rank4 = $this->_post('rank4');
            $rank5 = $this->_post('rank5');
            $rank6 = $this->_post('rank6');
            $rank7 = $this->_post('rank7');
            $min_loss = $this->_post('min_loss');
            $return_rate = $this->_post('return_rate');
            $min_flow = $this->_post('min_flow');
            $flow_rate = $this->_post('flow_rate');
            $poundage = $this->_post('poundage');
            $recharge_exp = $this->_post('recharge_exp');
            $sys_qq = $this->_post('sys_qq');
            $sys_qq_group = $this->_post('sys_qq_group');
            $sys_qq_key = $this->_post('sys_qq_key');
            $max_deduct_rate = $this->_post('max_deduct_rate');
            $min_deduct_rate = $this->_post('min_deduct_rate');
            $dcp_write_off_rate = $this->_post('dcp_write_off_rate');
            $app_download_url = $this->_post('app_download_url');
            $test_url = $this->_post('test_url');

            if($sys_qq)
            {
                $count = count(explode(',',$sys_qq));
                if($count > 3)
                {
                    $this->error('最多填写三个官方客服QQ');
                }
            }
            if($sys_qq_group)
            {
                $count = count(explode(',',$sys_qq_group));
                if($count > 3)
                {
                    $this->error('最多填写三个官方QQ群');
                }
            }
            //保存验证码短信模板
            //$sms_set_obj = new SMSSetModel();
            //$sms_set_obj->setSMSSettingByTag('verify_code', $verify_code_sms_tpl);

            $system_close_reason = $system_close_reason ? $system_close_reason : '啊,亲,太不巧了！由于累了所以我要休息一会儿，O(∩_∩)O~。很快就会回来哦！';
            $data                = array(
                'red_limit_money' => $red_limit_money,
                'red_expire_time' => $red_expire_time,
                'invite_award'     => $invite_award,
                'red_point'       => $red_point,
                'recharge_rebate' => $recharge_rebate,
                'system_close_reason' => $system_close_reason,
                'rank1' => $rank1,
                'rank2' => $rank2,
                'rank3' => $rank3,
                'rank4' => $rank4,
                'rank5' => $rank5,
                'rank6' => $rank6,
                'rank7' => $rank7,
                'min_loss' => $min_loss,
                'return_rate' => $return_rate,
                'min_flow' => $min_flow,
                'flow_rate' => $flow_rate,
                'poundage' => $poundage,
                'recharge_exp' => $recharge_exp,
                'sys_qq_group' => $sys_qq_group,
                'sys_qq_key' => $sys_qq_key,
                'sys_qq' => $sys_qq,
                'max_deduct_rate' => $max_deduct_rate,
                'min_deduct_rate' => $min_deduct_rate,
                'dcp_write_off_rate' => $dcp_write_off_rate,
                'app_download_url' => $app_download_url,
                'test_url' => $test_url,
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            $this->success('恭喜你，参数设置成功了!', '/AcpConfig/base_config/mod_id/0');
        }
        //用户登录大图
        //$system_logo_path = $this->system_config['SYSTEM_LOGO']? APP_PATH . $this->system_config['SYSTEM_LOGO']: false;
        //if($system_logo_path) $this->assign('system_logo_path', $system_logo_path);

        //$qr_code_path = $this->system_config['QR_CODE']? APP_PATH . $this->system_config['QR_CODE']: false;

        $config = $this->system_config;

        $this->assign('qr_code_data', array(
            'name'  => 'qr_code',
            'url'   => $config['QR_CODE'],
            'title' => '关注二维码',
            'help'  => '宽高600*375最佳',
        ));

        $this->assign('qr_code_kf_data', array(
            'name'  => 'qr_code_kf',
            'url'   => $config['QR_CODE_KF'],
            'title' => '客服二维码',
            'help'  => '宽高600*375最佳',
        ));

        $this->assign('qr_code_bg_data', array(
            'name'  => 'qr_code_bg',
            'url'   => $config['QR_CODE_BG'],
            'title' => '推广二维码背景图',
            'help'  => '宽高600*375最佳',
        ));

        $this->assign('head_title', '基础设置');
        $this->assign('config', $this->system_config);
        $this->display();
    }

    public function upload_image()
    {
        if (!$_GET['dir']) {
            $_GET['dir'] = 'config';
        }

        parent::upload_image();
    }

    /**
     * @access public
     * @todo 分销设置 tp_config表中需要设置的参数
     * @return void
     * @author jiangwei
     */
    public function fenxiao_config()
    {
        $act = $this->_post('act');
        if ($act == 'save') //提交保存时
        {
            $is_fenxiao_open         = $this->_post('is_fenxiao_open');
            $fenxiao_level           = $this->_post('fenxiao_level');
            $first_level_agent_rate  = $this->_post('first_level_agent_rate');
            $second_level_agent_rate = $this->_post('second_level_agent_rate');
            $third_level_agent_rate  = $this->_post('third_level_agent_rate');

            if (!$fenxiao_level || !is_numeric($fenxiao_level) || ($fenxiao_level < 1 || $fenxiao_level > 3)) {
                $this->error('对不起，分销级数请填写1-3的整数!', get_url());
            }

            if (!$first_level_agent_rate || (!is_numeric($first_level_agent_rate) && !is_float($first_level_agent_rate))) {
                $this->error('对不起，一级代理提成请填写整数或小数!', get_url());
            }

            if (!$second_level_agent_rate || (!is_numeric($second_level_agent_rate) && !is_float($second_level_agent_rate))) {
                $this->error('对不起，二级代理提成请填写整数或小数!', get_url());
            }

            if (!$third_level_agent_rate || (!is_numeric($third_level_agent_rate) && !is_float($third_level_agent_rate))) {
                $this->error('对不起，三级代理提成请填写整数或小数!', get_url());
            }

            $data = array(
                'is_fenxiao_open'         => $is_fenxiao_open,
                'fenxiao_level'           => $fenxiao_level,
                'first_level_agent_rate'  => $first_level_agent_rate,
                'second_level_agent_rate' => $second_level_agent_rate,
                'third_level_agent_rate'  => $third_level_agent_rate,
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            $this->success('恭喜你，参数设置成功了!', '/AcpConfig/base_config/mod_id/0');
        }

        $config = $this->system_config;
        $this->assign('head_title', '分销设置');
        $this->assign('config', $this->system_config);
        $this->display();
    }

    /**
     * @access public
     * @todo 配置短信相关设置
     * @return void
     * @author zhoutao@360shop.cc  zhoutao0928@sina.com
     */
    public function sms_config()
    {
        $SMSModel = new SMSModel();

        $act = $this->_post('act');
        if ($act == 'ajaxset') //进行异步设置
        {
            $send_name = $this->_post('type');
            $state     = $this->_post('state');
            $to_admin  = $this->_post('to_admin');
            $sms_text  = $this->_post('sms_text');
            $state     = $state ? $state : 0;
            $to_admin  = $to_admin ? $to_admin : 0;
            if (!$send_name || !$sms_text) {
                exit(json_encode(array('type' => -1, 'message' => '参数错误')));
            }
            $data = array(
                'state'    => $state,
                'to_admin' => $to_admin,
                'sms_text' => $sms_text,
            );
            if ($SMSModel->setSMSSetting($send_name, $data)) {
                exit(json_encode(array('type' => 1, 'message' => '恭喜你，设置成功！')));
            }
            exit(json_encode(array('type' => -1, 'message' => '对不起，设置失败,数据可能未作修改！')));
        } elseif ($act == 'set_all') {
            //订单创建
            $order_create_state    = $this->_post('order_create_state');
            $order_create_to_admin = $this->_post('order_create_to_admin');
            $order_create_sms_text = $this->_post('now_order_create');
            $order_create          = array(
                'state'    => $order_create_state,
                'to_admin' => $order_create_to_admin,
                'sms_text' => $order_create_sms_text,
            );

            //订单确认
            $order_check_state    = $this->_post('order_check_state');
            $order_check_to_admin = $this->_post('order_check_to_admin');
            $order_check_sms_text = $this->_post('now_order_check');
            $order_check          = array(
                'state'    => $order_check_state,
                'to_admin' => $order_check_to_admin,
                'sms_text' => $order_check_sms_text,
            );

            //订单支付
            $order_pay_state    = $this->_post('order_pay_state');
            $order_pay_to_admin = $this->_post('order_pay_to_admin');
            $order_pay_sms_text = $this->_post('now_order_pay');
            $order_pay          = array(
                'state'    => $order_pay_state,
                'to_admin' => $order_pay_to_admin,
                'sms_text' => $order_pay_sms_text,
            );

            //订单发货
            $order_ship_state    = $this->_post('order_ship_state');
            $order_ship_to_admin = $this->_post('order_ship_to_admin');
            $order_ship_sms_text = $this->_post('now_order_ship');
            $order_ship          = array(
                'state'    => $order_ship_state,
                'to_admin' => $order_ship_to_admin,
                'sms_text' => $order_ship_sms_text,
            );

            //用户修改密码
            $set_psw_state    = $this->_post('set_psw_state');
            $set_psw_to_admin = $this->_post('set_psw_to_admin');
            $set_psw_sms_text = $this->_post('now_set_psw');
            $set_psw          = array(
                'state'    => $set_psw_state,
                'to_admin' => $set_psw_to_admin,
                'sms_text' => $set_psw_sms_text,
            );

            //2014-05-08 zhoutao 由于注册时不能获取用户的手机号码，注释掉了注册时的短信发送
            $user_reg_sms_text = true;
//             //用户注册时
            //             $user_reg_state      = $this->_post('user_reg_state');
            //             $user_reg_to_admin     = $this->_post('user_reg_to_admin');
            //             $user_reg_sms_text     = $this->_post('now_user_reg');
            //             $user_reg = array(
            //                     'state'        =>    $user_reg_state,
            //                     'to_admin'    =>    $user_reg_to_admin,
            //                     'sms_text'    =>    $user_reg_sms_text
            //             );

            //管理员登录时
            $admin_login_state    = $this->_post('admin_login_state');
            $admin_login_to_admin = $this->_post('admin_login_to_admin');
            $admin_login_sms_text = $this->_post('now_admin_login');
            $admin_login          = array(
                'state'    => $admin_login_state,
                'to_admin' => $admin_login_to_admin,
                'sms_text' => $admin_login_sms_text,
            );

            if (!$order_create_sms_text || !$order_check_sms_text || !$order_ship_sms_text || !$order_pay_sms_text || !$set_psw_sms_text || !$user_reg_sms_text || !$admin_login_sms_text) {
                $this->error('对不起，存在空的短信模板，请在检查后重新保存。', get_url());
            }
            //保存短信设置
            $SMSModel->setSMSSetting('order_create', $order_create);
            $SMSModel->setSMSSetting('order_check', $order_check);
            $SMSModel->setSMSSetting('order_pay', $order_pay);
            $SMSModel->setSMSSetting('order_ship', $order_ship);
            $SMSModel->setSMSSetting('set_psw', $set_psw);
            #$SMSModel->setSMSSetting('user_reg',$user_reg);        //2014-05-08 zhoutao 注释掉
            $SMSModel->setSMSSetting('admin_login', $admin_login);

            $sms_open   = $this->_post('open');
            $sms_open   = $sms_open ? 1 : 0;
            $sms_mobile = $this->_post('sms_mobile');
            $sms_type   = $this->_post('sms_type'); //服务商类型1网建，2创蓝
            $sms_type   = $sms_type ? $sms_type : 1;
            require_once 'Lib/Model/ConfigBaseModel.class.php';
            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfig('sms_open', $sms_open);
            $ConfigBaseModel->setConfig('sms_mobile', $sms_mobile);
            $ConfigBaseModel->setConfig('sms_type', $sms_type);
            $this->success('恭喜你，设置保存成功！', get_url());
        } elseif ($act == 'test_send') //测试发送短信
        {
            $mobile = $this->_post('mobile');
            if (!checkMobile($mobile)) {
                exit(json_encode(array('type' => -1, 'message' => '请输入正确的手机号！')));
            }
            if ($GLOBALS['config_info']['SMS_TYPE'] == 2) {
                vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
                $sms_obj    = new ChuanglanSmsApi();
                $send_state = $sms_obj->sendSMS($mobile, '【b2c】这是一条短信', 'true');
                $result     = $sms_obj->execResult($send_state);
                //dump($result);
                //echo isset($result[1]) && $result[1]==0 ? 'success' : 'failure';
                if (!isset($result[1]) || $result[1] != 0) {
                    exit(json_encode(array('type' => -1, 'message' => '对不起，发送失败!')));
                }
            } else {
                $result = $SMSModel->sendSMS($mobile, '这是一条短信');
                if (!$result['status']) //发送失败时
                {
                    exit(json_encode(array('type' => -1, 'message' => '对不起，发送失败!')));
                }
            }

            exit(json_encode(array('type' => 1, 'message' => '恭喜你，测试短信已经发送成功!')));

        }

        $r   = $SMSModel->getSMSSettingList(); //获取短信配置项
        $arr = array();
        foreach ($r as $v) {
            $k       = $v['send_name'];
            $arr[$k] = $v;
        }
        $smsId     = (int) $GLOBALS['config_info']['SMS_API'];
        $client_id = (int) $GLOBALS['config_info']['CLIENT_ID'];
        //获取剩余短信数量
        $SMSLeftTotal = $SMSModel->getSMSLeftNumber();
        $config       = $this->system_config;

        $this->assign('sms_open', $config['SMS_OPEN']);
        $this->assign('sms_mobile', $config['SMS_MOBILE']);
        $this->assign('sms_type', $config['SMS_TYPE']);
        $this->assign('left_total', $SMSLeftTotal);
        $this->assign('sms_config', $arr);
        $this->assign('head_title', '短信设置');
        $this->display();
    }

    function setting()
    {
        if (IS_POST) {
            $post = I('post.');
            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs(
                array(
                    'limit_open'        => $post['limit_open'],
                    'limit_endtime'     => strtotime($post['limit_endtime']),
                    'limit_desc'        => $post['limit_desc'],
                )
            );
            $this->success("保存成功");
        }
        $this->assign('config', $this->system_config);
        $this->display();
    }
    /**
     * 友情链接列表
     * @author 姜伟
     * @return void
     * @todo 从link表取数据
     */
    public function list_link()
    {
        $link     = new LinkModel();
        $linkList = $link->getAllLinkListPage();
        //    echo "<pre>";
        //    print_r($linkList);die;

        if ($linkList && is_array($linkList)) {
            $pagination = array_pop($linkList);
            foreach ($linkList as $key => $val) {
                $linkList[$key]['link_name'] = mbSubStr($val['link_name'], 15);
                $linkList[$key]['link_url']  = mbSubStr($val['link_url'], 30);
            }

            $this->assign('pagination', $pagination);
            $this->assign('link_list', $linkList);
        }

        $this->assign('head_title', '友情链接列表');
        $this->display();
    }

    /**
     * 添加友情链接
     * @author 姜伟
     * @return void
     * @todo 把数据写进tp_link表
     */
    public function add_link()
    {
        $act = $this->_post('act');
        if ($act == 'submit') {
            $linkName     = $this->_post('link_name');
            $linkUrl      = $this->_post('link_url');
            $imgUrl       = $this->_post('img_url');
            $serial       = $this->_post('serial');
            $isUse        = $this->_post('isuse');
            $isBottomShow = $this->_post('is_bottom_show');

            if (!$linkName) {
                $this->error('请输入网站名称！');
            }
            if (!$linkUrl) {
                $this->error('请输入网站地址！');
            } else {
                if (!preg_match('/^http/i', $linkUrl)) {
                    $this->error('请输入带协议的完整URL地址！');
                } elseif (!preg_match(C('URL_PREG'), $linkUrl)) {
                    $this->error('请输入有效的URL地址！');
                }
            }
            if ($serial && !ctype_digit($serial)) {
                $this->error('请输入纯数字的排序号！');
            }
            if (!ctype_digit($isUse) || !ctype_digit($isBottomShow)) {
                $this->error('非法参数！');
            }

            $data = array(
                'is_bottom_show' => $isBottomShow,
                'link_name'      => $linkName,
                'link_url'       => $linkUrl,
                'link_logo'      => $imgUrl,
                'serial'         => $serial,
                'isuse'          => $isUse,
            );

            $link = new LinkModel();
            if ($link->addLink($data)) {
                $this->success('恭喜您，友情链接添加成功！', '/AcpConfig/list_link');
            } else {
                $this->error('对不起，友情链接添加失败，请稍后重试！');
            }
        }

        $this->assign('action_title', '友情链接列表');
        $this->assign('action_src', '/AcpConfig/list_link/mode_id/0');
        $this->assign('head_title', '添加友情链接');
        $this->display();
    }

    /**
     * 修改友情链接
     * @author 姜伟
     * @return void
     * @todo 修改tp_link表数据
     */
    public function edit_link()
    {
        $id   = $this->_get('id');
        $act  = $this->_post('act');
        $link = new LinkModel();

        if ($act == 'submit') {
            $linkName     = $this->_post('link_name');
            $linkUrl      = $this->_post('link_url');
            $imgUrl       = $this->_post('img_url');
            $serial       = $this->_post('serial');
            $isUse        = $this->_post('isuse');
            $isBottomShow = $this->_post('is_bottom_show');

            if (!$linkName) {
                $this->error('请输入网站名称！');
            }
            if (!$linkUrl) {
                $this->error('请输入网站地址！');
            } else {
                if (!preg_match('/^http/i', $linkUrl)) {
                    $this->error('请输入带协议的完整URL地址！');
                } elseif (!preg_match(C('URL_PREG'), $linkUrl)) {
                    $this->error('请输入有效的URL地址！');
                }
            }
            if ($serial && !ctype_digit($serial)) {
                $this->error('请输入纯数字的排序号！');
            }
            if (!ctype_digit($isUse) || !ctype_digit($isBottomShow)) {
                $this->error('非法参数！');
            }

            $data = array(
                'is_bottom_show' => $isBottomShow,
                'link_name'      => $linkName,
                'link_url'       => $linkUrl,
                'link_logo'      => $imgUrl,
                'serial'         => $serial,
                'isuse'          => $isUse,
            );

            if (false !== $link->editLink($id, $data)) {
                $this->success('恭喜您，友情链接修改成功！', '/AcpConfig/list_link');
            } else {
                $this->error('对不起，友情链接修改失败，请稍后重试！');
            }
        }

        if (!$id || !ctype_digit($id)) {
            $this->error('非法参数！', '/AcpConfig/list_link');
        }
        $linkInfo = $link->getLink($id);
        if (!$linkInfo) {
            $this->error('无效ID！', '/AcpConfig/list_link');
        }

        $this->assign('link_info', $linkInfo);

        $this->assign('action_title', '友情链接列表');
        $this->assign('action_src', '/AcpConfig/list_link/mod_id/0');
        $this->assign('head_title', '修改友情链接');
        $this->display();
    }

    /**
     * 删除友情链接
     * @author 姜伟
     * @return void
     * @todo 删除tp_link表数据
     */
    public function del_link()
    {
        $this->display();
    }

    /**
     * 顶部菜单列表
     * @author 姜伟
     * @return void
     * @todo 从tp_menu表列出数据
     */
    public function list_menu()
    {
        //引入语言包
        require_lang();
        global $lang;

        //获得菜单类型
        $config_model = new ConfigBaseModel();
        $menu_list    = $config_model->getMenuList();

        //引入文章分类模型
        $article_model = new ArticleCategoryModel();

        foreach ($menu_list as $temp => $value) {
            $menu_list[$temp]['menu_type'] = $lang['top_menu_type'][$value['menu_type']];

            if ($value['menu_type'] == MENU_TYPE_ARTICLE_CLASS) {
                $menu_list[$temp]['link_id'] = $article_model->getClassField($value['link_id'], 'article_sort_name');
            } else if ($value['menu_type'] == MENU_TYPE_OUT_LINK) {
                $menu_list[$temp]['link_id'] = $value['out_url'];
            }
        }

        $this->assign('top_menu_list', $menu_list);
        $this->assign('head_title', '头部菜单设置');
        $this->display();
    }

    /**
     * 添加顶部菜单
     * @author 姜伟
     * @return void
     * @todo 插入tp_menu表数据
     */
    public function add_menu()
    {
        //保存修改
        $action = $this->_post('action');
        if ($action == 'add') {
            $title       = $this->_post('title');
            $menu_type   = $this->_post('menu_type');
            $link_id     = $this->_post('link_id');
            $limit_total = $this->_post('limit_total');
            $out_url     = $this->_post('out_url');
            $target      = $this->_post('target');
            $serial      = $this->_post('serial');
            $isuse       = $this->_post('isuse');

            //表单验证
            if (!$title) {
                $this->error('请填写菜单显示文字~', '__APP__/AcpConfig/add_menu');
            }
            if (!$menu_type) {
                $this->error('请选择菜单类型~', '__APP__/AcpConfig/add_menu');
            }

            $path_img = '';
            // 上传图片
            if ($upData = uploadImg()) {
                $path_img = $upData['pic_url'];
            }

            $data['title']       = $title;
            $data['menu_type']   = $menu_type;
            $data['link_id']     = $link_id;
            $data['limit_total'] = $limit_total;
            $data['out_url']     = $out_url;
            $data['target']      = $target;
            $data['path_img']    = $path_img;
            $data['serial']      = $serial;
            $data['isuse']       = $isuse;

            $generalArticle = new ConfigBaseModel();
            if ($id = $generalArticle->addMenu($data)) {
                $this->success('恭喜您，菜单项添加成功~', '__APP__/AcpConfig/list_menu');
            } else {
                $this->error('对不起，菜单项添加失败~', '__APP__/AcpConfig/add_menu');
            }
        }

        //获得菜单类型
        $menu_type      = new ConfigBaseModel();
        $menu_type_list = $menu_type->getMenuType();
        $this->assign('menu_type_options', $menu_type_list);

        //获得商品分类
        $item_list_options = array();
        $item_class        = new ClassModel();
        $item_list         = $item_class->getClassList();
        foreach ($item_list as $temp => $value) {
            if (!$value['class_id']) {
                continue;
            }

            $item_list_options[$value['class_id']] = $value['class_name'];
        }

        $this->assign('item_list_options', $item_list_options);

        //获得文章分类
        $article_class        = new ArticleCategoryModel();
        $article_list         = $article_class->getArticleCategoryList();
        $article_list_options = array();
        foreach ($article_list as $temp => $value) {
            if (!$value['article_sort_id']) {
                continue;
            }

            $article_list_options[$value['article_sort_id']] = $value['article_sort_name'];
        }
        $this->assign('article_list_options', $article_list_options);

        //获取归大的serialID
        $max_serial = $menu_type->getMaxMenuSerial();
        $this->assign('max_serial', $max_serial + 1);

        $this->assign('action_title', '头部菜单设置');
        $this->assign('action_src', '/AcpConfig/list_menu/mod_id/0');
        $this->assign('head_title', '添加头部菜单');
        $this->display();
    }

    /**
     * 修改顶部菜单
     * @author 姜伟
     * @return void
     * @todo 修改tp_menu表数据
     */
    public function edit_menu()
    {
        $id     = $this->_request('id');
        $action = $this->_post('action');

        //保存入库
        if ($action == 'edit') {
            $title       = $this->_post('title');
            $menu_type   = $this->_post('menu_type');
            $link_id     = $this->_post('link_id');
            $limit_total = $this->_post('limit_total');
            $out_url     = $this->_post('out_url');
            $target      = $this->_post('target');
            $serial      = $this->_post('serial');
            $isuse       = $this->_post('isuse');

            //表单验证
            if (!$title) {
                $this->error('请填写菜单显示文字~', '__APP__/AcpConfig/add_menu');
            }
            if (!$menu_type) {
                $this->error('请选择菜单类型~', '__APP__/AcpConfig/add_menu');
            }

            // 上传图片
            if ($upData = uploadImg()) {
                $path_img         = $upData['pic_url'];
                $data['path_img'] = $path_img;
            }

            $data['title']       = $title;
            $data['menu_type']   = $menu_type;
            $data['link_id']     = $link_id;
            $data['limit_total'] = $limit_total;
            $data['out_url']     = $out_url;
            $data['target']      = $target;
            $data['serial']      = $serial;
            $data['isuse']       = $isuse;

            $generalArticle = new ConfigBaseModel();
            if ($generalArticle->editMenu($id, $data)) {
                $this->success('恭喜您，菜单项修改成功~', '__APP__/AcpConfig/edit_menu/id/' . $id);
            } else {
                $this->error('对不起，菜单项修改失败~', '__APP__/AcpConfig/edit_menu/id/' . $id);
            }
        }

        //显示详情
        $config_model = new ConfigBaseModel();
        $menu_ary     = $config_model->getMenu($id);
        $this->assign('menu_ary', $menu_ary);

        //获得菜单类型
        $menu_type      = new ConfigBaseModel();
        $menu_type_list = $menu_type->getMenuType();
        $this->assign('menu_type_options', $menu_type_list);

        //获得商品分类
        $item_list_options = array();
        $item_class        = new ClassModel();
        $item_list         = $item_class->getClassList();
        foreach ($item_list as $temp => $value) {
            if (!$value['class_id']) {
                continue;
            }

            $item_list_options[$value['class_id']] = $value['class_name'];
        }

        $this->assign('item_list_options', $item_list_options);

        //获得文章分类
        $article_class        = new ArticleCategoryModel();
        $article_list         = $article_class->getArticleCategoryList();
        $article_list_options = array();
        foreach ($article_list as $temp => $value) {
            if (!$value['article_sort_id']) {
                continue;
            }

            $article_list_options[$value['article_sort_id']] = $value['article_sort_name'];
        }
        $this->assign('article_list_options', $article_list_options);
        $this->assign('action_title', '头部菜单设置');
        $this->assign('action_src', '/AcpConfig/list_menu/mod_id/0');
        $this->assign('head_title', '修改头部菜单');
        $this->display();
    }

    /**
     * 删除顶部菜单
     * @author 姜伟
     * @return void
     * @todo 从tp_menu删除该行数据
     */
    public function del_menu()
    {
        $this->display();
    }

    /**
     * 设置微信在线客服的账号
     * @author 姜伟
     * @return void
     * @todo 把数据写进tp_customer_server_online表
     */
    public function set_weixin_service_account()
    {
        $act                   = $this->_post('act');
        $config                = new ConfigBaseModel();
        $customerServiceOnline = new CustomerServiceOnlineModel();
        if ($act == 'submit') {
            $_post         = $this->_post();
            $onlineDisplay = $_post['online_display'];
            $data          = $_post['data'];
            //    echo "<pre>";
            //    print_r($data);die;

            $totalInsert   = 0; //待插入数
            $totalUpdate   = 0; //待更新数
            $successInsert = 0; //插入成功数
            $successUpdate = 0; //更新成功数
            foreach ($data as $key => $val) {
                foreach ($val as $k => $v) {
                    $v['service_type'] = $key;
                    if (!$v['customer_service_online_id']) {
                        $totalInsert++;
                        if ($customerServiceOnline->addCustomerServiceOnlice($v)) {
                            $successInsert++;
                        }
                    } else {
                        $totalUpdate++;
                        if (false !== $customerServiceOnline->editCustomerServiceOnlice($v['customer_service_online_id'], $v)) {
                            $successUpdate++;
                        }
                    }
                }
            }
            if ($totalInsert == $successInsert && $totalUpdate == $successUpdate) {
                $this->success('恭喜您，客服设置成功！');
            } else {
                $this->error('对不起，客服设置失败，请稍后重试！');
            }
        }
        $customerServiceOnlineList = $customerServiceOnline->getCustomerServiceOnlineList();
        if ($customerServiceOnlineList) {
            foreach ($customerServiceOnlineList as $key => $val) {
                switch ($val['service_type']) {
                    case 1:
                        $customerServiceOnlineListGroup[$val['service_type']][] = $val;
                        break;
                    case 2:
                        $customerServiceOnlineListGroup[$val['service_type']][] = $val;
                        break;
                    case 3:
                        $customerServiceOnlineListGroup[$val['service_type']][] = $val;
                        break;
                }
            }
        } else {
            $customerServiceOnlineListGroup[1][] = array();
            $customerServiceOnlineListGroup[2][] = array();
            $customerServiceOnlineListGroup[3][] = array();
        }
        #echo "<pre>";
        #print_r($customerServiceOnlineListGroup);
        #die;

        $this->assign('customer_service_online_list_group', $customerServiceOnlineListGroup);

        $this->assign('head_title', '在线客服帐号管理');
        $this->display();
    }

    /**
     * 设置在线客服的账号
     * @author 姜伟
     * @return void
     * @todo 把数据写进tp_customer_server_online表
     */
    public function set_service_account()
    {
        $act                   = $this->_post('act');
        $config                = new ConfigBaseModel();
        $customerServiceOnline = new CustomerServiceOnlineModel();
        if ($act == 'submit') {
            $_post         = $this->_post();
            $onlineDisplay = $_post['online_display'];
            $data          = $_post['data'];
            //    echo "<pre>";
            //    print_r($data);die;

            $totalInsert   = 0; //待插入数
            $totalUpdate   = 0; //待更新数
            $successInsert = 0; //插入成功数
            $successUpdate = 0; //更新成功数
            foreach ($data as $key => $val) {
                foreach ($val as $k => $v) {
                    $v['service_type'] = $key;
                    if (!$v['customer_service_online_id']) {
                        $totalInsert++;
                        if ($customerServiceOnline->addCustomerServiceOnlice($v)) {
                            $successInsert++;
                        }
                    } else {
                        $totalUpdate++;
                        if (false !== $customerServiceOnline->editCustomerServiceOnlice($v['customer_service_online_id'], $v)) {
                            $successUpdate++;
                        }
                    }
                }
            }

            if (false === $config->setConfig('online_display', $onlineDisplay)) {
                if ($onlineDisplay == 'block') {
                    $this->error(0, null, '对不起，在线客服启用失败，请稍后重试！');
                } elseif ($onlineDisplay == 'none') {
                    $this->error(0, null, '对不起，在线客服禁用失败，请稍后重试！');
                }
            }
            if ($totalInsert == $successInsert && $totalUpdate == $successUpdate) {
                $this->success('恭喜您，客服设置成功！', '/AcpConfig/set_service_account');
            } else {
                $this->error('对不起，客服设置失败，请稍后重试！');
            }
        }
        $onlineDisplay             = $config->getConfig('online_display');
        $customerServiceOnlineList = $customerServiceOnline->getCustomerServiceOnlineList();
        if ($customerServiceOnlineList) {
            foreach ($customerServiceOnlineList as $key => $val) {
                switch ($val['service_type']) {
                    case 1:
                        $customerServiceOnlineListGroup[$val['service_type']][] = $val;
                        break;
                    case 2:
                        $customerServiceOnlineListGroup[$val['service_type']][] = $val;
                        break;
                }
            }
        } else {
            $customerServiceOnlineListGroup[1][] = array();
            $customerServiceOnlineListGroup[2][] = array();
        }
        //    echo "<pre>";
        //    print_r($customerServiceOnlineListGroup);die;

        $this->assign('online_display', $onlineDisplay);
        $this->assign('customer_service_online_list_group', $customerServiceOnlineListGroup);

        $this->assign('head_title', '在线客服帐号管理');
        $this->display();
    }

    /**
     * @access public
     * @todo 短信充值
     * @author zhoutao@360shop.cc zhoutao0928@sina.com
     * @return void
     */

    public function sms_pay()
    {
        $act                 = $this->_post('act');
        $az                  = new AzModel();
        $az_id               = intval($GLOBALS['install_info']['az_id']);
        $user_left_sms_total = $az->__call_api('get_sms_total_by_az_id', array('az_id' => $az_id)); //客户当前还剩余的可发短信总数
        $this->assign('left_sms_total', $user_left_sms_total);

        if ($act == 'pay') {
            //充值金额
            $total_fee = $this->_post('total_fee');
            if (!$total_fee) {
                $this->error('对不起，请填写充值金额');
            }
            $total_fee = floatval($total_fee);
            if ($total_fee < 0.01) {
                $this->error('对不起，充值金额不得小于0.01元');
            }

            //支付方式
            $payway_id = intval($this->_post('payway_id'));
            if (!$payway_id) {
                $this->error('对不起，请选择支付方式');
            }

            $sms_total = 0; //要充值的总条数
            switch ($total_fee) //这里当前写死了（2014-05-04）
            {
                case 50:
                    $sms_total = 500;
                    break;
                case 100:
                    $sms_total = 1200;
                    break;
                case 200:
                    $sms_total = 2500;
                    break;
                case 500:
                    $sms_total = 7800;
                    break;
                default:
                    $sms_total = 0;
            }

            $smsId     = (int) $GLOBALS['config_info']['SMS_API']; //短信接口ID（az库中tp_sms表）
            $param_ary = array(
                'id' => $smsId,
            );
            $result = $az->__call_api('get_sms_total', $param_ary); //系统当前短信剩余的总条数(az系统数据库中的短信池中的剩余总数)
            if ($sms_total > $result) //此情况出现的几率非常低（系统余剩的短信量已经不够客户本次充值）
            {
                $this->error('对不起，当前不能充值，请您速联系"360shop客服人员"', get_url());
            }

            //获取支付方式的pay_tag
            $payway_obj  = new PaywayModel();
            $payway_info = $payway_obj->getPaywayInfoById($payway_id);
            $pay_tag     = $payway_info['pay_tag'];

            if ($pay_tag == 'alipay') {
                $payment_obj      = new AlipayModel();
                $link             = $payment_obj->pay_code(0, $total_fee, 0, true);
                $qr_pay_mode_link = $payment_obj->pay_code(0, $total_fee, 1, true);
                $this->assign('qr_pay_mode_link', $qr_pay_mode_link);
                $this->assign('links', $link);
            } elseif ($pay_tag == 'chinabank') {
                $payment_obj = new ChinabankModel();
                $parameter   = $payment_obj->pay_code(0, $total_fee, 0, true);
                $this->assign('parameter', $parameter);
            }

            $this->assign('amount', $total_fee);
            $this->assign('pay_tag', $pay_tag);

            #//调用支付宝模型的pay_code获取支付链接
            #$alipay_obj = new AlipayModel();
            #$link = $alipay_obj->pay_code(0, $total_fee);
            #redirect($link);
        }
        /*if ($act == 'voucher')
        {
        //充值金额
        $total_fee = $this->_post('total_fee');
        if (!$total_fee)
        {
        $this->error('对不起，请填写充值金额');
        }
        $total_fee = floatval($total_fee);
        if ($total_fee < 0.01)
        {
        $this->error('对不起，充值金额不得小于0.01元');
        }

        //调用支付宝模型的pay_code获取支付链接
        $alipay_obj = new AlipayModel();
        $link = $alipay_obj->pay_code(0, $total_fee, 1);
        redirect($link);
        }*/

        //获取支付方式列表
        $payway_obj  = new PaywayModel();
        $payway_list = $payway_obj->getPaywayList();
        $this->assign('payway_list', $payway_list); #myprint($payway_list);

        $payway_info['pay_desc'] = html_entity_decode($payway_info['pay_desc']);
        $this->assign('payway_info', $payway_info);
        $this->assign('pay_tag', $pay_tag);

        $this->assign('head_title', '短信充值');
        $this->display();

        //         require_once('Common/func_sms.php');    //测试用代码（在支付成功后执行的操作）
        //         myprint(sms_recharge(50));
    }

    /**
     * 支付宝付款成功后的回调页面
     * @author zt
     * @param void
     * @return void
     * @todo 调用支付宝支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
     */
    public function alipay_response()
    {
        $alipay_obj   = new AlipayModel();
        $return_state = $alipay_obj->pay_response();
        if ($return_state == 2) {
            $total_fee = $_GET['total_fee']; //充值的总额
            require_once 'Common/func_sms.php';
            $sms_total = sms_recharge($total_fee); //为客户充值的短信数量

            //短信充值日志的记录
            $account = new AccountModel();
            $account->addSMSPayLog($_GET['out_trade_no'], 1, $total_fee, $sms_total);

            $this->success('恭喜您，充值成功', '/AcpConfig/sms_config/mod_id/0');
        } else {
            $this->error('对不起，非法访问', U('/'));
        }
    }

    /**
     * 网银在线付款成功后的回调页面
     * @author 姜伟
     * @param void
     * @return void
     * @todo 调用网银在线支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
     */
    public function chinabank_response()
    {
        $chinabank_obj = new ChinabankModel();
        $return_state  = $chinabank_obj->pay_response();
        if ($return_state == 2) {
            $this->success('恭喜您，充值成功', '/AcpConfig/sms_config/mod_id/0');
        } else {
            $this->error('对不起，非法访问', U('/'));
        }
    }

    /**
     * 设置前台轮播图片
     * @author 姜伟
     * @return void
     * @todo 设置前台轮播图片
     */
    public function set_cust_flash()
    {
        $act = $this->_post('act');
        if ($act == 'add') {
            if (count($_POST['pic']) == 0) {
                $this->error('抱歉，请至少上传一张图片');
            }

            $pic_str         = implode(';', $_POST['pic']);
            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfig('cust_flash_list', $pic_str);
            $this->success('恭喜您，设置成功！');
            #echo $pic_str . "<br>";
            #echo strlen($pic_str);
            #echo "<pre>";
            #print_r($_POST);
            #die;
        }

        $cust_flash_list = $GLOBALS['config_info']['CUST_FLASH_LIST'];
        $cust_flash_list = explode(';', $cust_flash_list);
        $this->assign('cust_flash_list', $cust_flash_list);
        $this->display();
    }

    /**
     * 设置关注时推送的图文
     * @author 姜伟
     * @return void
     * @todo 设置关注时推送的图文
     */
    public function subscribe_set()
    {
        $act = $this->_post('act');
        if ($act == 'set') //提交保存时
        {
            $subscribe_title   = $this->_post('subscribe_title');
            $subscribe_link    = $this->_post('subscribe_link');
            $subscribe_pic     = $this->_post('subscribe_pic');
            $subscribe_content = $this->_post('subscribe_content');

            if (!$subscribe_title) {
                $this->error('对不起，请填写图文标题', get_url());
            }

            if (!$subscribe_link) {
                $this->error('对不起，请填写图文链接', get_url());
            }

            if (!$subscribe_pic) {
                $this->error('对不起，请填写图文缩略图', get_url());
            }

            if (!$subscribe_content) {
                $this->error('对不起，请填写图文简述', get_url());
            }

            $data = array(
                'subscribe_title'   => $subscribe_title,
                'subscribe_link'    => $subscribe_link,
                'subscribe_pic'     => $subscribe_pic,
                'subscribe_content' => $subscribe_content,
            );

            $ConfigBaseModel = new ConfigBaseModel();
            $ConfigBaseModel->setConfigs($data);
            $this->success('设置成功!');
        }

        $config = $this->system_config;

        $this->assign('subscribe_pic_data', array(
            'name'  => 'subscribe_pic',
            'url'   => $config['SUBSCRIBE_PIC'],
            'title' => '缩略图',
        ));

        $this->assign('head_title', '新用户关注推送图文设置');
        $this->assign('config', $this->system_config);
        $this->display();
    }

    /**
     * 修改密码
     * @param void
     * @return void
     * @todo
     */
    public function set_password()
    {
        $act = $this->_post('act');
        if ($act == 'save') {
            $user_id      = intval(session('user_id'));
            $old_password = $this->_post('old_password');
            $this->assign('old_password', $old_password);
            $new_password = $this->_post('new_password');
            $this->assign('new_password', $new_password);
            $confirm_password = $this->_post('confirm_password');
            $this->assign('confirm_password', $confirm_password);
            if (!$old_password) {
                $this->error('请输入旧密码');
            }

            if (!$new_password) {
                $this->error('请输入新密码');
            }

            if (strlen($new_password) < 6) {
                $this->error('密码长度不得小于6位');
            }

            if ($confirm_password != $new_password) {
                $this->error('验证密码和新密码必须一致');
            }

            //验证旧密码
            $user_obj  = new UserModel($user_id);
            $user_info = $user_obj->getUserInfo('user_id', 'user_id = ' . $user_id . ' AND password = "' . MD5($old_password) . '"');
            if (!$user_info) {
                $this->error('旧密码不正确');
            }

            //修改密码
            $arr = array(
                'password' => MD5($new_password),
            );
            $user_obj->setUserInfo($arr);
            $success = $user_obj->saveUserInfo();
            if ($success) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }

        $this->assign('head_title', '修改密码');
        $this->display(APP_PATH . 'Tpl' . DS . MODULE_NAME . DS . ACTION_NAME . '.html');
    }

    /**
     * 满减优惠
     * @author wzg
     */
    public function shopping_fare()
    {
        $act        = I('act', '', 'strip_tags');
        $config_obj = new ConfigBaseModel();
        if ('save' == $act) {
            $base_fare = I('base_fare', 0.00, 'float');
            $full_cost = I('full_cost', 0.00, 'float');

            $arr = array(
                'base_fare' => $base_fare,
                'full_cost' => $full_cost,
            );
            $success = $config_obj->setConfigs($arr);

            if ($success) {
                $this->success('设置成功');
            }
            $this->error('设置失败');
        }
        $this->assign('head_title', '运费设置');
        $this->display();
    }

    //test up_token
    public function test()
    {
        $this->assign();
        $this->display();
    }

    /**
     * 菜单和界面设置
     */
    public function menu_set()
    {
		//获取自定义菜单
        $appid = C('APPID');
        $secret = C('APPSECRET');
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        #$custom_info = json_decode('{"button":[{"type":"view","name":"\u5546\u57ce","url":"http:\/\/b2c.beyondin.com"},{"type":"view","name":"\u4e2a\u4eba","url":"http:\/\/b2c.beyondin.com\/FrontUser\/personal_center"},{"name":"\u5e2e\u52a9","sub_button":[{"type":"click","name":"\u5ba2\u670d","key":"customer_service"},{"type":"view","name":"\u5e2e\u52a9\u4e2d\u5fc3","url":"http:\/\/b2c.beyondin.com\/FrontHelp\/help_list"},{"type":"view","name":"\u5173\u4e8e","url":"http:\/\/b2c.beyondin.com\/FrontHelp\/about"},{"type":"view","name":"\u4e13\u5c5e\u63a8\u5e7f\u4e8c\u7ef4\u7801","url":"http:\/\/b2c.beyondin.com\/FrontUser\/my_qr_code"}]}]}', true);
        //获取当前数据库中的设置
        $wx_menu_obj = M('wx_menu');
        $custom_info = $wx_menu_obj->where()->getField('wx_menu');
        $custom_info = json_decode(htmlspecialchars_decode($custom_info), true);

        //判断用户是否设置过菜单,没有就获取默认的菜单
		if (!$custom_info && $menu_list)
		{
			$custom_info = $menu_list['menu'];
		}
		elseif (!$menu_list)
		{
			$custom_info = json_decode('{"button":[{"type":"view","name":"\u5546\u57ce","url":"http:\/\/b2c.beyondin.com"},{"type":"view","name":"\u4e2a\u4eba","url":"http:\/\/b2c.beyondin.com\/FrontUser\/personal_center"},{"name":"\u5e2e\u52a9","sub_button":[{"type":"click","name":"\u5ba2\u670d","key":"customer_service"},{"type":"view","name":"\u5e2e\u52a9\u4e2d\u5fc3","url":"http:\/\/b2c.beyondin.com\/FrontHelp\/help_list"},{"type":"view","name":"\u5173\u4e8e","url":"http:\/\/b2c.beyondin.com\/FrontHelp\/about"},{"type":"view","name":"\u4e13\u5c5e\u63a8\u5e7f\u4e8c\u7ef4\u7801","url":"http:\/\/b2c.beyondin.com\/FrontUser\/my_qr_code"}]}]}', true);
		}
        $this->assign('custom_info', $custom_info['button']);
        $this->assign('menu_length', count($custom_info));
        #echo "<pre>";
        #print_r($custom_info);
        #die;

        $select_type = array(
            'view'     => '跳转网址',
            // 'media_id' => '推送图文',
            // 'click'    => '自动回复',
            // '4'        => '自定义页面',
        );
        $this->assign('select_type', $select_type);

        //TITLE中的页面标题
        $this->assign('head_title', '菜单和界面设置');
        $this->display();
    }

    /**
     * 保存菜单设置
     */
    public function save_platform()
    {
        $az_id         = $GLOBALS['install_info']['az_id'];
        $platform_info = htmlspecialchars_decode($this->_post('platform_info'));

        //因为tp框架接收json会把数据过滤了所以要把json数据反过滤一下
        $platform_info = html_entity_decode($platform_info);
        $platform_info = str_replace('\\', '', $platform_info);
        $platform_arr  = json_decode($platform_info, true);
        //log_file($platform_arr, 'aaa', true);
		$menu = array();
		foreach ($platform_arr AS $k => $v)
		{
			$key = $v['type_value'] == 'view' ? 'url' : 'key';
			$arr = array(
				'type'	=> $v['type_value'],
				$key	=> $v['value'],
			);
			//无子级菜单
			#dump($v['type']);
			if ($v['type'] == 1)
			{
				$arr['name'] = $v['name'];
				$menu[$v['menu_serial'] - 1] = $arr;
				#dump($menu);
			}
			else
			{
				#dump($v['menu_serial'] - 1);
				$arr['name'] = $v['type_name'];
				//有子级菜单
				$menu[$v['menu_serial'] - 1]['name'] = $v['name'];
				$menu[$v['menu_serial'] - 1]['sub_button'][] = $arr;
				dump($menu);
			}
		}
        //更新到数据库
		$config_obj = new ConfigBaseModel();
		$menu = array(
			'button'	=> $menu
		);
		#dump($menu);
		#die;
        $wx_menu_obj = M('wx_menu');
		$arr = array(
			'wx_menu'	=> json_encode($menu)
		);
		$wx_menu_obj->where('1')->save($arr);
		#echo $wx_menu_obj->getLastSql();
		#die;
		exit(json_encode(array('code' => '200', 'msg' => '恭喜您,菜单保存成功!')));
    }

    /**
     * 调用支付宝接口创建菜单
     */
    public function add_platform()
    {
        //获取当前数据库中的设置
        $wx_menu_obj = M('wx_menu');
        $custom_info = $wx_menu_obj->where()->getField('wx_menu');
        $custom_info = json_decode(htmlspecialchars_decode($custom_info), true);

        $appid = C('APPID');
        $secret = C('APPSECRET');
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $result = $weixin_obj->menu_create($custom_info);

		$arr = array(
			'code'	=> $result['errcode'] == 0 ? 200 : -1,
			'msg'	=> $result['errcode'] == 0 ? '发布成功，请在微信公众号界面查看结果（可能会有几分钟的缓存，若着急查看，可重复"取消关注->重新关注"操作查看）' : -1,
		);
		exit(json_encode($arr));
		dump($result);
    }
	
	//前端页面添加2级菜单的默认html代码输出
	public function js_get_tow()
	{
		$id = $this->_post('id');

		if($id)
		{
$str = <<< ABC
<li class="j-nav-erji" id="$id">
	<div class="ali-meun-c-icon"><i class="move"></i><i class="del"></i></div>
	<div class="ali-meun-c-name j-meunparent">
		<div class="ali-meun-c-name-text j-alltext" style="display:none;"></div>
        <div class="ali-meun-c-name-edit j-alledit" style="display:block;">
            <input name="ali-meun-name-erji" type="text" value=""  onKeyUp="CheckLength(this,36)"/>
        </div>
	</div>
	<div class="ali-meun-c-type j-meunparent">
		<div class="ali-meun-c-type-text j-alltext" style="display:none;">跳转网址</div>
		<div class="ali-meun-c-type-edit j-alledit" style="display:block;">
			<i></i>
			<span>跳转网址</span>
			<select class="ali-m-se-type">
                <option value="1" selected="selected">跳转网址</option>
            </select>
        </div>
	</div>
<div class="ali-meun-c-xsnr j-meunparent">
	<div class="ali-meun-c-xsnr-text j-alltext" style="display: none;"></div>
	<div class="ali-meun-c-xsnr-edit j-alledit" style="display: block;">
	<div class="textp-wap j-xsnrspan clearfix" style="display:block;">
		<span class=""><input type="text" onchange="change_value(this);" class="text" name="val" value=""></span>
	</div>
	<div class="textp-tel" style="display:none;"><input name="ali-m-input-tel" type="text" value=""></div>
	<div class="textp-auto j-xsnrspan clearfix" style="display:none;">
		<span class="text">选择图文信息</span>
	    <span class="replace">选择</span>
	</div>
	<div class="textp-custom j-xsnrspan clearfix" style="display:none;">
		<span class="text">选择自定义页面</span>
	</div>
	</div>
	</div>
	<div style="clear:both;"></div>
	<input type="hidden" class="ali-meun-lihidden" ejid="" ejname="" ejtype="view" ejobjname="" ejclassid="" ejdataid="" ejedit="" ejpagename="" ejplatid="" pagename="" thisplanid="" name="" value=""/>
</li>
ABC;

			exit(json_encode(array('code' => '200', 'msg' => '恭喜您!请求成功!' , 'data' => $str)));         
		}
		else 
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起!参数不正确1')));         
		}
	}
    //微信关键字回复
    public function auto_reply_list()
    {
        $wx_reply_obj = D('WxKwReply');
        import('ORG.Util.Pagelist');
        $where = '1';
        $count = $wx_reply_obj->getWxKwReplyNum($where);
        $Page  = new Pagelist($count, C('PER_PAGE_NUM'));
        $wx_reply_obj->setStart($Page->firstRow);
        $wx_reply_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);
        $reply_list = $wx_reply_obj->getWxKwReplyList('', $where);
        foreach ($reply_list as $k => $v) {
            switch ($v['reply_type']) {
                case 'text':
                    $reply_list[$k]['reply_type'] = '文本';
                    break;
                case 'news':
                    $reply_list[$k]['reply_type'] = '图文';
                    break;
                case 'image':
                    $reply_list[$k]['reply_type'] = '图片';
                    break;
                default:
                    break;
            }
        }
        $this->assign('reply_list', $reply_list);
        $this->assign('head_title', '公众号自动回复设置');
        $this->display();
    }

    //增加关键字回复
    public function add_auto_reply()
    {

        $act = I('act');
        if ($act == 'add') {
            $reply_type = I('reply_type');
            $rule_name  = I('rule_name');
            $keyword    = I('keyword');
            if (!$reply_type) {
                $this->error('请选择回复类型');
            }
            if (!$rule_name) {
                $this->error('请填写规则名称');
            }
            if (!$keyword) {
                $this->error('请填写关键字');
            }
            $arr = array(
                'reply_type' => $reply_type,
                'rule_name'  => $rule_name,
                'keyword'    => $keyword,
            );
            $wx_reply_obj = D('WxKwReply');
            if ($reply_type == 'news') {
                //图文消息
                $news_title = I('news_title');
                $news_link  = I('news_link');
                $img_url    = I('img_url');
                $text_value = $this->_post('text_value');
                if (!$news_title) {
                    $this->error('请填写图文标题');
                }
                if (!$news_link) {
                    $this->error('请填写图文链接');
                }
                if (!$img_url) {
                    $this->error('请上传缩略图');
                }
                if (!$text_value) {
                    $this->error('请填写图文简述');
                }
                $arr['news_title'] = $news_title;
                $arr['news_link']  = $news_link;
                $arr['img_url']    = $img_url;
                $arr['text_value'] = $text_value;
            } elseif ($reply_type == 'text') {
                //文本消息
                $text_value = $this->_post('text_value');
                if (!$text_value) {
                    $this->error('请填写文本内容');
                }
                $arr['text_value'] = $text_value;
            } elseif ($reply_type == 'image') {
                //图片消息
                $img_url = I('img_url');
                if (!$img_url) {
                    $this->error('请上传缩略图');
                }
                $arr['img_url'] = $img_url;
            } else {
                $this->error('回复类型不存在');
            }
            if ($wx_reply_obj->addWxKwReply($arr)) {
                $this->success('添加成功');
            }
            $this->error('添加失败');
        }

        $this->assign('head_title', '增加自动回复');
        $this->display();
    }

    //修改关键字回复
    public function edit_auto_reply()
    {
        $id = I('id');
        if (!is_numeric($id)) {
            $this->error('非法访问');
        }
        $wx_reply_obj = D('WxKwReply');
        $reply_info   = $wx_reply_obj->getWxKwReplyInfo('wx_kw_reply_id =' . $id);
        if (!$reply_info) {
            $this->error('对不起，回复不存在');
        }

        $act = I('act');
        if ($act == 'add') {
            $reply_type = I('reply_type');
            $rule_name  = I('rule_name');
            $keyword    = I('keyword');
            if (!$reply_type) {
                $this->error('请选择回复类型');
            }
            if (!$rule_name) {
                $this->error('请填写规则名称');
            }
            if (!$keyword) {
                $this->error('请填写关键字');
            }
            $arr = array(
                'reply_type' => $reply_type,
                'rule_name'  => $rule_name,
                'keyword'    => $keyword,
            );
            $wx_reply_obj = D('WxKwReply');
            if ($reply_type == 'news') {
                //图文消息
                $news_title = I('news_title');
                $news_link  = I('news_link');
                $img_url    = I('img_url');
                $text_value = $this->_post('text_value');
                if (!$news_title) {
                    $this->error('请填写图文标题');
                }
                if (!$news_link) {
                    $this->error('请填写图文链接');
                }
                if (!$img_url) {
                    $this->error('请上传缩略图');
                }
                if (!$text_value) {
                    $this->error('请填写图文简述');
                }
                $arr['news_title'] = $news_title;
                $arr['news_link']  = $news_link;
                $arr['img_url']    = $img_url;
                $arr['text_value'] = $text_value;
            } elseif ($reply_type == 'text') {
                //文本消息
                $text_value = $this->_post('text_value');
                if (!$text_value) {
                    $this->error('请填写文本内容');
                }
                $arr['text_value'] = $text_value;
            } elseif ($reply_type == 'image') {
                //图片消息
                $img_url = I('img_url');
                if (!$img_url) {
                    $this->error('请上传缩略图');
                }
                $arr['img_url'] = $img_url;
            } else {
                $this->error('回复类型不存在');
            }
            //先删除再添加
            if ($wx_reply_obj->delWxKwReply($id)) {
                if ($n_id = $wx_reply_obj->addWxKwReply($arr)) {
                    $this->success('修改成功', '/AcpConfig/edit_auto_reply/id/' . $n_id . '/mod_id/0');
                }
                $this->error('修改失败');
            }

            $this->error('修改失败');
        }

        $this->assign('img_url_data', array(
            'name'  => 'img_url',
            'title' => '缩略图',
            'url'   => $reply_info['img_url'],
        ));

        $this->assign('reply_id', $id);
        $this->assign('reply_info', $reply_info);
        $this->assign('head_title', '修改自动回复');
        $this->display();
    }

    //删除关键字回复
    public function delete_reply()
    {
        if (IS_AJAX && IS_POST) {
            $reply_id     = I('reply_id');
            $wx_reply_obj = D('WxKwReply');
            $where        = 'wx_kw_reply_id =' . $reply_id;
            $reply_num    = $wx_reply_obj->getWxKwReplyNum($where);
            if (!$reply_num) {
                $this->ajaxReturn('failure');
            }
            if ($wx_reply_obj->delWxKwReply($reply_id)) {
                $this->ajaxReturn('success');
            }
            $this->ajaxReturn('failure');
        } else {
            $this->ajaxReturn('failure');
        }
    }

    //批量删除关键字回复
    public function batch_delete_reply()
    {
        if (IS_AJAX && IS_POST) {
            $reply_ids = $this->_post('reply_ids');
            if ($reply_ids) {
                $reply_id_ary = explode(',', $reply_ids);
                $success_num  = 0;
                $wx_reply_obj = D('WxKwReply');
                foreach ($reply_id_ary as $reply_id) {
                    $num = $wx_reply_obj->getWxKwReplyNum('wx_kw_reply_id = ' . $reply_id);
                    if (!$num) {
                        continue;
                    }

                    $success_num += $wx_reply_obj->delWxKwReply($reply_id);
                }
                echo $success_num ? 'success' : 'failure';
                exit;
            }

            $this->ajaxReturn('failure');
        } else {
            $this->ajaxReturn('failure');
        }
    }

    //微信群发消息设置
    public function set_mass_msg()
    {
        if (IS_AJAX && IS_POST) {
            $msg_type = I('msg_type');
            $media_id = I('media_id');
            $group    = I('group');
            if (!$msg_type || !$media_id || $group === false) {
                $this->ajaxReturn('failure');
            }
            $arr = array(
                'msg_type' => $msg_type,
                'media_id' => $media_id,
                'group'    => $group,
            );
            $mass_msg_obj = D('WxMassMsg');
            if ($mass_msg_obj->addWxMassMsg($arr)) {
                $this->ajaxReturn('success');
            }
            $this->ajaxReturn('failure');
        }

        Vendor('Wxin.WeiXin');
        $appid        = C('APPID');
        $secret       = C('APPSECRET');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj   = new WxApi($access_token);
        $groups       = $weixin_obj->groups_get();
        $this->assign('groups', $groups['groups']);
        $mass_msg_obj = D('WxMassMsg');
        $mass_msg     = $mass_msg_obj->getWxMassMsg();
        $this->assign('mass_msg', $mass_msg);
        $page = I('page', 0);
        $this->assign('item_count', C('PER_PAGE_NUM'));
        $this->assign('page', $page);
        $this->assign('head_title', '微信群发消息设置');
        $this->display();
    }

    //获取微信公众平里的素材
    public function get_wx_material()
    {
        $type = I('type');
        if (!$type || $type == 'text') {
            $this->ajaxReturn('failure');
        }
        $page   = I('page', 0);
        $page   = $page - 1;
        $page   = $page < 0 ? 0 : $page;
        $offset = $page * 2;
        $offset = $offset < 0 ? 0 : $offset;
        $appid  = C('APPID');
        $secret = C('APPSECRET');
        Vendor('Wxin.WeiXin');
        $wxapi_obj     = new WxApi();
        $material_list = $wxapi_obj->get_material_list($appid, $secret, $type, $offset);
        $this->ajaxReturn($material_list);
    }

    public function get_thumb()
    {
        $url = I('url');
        $img = file_get_contents($url);
        echo $img;
    }

    //快速生成模型文件
    public function create_model()
    {
        if(IS_POST)
        {
            $table_name = I('post.table_name'); //表名用下划线隔开
            if(!$table_name)
            {
                $this->error('请填写表名!');
            }

            $model_name = implode(array_map('ucfirst', explode('_', $table_name)));
            $py_key     = I('post.py_key') ? I('post.py_key') : $table_name .'_id';
            $remark     = I('post.remark');
            if(!$remark)
            {
                $this->error('请填写中文备注!');
            }

            $content = file_get_contents(APP_PATH.'/Public/template/model.template');
            $content = str_replace("__REMARK__",$remark,$content);
            $content = str_replace("__MODEL_NAME__",$model_name,$content);
            $content = str_replace("__PY_KEY__",$py_key,$content);
            $content = str_replace("__TABLE_NAME__",$table_name,$content);

            $save_path = APP_PATH.'/Lib/Model/'.$model_name.'Model.class.php';
            if(file_exists($save_path))
            {
                $this->error('该模型文件已存在！');
            }
            file_put_contents($save_path, $content);
            $this->success($model_name.'Model.class.php文件添加成功','/AcpConfig/create_model');

        }
        $this->assign('head_title','生成模型方法');
        $this->display();
    }


    public function home_page(){
        //代理总充值，总提现，（代理）总充值利润；
        $account_obj = new AccountModel();
        $total_recharge = $account_obj->where('change_type ='.AccountModel::RECHARGE.' AND operater != 1')->getField('sum(amount_in)');
        $deposit_obj = new DepositApplyModel();
        $total_deposit = $deposit_obj->where('state = 1')->getField('sum(money)');

        $recharge_out = $account_obj->where('change_type =' . AccountModel::RECHARGEOUT . ' AND user_id != 1')->getField('sum(amount_out)');
        $total_profit = $total_recharge - $recharge_out;

        $total_recharge = feeHandle($total_recharge);
        $total_deposit = feeHandle($total_deposit);
        $total_profit = feeHandle($total_profit);
        //今日注册 今日登陆
        $today = strtotime(date('Y-m-d'),time());
        $user_obj = new UserModel();
        $today_reg = $user_obj->where('reg_time >= '.$today)->count();
        $login_log_obj = new LoginLogModel();
        $today_login = $login_log_obj->where('login_time >= '.$today)->group('user_id')->count();

        $this->assign('today_reg', $today_reg?:0);
        $this->assign('today_login', $today_login?:0);
        $this->assign('total_recharge', $total_recharge?:0);
        $this->assign('total_deposit', $total_deposit?:0);
        $this->assign('total_profit', $total_profit?:0);
        $this->assign('head_title', '首页');
        $this->display();
    }
}
