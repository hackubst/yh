<?php
//学校类
class AcpSchoolAction extends AcpAction{

	//学校列表
	public function get_school_list(){
		$school_obj = new SchoolModel();
		$where = '';
		//数据总量
		$total = $school_obj->getSchoolNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$school_obj->setStart($Page->firstRow);
  		$school_obj->setLimit($Page->listRows);

		$page_str = $Page->show();
		$this->assign('page_str',$page_str);

		$school_list = $school_obj->getSchoolList();
		$this->assign('school_list', $school_list);
		$this->assign('head_title', '学校列表');
		$this->display();
	}

	//添加学校
	public function add_school(){

		$act = I('act');
		if($act == 'add'){
			$school_name = I('school_name');
			$school_code = I('school_code');
			$serial = I('serial');
			$center_lng = I('center_lng');
			$center_lat = I('center_lat');
			//$geo_fencing = I('geo_fencing');
			if(!$school_name){
				$this->error('学校名称不能为空');
			}
			if(!$school_code){
				$this->error('特征码不能为空');
			}
			if(!ctype_digit($serial)){
				$this->error('排序号错误');
			}
			if(!$center_lng){
				$this->error('请输入经度');
			}
			if(!$center_lat){
				$this->error('请输入纬度');
			}
			/*if(!$geo_fencing){
				$this->error('地理围栏不能为空');
			}*/
			$data = array(
				'school_name' => $school_name,
				'school_code' => $school_code,
				'serial' => $serial,
				'center_lat' => $center_lat,
				'center_lng' => $center_lng
				);
			$school_obj = new SchoolModel();
			$success = $school_obj->addSchool($data);
			if($success){
				$this->success('恭喜您，学校添加成功！', '/AcpSchool/add_school');
			}else{
				$this->error('抱歉，学校添加失败！', '/AcpSchool/add_school');
			}
		}

		$this->assign('head_title', '添加学校');
		$this->display();
	}

	//修改学校
	public function edit_school(){
		$school_id = I('school_id','','intval');
		if(!$school_id){
			$this->error('对不起，非法访问！', '/AcpSchool/list_school');
		}
		$school_obj = new SchoolModel();
		$school_info = $school_obj->getSchool($school_id);
		
		if (!$school_info) {
			$this->error('对不起，不存在相关学校！', '/AcpSchool/list_school');
		}
		$act = I('act');
		if($act == 'edit'){

			$school_name = I('school_name');
			$school_code = I('school_code');
			$serial = I('serial');
			$center_lng = I('center_lng');
			$center_lat = I('center_lat');
			//$geo_fencing = I('geo_fencing');

			if(!$school_name){
				$this->error('学校名称不能为空');
			}
			if(!$school_code){
				$this->error('特征码不能为空');
			}
			if(!ctype_digit($serial)){
				$this->error('排序号错误');
			}
			if(!$center_lng){
				$this->error('请输入经度');
			}
			if(!$center_lat){
				$this->error('请输入纬度');
			}
			/*if(!$geo_fencing){
				$this->error('地理围栏不能为空');
			}*/
			$data = array(
				'school_name' => $school_name,
				'school_code' => $school_code,
				'serial' => $serial,
				'center_lat' => $center_lat,
				'center_lng' => $center_lng
				);
			$url = '/AcpSchool/edit_school/school_id/' . $school_id;

			$school_obj = new SchoolModel();
			if($school_obj->editSchool($school_id, $data)){
				$this->success('恭喜您，学校修改成功！', $url);
			}else{
				$this->error('抱歉，学校修改失败！');
			}
		}
		$this->assign('school_info', $school_info);
		$this->assign('head_title', '修改学校');
		$this->display();
	}

	//删除学校
	public function delete_school(){
		$school_id = I('school_id', '', 'intval');
		if($school_id){
			$school_obj = new SchoolModel();
			$result = $school_obj->delSchool($school_id);
			exit($result ? 'success' : 'failure');
		}
		exit('failure');
	}

	//批量删除学校
	public function batch_delete_school(){
		$school_ids = $this->_post('school_ids');

		if ($school_ids) {
			$school_id_ary = explode(',', $school_ids);

			$success_num = 0;
            $school_obj = new SchoolModel();
			foreach ($school_id_ary AS $school_id)
			{
                $num = $school_obj->getSchoolNum('school_id ='.$school_id);

                if (!$num) continue;

				$success_num += $school_obj->delSchool($school_id);

			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}



	//设置地理围栏
	public function set_wl(){
		$school_id = I('school_id','','intval');
		if(!$school_id){
			$this->error('对不起，非法访问！', '/AcpSchool/list_school');
		}
		$school_obj = new SchoolModel();
		$school_info = $school_obj->getSchool($school_id);
		
		if (!$school_info) {
			$this->error('对不起，不存在相关学校！', '/AcpSchool/list_school');
		}
		$act = I('act');
		if($act == 'save'){
			
			$points = $_POST['points'];
			if(!$points){
				$this->error('请设置地理围栏');
			}
			$data = array(
				'school_id' => $school_id,
				'wl' =>$points
				);
			$wl_obj = new SchoolWlModel();
			if($wl_obj->setWl($school_id, $data)){
				$this->success('地理围栏设置成功');
			}
			$this->error('地理围栏设置失败');
		}
		$wl_obj = new SchoolWlModel();
		$wl_info = $wl_obj->getWl('school_id ='.$school_id);
		$wl = $wl_info['wl'];
		
		$this->assign('wl',$wl);
		$this->assign('school_info', $school_info);
		$this->assign('head_title', '设置地理围栏');
		$this->display();
	}
}