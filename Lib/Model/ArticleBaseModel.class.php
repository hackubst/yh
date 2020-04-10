<?php
/**
 * 文章基类Model
 *
 * @author zhengzhen
 * @date 2014/2/21
 *
 */
class ArticleBaseModel extends Model
{
	protected $tableName = 'article';
	
	
	/**
	 * 添加文章，包括表tp_article、tp_article_txt相关记录插入
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @param bool $isFill 若keywords、description字段值，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_article、tp_article_txt插入一条记录
	 *
	 */
	public function addArticle(array $data, $isFill = true, $isReplace = true)
    {
		$_data = array();
		$_data['article_sort_id'] = ($data['article_sort_id'])?$data['article_sort_id']:0;
		$_data['title'] 		  = $data['title'];
		$_data['author'] 		  = isset($data['author']) ? $data['author'] : '';
		$_data['article_source']  = isset($data['article_source']) ? $data['article_source'] : '';
		$_data['article_tag']  	  = isset($data['article_tag']) ? $data['article_tag'] : '';
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
		
		if($id = $this->_saveArticleInfo($_data))
		{
			if($data['contents'])
			{
				$_data = array();
				$_data['article_id'] = $id;
				$_data['contents'] = $data['contents'];
				$this->_saveArticleContents($_data, null, $isReplace);
			}
			return $id;
		}
		return false;
	}
	
