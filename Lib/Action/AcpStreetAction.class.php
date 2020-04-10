<?php
/**
 *区域分类类
 * */
class AcpStreetAction extends AcpAction
{
	/**
     * 构造函数
     * @author wzg 
     * @return void
     * @todo 
     */
	public function AcpStreetAction()
	{
		parent::_initialize();
	}

    /**
     * 查看街道列表
     * @author wzg
     * @return void
     * @todo 从街道表中取出数据
     */
    public function get_street_list()
    {
        $where = 'isuse <> 2';
        $street_obj = D('Street');
        //数据总量
        $num   = $street_obj->getStreetNum($where);
        //处理分页
        import('ORG.Util.Pagelist');
        $per_page_num = C('PER_PAGE_NUM');
        $Page = new Pagelist($num, $per_page_num);
        $street_obj->setStart($Page->firstRow);
        $street_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show',$show);
        #echo $show;die;
        
        $street_list = $street_obj->getStreetList('', $where);
        $street_list = $street_obj->getListData($street_list);
        #dump($street_list);die;

        $this->assign('head_title', '街道列表');
        $this->assign('street_list', $street_list);
        $this->display();

    }

    /**
     * 添加街道
     * @author wzg
     * @return void
     * @todo 添加数据到street表
     * */
    public function add_street()
    {
        $act = I('act');
        if ('add' == $act)
        {
            $street_name = I('street_name', '', 'strip_tags');
            $isuse       = I('isuse', -1, 'int');
            $serial      = I('serial', 0, 'int');
            $province_id = I('province_id', 0, 'int');
            $city_id     = I('city_id', 0, 'int');
            $area_id     = I('area_id', 0, 'int');

            if (!$province_id || !$city_id || !$area_id) {
                $this->error('请选择所在城市');
            }

            if (!$street_name) {
            
                $this->error('街道名称不能为空');
            }
            if (!$serial) {
            
                $this->error('排序号不能为空');
            }


            $data = array(

                'street_name' => $street_name,
                'isuse'       => $isuse,
                'serial'      => $serial,
                'province_id' => $province_id,
                'city_id'      => $city_id,
                'area_id'      => $area_id,
            );

            $street_obj = D('Street');
            if (!$street_obj->add($data)) {

                $this->error('添加失败');
            } else {

                $this->success('添加成功');
            }

        }

        //地址列表
        $province_list = M('AddressProvince')->field('province_id, province_name')->select();

        $this->assign('province_list', $province_list);
        $this->assign('head_title', '添加街道');
        $this->display();
    }

    /**
     * 编辑街道
     * @author wzg
     * @return void
     * @todo 修改街道表中的街道
     * */
     public function edit_street()
     {
        $street_id = I('get.street_id', 0, 'int');
        $street_obj = D('Street');
        $street_info = $street_obj->getStreetInfo('street_id = ' . $street_id);
        $act       = I('act');
        if ('add' == $act)
        {
            $street_name = I('street_name', '', 'strip_tags');
            $isuse       = I('isuse', 0, 'int');
            $serial      = I('serial', 88, 'int');
            $province_id = I('province_id', 0, 'int');
            $city_id     = I('city_id', 0, 'int');
            $area_id     = I('area_id', 0, 'int');

            if (!$province_id || !$city_id || !$area_id) {
                $this->error('请选择所在城市');
            }
            if (!$street_name) {
            
                $this->error('一级分类名称不能为空');
            }
            if (!$serial) {
            
                $this->error('排序号不能为空');
            }
            $data = array(

                'street_name' => $street_name,
                'isuse'       => $isuse,
                'serial'      => $serial,
                'province_id' => $province_id,
                'city_id'      => $city_id,
                'area_id'      => $area_id,
            );

            $state = $street_obj->where('street_id = ' . $street_id)->save($data);
            if (!state) {

                $this->error('修改失败');
                
            } else {

                $this->success('修改成功');
            }
        }

        //地址列表
        $province_list = M('AddressProvince')->field('province_id, province_name')->select();
        $city_list = M('AddressCity')->field('city_id, city_name')->where('province_id = ' . $street_info['province_id'])->select();
        $area_list = M('AddressArea')->field('area_id, area_name')->where('city_id = ' . $street_info['city_id'])->select();

        $this->assign('province_list', $province_list);
        $this->assign('city_list', $city_list);
        $this->assign('area_list', $area_list);
        $this->assign('street_info', $street_info);
        $this->assign('head_title', '修改街道');
        $this->display();
     }

    /**
     * 删除街道
     * @author wzg
     * @return viod
     * @todo 从街道表中删除街道，假删除（只修改表中is_enable = 2）
     * */
    public function delete_street()
    {
        $street_id = I('street_id', 0, 'int');
        if(!$street_id) exit('failure');

        //街道下是否有二级
        $count = M('AreaSort')->where('isuse = 1 AND street_id = ' . $street_id)->count();
        if($count) exit('have');

        $data = array('isuse' => 2);
        $state = M('Street')->where('street_id = ' . $street_id)->setField($data);
        if ($state) {
            exit('success');
        } else {
            exit('failure');
        }
    }

    /**
     * 批量删除街道
     * @author wzg 
     * @return viod
     * @todo 修改街道表中的 isuse
     * */
    public function batch_delete_street()
    {
        $street_ids = I('street_ids', '', 'strip_tags');

		if ($street_ids) {

            $merchant_obj = D('Merchant');
            $additional_service_obj = D('AdditionalService');

			$street_id_ary = explode(',', $street_ids);
			$success_num = 0;
            $street_obj = D('Street');
			foreach ($street_id_ary AS $street_id)
			{
                //街道下是否有二级
                $count = M('AreaSort')->where('isuse = 1 AND street_id = ' . $street_id)->count();
                if($count) continue;

                $data = array('isuse' => 2);
				$success_num += $street_obj->where('street_id = ' . $street_id)->setField($data);
			}
			exit($success_num ? 'success' : 'failure');
		
		}

		exit('failure');
    }
}
