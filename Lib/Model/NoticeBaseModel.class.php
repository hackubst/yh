<?php
/**
 * 文章基类Model
 *
 * @author zhengzhen
 * @date 2014/2/21
 *
 */
class NoticeBaseModel extends Model
{
	protected $tableName = 'notice';
	
	
	/**
	 * 添加文章，包括表tp_notice、tp_notice_txt相关记录插入
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param bool $isFill 若keywords、description字段值，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_notice、tp_notice_txt插入一条记录
	 *
	 */
	public function addNotice(array $data, $isFill = true, $isReplace = true)
    {
		$_data = array();
		$_data['notice_sort_id'] = ($data['notice_sort_id'])?$data['notice_sort_id']:0;
		$_data['title'] 		  = $data['title'];
		$_data['author'] 		  = isset($data['author']) ? $data['author'] : '';
		$_data['notice_source']  = isset($data['notice_source']) ? $data['notice_source'] : '';
		$_data['notice_tag']  	  = isset($data['notice_tag']) ? $data['notice_tag'] : '';
		$_data['addtime'] 		  = isset($data['addtime']) ? $data['addtime'] : time();
		$_data['serial'] 		  = isset($data['serial']) ? $data['serial'] : 0;
		$_data['isuse'] 		  = isset($data['isuse']) ? $data['isuse'] : 1;
		
		if(isset($data['path_img']))
		{
			if(false !== strpos($data['path_img'], C('IMG_DOMAIN')))
			{
				$_data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
			}
			else
			{
				$_data['path_img'] = str_replace('/Uploads', '##img_domain##', $data['path_img']);
			}
		}
		$metaData = $this->_metaDataHandler($data, $isFill);
		$_data = array_merge($_data, $metaData);
		
		if($id = $this->_saveNoticeInfo($_data))
		{
			if($data['contents'])
			{
				$_data = array();
				$_data['notice_id'] = $id;
				$_data['contents'] = $data['contents'];
				$this->_saveNoticeContents($_data, null, $isReplace);
			}
			return $id;
		}
		return false;
	}
	
	/**
	 * 修改文章，包括表tp_notice、tp_notice_txt相关记录修改
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isFill 若keywords、description字段值，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return bool成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_notice、tp_notice_txt中notice_id为$id数据为$data
	 *
	 */
	public function saveNotice($id, array $data, $isFill = true, $isReplace = true)
    {
		$_data = array();
		$_data['notice_sort_id'] = $data['notice_sort_id'];
		$_data['title'] 		  = $data['title'];
		$_data['author'] 		  = isset($data['author']) ? $data['author'] : '';
		$_data['notice_source']  = isset($data['notice_source']) ? $data['notice_source'] : '';
		$_data['notice_tag']  	  = isset($data['notice_tag']) ? $data['notice_tag'] : '';
		$_data['serial'] 		  = isset($data['serial']) ? $data['serial'] : 0;
		$_data['isuse'] 		  = isset($data['isuse']) ? $data['isuse'] : 1;
		
		if(isset($data['path_img']))
		{
			if(false !== strpos($data['path_img'], C('IMG_DOMAIN')))
			{
				$_data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
			}
			else
			{
				$_data['path_img'] = str_replace('/Uploads', '##img_domain##', $data['path_img']);
			}
		}
		$metaData = $this->_metaDataHandler($data, $isFill);
		$_data = array_merge($_data, $metaData);
		
		if(false !== $this->_saveNoticeInfo($_data, $id))
		{
			$_data = array();
			if($this->getNoticeContents($id))
			{
				if(!$data['contents'])
				{
					return $this->deleteNoticeContents($id);
				}
				$_data['contents'] = $data['contents'];
				$this->_saveNoticeContents($_data, $id, $isReplace);
			}
			else
			{
				$_data['notice_id'] = $id;
				$_data['contents'] = $data['contents'];
				$this->_saveNoticeContents($_data, null, $isReplace);
			}
			return true;
		}
		return false;
	}
	
