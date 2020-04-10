<?php
/**
 * 活动类
 */

class AcpActivityAction extends AcpAction
{
	public function AcpActivityAction()
	{
		parent::_initialize();
	}

	private function get_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//标题
		$marketing_rule_name = $this->_request('marketing_rule_name');
		if ($marketing_rule_name)
		{
			$where .= ' AND marketing_rule_name LIKE "%' . $marketing_rule_name . '%"';
		}

		//状态
		$isuse = $this->_request('isuse');
		if (is_numeric($isuse) && $isuse != -1)
		{
			$where .= ' AND isuse LIKE "%' . $isuse . '%"';
		}

		#echo $where;
		//重新赋值到表单
		$this->assign('marketing_rule_name', $marketing_rule_name);
		$this->assign('isuse', $isuse);

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $marketing_rule_name ? '/marketing_rule_name/' . $marketing_rule_name : '';
		$redirect .= $isuse ? '/isuse/' . $isuse : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取活动列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取活动列表，公共方法
     */
	public function get_marketing_rule_list()
	{
		$where = 'isuse = 1';
		$where .= $this->get_search_condition();
		$marketing_rule_obj = new MarketingRuleModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $marketing_rule_obj->getMarketingRuleNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$marketing_rule_obj->setStart($Page->firstRow);
        $marketing_rule_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$marketing_rule_list = $marketing_rule_obj->getMarketingRuleList('', $where, ' addtime DESC');
		$marketing_rule_list = $marketing_rule_obj->getListData($marketing_rule_list);
		$this->assign('marketing_rule_list', $marketing_rule_list);

		$this->assign('head_title', '首页活动列表');

		$this->display();

	}

    /**
     * @access public
     * @todo 添加活动
     *
     */
    public function add_marketing_rule()
    {
		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$marketing_type = $this->_post('marketing_type');
			$start_time	 = $this->_post('start_time');
			$end_time	 = $this->_post('end_time');

			$marketing_rule_name = $this->_post('marketing_rule_name');
			$contents	 = $this->_post('contents');
			$imgurl	 = $this->_post('imgurl');

			if(!$marketing_type)
			{
				$this->error('对不起，请选择活动类型');
			}
			if(!$start_time)
			{
				$this->error('对不起，请选择活动开始时间');
			}
			if(!$end_time)
			{
				$this->error('对不起，请选择活动结束时间');
			}
			if(!$marketing_rule_name)
			{
				$this->error('对不起，请填写活动名称');
			}
			if(!$imgurl)
			{
				$this->error('对不起，请上传图片');
			}
			if(!$contents)
			{
				$this->error('对不起，请填写活动内容');
			}

			$data = array(
					'marketing_type'=>	$marketing_type,
					'start_time'	=>	strtotime($start_time),
					'end_time'	=>	strtotime($end_time),
					'marketing_rule_name'=>	$marketing_rule_name,
					'imgurl'	=>	$imgurl,
					'contents'	=>	$contents,
			);
            // $this->_resizeImg(APP_PATH . ltrim($pic, '/'));

			$marketing_rule_obj = new MarketingRuleModel();
			$marketing_rule_id = $marketing_rule_obj->addMarketingRule($data);
			#echo "<pre>";
			#print_r($data);
			#die;
			if ($marketing_rule_id)
			{
				$this->success('恭喜您，活动添加成功','/AcpActivity/get_marketing_rule_list');
			}
			else
			{
				$this->success('抱歉，添加失败');
			}
		}

