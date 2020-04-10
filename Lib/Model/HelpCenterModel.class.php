<?php
/**
 * 帮助中心Model
 *
 * @author zhengzhen
 * @date 2014/3/18
 *
 */
class HelpCenterModel extends Model
{
	protected $tableName = 'help';
	
	
	/**
	 * 添加帮助，包括表tp_help、tp_help_txt相关记录插入
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_help、tp_help_txt插入一条记录
	 *
	 */
	public function addHelp(array $data, $isReplace = true)
    {
		$_data = array();
		$_data['title'] 	   		= $data['title'];
		$_data['help_sort_id'] 		= $data['help_sort_id'];
		$_data['serial'] 	   		= isset($data['serial']) ? $data['serial'] : 0;
		$_data['isuse'] 	   		= isset($data['isuse']) ? $data['isuse'] : 1;
		$_data['is_navigator_show'] = isset($data['is_navigator_show']) ? $data['is_navigator_show'] : 0;
		$_data['is_bottom_show'] 	= isset($data['is_bottom_show']) ? $data['is_bottom_show'] : 0;
		$_data['addtime'] 	= time();
		
		if($id = $this->_saveHelpInfo($_data))
		{
			if($data['contents'])
			{
				$_data = array();
				$_data['help_id'] = $id;
				$_data['contents'] = $data['contents'];
				$this->_saveHelpContents($_data, null, $isReplace);
			}
			return $id;
		}
		return false;
	}
	
	/**
	 * 修改帮助，包括表tp_help、tp_help_txt相关记录修改
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回int，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_help、tp_help_txt中help_id为$id数据为$data
	 *
	 */
	public function saveHelp($id, array $data, $isReplace = true)
    {
		$_data = array();
		$_data['title'] 	   		= $data['title'];
		$_data['help_sort_id'] 		= $data['help_sort_id'];
		$_data['serial'] 	   		= isset($data['serial']) ? $data['serial'] : 0;
		$_data['isuse'] 	   		= isset($data['isuse']) ? $data['isuse'] : 1;
		$_data['is_navigator_show'] = isset($data['is_navigator_show']) ? $data['is_navigator_show'] : 0;
		$_data['is_bottom_show'] 	= isset($data['is_bottom_show']) ? $data['is_bottom_show'] : 0;
		
		if(false !== $this->_saveHelpInfo($_data, $id))
		{
			$_data = array();
			if($this->getHelpContents($id))
			{
				if(!$data['contents'])
				{
					return $this->deleteHelpContents($id);
				}
				$_data['contents'] = $data['contents'];
				return $this->_saveHelpContents($_data, $id, $isReplace);
			}
			else
			{
				$_data['help_id'] = $id;
				$_data['contents'] = $data['contents'];
				return $this->_saveHelpContents($_data, null, $isReplace);
			}
		}
		return false;
	}
	
    /**
	 * 保存帮助基本信息
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param int $id 文章ID，默认null
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_help表插入一条记录
	 *
	 */
    protected function _saveHelpInfo(array $data, $id = null)
    {
		if(!isset($id))
		{
			$this->create($data);
			return $this->add();
		}
		else
		{
			if($id < 0)
			{
				return false;
			}
			$this->create($data);
			return $this->where('help_id=' . $id)->save();
		}
    }
	
	/**
	 * 保存帮助详情
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param int $id 文章ID，默认null
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_help_txt表中插入一条记录
	 *
	 */
	protected function _saveHelpContents(array $data, $id = null, $isReplace = true)
	{
		if(!$data['contents'])
		{
			return false;
		}
		elseif($isReplace)
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$helpTxt = M('help_txt');
		if(!isset($id))
		{
			$helpTxt->create($data);
			return $helpTxt->add();
		}
		else
		{
			if($id < 0)
			{
				return false;
			}
			$helpTxt->create($data);
			return $helpTxt->where('help_id=' . $id)->save();
		}
	}
	
	/**
	 * 保存帮助详情图片链接
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_help_txt_photo表插入一条记录
	 *
	 */
    public function saveHelpPhoto(array $data)
    {
		if(!$data['path_img'])
		{
			return false;
		}
		$data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
		$helpTxtPhoto = M('help_txt_photo');
		$helpTxtPhoto->create($data);
		return $helpTxtPhoto->add();
    }
    
    /**
	 * 获取帮助基本信息
	 *
	 * @param int $id 文章ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回帮助信息，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件help_id为$id，查询表tp_help表中$fields字段值
	 *
	 */
    public function getHelpInfo($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}
		