    /**
	 * 保存文章基本信息
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param int $id 文章ID，默认null
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 若$id未设置，则向表tp_notice中插入一条记录；否则更新表tp_notice中notice_id为$id的数据
	 *
	 */
    protected function _saveNoticeInfo(array $data, $id = null)
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
			return $this->where('notice_id=' . $id)->save();
		}
    }
	
	/**
	 * 保存文章内容详情
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param int $id 文章ID，默认null
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 若$id未设置，则向表tp_notice_txt中插入一条记录；否则更新表tp_notice_txt中notice_id为$id的数据
	 *
	 */
	protected function _saveNoticeContents(array $data, $id = null, $isReplace = true)
	{
		if(!$data['contents'])
		{
			return false;
		}
		elseif($isReplace)
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$noticeTxt = M('notice_txt');
		if(!isset($id))
		{
			$noticeTxt->create($data);
			return $noticeTxt->add();
		}
		else
		{
			if($id < 0)
			{
				return false;
			}
			$noticeTxt->create($data);
			$result = $noticeTxt->where('notice_id=' . $id)->save();
			return $result;
		}
	}
	
	/**
	 * 保存文章图片链接
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_notice_txt_photo表插入一条记录
	 *
	 */
    public function saveNoticePhoto(array $data)
    {
		if(!$data['path_img'])
		{
			return false;
		}
		$data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
		$noticeTxtPhoto = M('notice_txt_photo');
		$noticeTxtPhoto->create($data);
		return $noticeTxtPhoto->add();
    }
    
    /**
	 * 获取文章基本信息
	 *
	 * @param int $id 文章ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回文章信息，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件notice_id为$id，查询表tp_notice表中$fields字段值
	 *
	 */
    public function getNoticeInfo($id, $fields = '')
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
		
		$result = $_this->where('notice_id=' . $id)->find();
		if($result)
		{
			if($result['path_img'] && false !== strpos($result['path_img'], '##img_domain##'))
			{
				$result['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $result['path_img']);
			}
		}
		return $result;
    }
	
	/**
	 * 获取文章详情
	 *
	 * @param int $id 文章ID
	 * @param bool $isFormat 是否格式化文章详情，默认false
	 * @param bool $isReplace 是否替换详情中图片域名占位符'##img_domain##'为实际域名C('IMG_DOMAIN')，默认true
	 * @return mixed 成功返回文章内容，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件notice_id为$id，查询表tp_notice_txt中contents字段值，若$isFormat为true，则为表tp_notice_keywords中的关键字添加标签
	 *
	 */
    public function getNoticeContents($id, $isFormat = false, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		$noticeTxt = M('notice_txt');
		$result = $noticeTxt->where('notice_id=' . $id)->getField('contents');
		if(result)
		{
			//实体转换
			$result = htmlspecialchars_decode($result);
			if($isFormat)
			{
				//添加关键字标签
				$noticeKeywordsList = $this->getNoticeKeywordsList();
				if($noticeKeywordsList)
				{
					$lt = html_entity_encode('<');
					$gt = html_entity_encode('>');
					foreach($noticeKeywordsList as $key => $val)
					{
					//	$tag = '<a href="">' . $val['keyword'] .'</a>';
					//	$result = str_replace($val['keyword'], $tag, $result);
						$result = makeTag($result, $val['keyword']);
					}
				}
			}
			if($isReplace)
			{
				$result = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $result);
			}
		}
		return $result;
    }
	
	/**
	 * 通过文章ID获取文章详情图片
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回文章图片数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_txt_photo中notice_id为$id的文章图片路径path_img
	 *
	 */
	public function getNoticePhotos($id)
	{
		if($id < 0)
		{
			return false;
		}
		$noticeTxtPhoto = M('notice_txt_photo');
		return $noticeTxtPhoto->where('notice_id=' . $id)->getField('path_img', true);
	}
    
    /**
	 * 修改文章基本信息
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_notice中notice_id为$id信息为$data
	 *
	 */
    public function setNoticeInfo($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		if(isset($data['path_img']) && false !== strpos($data['path_img'], C('IMG_DOMAIN')))
		{
			$data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
		}
		return $this->where('notice_id=' . $id)->setField($data);
    }
	
	/**
	 * 修改文章详情
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_notice_txt中notice_id为$id的信息为$data
	 *
	 */
    public function setNoticeContents($id, array $data, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		if($isReplace && $data['contents'])
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$noticeTxt = M('notice_txt');
		return $noticeTxt->where('notice_id=' . $id)->setField($data);
    }
	
	/**
	 * 删除文章内容
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_notice_txt中notice_id为$id的记录
	 *
	 */
	public function deleteNoticeContents($id)
	{
		$noticeTxt = M('notice_txt');
		//删除文章内容记录
		if($noticeTxt->where('notice_id=' . $id)->delete())
		{
			//删除文章图片
			$this->deleteNoticePhotos($id);
			return true;
		}
		return false;
	}
	
	/**
	 * 删除文章内容中图片
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_notice_txt_photo中notice_id为$id的记录
	 *
	 */
	public function deleteNoticePhotos($id)
	{
		$noticeTxtPhoto = M('notice_txt_photo');
		$noticePhotos = $noticeTxtPhoto->where('notice_id=' . $id)->getField('path_img', true);
		
		if($noticePhotos)
		{
			//删除物理图片
			foreach($noticePhotos as $key => $val)
			{
				if(strpos($val, '##img_domain##') === false)
				{
					$noticePhoto = APP_PATH . 'Uploads/' . $val;
				}
				else
				{
					$noticePhoto = str_replace('##img_domain##', APP_PATH . 'Uploads', $val);
				}
				@unlink($noticePhoto);
			}
			//删除文章图片记录
			return $noticeTxtPhoto->where('notice_id=' . $id)->delete();
		}
		return 0;
	}
    
    /**
	 * 删除文章，包括删除表tp_notice、tp_notice_txt、tp_notice_txt_photo的相应记录
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_notice中notice_id为$id的文章记录，
	 * @todo 同时删除关联表tp_notice_txt、tp_notice_txt_photo表中相应记录，以及删除相应图片文件
	 *
	 */
    public function deleteNotice($id)
    {
		if($id < 0)
		{
			return false;
		}
		$noticeThumbPath = $this->where('notice_id=' . $id)->getField('path_img');
		//删除文章基本信息记录
		if($this->where('notice_id=' . $id)->delete())
		{
			//删除文章缩略图
			if($noticeThumbPath)
			{
				@unlink(str_replace('##img_domain##', APP_PATH . 'Uploads', $noticeThumbPath));
			}
			
			//删除文章内容
			$this->deleteNoticeContents($id);
			return true;
		}
		return false;
    }
    
    /**
	 * 获取文章列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @param string $join 联表查询，默认''
	 * @return mixed 成功返回文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_notice中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getNoticeList($limit = '', $order = '', $fields = '', $where = '', $join = '')
    {
		$_this = $this;
		$condition = 'notice_tag=""';
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
			$condition .= ' AND ' . $where;
		}
		
		if($join)
		{
			$_this = $_this->join($join);
		}
		
		$result = $_this->where($condition)->select();
		if($result)
		{
			if(!$fields || ($fields && false !== strpos($fields, 'path_img')))
			{
				foreach($result as $key => $val)
				{
					if($val['path_img'])
					{
						$result[$key]['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $val['path_img']);
					}
				}
			}
		}
		return $result;
    }
	
	/**
	 * 通过art_tag获取文章ID
	 *
	 * @param string $tag 文章标签
	 * @return mixed 成功返回文章ID，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice中notice_tag为$tag的notice_id值
	 *
	 */
	public function getNoticeIdByTag($tag, $fields = '')
	{
		return $this->field($fields)->where('notice_tag="' . $tag . '"')->find();
	}
	
	/**
	 * 文章点击率递增1
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_notice中notice_id为$id的clickdot递增1
	 *
	 */
	public function addClickdot($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		return $this->where('notice_id=' . $id)->setInc('clickdot');
	}
	
	/**
	 * 获取文章关键字列表
	 *
	 * @return mixed 成功返回文章关键字列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_keywords中所有字段
	 *
	 */
	public function getNoticeKeywordsList()
	{
		$noticeKeywords = M('notice_keywords');
		return $noticeKeywords->select();
	}
	
	/**
	 * 获取分页的文章关键字列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回文章关键字列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_keywords中所有字段，按$rows条数分页
	 *
	 */
	public function getNoticeKeywordsListPage($where = '', $rows = 15)
	{
		$noticeKeywords = M('notice_keywords');
		$total = $noticeKeywords->where($where)->count();
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		
		$result = $noticeKeywords->where($where)->limit($limit)->select();
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 添加文章关键字
	 *
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_notice_keywords中插入一条记录
	 *
	 */
	public function addNoticeKeyword(array $data)
	{
		$noticeKeywords = M('notice_keywords');
		$noticeKeywords->create($data);
		return $noticeKeywords->add();
	}
	
	/**
	 * 获取指定ID文章关键字
	 *
	 * @param int $id 关键词ID
	 * @return mixed 成功返回文章关键字，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_keywords中notice_keywords_id为$id的keyword字段
	 *
	 */
	public function getNoticeKeyword($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		$noticeKeywords = M('notice_keywords');
		return $noticeKeywords->where('notice_keywords_id=' . $id)->getField('keyword');
	}
	
	/**
	 * 修改文章关键字
	 *
	 * @param int $id 关键词ID
	 * @param string $data 关键词
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_notice_keywords中notice_keywords_id为$id的keyword字段为$data
	 *
	 */
	public function setNoticeKeyword($id, $data)
	{
		if($id < 0)
		{
			return false;
		}
		
		$noticeKeywords = M('notice_keywords');
		return $noticeKeywords->where('notice_keywords_id=' . $id)->setField('keyword', $data);
	}
	
	/**
	 * 删除文章关键词
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_notice_keywords中notice_keywords_id为$id的文章关键词记录
	 *
	 */
	public function deleteNoticeKeyword($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		$noticeKeywords = M('notice_keywords');
		return $noticeKeywords->where('notice_keywords_id=' . $id)->delete();
	}
	
	/**
	 * 下载一篇文章，即将AZ上表tp_notice、tp_notice_txt、tp_notice_txt_photo
	 * 相应记录拷贝到本地对应表中，同时下载图片文件至本地
	 *
	 * @param int $id 文章ID
	 * @param bool $isFill 若keywords、description字段值未设置，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 读取AZ上表tp_notice中notice_id为$id的字段notice_id,asort_id,author,notice_source,title,keywords,description，
	 * @todo 保存至本地表tp_notice中，同时读取AZ上表tp_notice_txt中notice_id为$id的字段contents保存至本地表tp_notice_txt中；
	 * @todo 以及表tp_notice_txt_photo中notice_id为$id的字段path_img保存至本地表tp_notice_txt_photo中
	 *
	 */
    protected function _downloadNotice($id, $isFill = true)
    {
		if($id < 0)
		{
			return false;
		}
		$azNotice = new AzNoticeModel();
		//文章基本信息
		$noticeInfo = $azNotice->get_notice_info($id);
		//文章内容
		$noticeContents = $azNotice->get_notice_contents($id);
		//文章图片
		$noticePhotos = $azNotice->get_notice_photos($id);
		
		if(!$noticeInfo)
		{
			return false;
		}
		
		//插表tp_notice
		$data = array();
		$data['notice_sort_id'] = $noticeInfo['asort_id'];
		$data['title'] 			 = $noticeInfo['title'];
		$data['az_notice_id'] 	 = $noticeInfo['notice_id'];
		$data['author'] 		 = $noticeInfo['author'];
		$data['notice_source']  = $noticeInfo['notice_source'];
		$data['addtime'] 		 = time();
		$data['isuse'] 			 = 1;
		
		$tmp = $data;
		$tmp['contents'] = $noticeContents;
		$metaData = $this->_metaDataHandler($tmp, $isFill);
		$data = array_merge($data, $metaData);
		
		if($id = $this->_saveNoticeInfo($data))
		{
			if($noticePhotos && is_array($noticePhotos))
			{
				//插表tp_notice_txt_photo
				foreach($noticePhotos as $key => $val)
				{
					$imageFile = self::downloadTxtPhoto($val);
					if($imageFile)
					{
						$noticeContents = str_replace($val, $imageFile, $noticeContents);
					}
					$data = array();
					$data['notice_id'] = $id;
					$data['path_img'] = $imageFile;
					$this->saveNoticePhoto($data);
				}
			}
			if($noticeContents)
			{
				//插表tp_notice_txt
				$data = array();
				$data['notice_id'] = $id;
				$data['contents'] = $noticeContents;
				$this->_saveNoticeContents($data);
			}
			return true;
		}
		return false;
    }
	
	/**
	 * 下载AZ文章详情图片至本地
	 *
	 * @param string $imageSourceUrl AZ图片URL
	 * @return mixed 成功返回本地图片路径，否则返回false
	 * @author zhengzhen
	 * @todo 获取AZ表tp_notice_txt_photo中path_img值，转换为url链接，下载至本地保存至指定路径下
	 * @todo 如：'http://image.fx.com/Uploads/image/notice/2014-02/20140224276618.jpg'	=>	'##img_domain##/image/notice/2014-03/20140324276618.jpg'
	 *
	 */
	public static function downloadTxtPhoto($imageSourceUrl)
	{
		$imageSavePath = APP_PATH . 'Uploads/image/notice/' . date('Y-m');
		$imageSourceExt = pathinfo($imageSourceUrl, PATHINFO_EXTENSION);
		$imageSaveFilename = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $imageSourceExt;
		$imageSaveFile = $imageSavePath . '/' . $imageSaveFilename;
		
		if(!file_exists($imageSavePath))
		{
			if(!@mkdir($imageSavePath, 0700, true))
			{
				return false;
			}
		}
		if(!@copy($imageSourceUrl, $imageSaveFile))
		{
			return false;
		}
		$imageFile = str_replace(APP_PATH . 'Uploads', '##img_domain##', $imageSaveFile);
		return $imageFile;
	}
	
	/**
	 * 获取文章总数
	 *
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回文章总数，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice中notice_sort_id为$id记录总数
	 *
	 */
	public function getTotal($where = '')
	{
		$_this = $this;
		$condition = 'notice_tag=""';
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		return $_this->where($condition)->count();
	}
	
	/**
	 * 处理表单中页面meta的keywords、description
	 *
	 * @param array $data 表单数据
	 * @param bool $isFill 若keywords、description字段值未设置，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @return array $_data 返回由keywords、description组成的数组
	 * @author zhengzhen
	 * @todo 若keywords、description字段值未设置，根据文章标题和内容生成keywords、description字段值
	 *
	 */
	protected function _metaDataHandler(array $data, $isFill)
	{
		$_data['keywords'] = $data['keywords'];
		$_data['description'] = $data['description'];
		if($isFill)
		{
			if(!$_data['keywords'])
			{
				$_data['keywords'] = $data['title'];
			}
			if(!$_data['description'])
			{
				if($data['contents'])
				{
					//实体转换
					$data['contents'] = htmlspecialchars_decode($data['contents']);
					//$data['contents']去除所有HTML标签以及\t,\n,\r,WhiteSpace（空格）字符后截取前200个字符
					$_data['description'] = filterAndSubstr($data['contents'], 200, '',false);
				}
				else
				{
					$_data['description'] = $data['title'];
				}
			}
		}
		return $_data;
	}
}
?>
