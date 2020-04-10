<?php
/**
 * 页面模板类
 * */
class AcppageAction extends AcpAction
{
	/**
     * 构造函数
     * @author jiangwei 
     * @return void
     * @todo 
     */
	public function AcppageAction()
	{
		parent::_initialize();
	}

    /**
     * 查看已选页面模板列表
     * @author jiangwei
     * @return void
     * @todo 取配置项中的模板配置项，遍历之，取出对应模板页面的信息并展示
     */
    public function get_selected_page_list()
    {
		//获取配置项中的模板配置项
		$templet_info = json_decode($GLOBALS['config_info']['TEMPLET_INFO'], true);
        $page_obj = new PageModel();
		$page_ids = '';
		foreach ($templet_info AS $k => $v)
		{
			$page_ids .= $v . ',';
		}
		$page_ids = substr($page_ids, 0, -1);
		$where = 'page_id IN (' . $page_ids . ')';

        $page_list = $page_obj->getPageList('', $where);
        $page_list = $page_obj->getListData($page_list);
		#dump($page_list);
		#die;
        //dump($page_list);die;

        $this->assign('head_title', '当前页面模板');
        $this->assign('page_list', $page_list);
        $this->display();
    }

    /**
     * 换页面模板
     * @author jiangwei
     * @return void
     * @todo 换页面模板
     * */
     public function edit_page()
     {
        $page_type = I('get.page_type', 0, 'int');
        $page_id = I('get.page_id', 0, 'int');
        $page_obj = new PageModel();
        $page_list = $page_obj->getRemotePageList($page_type);
        $act       = I('act');
        if ('save' == $act)
        {
            $page_id = intval(I('page_id'));
			#echo $page_id;
			#die;

            $state = $page_obj->usePage($page_id);
            if (!$state) {
                $this->error('启用失败');
            } else {
				$url = '/AcpPage/edit_page/page_type/' . $page_type . '/page_id/' . $page_id;
                $this->success('启用成功', $url);
            }
        }

		$this->assign('crm_host', C('TEMPLET_HOST'));
		$this->assign('page_id', $page_id);
		$this->assign('page_list', $page_list);
        $this->assign('head_title', '修改页面模板');
        $this->display();
     }

    /**
     * 换模板套装
     * @author jiangwei
     * @return void
     * @todo 换模板套装
     * */
     public function edit_package()
     {
        $package_type = I('get.package_type', 0, 'int');
        $templet_package_id = I('get.templet_package_id', 0, 'int');
        $page_obj = new PageModel();
        $act       = I('act');
        if ('save' == $act)
        {
            $templet_package_id = intval(I('templet_package_id'));
			#echo $templet_package_id;
			#die;

            $state = $page_obj->usePackage($templet_package_id);
            if (!state) {
                $this->error('启用失败');
            } else {
				$url = '/AcpPage/edit_package';
                $this->success('启用成功', $url);
            }
        }

		//当前使用的模板套装
		$this->assign('templet_package_id', $GLOBALS['config_info']['CUR_TEMPLET_PACKAGE_ID']);
		$this->assign('crm_host', C('TEMPLET_HOST'));

		//套装列表
        $package_list = $page_obj->getRemoteTempletList($package_type);
		$this->assign('package_list', $package_list);
        $this->assign('head_title', '修改页面模板');
        $this->display();
     }
}
