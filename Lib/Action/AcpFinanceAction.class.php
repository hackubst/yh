<?php

/**
 * 财务管理类
 *
 *
 */
class AcpFinanceAction extends AcpAction
{

    /**
     * 构造函数
     * @author 姜伟
     * @return void
     * @todo
     */
    public function AcpFinanceAction()
    {
        parent::_initialize();
    }

    //代理商余额变动明细
    public function get_agent_account_log()
    {
        $this->get_account_log('4', '代理余额变动明细');
    }

    //代理商充值记录
    public function get_recharge_log()
    {
        $start_time = $this->_request('start_time', '');
        $end_time = $this->_request('end_time', '');
        $user_id = $this->_request('user_id', 0);
        $change_type = $this->_request('change_type', 0);

        $alipay_account_name = $this->_request('alipay_account_name', '');
        $username = $this->_request('username', '');
        $mobile = $this->_request('mobile', '');
        $id = $this->_request('id', '');
        $agent_id = $this->_request('agent_id','');
        $opt = 3;
        $user_obj = new UserModel();
        if ($opt) {
            $user_ids = $user_obj->where('role_type =' . $opt)->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }
        $where['change_type'] = array('EQ',9);
        $where['operater'] = array('NEQ',1);
        //用户姓名筛选
        if ($alipay_account_name) {

            $user_ids = $user_obj->where('realname LIKE "%' . $alipay_account_name . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }

        }

        //用户账号筛选
        if ($username) {
            $user_ids = $user_obj->where('nickname LIKE "%' . $username . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        //手机号筛选
        if ($mobile) {
            $user_ids = $user_obj->where('mobile LIKE "%' . $mobile . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        //ID筛选
        if ($id) {
            $user_ids = $user_obj->where('id LIKE "%' . $id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        if($agent_id)
        {
            $user_ids = $user_obj->where('id LIKE "%' . $agent_id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['operater'] = array('in', $user_ids);
            } else {
                $where['operater'] = array('EQ', -1);
            }
        }

        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }

        //通过时间区间筛选数据
        if ($start_time && $end_time)
            $where['addtime'] = array(array('GT', strtotime($start_time)), array('LT', strtotime($end_time)), 'AND');
        //按用户id筛选
        if ($user_id)
            $where['user_id'] = $user_id;

        if ($change_type) {
            $where['change_type'] = array('EQ', $change_type);
        }

        //获取订单列表
        import('ORG.Util.Pagelist');// 导入分页类
        $AccountModel = new AccountModel();
        $count = $AccountModel->getAccountNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();
        $fields = 'user_id,proof,addtime,change_type,amount_in,amount_out,amount_after_pay,remark,account_id,state,bank_money_after,operater';
        $AccountModel->setStart($Page->firstRow);
        $AccountModel->setLimit(C('PER_PAGE_NUM'));
        $changed_list = $AccountModel->getAccountList($fields, $where, 'addtime desc,account_id desc');
        $UserModel = new UserModel();
        foreach ($changed_list as $key => $value) {
            $user_info = $UserModel->getParamUserInfo('user_id = ' . $value['user_id'], 'nickname,realname,id');

            $agent_user_info = $UserModel->getParamUserInfo('user_id = ' . $value['operater'], 'nickname,realname,id');
            $changed_list[$key]['nickname'] = $agent_user_info['realname'];
            $changed_list[$key]['id'] = $user_info['id'];
            $changed_list[$key]['agent_id'] = $agent_user_info['id'];

            $changed_list[$key]['change_type_name'] = change_change_type($value['change_type']);

            $changed_list[$key]['amount_in'] = feeHandle($value['amount_in']);
            $changed_list[$key]['amount_out'] = feeHandle($value['amount_out']);
            $changed_list[$key]['amount_after_pay'] = feeHandle($value['amount_after_pay']);
            $changed_list[$key]['bank_money_after'] = feeHandle($value['bank_money_after']);


        }
        // dump($changed_list);

        $change_type_list = $AccountModel->getNewChangeTypeList();

        $this->assign('change_type_list', $change_type_list);
        $this->assign('changed_list', $changed_list);
        $this->assign('page', $show);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('alipay_account_name', $alipay_account_name);
        $this->assign('username', $username);
        $this->assign('mobile', $mobile);
        $this->assign('change_type', $change_type);
        $this->assign('id', $id);
        $this->assign('agent_id', $agent_id);
        $this->assign('opt',$opt);
        $this->assign('action_title', '用户列表');
        $this->assign('action_src', '/AcpUser/get_all_user_list/mod_id/0');

        $this->assign('head_title', '代理商充值记录');
        $this->assign('way', 1);

        $this->display('get_recharge_log');
    }

    /**
     * 余额变动明细公共方法
     * @author 姜伟
     * @return void
     * @todo 取account表中该用户的所有数据
     */
    public function get_account_log($opt = '3', $head_title = '')
    {
        $start_time = $this->_request('start_time', '');
        $end_time = $this->_request('end_time', '');
        $user_id = $this->_request('user_id', 0);
        $change_type = $this->_request('change_type', 0);
        $change_type = 13;

        $alipay_account_name = $this->_request('alipay_account_name', '');
        $username = $this->_request('username', '');
        $mobile = $this->_request('mobile', '');
        $id = $this->_request('id', '');

        $user_obj = new UserModel();
        if ($opt) {
            $user_ids = $user_obj->where('role_type =' . $opt)->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }
        //用户姓名筛选
        if ($alipay_account_name) {

            $user_ids = $user_obj->where('realname LIKE "%' . $alipay_account_name . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }

        }

        //用户账号筛选
        if ($username) {
            $user_ids = $user_obj->where('nickname LIKE "%' . $username . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        //手机号筛选
        if ($mobile) {
            $user_ids = $user_obj->where('mobile LIKE "%' . $mobile . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        //ID筛选
        if ($id) {
            $user_ids = $user_obj->where('id LIKE "%' . $id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where['user_id'] = array('in', $user_ids);
            } else {
                $where['user_id'] = array('EQ', -1);
            }
        }

        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }

        //通过时间区间筛选数据
        if ($start_time && $end_time)
            $where['addtime'] = array(array('GT', strtotime($start_time)), array('LT', strtotime($end_time)), 'AND');
        //按用户id筛选
        if ($user_id)
            $where['user_id'] = $user_id;

        if ($change_type) {
            $where['change_type'] = array('EQ', $change_type);
        }

        //获取订单列表
        import('ORG.Util.Pagelist');// 导入分页类
        $AccountModel = new AccountModel();
        $count = $AccountModel->getAccountNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();
        $fields = 'user_id,proof,addtime,change_type,amount_in,amount_out,amount_after_pay,remark,account_id,state,bank_money_after';
        $AccountModel->setStart($Page->firstRow);
        $AccountModel->setLimit(C('PER_PAGE_NUM'));
        $changed_list = $AccountModel->getAccountList($fields, $where, 'addtime desc,account_id desc');
        $UserModel = new UserModel();
        foreach ($changed_list as $key => $value) {
            $user_info = $UserModel->getParamUserInfo('user_id = ' . $value['user_id'], 'nickname,realname,id');
            if ($opt == 3) {
                $changed_list[$key]['nickname'] = $user_info['nickname'];
            } else {
                $changed_list[$key]['nickname'] = $user_info['realname'];
            }
            $changed_list[$key]['id'] = $user_info['id'];

            $changed_list[$key]['change_type_name'] = change_change_type($value['change_type']);

            $changed_list[$key]['amount_in'] = feeHandle($value['amount_in']);
            $changed_list[$key]['amount_out'] = feeHandle($value['amount_out']);
            $changed_list[$key]['amount_after_pay'] = feeHandle($value['amount_after_pay']);
            $changed_list[$key]['bank_money_after'] = feeHandle($value['bank_money_after']);


        }
        // dump($changed_list);

        $change_type_list = $AccountModel->getNewChangeTypeList();

        $this->assign('change_type_list', $change_type_list);
        $this->assign('changed_list', $changed_list);
        $this->assign('page', $show);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('alipay_account_name', $alipay_account_name);
        $this->assign('username', $username);
        $this->assign('mobile', $mobile);
        $this->assign('change_type', $change_type);
        $this->assign('id', $id);
        $this->assign('opt',$opt);
        $this->assign('action_title', '用户列表');
        $this->assign('action_src', '/AcpUser/get_all_user_list/mod_id/0');

        $this->assign('head_title', $head_title);

        $this->display('get_account_log');
    }

    //充值用户列表
    public function recharge_list()
    {
        $alipay_account_name = $this->_request('alipay_account_name', '');
        $username = $this->_request('username', '');
        $mobile = $this->_request('mobile', '');

        $id = $this->_request('id', 0);

        //筛选条件
//        $where['role_type'] = array('EQ', 3);
        $where['role_type'] = array('in', array(3,4));
        $where['is_enable'] = 1;

        //用户姓名筛选
        if ($alipay_account_name)
            $where['realname'] = array('LIKE', '%' . $alipay_account_name . '%');

        //用户账号筛选
        if ($username)
            $where['nickname'] = array('LIKE', '%' . $username . '%');

        //用户id筛选
        if($id)
        {
            $where['id'] = $id;
        }
        //手机号筛选
        if ($mobile)
            $where['mobile'] = array('LIKE', '%' . $mobile . '%');

        import('ORG.Util.Pagelist');
        $UserModel = new UserModel();
        $count = $UserModel->getUserNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();

        $UserModel->setStart($Page->firstRow);
        $UserModel->setLimit(C('PER_PAGE_NUM'));
        $user_list = $UserModel->getUserList('nickname, alipay_account_name,realname, role_type, left_money, user_id,mobile,id', $where, 'user_id desc');
        foreach ($user_list as $k =>&$v){
            $v['left_money'] = feeHandle($v['left_money']);
        }unset($v);
        //缺少判断可操作的权限===============================================================================
        $this->assign('data', $user_list);
        $this->assign('page', $show);
        $this->assign('alipay_account_name', $alipay_account_name);
        $this->assign('username', $username);
        $this->assign('mobile', $mobile);
        $this->assign('id', $id);
        $this->assign('head_title', '金豆充值');

        $this->display();
    }

    /**
     * 简单地列出用户
     * @author 姜伟
     * @return void
     * @todo 从user表，取出属于代理商的数据列出来
     */
    public function edit_account()
    {
        $alipay_account_name = $this->_request('alipay_account_name', '');
        $username = $this->_request('username', '');
        $mobile = $this->_request('mobile', '');
        $id = $this->_request('id', 0);

        //筛选条件
        $where['role_type'] = array('in', array(3,4));
        $where['is_enable'] = 1;

        //用户姓名筛选
        if ($alipay_account_name)
            $where['realname'] = array('LIKE', '%' . $alipay_account_name . '%');

        //用户账号筛选
        if ($username)
            $where['nickname'] = array('LIKE', '%' . $username . '%');
        //用户id筛选
        if($id)
        {
            $where['id'] = $id;
        }
        //手机号筛选
        if ($mobile)
            $where['mobile'] = array('LIKE', '%' . $mobile . '%');

        import('ORG.Util.Pagelist');
        $UserModel = new UserModel();
        $count = $UserModel->getUserNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();

        $UserModel->setStart($Page->firstRow);
        $UserModel->setLimit(C('PER_PAGE_NUM'));
        $user_list = $UserModel->getUserList('nickname,realname, role_type,alipay_account_name, left_money, user_id,mobile,id,frozen_money', $where, 'user_id desc');
        foreach($user_list as $k=> &$v){
            $v['left_money'] = feeHandle($v['left_money']);
            $v['frozen_money'] = feeHandle($v['frozen_money']);
        }unset($v);
        //缺少判断可操作的权限===============================================================================
        $this->assign('data', $user_list);
        $this->assign('page', $show);
        $this->assign('alipay_account_name', $alipay_account_name);
        $this->assign('username', $username);
        $this->assign('mobile', $mobile);
        $this->assign('id', $id);
        $this->assign('head_title', '金豆调整');

        $this->display();
    }

    public function edit_agent_account()
    {
        $action = $this->_request('action', '');
        $user_id = $this->_request('user_id', 0);
        $amount_type = $this->_request('amount_type', 0);
        $amount = $this->_request('amount', 0.00);
        $remark = $this->_request('remark', '');
        //缺少判断可操作的权限===============================================================================
        if (!$user_id)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,没有找到可操作的ID!')));
        if (!$amount)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,变动金额必须大于0!')));
//        if ($amount && !preg_match('/^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$/', $amount))
//            exit(json_encode(array('code' => 400, 'msg' => '对不起,变动金额格式不正确!')));

        //RMB转换为金豆  *1000
        $amount = $amount * 1000;

        if ($action == 'set') {
            //加款还是减款
            $change_type = AccountModel::ADDMONEY;

            if ($amount_type) {
                $amount = '-' . $amount;
                $change_type = AccountModel::REDUCEMONEY;
            }


            //增加user表的left_money字段，并往account表插入一条记录
            $AccountModel = new AccountModel();
            $tag = $AccountModel->addAccount($user_id, $change_type, $amount, $remark, '', '');

            if ($tag == -1)
                exit(json_encode(array('code' => 400, 'msg' => '对不起,操作失败,当前减款金额大于总余额!')));

            exit(json_encode(array('code' => 200, 'msg' => '恭喜您,操作成功!')));
        }
    }

    /**
     * 待确认的入账
     * @author 姜伟
     * @return void
     * @todo 从tp_account_apply表取出申请的列表
     */
    public function get_account_apply_list()
    {
        $username = $this->_request('username', '');
        $order_id = $this->_request('order_id', '');
        $start_time = $this->_request('start_time', '');
        $end_time = $this->_request('end_time', '');
        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }

        //筛选条件
        $where['apply_state'] = 0;
        //用户账号筛选
        if ($username) {
            $UserModel = new UserModel();
            $user_info = $UserModel->getParamUserInfo('username like "%' . $username . '%"', 'user_id');
            if ($user_info) {
                $where['user_id'] = $user_info['user_id'];
            }
        }

        //通过时间区间筛选数据
        if ($start_time && $end_time)
            $where['addtime'] = array(array('gt', str_format_time($start_time)), array('lt', str_format_time($end_time)), 'and');

        //通过订单ID筛选
        if ($order_id)
            $where['order_id'] = $order_id;

        //获取入账申请列表
        import('ORG.Util.Pagelist');
        $AccountApplyModel = new AccountApplyModel();
        $count = $AccountApplyModel->getUnhandledAccountApplyNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();
        $account_apply_list = $AccountApplyModel->getAccountApplyList($where, '', 'addtime desc', $Page->firstRow . ',' . $Page->listRows);
        $UserModel = new UserModel();
        foreach ($account_apply_list as $key => $value) {
            $user_info = $UserModel->getParamUserInfo('user_id = ' . $value['user_id'], 'username');
            $account_apply_list[$key]['username'] = $user_info['username'];
        }
        // dump($account_apply_list);
        //缺少判断可操作的权限===============================================================================

        $this->assign('data', $account_apply_list);
        $this->assign('page', $show);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('username', $username);
        $this->assign('head_title', '线下入账申请');

        $this->display();
    }

    /**
     * 导出财务数据
     * @author 姜伟
     * @return void
     * @todo 从account表导出数据，支持选定代理商或起始时间
     */
    public function export_account()
    {

        $action = $this->_request('action');
        $agent_no = I('agent_no', '0');
        $start_time = I('start_time');
        $end_time = I('end_time');
        /*if($start_time)
        {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if($end_time)
        {
            $end_time = str_replace('+', ' ', $end_time);
        }*/

        $export_url = '';
        if ($action == 'select') {
            $tag = false;
            //通过代理商编号获取代理商的user_id
            if ($agent_no) {
                $where = array();
                $user_info = M('users')->where('agent_no = "' . $agent_no . '"')->field('user_id,username')->find();
                //echo  M('users')->_sql();

                //通过user_id获取指定数据
                if ($user_info)
                    $where['user_id'] = $user_info['user_id'];
                else
                    $tag = true;
            }

            //通过时间区间筛选数据
            if ($start_time && $end_time)
                $where['addtime'] = array(array('GT', str_format_time($start_time)), array('LT', str_format_time($end_time)), 'AND');

            //获取总条数
            $account = M('account');
            $count = $account->where($where)->order('addtime')->count();
            $export_url = '/AcpFinance/export_account/action/export/agent_no/' . $agent_no . '/start_time/' . $start_time . '/end_time/' . $end_time;
            $count = ($tag) ? 0 : $count;

        } else if ($action == 'export') {
            $tag = false;
            //通过代理商编号获取代理商的user_id
            if ($agent_no && $agent_no != 'start_time') {
                $where = array();
                $user_info = M('users')->where('agent_no = "' . $agent_no . '"')->field('user_id,username')->find();
                if ($user_info)
                    $where['user_id'] = $user_info['user_id'];
                else
                    $tag = true;
            }
            //通过时间区间筛选数据
            $start_time = I('start_time');
            $end_time = I('end_time');
            if ($start_time && $end_time) {
                $where = 'addtime >=' . strtotime($start_time) . ' and addtime <=' . strtotime($end_time);
                //$where['addtime'] = array('GT',str_format_time($start_time)); 
                //$where['addtime'] = array('LT', str_format_time($end_time)); 
            }

            //获取数据
            $account = M('account');
            $arr = $account->where($where)->order('addtime')->select();
            #echo $account->getLastSql();die;
            #echo "<pre>";
            #print_r($arr);die;

            $count = count($arr);
            $count = ($tag) ? 0 : $count;

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
            $objPHPExcel->getActiveSheet(0)->setTitle('财务变动明细报表');          //标题
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);      //单元格宽度
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小
            $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '用户名');
            $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '操作类型');
            $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '入账金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '出账金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '变动前预存款金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('F1', '变动后预存款结余');
            $objPHPExcel->getActiveSheet(0)->setCellValue('G1', '相关订单号');
            $objPHPExcel->getActiveSheet(0)->setCellValue('H1', '操作人');
            $objPHPExcel->getActiveSheet(0)->setCellValue('I1', '记录生成时间');
            $objPHPExcel->getActiveSheet(0)->setCellValue('J1', '备注');
            $objPHPExcel->getActiveSheet(0)->setCellValue('K1', '支付凭证号');
            $objPHPExcel->getActiveSheet(0)->setCellValue('L1', '操作人IP');

            //每行数据的内容
            foreach ($arr as $value) {
                static $i = 2;
                //获取用户信息
                $user_info = M('users')->where('user_id = ' . $value['user_id'])->field('username')->find();

                //用户名
                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $user_info['username']);
                //操作类型
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, change_change_type($value['change_type']));
                //入账金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $value['amount_in']);
                //出账金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['amount_out']);
                //变动前预存款金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $value['amount_before_pay']);
                //变动后预存款结余
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $value['amount_after_pay']);
                //相关订单号
                $order_sn = '--';
                if ($value['order_id']) {
                    try {
                        $order_obj = new OrderModel($value['order_id']);
                        $order_info = $order_obj->getOrderInfo('order_sn');
                        $order_sn = "'" . $order_info['order_sn'];
                    } catch (Exception $e) {
                        //订单不存在，不处理
                    }
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $order_sn);
                //操作人姓名
                $realname = '--';
                if ($value['operater']) {
                    $user_obj = new UserModel($value['operater']);
                    $user_info = $user_obj->getUserInfo('realname');
                }
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $realname);
                //记录生成时间
                $value['addtime'] = date('Y-m-d H:i:s', $value['addtime']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, $value['addtime']);
                //备注
                $objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, $value['remark']);
                //支付凭证号
                $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $i, $value['proof']);
                //操作人IP
                $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $i, $value['ip']);
                $i++;
            }

            //直接输出文件到浏览器下载
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="财务变动明细报表_' . date("YmdHis") . '.xls"');
            header('Cache-Control: max-age=0');
            ob_clean(); //关键
            flush(); //关键
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }

        if ($start_time == 'end_time') {
            $start_time = '';
        }


        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('count', $count);
        $this->assign('export_url', $export_url);
        $this->assign('head_title', '导出财务数据');

        $this->display();
    }


    /**
     * 申请入帐明细
     * @author 姜伟
     * @return void
     * @todo 从tp_account_apply表取出申请的列表
     */
    public function account_apply_list()
    {
        $username = $this->_request('username', '');
        $start_time = $this->_request('start_time', '');
        $end_time = $this->_request('end_time', '');
        $apply_state = $this->_request('apply_state', 4);
        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }
        //筛选条件
        if ($apply_state != 4)
            $where['apply_state'] = $apply_state;
        //用户名称筛选
        if ($username) {
            $UserModel = new UserModel();
            $user_info = $UserModel->getParamUserInfo('username like "%' . $username . '%"', 'user_id');
            if ($user_info)
                $where['user_id'] = $user_info['user_id'];
        }

        //通过时间区间筛选数据
        if ($start_time && $end_time)
            $where['addtime'] = array(array('gt', str_format_time($start_time)), array('lt', str_format_time($end_time)), 'and');

        //获取订单列表
        import('ORG.Util.Pagelist');
        $AccountApplyModel = new AccountApplyModel();
        $count = $AccountApplyModel->getUnhandledAccountApplyNum($where);

        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $show = $Page->show();
        $account_apply_list = $AccountApplyModel->getAccountApplyList($where, '', '', $Page->firstRow . ',' . $Page->listRows);
        //dump($account_apply_list);
        $UserModel = new UserModel();
        foreach ($account_apply_list as $key => $value) {
            $user_info = $UserModel->getParamUserInfo('user_id = ' . $value['user_id'], 'username');
            $account_apply_list[$key]['username'] = $user_info['username'];
        }


        //缺少判断可操作的权限===============================================================================

        $this->assign('data', $account_apply_list);
        $this->assign('page', $show);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('username', $username);
        $this->assign('apply_state', $apply_state);
        $this->assign('head_title', '申请入账明细');

        $this->display();
    }


    /**
     * 通过,或拒绝入账申请
     * @author 姜伟
     * @return void
     * @todo 往account表插入一条记录
     * @todo 修改用户的预存金额
     * @todo 修改account表中记录的行状态
     * @todo 向款项出入明细表增加一条记录
     */
    public function confirm_enter()
    {
        $action = $this->_request('action', '');
        $account_apply_id = $this->_request('id', 0);
        //缺少判断可操作的权限===============================================================================
        if (!$account_apply_id)
            $this->error('对不起,没有找到可操作的ID!');

        if ($action == 'by') {
            //获取该条信息记录
            $AccountApplyModel = new AccountApplyModel($account_apply_id);
            $account_apply_info = $AccountApplyModel->getAccountApplyById($account_apply_id);

            if (!$account_apply_info)
                $this->error('对不起,没有你要查询的数据!');

            $AccountModel = new AccountModel();
            $left_money = $AccountModel->addAccount($account_apply_info['user_id'], 3, $account_apply_info['amount'], '管理员手动通过入账申请', $account_apply_info['order_id'], $account_apply_info['proof']);
            if ($left_money < 0)
                $this->error('对不起,余额不足，修改用户预存款失败!');

            //根据订单号取订单ID
            $order_obj = new OrderModel();
            $order_id = $order_obj->getOrderIdByOrderSn($account_apply_info['order_id']);
            //若申请信息中有订单号，自动为该笔订单付款
            $order_id = intval($order_id);
            if ($order_id) {
                //获取订单信息
                $order_obj = new OrderModel($order_id);
                try {
                    $order_info = $order_obj->getOrderInfo('order_sn, pay_amount, order_status, order_type');
                    //若未付款，为该笔订单付款，否则不处理
                    if ($order_info['order_status'] == 0) {
                        //为该笔订单付款

                        $is_virtual_stock_order = $order_info['order_type'] == 2 ? true : false;
                        $left_money = $AccountModel->addAccount($account_apply_info['user_id'], 5, floatval($order_info['pay_amount']) * -1, '支付订单号为' . $order_info['order_sn'] . '的订单', $order_id, $account_apply_info['proof'], $is_virtual_stock_order);
                    }
                } catch (Exception $e) {
                    //订单不存在，不处理
                }
            }

            //修改account表中记录的行状态为通过
            $apply_state_tag = $AccountApplyModel->passAccountApply(array('apply_state' => 1));

            if ($apply_state_tag)
                $this->success('恭喜您,操作已成功！该用户账户余额已调整为：' . $left_money);

        } else if ($action == 'refusal') {
            //修改account表中记录的行状态为拒绝
            $AccountApplyModel = new AccountApplyModel($account_apply_id);
            $apply_state_tag = $AccountApplyModel->passAccountApply(array('apply_state' => 2));

            if ($apply_state_tag)
                $this->success('恭喜您,操作已成功!');
        }
    }


    //充值
    public function do_recharge()
    {
        $action = $this->_request('action', '');
        $user_id = $this->_request('user_id', 0);
        $amount = $this->_request('amount', 0.00);
        $remark = $this->_request('remark', '');
        //缺少判断可操作的权限===============================================================================
        if (!$user_id)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,没有找到可操作的ID!')));
        if (!$amount)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,变动金额必须大于0!')));

        //7-23 总后台充值的是金豆数
//        //RMB转换为金豆  *1000
//        $amount = $amount * 1000;
        if ($action == 'set') {
            //充值
            $change_type = AccountModel::RECHARGE;

            //增加user表的left_money字段，并往account表插入一条记录
            $AccountModel = new AccountModel();
            $tag = $AccountModel->addAccount($user_id, $change_type, $amount, $remark, '', '');

            if($tag == -1){
                exit(json_encode(array('code' => 400, 'msg' => '余额不足，操作失败!')));
            }
            
//            $config_obj = new ConfigBaseModel();
//            $recharge_exp = $config_obj->getConfig('recharge_exp'); //充值返经验比例
//
//            $user_obj = new UserModel($user_id);
//            $user_info = $user_obj->getUserInfo('exp,level_id', 'user_id = ' . $user_id);

//            $new_exp = intval($user_info['exp'] + $amount * $recharge_exp);
//            if ($new_exp >= 1) {
//                $level_obj = new LevelModel();
//                $level_info = $level_obj->where('min_exp <='.$new_exp)->order('min_exp DESC')->find();
//                $level_id = $level_info['level_id'];  //最新等级id
//                $arr['exp'] = $new_exp;
//                if($user_info['level_id'] != $level_id && $level_id)
//                {
//                    $arr['level_id'] = $level_id;
//                }
//                $user_obj = new UserModel($user_id);
//                $user_obj->editUserInfo($arr);
//            }
            exit(json_encode(array('code' => 200, 'msg' => '恭喜您,操作成功!')));
        }
    }

    /**
     * 修改代理商的账户余额
     * @author 姜伟
     * @return void
     * @todo 如果金额为0，返回加款失败；如果减款金额大于总余额，返回失败；
     * @todo 增加user表的left_money字段，往account表插入一条记录
     */
    public function set_amount()
    {
        $action = $this->_request('action', '');
        $user_id = $this->_request('user_id', 0);
        $amount_type = $this->_request('amount_type', 0);
        $amount = $this->_request('amount', 0.00);
        $remark = $this->_request('remark', '');
        $left_type = $this->_request('left_type',0);
        //缺少判断可操作的权限===============================================================================
        if (!$user_id)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,没有找到可操作的ID!')));
        if (!$amount)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,变动金额必须大于0!')));
//        if ($amount && !preg_match('/^[1-9]\d*\.\d*|0\.\d*[1-9]\d*$/', $amount))
//            exit(json_encode(array('code' => 400, 'msg' => '对不起,变动金额格式不正确!')));


        //fix 7-23 总后台充值的是金豆数
//        //RMB转换为金豆  *1000
//        $amount = $amount * 1000;

        if ($action == 'set') {
            if($left_type == 0)//调整游戏分
            {
                //加款还是减款
                $change_type = AccountModel::ADD;

                if ($amount_type) {
                    $amount = '-' . $amount;
                    $change_type = AccountModel::REDUCE;
                }
            }else{//调整银行分
                //加款还是减款
                $change_type = AccountModel::ADD_BANK;

                if ($amount_type) {
                    $amount = '-' . $amount;
                    $change_type = AccountModel::REDUCE_BANK;
                }
            }



            //增加user表的left_money字段，并往account表插入一条记录
            $AccountModel = new AccountModel();
            $tag = $AccountModel->addAccount($user_id, $change_type, $amount, $remark, '', '');

            if ($tag == -1)
                exit(json_encode(array('code' => 400, 'msg' => '对不起,操作失败,当前减款金额大于总余额!')));

            exit(json_encode(array('code' => 200, 'msg' => '恭喜您,操作成功!')));
        }
    }


    /**
     * 入账比例报表
     * @author 姜伟
     * @return void
     * @todo 从order表中取出多种v，按它们已付款的订单额，显示成线状图(除电子钱包外的饼图)
     */
    public function chart_payment()
    {
        $start_time = $this->_request('start_time', '');
        $end_time = $this->_request('end_time', '');
        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }
        $s_time = str_format_time($start_time);
        $e_time = str_format_time($end_time);

        if ($s_time > $e_time)
            $this->error('对不起,您选择的开始时间不能大于结束时间!');

        //按时间统计出每种支付方式的总金额
        $OrderModel = new OrderModel();
        $OrderModel->setListFields('payway_name, pay_time');
        $order_listt = $OrderModel->getVolumeOfTradeGroupByPayway($s_time, $e_time);

        //计算总金额的百分之1
        $order_total_volumn = $OrderModel->getVolumeOfTrade($s_time, $e_time);
        $order_total_volumn_one = number_format(($order_total_volumn / 100), 2, '.', '');

        //输出饼状图
        import('ORG.Util.Highcharts');
        $arr = array();
        foreach ($order_listt as $k => $value) {
            $proportion = number_format(($value['total_volumn'] / $order_total_volumn_one), 2, '.', '');
            $arr[$k]['name'] = $value['payway_name'] . ":" . $value['total_volumn'] . '元';
            $arr[$k]['num'] = $proportion;
        }
        $data = ch_json_encode($arr);
        $this->assign('data', $data);

        $this->assign('time', $start_time . ' 至  ' . $end_time);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        $this->assign('order_listt', $order_listt);
        $this->assign('head_title', '入账比例报表');

        $this->display();
    }

    /**
     * 充值按日统计
     */
    public function recharge_stat_by_day()
    {
        $add_time = $this->_post('add_time');
        $start_time = 0;
        $end_time = 0;
        $date = '';

        if ($add_time) {
            $start_time = strtotime(date('Y-m-d', strtotime($add_time)));
            #$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
            $end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
            $date = date('Y-m-d', strtotime($add_time));
        } else {
            //今天0点的时间戳
            $end_time = strtotime(date('Y-m-d', time())) + 86400;

            //昨天0点的时间戳
            #$start_time = strtotime(date('Y-m-d', time())) - 86400;
            $start_time = strtotime(date('Y-m-d', time()));
            $date = date('Y-m-d', $start_time);
        }

        //获取充值统计数据
        $account_obj = new AccountModel();
        $recharge_stat_list = $account_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%H") AS hour, COUNT(*) AS recharge_num, SUM(amount_in) AS total_amount')->where('change_type >= 1 AND change_type <= 2 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time)->group('hour')->order('addtime DESC')->select();

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 24; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['hour'])] = $val['total_amount'];
        }

        $this->assign('recharge_amount_stat_list', $new_recharge_stat_list);

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 24; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['hour'])] = $val['recharge_num'];
        }

        $this->assign('recharge_num_stat_list', $new_recharge_stat_list);
        $this->assign('date', $date);
        #echo "<pre>";
        #print_r($recharge_stat_list);
        #echo $account_obj->getLastSql();
        #print_r($new_uv_list);

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '充值按日统计');
        $this->display();
    }

    /**
     * 充值按月统计
     */
    public function recharge_stat_by_month()
    {
        $year = $this->_post('year');
        $month = $this->_post('month');
        $year = $year ? $year : date('Y');
        $month = $month ? $month : date('m');
        $start_time = 0;
        $end_time = 0;
        $date = '';

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
            $date = $year . '-' . date('m');
        }

        //获取充值统计数据
        $account_obj = new AccountModel();
        $recharge_stat_list = $account_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%m") AS day, COUNT(*) AS recharge_num, SUM(amount_in) AS total_amount')->where('change_type >= 1 AND change_type <= 2 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time)->group('day')->order('addtime DESC')->select();

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 30; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['day'])] = $val['total_amount'];
        }

        $this->assign('recharge_amount_stat_list', $new_recharge_stat_list);

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 30; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['day'])] = $val['recharge_num'];
        }

        $this->assign('recharge_num_stat_list', $new_recharge_stat_list);
        $this->assign('date', $date);
        $this->assign('day_num', date('d', mktime(0, 0, 0, $month + 1, 0, $year)));

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按月统计');
        $this->display();
    }

    /**
     * 充值按年统计
     */
    public function recharge_stat_by_year()
    {
        $year = $this->_post('year');
        $start_time = 0;
        $end_time = 0;
        $date = '';

        if ($year) {
            $start_time = mktime(0, 0, 0, 1, 1, $year);
            $end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
            $date = date('Y-m-d', strtotime($year));
        } else {
            $year = date('Y');
            $start_time = mktime(0, 0, 0, 1, 1, $year);
            $end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
        }
        $this->assign('year', $year);

        //获取充值统计数据
        $account_obj = new AccountModel();
        $recharge_stat_list = $account_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%m") AS month, COUNT(*) AS recharge_num, SUM(amount_in) AS total_amount')->where('change_type >= 1 AND change_type <= 2 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time)->group('month')->order('addtime DESC')->select();

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 12; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['month'])] = $val['total_amount'];
        }

        $this->assign('recharge_amount_stat_list', $new_recharge_stat_list);

        $new_recharge_stat_list = array();
        for ($i = 0; $i <= 12; $i++) {
            $new_recharge_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($recharge_stat_list AS $key => $val) {
            $new_recharge_stat_list[intval($val['month'])] = $val['recharge_num'];
        }

        $this->assign('recharge_num_stat_list', $new_recharge_stat_list);
        $this->assign('date', $date);

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按年统计');
        $this->display();
    }