	/**
	 * 修改文章，包括表tp_article、tp_article_txt相关记录修改
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isFill 若keywords、description字段值，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return bool成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_article、tp_article_txt中article_id为$id数据为$data
	 *
	 */
	public function saveArticle($id, array $data, $isFill = true, $isReplace = true)
    {
		$_data = array();
		$_data['article_sort_id'] = $data['article_sort_id'];
		$_data['title'] 		  = $data['title'];
		$_data['author'] 		  = isset($data['author']) ? $data['author'] : '';
		$_data['article_source']  = isset($data['article_source']) ? $data['article_source'] : '';
		$_data['article_tag']  	  = isset($data['article_tag']) ? $data['article_tag'] : '';
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
		
		if(false !== $this->_saveArticleInfo($_data, $id))
		{
			$_data = array();
			if($this->getArticleContents($id))
			{
				if(!$data['contents'])
				{
					return $this->deleteArticleContents($id);
				}
				$_data['contents'] = $data['contents'];
				$this->_saveArticleContents($_data, $id, $isReplace);
			}
			else
			{
				$_data['article_id'] = $id;
				$_data['contents'] = $data['contents'];
				$this->_saveArticleContents($_data, null, $isReplace);
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
	 * @todo 若$id未设置，则向表tp_article中插入一条记录；否则更新表tp_article中article_id为$id的数据
	 *
	 */
    protected function _saveArticleInfo(array $data, $id = null)
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
			return $this->where('article_id=' . $id)->save();
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
	 * @todo 若$id未设置，则向表tp_article_txt中插入一条记录；否则更新表tp_article_txt中article_id为$id的数据
	 *
	 */
	protected function _saveArticleContents(array $data, $id = null, $isReplace = true)
	{
		if(!$data['contents'])
		{
			return false;
		}
		elseif($isReplace)
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$articleTxt = M('article_txt');
		if(!isset($id))
		{
			$articleTxt->create($data);
			return $articleTxt->add();
		}
		else
		{
			if($id < 0)
			{
				return false;
			}
			$articleTxt->create($data);
			$result = $articleTxt->where('article_id=' . $id)->save();
			return $result;
		}
	}
	
	/**
	 * 保存文章图片链接
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_article_txt_photo表插入一条记录
	 *
	 */
    public function saveArticlePhoto(array $data)
    {
		if(!$data['path_img'])
		{
			return false;
		}
		$data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
		$articleTxtPhoto = M('article_txt_photo');
		$articleTxtPhoto->create($data);
		return $articleTxtPhoto->add();
    }
    
    /**
	 * 获取文章基本信息
	 *
	 * @param int $id 文章ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回文章信息，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件article_id为$id，查询表tp_article表中$fields字段值
	 *
	 */
    public function getArticleInfo($id, $fields = '')
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
		
		$result = $_this->where('article_id=' . $id)->find();
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
	 * @todo 通过条件article_id为$id，查询表tp_article_txt中contents字段值，若$isFormat为true，则为表tp_article_keywords中的关键字添加标签
	 *
	 */
    public function getArticleContents($id, $isFormat = false, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		$articleTxt = M('article_txt');
		$result = $articleTxt->where('article_id=' . $id)->getField('contents');
		#echo "<pre>";print_r($result);echo $articleTxt->getLastSql();die;
		if(result)
		{
			//实体转换
			$result = htmlspecialchars_decode($result);
			if($isFormat)
			{
				//添加关键字标签
				$articleKeywordsList = $this->getArticleKeywordsList();
				if($articleKeywordsList)
				{
					$lt = html_entity_encode('<');
					$gt = html_entity_encode('>');
					foreach($articleKeywordsList as $key => $val)
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
	 * @todo 获取表tp_article_txt_photo中article_id为$id的文章图片路径path_img
	 *
	 */
	public function getArticlePhotos($id)
	{
		if($id < 0)
		{
			return false;
		}
		$articleTxtPhoto = M('article_txt_photo');
		return $articleTxtPhoto->where('article_id=' . $id)->getField('path_img', true);
	}
    
    /**
	 * 修改文章基本信息
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_article中article_id为$id信息为$data
	 *
	 */
    public function setArticleInfo($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		if(isset($data['path_img']) && false !== strpos($data['path_img'], C('IMG_DOMAIN')))
		{
			$data['path_img'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['path_img']);
		}
		return $this->where('article_id=' . $id)->setField($data);
    }
	
	/**
	 * 修改文章详情
	 *
	 * @param int $id 文章ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @param bool $isReplace 是否替换详情中图片域名C('IMG_DOMAIN')为占位符'##img_domain##'，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_article_txt中article_id为$id的信息为$data
	 *
	 */
    public function setArticleContents($id, array $data, $isReplace = true)
    {
		if($id < 0)
		{
			return false;
		}
		if($isReplace && $data['contents'])
		{
			$data['contents'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['contents']);
		}
		$articleTxt = M('article_txt');
		return $articleTxt->where('article_id=' . $id)->setField($data);
    }
	
	/**
	 * 删除文章内容
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_article_txt中article_id为$id的记录
	 *
	 */
	public function deleteArticleContents($id)
	{
		$articleTxt = M('article_txt');
		//删除文章内容记录
		if($articleTxt->where('article_id=' . $id)->delete())
		{
			//删除文章图片
			$this->deleteArticlePhotos($id);
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
	 * @todo 删除表tp_article_txt_photo中article_id为$id的记录
	 *
	 */
	public function deleteArticlePhotos($id)
	{
		$articleTxtPhoto = M('article_txt_photo');
		$articlePhotos = $articleTxtPhoto->where('article_id=' . $id)->getField('path_img', true);
		
		if($articlePhotos)
		{
			//删除物理图片
			foreach($articlePhotos as $key => $val)
			{
				if(strpos($val, '##img_domain##') === false)
				{
					$articlePhoto = APP_PATH . 'Uploads/' . $val;
				}
				else
				{
					$articlePhoto = str_replace('##img_domain##', APP_PATH . 'Uploads', $val);
				}
				@unlink($articlePhoto);
			}
			//删除文章图片记录
			return $articleTxtPhoto->where('article_id=' . $id)->delete();
		}
		return 0;
	}
    
    /**
	 * 删除文章，包括删除表tp_article、tp_article_txt、tp_article_txt_photo的相应记录
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_article中article_id为$id的文章记录，
	 * @todo 同时删除关联表tp_article_txt、tp_article_txt_photo表中相应记录，以及删除相应图片文件
	 *
	 */
    public function deleteArticle($id)
    {
		if($id < 0)
		{
			return false;
		}
		$articleThumbPath = $this->where('article_id=' . $id)->getField('path_img');
		//删除文章基本信息记录
		if($this->where('article_id=' . $id)->delete())
		{
			//删除文章缩略图
			if($articleThumbPath)
			{
				@unlink(str_replace('##img_domain##', APP_PATH . 'Uploads', $articleThumbPath));
			}
			
			//删除文章内容
			$this->deleteArticleContents($id);
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
	 * @todo 通过条件$where，查询表tp_article中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getArticleList($limit = '', $order = '', $fields = '', $where = '', $join = '')
    {
		$_this = $this;
		$condition = 'article_tag=""';
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
	 * @todo 获取表tp_article中article_tag为$tag的article_id值
	 *
	 */
	public function getArticleIdByTag($tag, $fields = '')
	{
		return $this->field($fields)->where('article_tag="' . $tag . '"')->find();
	}
	
	/**
	 * 文章点击率递增1
	 *
	 * @param int $id 文章ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_article中article_id为$id的clickdot递增1
	 *
	 */
	public function addClickdot($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		return $this->where('article_id=' . $id)->setInc('clickdot');
	}
	
	/**
	 * 获取文章关键字列表
	 *
	 * @return mixed 成功返回文章关键字列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_article_keywords中所有字段
	 *
	 */
	public function getArticleKeywordsList()
	{
		$articleKeywords = M('article_keywords');
		return $articleKeywords->select();
	}
	
	/**
	 * 获取分页的文章关键字列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回文章关键字列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_article_keywords中所有字段，按$rows条数分页
	 *
	 */
	public function getArticleKeywordsListPage($where = '', $rows = 15)
	{
		$articleKeywords = M('article_keywords');
		$total = $articleKeywords->where($where)->count();
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		
		$result = $articleKeywords->where($where)->limit($limit)->select();
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
	 * @todo 向表tp_article_keywords中插入一条记录
	 *
	 */
	public function addArticleKeyword(array $data)
	{
		$articleKeywords = M('article_keywords');
		$articleKeywords->create($data);
		return $articleKeywords->add();
	}
	
	/**
	 * 获取指定ID文章关键字
	 *
	 * @param int $id 关键词ID
	 * @return mixed 成功返回文章关键字，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_article_keywords中article_keywords_id为$id的keyword字段
	 *
	 */
	public function getArticleKeyword($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		$articleKeywords = M('article_keywords');
		return $articleKeywords->where('article_keywords_id=' . $id)->getField('keyword');
	}
	
	/**
	 * 修改文章关键字
	 *
	 * @param int $id 关键词ID
	 * @param string $data 关键词
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_article_keywords中article_keywords_id为$id的keyword字段为$data
	 *
	 */
	public function setArticleKeyword($id, $data)
	{
		if($id < 0)
		{
			return false;
		}
		
		$articleKeywords = M('article_keywords');
		return $articleKeywords->where('article_keywords_id=' . $id)->setField('keyword', $data);
	}
	
	/**
	 * 删除文章关键词
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_article_keywords中article_keywords_id为$id的文章关键词记录
	 *
	 */
	public function deleteArticleKeyword($id)
	{
		if($id < 0)
		{
			return false;
		}
		
		$articleKeywords = M('article_keywords');
		return $articleKeywords->where('article_keywords_id=' . $id)->delete();
	}
	
	/**
	 * 下载一篇文章，即将AZ上表tp_article、tp_article_txt、tp_article_txt_photo
	 * 相应记录拷贝到本地对应表中，同时下载图片文件至本地
	 *
	 * @param int $id 文章ID
	 * @param bool $isFill 若keywords、description字段值未设置，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 读取AZ上表tp_article中article_id为$id的字段article_id,asort_id,author,article_source,title,keywords,description，
	 * @todo 保存至本地表tp_article中，同时读取AZ上表tp_article_txt中article_id为$id的字段contents保存至本地表tp_article_txt中；
	 * @todo 以及表tp_article_txt_photo中article_id为$id的字段path_img保存至本地表tp_article_txt_photo中
	 *
	 */
    protected function _downloadArticle($id, $isFill = true)
    {
		if($id < 0)
		{
			return false;
		}
		$azArticle = new AzArticleModel();
		//文章基本信息
		$articleInfo = $azArticle->get_article_info($id);
		//文章内容
		$articleContents = $azArticle->get_article_contents($id);
		//文章图片
		$articlePhotos = $azArticle->get_article_photos($id);
		
		if(!$articleInfo)
		{
			return false;
		}
		
		//插表tp_article
		$data = array();
		$data['article_sort_id'] = $articleInfo['asort_id'];
		$data['title'] 			 = $articleInfo['title'];
		$data['az_article_id'] 	 = $articleInfo['article_id'];
		$data['author'] 		 = $articleInfo['author'];
		$data['article_source']  = $articleInfo['article_source'];
		$data['addtime'] 		 = time();
		$data['isuse'] 			 = 1;
		
		$tmp = $data;
		$tmp['contents'] = $articleContents;
		$metaData = $this->_metaDataHandler($tmp, $isFill);
		$data = array_merge($data, $metaData);
		
		if($id = $this->_saveArticleInfo($data))
		{
			if($articlePhotos && is_array($articlePhotos))
			{
				//插表tp_article_txt_photo
				foreach($articlePhotos as $key => $val)
				{
					$imageFile = self::downloadTxtPhoto($val);
					if($imageFile)
					{
						$articleContents = str_replace($val, $imageFile, $articleContents);
					}
					$data = array();
					$data['article_id'] = $id;
					$data['path_img'] = $imageFile;
					$this->saveArticlePhoto($data);
				}
			}
			if($articleContents)
			{
				//插表tp_article_txt
				$data = array();
				$data['article_id'] = $id;
				$data['contents'] = $articleContents;
				$this->_saveArticleContents($data);
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
	 * @todo 获取AZ表tp_article_txt_photo中path_img值，转换为url链接，下载至本地保存至指定路径下
	 * @todo 如：'http://image.fx.com/Uploads/image/article/2014-02/20140224276618.jpg'	=>	'##img_domain##/image/article/2014-03/20140324276618.jpg'
	 *
	 */
	public static function downloadTxtPhoto($imageSourceUrl)
	{
		$imageSavePath = APP_PATH . 'Uploads/image/article/' . date('Y-m');
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
	 * @todo 获取表tp_article中article_sort_id为$id记录总数
	 *
	 */
	public function getTotal($where = '')
	{
		$_this = $this;
		$condition = 'article_tag=""';
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
