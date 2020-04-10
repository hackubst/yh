<?php
/**
 * 页面模型类
 */

class PageModel extends Model
{
    // 页面id
    public $page_id;

    /**
     * 构造函数
     * @author wzg
     * @param $page_id 页面ID
     * @return void
     * @todo 初始化页面id
     */
    public function PageModel($page_id)
    {
        parent::__construct('page');

        if ($page_id = intval($page_id))
		{
            $this->page_id = $page_id;
		}
    }

    /**
     * 获取页面信息
     * @author wzg
     * @param int $page_id 页面id
     * @param string $fields 要获取的字段名
     * @return array 页面基本信息
     * @todo 根据where查询条件查找页面表中的相关数据并返回
     */
    public function getPageInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改页面信息
     * @author wzg
     * @param array $arr 页面信息数组
     * @return boolean 操作结果
     * @todo 修改页面信息
     */
    public function editPage($arr)
    {
        return $this->where('page_id = ' . $this->page_id)->save($arr);
    }

    /**
     * 添加页面
     * @author wzg
     * @param array $arr 页面信息数组
     * @return boolean 操作结果
     * @todo 添加页面
     */
    public function addPage($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除页面
     * @author wzg
     * @param int $page_id 页面ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPage($page_id)
    {
        if (!is_numeric($page_id)) return false;
		return $this->where('page_id = ' . $page_id)->delete();
    }

    /**
     * 根据where子句获取页面数量
     * @author wzg
     * @param string|array $where where子句
     * @return int 满足条件的页面数量
     * @todo 根据where子句获取页面数量
     */
    public function getPageNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询页面信息
     * @author wzg
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 页面基本信息
     * @todo 根据SQL查询字句查询页面信息
     */
    public function getPageList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取页面列表页数据信息列表
     * @author wzg
     * @param array $page_list
     * @return array $page_list
     * @todo 根据传入的$page_list获取更详细的页面列表页数据信息列表
     */
    public function getListData($page_list)
    {
		foreach ($page_list AS $k => $v)
		{
			//获取页面类型名称
            $page_list[$k]['page_type_name'] = self::convertPageType($v['page_type']);
		}

		return $page_list;
    }

    /**
     * 判断页面下是否有商户（删除除外）
     * @author wzg
     * @return array
     * @todo merchant,users
     * */
    public function is_have_merchant($page_id)
    {
        $merchant_obj = D('Merchant');
        $merchant_list = $merchant_obj->getMerchantList('merchant_id', 'page_id = ' . $page_id);
        $count = 0;
        if($merchant_list) {
            foreach ($merchant_list AS $k => $v)
            {
                $user_obj = D('Users');
                $is_enable = $user_obj->where('user_id = ' . $v['merchant_id'])->getField('is_enable');
                if(7 == $is_enable) {
                    continue;
                } else {
                    $count += 1;
                }

            }
        }

        return $count;
    }

    /**
     * 获取页面类型列表
     * @author jw
     * @return array
     * @todo 返回数组
     * */
    public static function getPageTypeList()
    {
		return array(
			'1'	=> 'mall_home',
			'2'	=> 'item_list',
			'3'	=> 'item_detail',
			'4'	=> 'group_buy',
		);
	}

    /**
     * 获取页面类型名称
     * @author jw
     * @return array
     * @todo 返回数组
     * */
    public static function convertPageType($page_type)
    {
		$arr = array(
			'1'	=> '首页',
			'2'	=> '商品列表页',
			'3'	=> '商品详情页',
			'4'	=> '团购页',
		);
		return $arr[$page_type];
	}

	//使用某个模板，先调用接口获取模板信息，存入数据库（若已存在，则更新；否则新增），然后下载zip包，解压到相应目录（若目录存在，则覆盖之）
	function usePage($page_id)
	{
		/*** 获取模板信息并入库begin ***/
		//获取模板信息
		$data = array(
			'page_id'			=> $page_id,
		);
		$page_info = $this->call_api('crm.templet.getPageInfo', $data);
		
		if (!$page_info)
		{
			return -1;
		}
		$page_info = $page_info['data'];

		//入库
		$page_obj = new PageModel();
		$page_num = $page_obj->getPageNum('page_id = ' . $page_id);
		if (!$page_num)
		{
			$page_obj->addPage($page_info);
		}
		else
		{
			$page_obj->page_id = $page_info['page_id'];
			$page_obj->editPage($page_info);
		}
		#echo $page_obj->getLastSql();
		#die;
		/*** 获取模板信息并入库end ***/

		/*** 下载zip包begin ***/
		$data = array(
			'api_name'			=> 'crm.templet.getZip',
			'appid'				=> C('CRM_APPID'),
			'page_id'			=> $page_id,
		);
		$token = generateSign($data);
		$host = C('TEMPLET_HOST') ? C('TEMPLET_HOST') : 'http://crm.com';
		$from_path = $host . '/api/api/appid/' . C('CRM_APPID') . '/api_name/crm.templet.getZip/page_id/' . $page_id . '/token/' . $token;
		$to_path = 'Uploads/' . time() . '.zip';
		#$result = file_get_contents($from_path);
		#dump($result);
		#die;

		//下载zip包
		put_file_from_url_content($from_path, $to_path);
		/*** 下载zip包end ***/

		//删除原有的目录
		$page_type_list = PageModel::getPageTypeList();
		$page_type = $page_type_list[$page_info['page_type']];
		#$file_name = APP_PATH . 'Tpl/Public/' . $page_type . '/' . $page_info['file_name'] . '/';
		$file_name = APP_PATH . 'Tpl/Public/' . $page_type . '/';
		#echo $file_name;
		if (file_exists($file_name))
		{
			deldir($file_name);
		}
		else
		{
			mkdir($file_name);
		}

		//解压缩
		#$command = 'unzip ' . $to_path . ' -d ' . $file_name;
		$command = PHP_OS == 'WINNT' ? ('expand ' . $to_path . ' ' . $file_name) : ('unzip ' . $to_path . ' -d ' . $file_name);
		log_file('file_name = ' . $file_name, 'unzip', true);
		log_file($command, 'unzip', true);
		$success = @exec($command);
		//删除zip包
		unlink($to_path);
		#$success = @system($command);
		#dump($success);
		#echo $command;
		#die;

		//将更新配置项中的模板配置
		$templet_info = json_decode($GLOBALS['config_info']['TEMPLET_INFO'], true);
		#dump($templet_info);
		#dump($page_type);
		#dump($page_id);
		$templet_info[$page_type] = $page_id;
		#dump($templet_info);
		$config_obj = new ConfigBaseModel();
		$success = $config_obj->setConfig('templet_info', json_encode($templet_info));
		$GLOBALS['config_info']['TEMPLET_INFO'] = json_encode($templet_info);
		#echo $config_obj->getLastSql();
		return $success;
	}

	function call_api($api_name, $params = array())
	{
		$data = array(
			'api_name'	=> $api_name,
			'appid'		=> C('CRM_APPID'),
		);
		$data = array_merge($data, $params);
		$token = generateSign($data);
		$host = C('TEMPLET_HOST') ? C('TEMPLET_HOST') : 'http://crm.com';
		$url = $host . '/api/api/token/' . $token;
		foreach ($data AS $k => $v)
		{
			$url .= '/' . $k . '/' . $v;
		}
		$result = file_get_contents($url);
		#echo "<pre>";
		#print_r(json_decode($result, true));
		#dump($result);
		#die;
		#echo $url;
		#echo $api_name;
		#dump($result);
		$result = json_decode($result, true);
		return $result;
	}

	//更新某个模板，先调用接口获取模板信息，对比版本号，若版本号一致，直接退出即可；若版本号不一致，更新数据库，然后下载zip包，解压到相应目录（若目录存在，则覆盖之）
	function updatePage($page_id)
	{
		/*** 获取模板信息并入库begin ***/
		//获取模板信息
		$data = array(
			'page_id'			=> $page_id,
		);
		$page_info = $this->call_api('crm.templet.getPageInfo', $data);
		if (!$page_info)
		{
			return -1;
		}
		$page_info = $page_info['data'];

		//对比版本号
		$page_obj = new PageModel();
		$cur_page_info = $page_obj->getPageInfo('page_id = ' . $page_id, 'version');
		if ($cur_page_info['version'] == $page_info['version'])
		{
			//版本号一致
			return -1;
		}

		//版本号不一致，更新
		$page_obj->page_id = $page_info['page_id'];
		$page_obj->editPage($page_info);
		#echo $page_obj->getLastSql();
		#die;
		/*** 获取模板信息并入库end ***/

		/*** 下载zip包begin ***/
		$data = array(
			'api_name'			=> 'crm.templet.getZip',
			'appid'				=> C('CRM_APPID'),
			'page_id'			=> $page_id,
		);
		$token = generateSign($data);
		$host = C('TEMPLET_HOST') ? C('TEMPLET_HOST') : 'http://crm.com';
		$from_path = $host . '/api/api/appid/' . C('CRM_APPID') . '/api_name/crm.templet.getZip/page_id/' . $page_id . '/token/' . $token;
		$to_path = 'Uploads/' . time() . '.zip';
		$result = file_get_contents($from_path);
		#dump($result);
		#die;

		//下载zip包
		put_file_from_url_content($from_path, $to_path);
		/*** 下载zip包end ***/

		//删除原有的目录
		$page_type_list = PageModel::getPageTypeList();
		$page_type = $page_type_list[$page_info['page_type']];
		#$file_name = APP_PATH . 'Tpl/Public/' . $page_type . '/' . $page_info['file_name'] . '/';
		$file_name = APP_PATH . 'Tpl/Public/' . $page_type . '/';
		#echo $file_name;
		if (file_exists($file_name))
		{
			deldir($file_name);
		}

		//解压缩
		#$command = 'unzip ' . $to_path . ' -d ' . $file_name;
		$command = PHP_OS == 'WINNT' ? ('expand ' . $to_path . ' ' . $file_name) : ('unzip ' . $to_path . ' -d ' . $file_name);
		log_file('file_name = ' . $file_name, 'unzip', true);
		log_file($command, 'unzip', true);
		$success = @exec($command);
		//删除zip包
		unlink($to_path);
		#$success = @system($command);
		#dump($success);
		#echo $command;
		#die;
	}

	//根据类型获取CRM上的模板列表
	function getRemotePageList($page_type)
	{
		/*** 获取模板信息并入库begin ***/
		//获取模板信息
		$data = array(
			'page_type'	=> $page_type,
		);
		$result = $this->call_api('crm.templet.getRemotePageList', $data);
		#dump($result);
		#die;
		if (!$result)
		{
			return -1;
		}
		$page_list = $result['data'];

		return $page_list;
	}

	//根据类型获取CRM上的模板套装列表
	function getRemoteTempletList($templet_type)
	{
		/*** 获取模板信息并入库begin ***/
		//获取模板信息
		$data = array(
			'templet_type'	=> $templet_type,
		);
		$result = $this->call_api('crm.templet.getRemoteTempletList', $data);
		if (!$result)
		{
			return -1;
		}
		$templet_list = $result['data'];

		return $templet_list;
	}

	//根据套装ID获取CRM上的模板套装详情
	function getRemoteTempletDetail($templet_package_id)
	{
		/*** 获取模板信息并入库begin ***/
		//获取模板信息
		$data = array(
			'templet_package_id'	=> $templet_package_id,
		);
		$result = $this->call_api('crm.templet.getRemoteTempletDetail', $data);
		if (!$result)
		{
			return -1;
		}
		$page_list = $result['data'];

		return $page_list;
	}

	//使用某个套装
	function usePackage($templet_package_id)
	{
		$page_list = $this->getRemoteTempletDetail($templet_package_id);
		if (!$page_list)
		{
			return -1;
		}

		foreach ($page_list AS $k => $v)
		{
			#echo $v['page_id'] . "<br>";
			$this->usePage($v['page_id']);
		}

		//更新配置项中的模板ID
		$config_obj = new ConfigBaseModel();
		$config_obj->setConfig('cur_templet_package_id', $templet_package_id);
		#die;
	}
}