//    /**
//     * 撤销
//     * @date: 2019/5/7
//     * @author: hui
//     */
//    public function set_back()
//    {
//        if (IS_AJAX && IS_POST) {
//            $account_id = I('account_id');
//            $account_obj = new AccountModel();
//            $account_info = $account_obj->where('account_id =' . $account_id)->find();
//            log_file($account_obj->getLastSql(),'set_back');
//            $user_obj = new UserModel();
//            $left_money = $user_obj->where('user_id =' . $account_info['user_id'])->getField('left_money');
//            log_file($left_money,'set_back');
//
//            $new_money = $left_money - $account_info['amount_in'] + $account_info['amount_out'];
//            $res1 = $user_obj->where('user_id =' . $account_info['user_id'])->save(['left_money' => $new_money]);
//            log_file($user_obj->getLastSql(),'set_back');
//
//            if ($res1 === false) {
//                exit('failure');
//            }
//            $res2 = $account_obj->where('account_id =' . $account_id)->save(['state' => 1]);
//            log_file($account_obj->getLastSql(),'set_back');
//            if ($res2 === false) {
//                exit('failure');
//            }
//            exit('success');
//
//        }
//    }

    /**
     * 撤销充值以及一系列操作
     * @author yzp
     * @Date:  2019/11/4
     * @Time:  18:12
     */
    public function set_back()
    {
        if (IS_AJAX && IS_POST) {
            $account_id = I('account_id');
            $account_obj = new AccountModel();
            $account_info = $account_obj->where('account_id =' . $account_id)->find();
            $amount = $account_info['amount_in'];

            $recharge_id = $account_info['recharge_id'];
            $account_obj = new AccountModel();
            $recharge_info = $account_obj->where('account_id =' . $recharge_id)->find();
            if(!$recharge_id || !$recharge_info)
            {
                exit('failure');
            }
            $user_obj = new UserModel($account_info['user_id']);
            $left_money = $user_obj->getLeftMoney();
            $user_obj = new UserModel($account_info['user_id']);
            $left_bank = $user_obj->getLeftBankMoney();
            if($left_money < $amount)
            {
                exit('lack_left_money');
            }
            //查询活动充值赠送金豆
            $activity_account_info = $account_obj->where('recharge_id =' . $recharge_id.' AND change_type ='.AccountModel::DAILYCHARGE)->find();
            if($activity_account_info && $left_bank < $activity_account_info['amount_in'])
            {
                exit('lack_left_bank');
            }
            $account_obj = new AccountModel();
            //扣除已经充值的金豆
            $account_obj->addAccount($account_info['user_id'], AccountModel::CANCEL_RECHARGE, -1*$amount, '撤销充值扣除金豆', '', '');
            if($activity_account_info)//如果有参加活动充值赠送金豆一起扣除
            {
                $account_obj->addAccount($account_info['user_id'], AccountModel::CANCEL_RECHARGE_ACT, -1*$activity_account_info['amount_in'], '撤销充值扣除活动赠送金豆', '', '');
            }
            //返还代理充值的金豆
            $account_obj = new AccountModel();
            $res1 = $account_obj->addAccount($recharge_info['user_id'], AccountModel::RETURN_RECHARGE, $recharge_info['amount_out'], '撤销充值返还金豆', '', '');
            if ($res1 === false) {
                exit('failure');
            }
            $res2 = $account_obj->where('account_id =' . $account_id)->save(['state' => 1]);
            $account_obj->where('account_id =' . $recharge_id)->save(['state' => 1]);
            log_file($account_obj->getLastSql(),'set_back');
            if ($res2 === false) {
                exit('failure');
            }
            exit('success');

        }
    }


}

?>