        $this->assign('pic_data', array(
            'name' => 'imgurl',
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

		$this->assign('head_title','添加活动');
		$this->display();
	}

    /**
     * @access public
     * @todo 修改活动
     *
     */
    public function edit_marketing_rule()
    {
		$redirect = $this->_get('redirect');
		$marketing_rule_id = intval($this->_get('marketing_rule_id'));
		$marketing_rule_obj = new MarketingRuleModel();
		$marketing_rule_info = $marketing_rule_obj->getMarketingRuleInfo('marketing_rule_id = ' . $marketing_rule_id, '');
		#echo $marketing_rule_obj->getLastSql();
		#echo "<pre>";
		#print_r($marketing_rule_info);
		#die;

		if (!$marketing_rule_info)
		{
			$this->error('抱歉，活动不存在', U('/AcpActivity/get_marketing_rule_list'));
		}

		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$marketing_type = $this->_post('marketing_type');
			$start_time	 = $this->_post('start_time');
			$end_time	 = $this->_post('end_time');

			$marketing_rule_name = $this->_post('marketing_rule_name');
			$contents	 = $this->_post('contents');
			$imgurl	 = $this->_post('imgurl');

			if(!$marketing_type)
			{
				$this->error('对不起，请选择活动类型');
			}
			if(!$start_time)
			{
				$this->error('对不起，请选择活动开始时间');
			}
			if(!$end_time)
			{
				$this->error('对不起，请选择活动结束时间');
			}
			if(!$marketing_rule_name)
			{
				$this->error('对不起，请填写活动名称');
			}
			if(!$imgurl)
			{
				$this->error('对不起，请上传图片');
			}
			if(!$contents)
			{
				$this->error('对不起，请填写活动内容');
			}

			$data = array(
					'marketing_type'=>	$marketing_type,
					'start_time'	=>	strtotime($start_time),
					'end_time'	=>	strtotime($end_time),
					'marketing_rule_name'=>	$marketing_rule_name,
					'imgurl'	=>	$imgurl,
					'contents'	=>	$contents,
			);

			$marketing_rule_obj = new MarketingRuleModel($marketing_rule_id);
			$success = $marketing_rule_obj->editMarketingRule('marketing_rule_id ='.$marketing_rule_id,$data);
            // echo $marketing_rule_obj->getLastSql();die;
			if ($success)
			{
				$this->success('恭喜您，活动修改成功','/AcpActivity/get_marketing_rule_list');
			}
			else
			{
				$this->success('抱歉，修改失败');
			}
		}

        $this->assign('pic_data', array(
            'name' => 'imgurl',
            'url' => $marketing_rule_info['imgurl'],
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));
        $marketing_rule_info['contents'] = htmlspecialchars_decode($marketing_rule_info['contents']);
        // dump($marketing_rule_info);die;
        $this->assign('marketing_rule_info',$marketing_rule_info);
		$this->assign('head_title','修改活动');
		$this->display();
	}

