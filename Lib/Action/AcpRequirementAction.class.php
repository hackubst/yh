<?php
/**
 * acp后台商品分类
 */
class AcpRequirementAction extends AcpAction {
    /**
     * 初始化
     * @author 张勇
     * @return void
     * @todo 初始化方法
     */
    function _initialize() {
        parent::_initialize();
    }

    const UNFINSHED = 0; //已经解决
    const FINISHED  = 1; //已经解决
    const REJECTED  = 2; //已拒绝

    /**
     * @author wsq
     *
     */
    public function requirement_list($condition = '', $headtitle='', $opt) {
        $where  = '';
        $where .= $condition;


        import('ORG.Util.Pagelist');

        $user_requirement_obj = new UserRequirementModel();
        $count = $user_requirement_obj->getUserRequirementNum($where);
        $Page  = new Pagelist($count,C('PER_PAGE_NUM'));
		$user_requirement_obj->setStart($Page->firstRow);
        $user_requirement_obj->setLimit($Page->listRows);
        $show = $Page->show();

        $user_requirement_list = $user_requirement_obj->getUserRequirementList('', $where, 'addtime DESC');
        $user_requirement_list = $user_requirement_obj->getListData($user_requirement_list);


        #echo "<pre>";
        #print_r($user_requirement_list);die;

        $this->assign('user_requirement_list', $user_requirement_list);
        $this->assign('head_title', $headtitle);
        $this->assign('opt',$opt);
        $this->display(APP_PATH . 'Tpl/AcpRequirement/requirement_list.html');
    }

    /**
     * @author wsq
     *
     */
    public function get_all_requirement_list() {
        $this->requirement_list('', "所有需求列表");
        // body...
    }


    public function get_pre_handle_requirement_list() {
        $this->requirement_list('state = ' . UserRequirementModel::UNFINISHED, "待解决需求列表");
        // body...
    }

    public function get_finished_requirement_list() {
        $this->requirement_list('state = 1 OR state = 2', "已经完成需求列表");

        // body...
    }

    public function set_state() {
    
        $state   = intval($this->_post('state'));
        $id      = intval($this->_post('id'));
        $content = $this->_post('content');
        $user_id = $this->_post('user_id');

		if ($id && $state && $content) {
            $user_requirement_obj  = new UserRequirementModel($id);
            $info                  = $user_requirement_obj->getUserRequirementById($id);

            if (!$info) exit('failure1');

            $success = $user_requirement_obj->editUserRequirement($id, array(
                'state'   => $state,
            ));

            //添加日志
            $requirement_obj       = M('user_requirement_log');

            $requirement_obj->add(array(
                'user_id'             => $user_id,
                'message'            => $content,
                'addtime'             => time(),
                'user_requirement_id' => $id,
            ));

			exit($success ? 'success' : 'failure');
		}
		exit('failure');
    }

    public function requirement_detail() {
        $id = intval($this->_get('id'));
        if (!$id)  $this->error('id无效', U('/AcpRequirement/get_all_requirement_list'));
        $user_requirement_obj = new UserRequirementModel();
        $info                 = $user_requirement_obj->getUserRequirementById($id);
        if (!$info) $this->error('记录不存在', U('/AcpRequirement/get_all_requirement_list'));

        //用户名
        $user_obj   = new UserModel($info['user_id']);
        $user_info  = $user_obj->getUserInfo('realname, nickname');
        $user_name  = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];

        #echo "<pre>"; die(print_r($info));

        $this->assign('info', $info);
        $this->assign('user_name', $user_name);
        $this->assign('head_title', '详细需求信息');
        $this->display();
    }

}
