<?php
/**
 * 配送类
 * 
 *
 */
class AcpBigAreaAction extends AcpAction {
	
	
	 /**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
    public function AcpBigAreaAction()
	{
		parent::_initialize();
	}
	
	/**
     * 大区列表
     * @author wsq
     * @return void
     * @todo 
     */
	public function list_area()
	{

		$big_area_obj = new BigAreaModel();
		//数据总量
		$total          = $big_area_obj->getBigAreaNum();
        $big_area_list  = $big_area_obj->getBigAreaList();
        $big_area_list  = $big_area_obj->getListData($big_area_list);

        //echo "<pre>"; die(print_r($big_area_list));

		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$big_area_obj->setStart($Page->firstRow);
        $big_area_obj->setLimit($Page->listRows);

		$page_str = $Page->show();

        $this->assign("head_title", "大区列表");
        $this->assign("big_area_list", $big_area_list);
		$this->display();
	}
	
	/**
     * 添加大区
     * @author wsq
     * @return void
     */
	public function add_area()
	{
		$act = $this->_post('act');

		if($act == 'submit')
		{
			$_post         = $this->_post();
			$big_area_name = $_post['big_area_name'];
            $province_ids  = $_post['province_ids'];

            //校验表单数据
            if (!$big_area_name) $this->error('请输入大区名称');
            if (!$province_ids || count($province_ids) == 0) $this->error('请选择所在省份');

            $big_area_obj  = new BigAreaModel(); 

            $success       = $big_area_obj->addBigArea(
                array(
                    "area_name"    => $big_area_name,
                    "province_ids"     => implode(',', $province_ids),
                )
            );

            if ($success) {
                $this->success('恭喜，大区添加成功', '/AcpBigArea/list_area');
            } else {
                $this->error('抱歉，大区添加失败，请稍候再试!');
            }
        }
			
        $province_obj  = M('address_province');
        $province_list = $province_obj->select(); 
        $this->assign('province_list', $province_list);
		$this->assign('head_title', '添加大区');
		$this->display();
	}
	
	/**
     * 修改大区信息
     * @author wsq
     * @return void
     */
	public function edit_area()
	{
		$id  = $this->_get('big_area_id');
		$act = $this->_post('act');

        $big_area_obj = new BigAreaModel($id);
        $info         = $big_area_obj->getBigAreaInfo($id);

        if (!$id || !$info) $this->error('抱歉，信息不存在,请稍候再试!', '/AcpBigArea/list_area');
		
		if($act == 'submit')
		{
			$_post         = $this->_post();
			$big_area_name = $_post['big_area_name'];
            $province_ids  = $_post['province_ids'];

            //校验表单数据
            if (!$big_area_name) $this->error('请输入大区名称');
            if (!$province_ids || count($province_ids) == 0) $this->error('请选择所在省份');

            $big_area_obj  = new BigAreaModel(); 

            $data_array    =  array(
                    "area_name"        => $big_area_name,
                    "province_ids"     => implode(',', $province_ids),
                );

            if ($big_area_obj->setBigArea($id, $data_array)){
                $this->success('恭喜，大区信息修改成功', '/AcpBigArea/list_area');
            } else {
                $this->error('抱歉，大区信息修改失败，请稍候再试!');
            }
        }
			
        $province_obj  = M('address_province');
        $province_list = $province_obj->select(); 
        $this->assign('province_list', $province_list);

        $this->assign('big_area_info', $info);
        $this->assign('big_area_provices', explode(',',$info['province_ids']));
		$this->assign('head_title', '修改配送方式');
		$this->display(APP_PATH . 'Tpl/AcpBigArea/add_area.html');
	}


    public function del_area() {
    
		$big_area_id = intval($this->_post('big_area_id'));
		if ($big_area_id) {
			$big_area_obj = new BigAreaModel($big_area_id);

			$success = $big_area_obj->delBigArea();
			exit($success ? 'success' : 'failure');
		}

		exit('failure');
    }

	
}
?>
