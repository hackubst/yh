<?php
/**
 * 文章管理ajax类
 *
 *
 */
class AcpArticleAjaxAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
//	/**
//     * 添加文章栏目
//	 *
//     * @author zhengzhen
//     * @return string 返回JSON字符串
//     * @todo 向表tp_article_sort中插入一条记录
//     */
//	public function add_sort()
//	{
//		if($this->isAjax())
//		{
//			$sortName = $this->_get('sort_name');
//			$description = $this->_get('description');
//
//			if(!$sortName)
//			{
//				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
//			}
//			$data['article_sort_name'] = $sortName;
//			$data['description'] = $description;
//			$data['isuse'] = 1;
//			$articleCategory = new ArticleCategoryModel();
//			if($articleCategory->addArticleCategory($data))
//			{
//				$this->_ajaxFeedback(1, null, '恭喜您，栏目添加成功！');
//			}
//			$this->_ajaxFeedback(0, null, '对不起，栏目添加失败，请稍后重试！');
//		}
//	}

	
//	/**
//     * 修改文章栏目
//	 *
//     * @author zhengzhen
//     * @return string 返回JSON字符串
//     * @todo 更新表tp_article_sort中article_sort_id为$id的文章栏目信息
//     */
//	public function edit_sort()
//	{
//		$id = $this->_get('id');
//		if($this->isAjax() && $id)
//		{
//			//验证ID是否为数字
//			if(!ctype_digit($id))
//			{
//				$this->_ajaxFeedback(0, null, '参数无效！');
//			}
//			//验证该栏目ID是否10以内
//			if($id <= 10)
//			{
//				$this->_ajaxFeedback(0, null, '对不起，该栏目不能修改！');
//			}
//			$sortName = $this->_get('sort_name');
//			$description = $this->_get('description');
//
//			if(!$sortName)
//			{
//				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
//			}
//			$data['article_sort_name'] = $sortName;
//			$data['description'] = $description;
//			$articleCategory = new ArticleCategoryModel();
//			if(false !== $articleCategory->setArticleCategory($id, $data))
//			{
//				$sortName = $articleCategory->getArticleCategory($id, 'article_sort_name');
//				$data['article_sort_id'] = $id;
//				$data['article_sort_name'] = $sortName['article_sort_name'];
//				$this->_ajaxFeedback(1, $data, '恭喜您，栏目修改成功！');
//			}
//			$this->_ajaxFeedback(0, null, '对不起，栏目修改失败，请稍后重试！');
//		}
//	}
	
	/**
     * 删除文章栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除表tp_article_sort中article_sort_id为$id的文章栏目记录，
	 * 删除前确定是否有该栏目下的文章，提示无法删除，请先调整文章栏目。表内ID20以内的数据不能删除
     */
	public function del_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			//验证该栏目ID是否10以内
			if($id <= 10)
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目不能删除！');
			}
			//验证该栏目下是否有文章
			$where = 'article_sort_id=' . $id;
			$generalArticle = new GeneralArticleModel();
			if($generalArticle->getGeneralArticleList('', '', $where))
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目下有文章，无法删除，请先调整！');
			}
			
			$articleCategory = new ArticleCategoryModel();
			if($articleCategory->deleteArticleCategory($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，栏目删除成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，栏目删除失败，请稍后重试！');
		}
	}
	