		$_this = $this;
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		
		return $_this->where('help_id=' . $id)->find();
    }
	
	/**
	 * 获取帮助详情
	 *
	 * @param int $id 文章ID
	 * @param bool $isReplace 是否替换详情中图片域名占位符'##img_domain##'为实际域名C('IMG_DOMAIN')，默认true
	 * @return mixed 成功返回帮助详情，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件help_id为$id，查询表tp_help_txt中contents字段值
	 *
	 */
    public function getHelpContents($id, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		$helpTxt = M('help_txt');
		$result = $helpTxt->where('help_id=' . $id)->getField('contents');
		if($result && $isReplace)
		{
			//实体转换
			$result = htmlspecialchars_decode($result);
			$result = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $result);
		}
		return $result;
    }
	
	/**
	 * 通过文章ID获取帮助详情图片
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回帮助详情图片数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_help_txt_photo中help_id为$id的文章图片路径path_img
	 *
	 */
	public function getHelpPhotos($id)
	{
		if($id < 0)
		{
			return false;
		}
		$helpTxtPhoto = M('help_txt_photo');
		return $helpTxtPhoto->where('help_id=' . $id)->getField('path_img', true);
	}
    
    /**
	 * 修改帮助基本信息
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_help中help_id为$id信息为$data
	 *
	 */
    public function setHelpInfo($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('help_id=' . $id)->setField($data);
    }
	
	/**
	 * 修改帮助详情
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isReplace 是否替换详情中图片链接域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_help_txt中help_id为$id的信息为$data
	 *
	 */
    public function setHelpContents($id, array $data, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		if($isReplace && $data['contents'])
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$helpTxt = M('help_txt');
		return $helpTxt->where('help_id=' . $id)->setField($data);
    }
	
	/**
	 * 设置帮助排序
	 *
	 * @param int $id 文章ID
	 * @param int $serial 排序号
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 *
	 */
	public function setSerial($id, $serial)
	{
		return $this->setHelpInfo($id, array('serial' => $serial));
	}
	
	/**
	 * 删除帮助内容
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_help_txt中help_id为$id的记录
	 *
	 */
	public function deleteHelpContents($id)
	{
		$helpTxt = M('help_txt');
		//删除文章内容记录
		if($helpTxt->where('help_id=' . $id)->delete())
		{
			//删除文章图片
			$this->deleteHelpPhotos($id);
			return true;
		}
		return false;
	}
	
	/**
	 * 删除帮助内容中图片
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_help_txt_photo中help_id为$id的记录，同时删除图片文件
	 *
	 */
	public function deleteHelpPhotos($id)
	{
		$helpTxtPhoto = M('help_txt_photo');
		$helpPhotos = $helpTxtPhoto->where('help_id=' . $id)->getField('path_img', true);
		
		if($helpPhotos)
		{
			//删除物理图片
			foreach($helpPhotos as $key => $val)
			{
				if(strpos($val, '##img_domain##') === false)
				{
					$helpPhoto = APP_PATH . 'Uploads/' . $val;
				}
				else
				{
					$helpPhoto = str_replace('##img_domain##', APP_PATH . 'Uploads', $val);
				}
				@unlink($helpPhoto);
			}
			//删除文章图片记录
			return $helpTxtPhoto->where('help_id=' . $id)->delete();
		}
		return 0;
	}
    
    /**
	 * 删除帮助，包括删除表tp_help、tp_help_txt、tp_help_txt_photo的相应记录
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_help中help_id为$id的记录，
	 * @todo 同时删除关联表tp_help_txt、tp_help_txt_photo表中相应记录，以及删除相应图片文件
	 *
	 */
    public function deleteHelp($id)
    {
		if($id < 0)
		{
			return false;
		}
		//删除文章基本信息记录
		if($this->where('help_id=' . $id)->delete())
		{
			//删除文章内容
			$this->deleteHelpContents($id);
			return true;
		}
		return false;
    }
    
    /**
	 * 获取帮助列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @param string $join 联表查询，默认''
	 * @return mixed 成功返回帮助列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_help中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getHelpList($limit = '', $order = '', $fields = '', $where = '', $join = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		
		if($order)
		{
			$_this = $_this->order($order);
		}
		
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		
		if($where)
		{
			$_this = $_this->where($where);
		}
		
		if($join)
		{
			$_this = $_this->join($join);
		}
		
		return $_this->select();
    }
	
	/**
	 * 获取分页的帮助列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回帮助列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_help中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getHelpListPage($where = '', $rows = 15)
	{
		$total = $this->getTotal($where);
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$_table = $this->getTableName();
		$order = $_table . '.serial';
		$fields = $_table . '.help_id,' . $_table . '.title,' . $_table . '.serial,' .
				$_table . '.isuse,h_s.help_sort_name';
		$join = C('DB_PREFIX') . 'help_sort AS h_s ON h_s.help_sort_id=' . $_table . '.help_sort_id';
		$result = $this->getHelpList($limit, $order, $fields, $where, $join);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取指定ID帮助
	 *
	 * @param int $id 文章ID
	 * @param bool $isReplace 是否替换详情中图片域名占位符'##img_domain##'为实际域名C('IMG_DOMAIN')，默认true
	 * @return mixed
	 * @author zhengzhen
	 * @todo 获取表tp_help中help_id为$id所有字段值，同时获取表tp_help_txt中contents值
	 *
	 */
	public function getHelpById($id, $isReplace = true)
	{
		$helpInfo = $this->getHelpInfo($id);
		$helpInfo['contents'] = '';
		if($helpContents = $this->getHelpContents($id, $isReplace))
		{
			$helpInfo['contents'] = $helpContents;
		}
		return $helpInfo;
	}
	
	/**
	 * 通过help_tag获取帮助ID
	 *
	 * @param string $tag 文章标签
	 * @return mixed 成功返回文章ID，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_help中help_tag为$tag的help_id值
	 *
	 */
	public function getHelpIdByTag($tag)
	{
		return $this->where('help_tag="' . $tag . '"')->find();
	}
	
	/**
	 * 获取帮助中心文章总数
	 *
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回文章总数，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_help中help_sort_id为$id记录总数
	 *
	 */
	public function getTotal($where = '')
	{
		$_this = $this;
		if($where)
		{
			$_this = $_this->where($where);
		}
		return $_this->count();
	}
}
?>