	//删除活动
	public function del_marketing_rule()
    {
		$id        = intval($this->_post('id'));
		if ($id)
		{

            $marketing_rule_obj = new MarketingRuleModel($id);
            $success = $marketing_rule_obj->delMarketingRule($id);

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
    }

	/**
	 * 获取广告图片列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取广告图片列表，公共方法
     */
	function adv_list($where = '', $head_title = '', $opt = '')
	{
		$where .= $this->get_search_condition();
		$adv_obj = D('Adv');

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $adv_obj->getAdvNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
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
     * @todo 添加活动
     *
     */
    public function add_adv()
    {
		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$title	 = $this->_post('title');
			$link = $this->_post('link');
			$serial = $this->_post('serial');
			$pic	 = $this->_post('pic');
			$isuse	 = $this->_post('isuse');


			if(!is_numeric($serial))
			{
				$this->error('对不起，排序号必须为0-255的整数，请重新输入');
			}

			if(!$pic || !file_exists(APP_PATH . $pic))
			{
				$this->error('对不起，请上传图片');
			}

			$data = array(
					'title'	=>	$title,
					'link'	=>	$link,
					'serial'=>	$serial,
					'pic'	=>	$pic,
					'isuse'	=>	$isuse,
					'adv_type' => 1,
			);
            $this->_resizeImg(APP_PATH . ltrim($pic, '/'));

			$adv_obj = new AdvModel();
			$adv_id = $adv_obj->addAdv($data,2);
			#echo "<pre>";
			#print_r($data);
			#die;
			if ($adv_id)
			{
				$this->success('恭喜您，图片添加成功','/AcpCustFlash/top_adv_list');
			}
			else
			{
				$this->success('抱歉，添加失败');
			}
		}

		$this->assign('head_title','添加活动');
		$this->display();
	}

    /**
     * @access public
     * @todo 修改活动
     *
     */
    public function edit_adv()
    {
		$redirect = $this->_get('redirect');
		$adv_id = intval($this->_get('adv_id'));
		$adv_obj = new AdvModel();
		$adv_info = $adv_obj->getAdvInfo('adv_id = ' . $adv_id, '');
		#echo $adv_obj->getLastSql();
		#echo "<pre>";
		#print_r($adv_info);
		#die;

		if (!$adv_info)
		{
			$this->error('抱歉，广告图位不存在', U('/AcpCustFlash/top_adv_list'));
		}

		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$title	 = $this->_post('title');
			$link = $this->_post('link');
			$serial = $this->_post('serial');
			$pic	 = $this->_post('pic');
			$isuse	 = $this->_post('isuse');



			if(!is_numeric($serial))
			{
				$this->error('对不起，排序号必须为0-255的整数，请重新输入');
			}

			if(!$pic || !file_exists(APP_PATH . $pic))
			{
				$this->error('对不起，请上传图片');
			}

			if(!is_numeric($isuse))
			{
				$this->error('对不起，请选择是否显示');
			}

			$data = array(
					'title'	=>	$title,
					'link'	=>	$link,
					'serial'=>	$serial,
					'pic'	=>	$pic,
					'isuse'	=>	$isuse,
			);
			if($adv_info['pic'] != $pic)
            	$this->_resizeImg(APP_PATH . ltrim($pic, '/'));

			$adv_obj = new AdvModel($adv_id);
			$success = $adv_obj->editAdv($data);
			if ($success)
			{
				$this->success('恭喜您，图片修改成功');
			}
			else
			{
				$this->success('抱歉，修改失败');
			}
		}

		foreach ($adv_info AS $k => $v)
		{
			if ($k == 'pic')
			{
				$this->assign('pic_img_path', APP_PATH . $v);
			}
			if ($k == 'link')
			{
				$this->assign('adv_link', $v);
			}
			$this->assign($k, $v);
		}

		$this->assign('head_title','修改广告图片');
		$this->display();
	}

	//删除轮播图
	public function del_adv()
    {
		$id        = intval($this->_post('id'));
		if ($id)
		{

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
    protected function _resizeImg($base_img) {
        import('ORG.Util.Image');
        $Image = new Image();

        $arr_img = array();

        if (!is_file($base_img)) return $arr_img;

        $base_img_info = pathinfo($base_img);
        $img_path = $base_img_info['dirname'] . '/';
        $img_extension = $base_img_info['extension'];
        $img_name = str_replace('.' . $img_extension, '', $base_img_info['basename']);

        /***** 等比缩放 开始 *****/

        // 生成大图
        $big_img_path = $img_path . $img_name . C('BIG_IMG_SUFFIX') . '.' . $img_extension;
        $big_img = $Image->thumb($base_img, $big_img_path, $img_extension, C('BIG_IMG_SIZE'), C('BIG_IMG_SIZE'));

        // 生成中图
        $middle_img_path = $img_path . $img_name . C('MIDDLE_IMG_SUFFIX') . '.' . $img_extension;
        $middle_img = $Image->thumb($base_img, $middle_img_path, $img_extension, C('MIDDLE_IMG_SIZE'), C('MIDDLE_IMG_SIZE'));

        // 生成小图
        $small_img_path = $img_path . $img_name . C('SMALL_IMG_SUFFIX') . '.' . $img_extension;
        $small_img = $Image->thumb($base_img, $small_img_path, $img_extension, C('SMALL_IMG_WIDTH'), C('SMALL_IMG_HEIGHT'));
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
?>