//	/**
//     * 获取指定ID文章栏目
//	 *
//     * @author zhengzhen
//     * @return string 返回JSON字符串
//     * @todo 获取表tp_article_sort中article_sort_id为$id的文章栏目信息
//     */
//	public function fetch_sort()
//	{
//		$id = $this->_get('id');
//		if($this->isAjax() && $id)
//		{
//			//验证ID是否为数字
//			if(!ctype_digit($id))
//			{
//				$this->_ajaxFeedback(0, null, '参数无效！');
//			}
//
//			$fields = 'article_sort_id,article_sort_name,description';
//			$articleCategory = new ArticleCategoryModel();
//			$articleCategoryData = $articleCategory->getArticleCategory($id, $fields);
//			if($articleCategoryData)
//			{
//				$this->_ajaxFeedback(1, $articleCategoryData);
//			}
//			$this->_ajaxFeedback(0, null, '对不起，服务器忙，稍后请重试！');
//		}
//	}
	
	/**
     * 快速修改文章栏目序号
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改文章栏目表中serial的序号，改完后不刷新当前页
     */
	public function edit_cat_serial()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
			}
			
			$articleCategory = new ArticleCategoryModel();
			if(false !== $articleCategory->setArticleCategory($id, array('serial' => $serial)))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
		}
	}
	
	/**
     * 快速修改文章栏目启用状态
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改文章栏目表中isuse的值，改完后不刷新当前页
     */
	public function edit_cat_isuse()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$articleCategory = new ArticleCategoryModel();
			$isUse = $articleCategory->getArticleCategoryState($id);
			$isUse = abs($isUse - 1);
			if(false !== $articleCategory->setArticleCategory($id, array('isuse' => $isUse)))
			{
				$data = array('isuse' => $isUse);
				if($isUse === 0)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目禁用成功！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目启用成功！');
				}
			}
			else
			{
				if($isUse === 0)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目禁用失败，请稍后再试！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目启用失败，请稍后再试！');
				}
			}
		}
	}
	
	/**
     * 快速修改文章序号
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 修改文章表中serial的序号，改完后不刷新当前页
     */
	public function edit_serial()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
			}
			
			$generalArticle = new GeneralArticleModel();
			if(false !== $generalArticle->setSerial($id, $serial))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
		}
	}
	
	/**
     * 删除文章
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除文章相关表中article_id为$id的记录，改完后不刷新当前页
     */
	public function del_article()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$success = 0;//删除成功数
			$failure = 0;//删除失败数
			$generalArticle = new GeneralArticleModel();
			if(is_array($id))
			{
				foreach($id as $key => $val)
				{
					//验证ID是否为数字
					if(!ctype_digit($val))
					{
						continue;
					}
					if($generalArticle->deleteArticle($val))
					{
						$data[] = array('id' => $val, 'error' => 0);
						$success++;
					}
					else
					{
						$data[] = array('id' => $val, 'error' => 1);
						$failure++;
					}
				}
			}
			else
			{
				//验证ID是否为数字
				if(!ctype_digit($id))
				{
					$this->_ajaxFeedback(0, null, '参数无效！');
				}
				if($generalArticle->deleteArticle($id))
				{
					$data[] = array('id' => $val, 'error' => 0);
					$success++;
				}
				else
				{
					$data[] = array('id' => $val, 'error' => 1);
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '篇文章删除成功！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '篇文章删除失败，请稍后重试！';
			}
			$msg = implode("<br />", $msg);
			if($success > 0)
			{
				$this->_ajaxFeedback(1, $data, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, $data, $msg);
			}
		}
	}
	
	
	/**
     * 文章从AZ下载到本地
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 从AZ下载文章到本地文章表，article、article_txt、article_txt_photo表的数据全部下载到本地；图片也保存到本地；本地保存后的sort_id=传入的sort_id
     */
	public function down_article_cloud()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			//下载资源
			$success = 0;//下载成功数
			$exist = 0;//已下载数（重复下载）
			$failure = 0;//下载失败数
			$info = new InfomationModel();
			$isDownload = $info->downloadInfomationSource($id);
			if($isDownload === true)
			{
				$status = 1;
				$data[] = $id;
				$msg =  '恭喜您，1篇文章下载成功！';
			}
			elseif($isDownload === 'isDownloaded')
			{
				$status = 0;
				$msg = '对不起，1篇文章已经下载，无需重复下载！';
			}
			else
			{
				$status = 0;
				$msg = '对不起，1篇文章下载失败，请稍后重试！';
			}
			$this->_ajaxFeedback($status, $data, $msg);
		}
	}
	
	/**
     * 从AZ批量下载文章到本地
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 从AZ下载文章到本地文章表，article、article_txt、article_txt_photo表的数据全部下载到本地；图片也保存到本地；本地保存后的sort_id=传入的sort_id
     */
	public function down_article_cloud_batch()
	{
		$ids = $this->_get('id');
		if($this->isAjax() && $ids)
		{
			$success = 0;//下载成功数
			$exist = 0;//已下载数（重复下载）
			$failure = 0;//下载失败数
			$info = new InfomationModel();
			foreach($ids as $key => $val)
			{
				//验证ID是否为数字
				if(!ctype_digit($val))
				{
					continue;
				}
				//下载资源
				$isDownload = $info->downloadInfomationSource($val);
				if($isDownload === true)
				{
					$data[] = array('id' => $val, 'error' => 0);
					$success++;
				}
				elseif($isDownload === 'isDownloaded')
				{
					$data[] = array('id' => $val, 'error' => 1);
					$exist++;
				}
				else
				{
					$data[] = array('id' => $val, 'error' => 1);
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '篇文章下载成功！';
			}
			if($exist > 0)
			{
				$msg[] = '对不起，' . $exist . '篇文章已经下载，无需重复下载！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '篇文章下载失败，请稍后重试！';
			}
			$msg = implode("<br />", $msg);
			if($success > 0)
			{
				$this->_ajaxFeedback(1, $data, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, $data, $msg);
			}
		}
	}
	
	/**
     * 获取文章内容
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 获取表tp_article_txt中article_id为$id的contents字段值
     */
	public function get_article_contents()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$azArticle = new AzArticleModel();
			if($articleContents = $azArticle->get_article_contents($id))
			{
				$articleInfo = $azArticle->get_article_info($id, 'title');
				$data = array(
					'title' => $articleInfo['title'],
					'contents' => $articleContents
				);
				$this->_ajaxFeedback(1, $data);
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，数据初始化失败，请稍后再试！');
			}
		}
	}
	
	/**
     * 添加文章关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 向表tp_article_keywords中插入一条记录
     */
	public function add_article_keywords()
	{
		if($this->isAjax())
		{
			$keywords = $this->_get('keywords');
			
			if(!$keywords)
			{
				$this->_ajaxFeedback(0, null, '请输入关键词！');
			}
			$data['keyword'] = $keywords;
			$generalArticle = new GeneralArticleModel();
			if($generalArticle->addArticleKeyword($data))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，关键词添加成功！');
			}
		}
	}
	
	/**
     * 修改文章关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 更新表tp_article_keywords中article_keywords_id为$id的文章关键词
     */
	public function edit_article_keywords()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			$keywords = $this->_get('keywords');
			
			if(!$keywords)
			{
				$this->_ajaxFeedback(0, null, '请输入关键词！');
			}
			
			$generalArticle = new GeneralArticleModel();
			if(false !== $generalArticle->setArticleKeyword($id, $keywords))
			{
				$data = array('keywords' => $keywords);
				$this->_ajaxFeedback(1, $data, '恭喜您，关键词修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，关键词修改失败，请稍后重试！');
		}
	}
	
	/**
     * 删除文章关键词
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除表tp_article_keywords中article_keywords_id为$id的记录，改完后不刷新当前页
     */
	public function del_article_keywords()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$generalArticle = new GeneralArticleModel();
			if($generalArticle->deleteArticleKeyword($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，关键词删除成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，关键词删除失败，请稍后重试！');
		}
	}
	
	/**
     * 获取指定ID文章关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 获取表tp_article_keywords中article_keywords_id为$id的文章关键词
     */
	public function fetch_article_keyword()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$generalArticle = new GeneralArticleModel();
			$articleKeyword = $generalArticle->getArticleKeyword($id);
		}
		if($articleKeyword)
		{
			$data['keyword'] = $articleKeyword;
			$this->_ajaxFeedback(1, $data);
		}
		$this->_ajaxFeedback(0, null, '对不起，服务器忙，稍后请重试！');
	}
	
	/**
	 * 文章预览图上传处理
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 调用upImageHandler函数处理上传图片
	 *
	 */
	public function uploadHandler()
	{
		upImageHandler($_FILES['imgFile'], '/article/thumb');
	}
	
	/**
	 * 删除文章缩略图
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 分两种情况：一，删除未入库图片，此时传入参数为img_url，将此图片URL替换为图片路径直接删除即可；
	 * @todo 二，删除已入库图片，此时传入参数为id，查询表tp_article中article_id为$id的path_img值，替换其图片域名占位符为图片路径，然后删除图片，同时将此path_img值置空。
	 *
	 */
	public function delImage()
	{
		$id = $this->_post('id');
		$imgUrl = $this->_post('img_url');
		
		if($this->isAjax())
		{
			if($id)
			{
				$generalArticle = new GeneralArticleModel();
				$articleThumbPath = $generalArticle->getArticleInfo($id, 'path_img');
				$articleThumbFile = str_replace('##img_domain##', APP_PATH . 'Uploads', $articleThumbPath['path_img']);
				if($generalArticle->setArticleInfo($id, array('path_img' => '')))
				{
					@unlink($articleThumbFile);
					$this->_ajaxFeedback(1);
				}
			}
			elseif($imgUrl)
			{
				$imgDomain = C('IMG_DOMAIN');
				$imgDomain = (false !== strpos($imgUrl, $imgDomain)) ? $imgDomain : '';
				$imgFile = str_replace($imgDomain . '/Uploads', APP_PATH . 'Uploads', $imgUrl);
				@unlink($imgFile);
				$this->_ajaxFeedback(1);
			}
			else
			{
				$this->_ajaxFeedback(0);
			}
		}
	}

    /**
     * @todo 添加文章分类
     * @author lye
     * Date: 2018-06-19
     * Time: 16:25
     */
    public function add_sort()
    {
        if($this->isAjax())
        {
            $sort_name   = $this->_get('sort_name');
            $description = $this->_get('description');

            if(!$sort_name)
            {
                $this->_ajaxFeedback(0, null, '请输入栏目名称！');
            }
            $add_data['article_sort_name'] = $sort_name;
            $add_data['description']           = $description;

            if(D('ArticleSort')->addArticleSort($add_data))
            {
                $this->_ajaxFeedback(1, null, '恭喜您，栏目添加成功！');
            }
            $this->_ajaxFeedback(0, null, '对不起，栏目添加失败，请稍后重试！');
        }
    }

    /**
     * @todo 根据Id查询文章详情信息
     * @author lye
     * Date: 2018-06-19
     * Time: 16:30
     */
    public function fetch_sort()
    {
        $article_sort_id = $this->_request('id');
        if($this->isAjax() && $article_sort_id)
        {
            //验证ID是否为数字
            if(!ctype_digit($article_sort_id))
            {
                $this->_ajaxFeedback(0, null, '参数无效！');
            }

            $where  = 'isuse=1 AND article_sort_id='.$article_sort_id;
            $fields = 'article_sort_id,article_sort_name,description';
            $article_sort_info = D('ArticleSort')->getArticleSortInfo($where, $fields);
            if($article_sort_info)
            {
                $this->_ajaxFeedback(1, $article_sort_info);
            }
            $this->_ajaxFeedback(0, null, '对不起，服务器忙，稍后请重试！');
        }
    }

    /**
     * @todo 修改文章栏目
     * @author lye
     * Date: 2018-06-19
     * Time: 16:35
     */
    public function edit_sort()
    {
        $article_sort_id = $this->_request('id');
        if($this->isAjax() && $article_sort_id)
        {
            //验证ID是否为数字
            if(!ctype_digit($article_sort_id))
            {
                $this->_ajaxFeedback(0, null, '参数无效！');
            }
            //验证该栏目ID是否10以内
//            if($id <= 10)
//            {
//                $this->_ajaxFeedback(0, null, '对不起，该栏目不能修改！');
//            }
            $sort_name   = $this->_request('sort_name');
            $description = $this->_request('description');

            if(!$sort_name)
            {
                $this->_ajaxFeedback(0, null, '请输入栏目名称！');
            }
            $save_data['article_sort_name'] = $sort_name;
            $save_data['description']       = $description;
            $article_sort_obj = new ArticleSortModel($article_sort_id);
            $res = $article_sort_obj->editArticleSort($save_data);

            if($res !== false)
            {
                $data['article_sort_id']   = $article_sort_id;
                $data['article_sort_name'] = $sort_name;
                $this->_ajaxFeedback(1, $data, '恭喜您，栏目修改成功！');
            }
            $this->_ajaxFeedback(0, null, '对不起，栏目修改失败，请稍后重试！');
        }
    }
    
}
?>
