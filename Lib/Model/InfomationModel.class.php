<?php
/**
 * 云文章Model
 *
 * @author zhengzhen
 * @date 2014/2/24
 *
 */
class InfomationModel extends ArticleBaseModel
{
	/**
	 * 获取AZ开网店教程列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回开网店教程列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及asort_id为ARTICLE_SORT_TECH，查询表tp_article中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getAzShopTutorialSourceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$azArticle = new AzArticleModel();
		return $azArticle->get_shop_tutorial_source_list($limit, $order, $fields, $where);
    }
	
	/**
	 * 获取分页的AZ开网店教程列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回开网店教程列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getAzShopTutorialSourceListPage($where = '', $rows = 15)
	{
		$azArticle = new AzArticleModel();
		$total = $azArticle->getTotal(ARTICLE_SORT_TECH, $where);
		if(!total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'serial,addtime DESC';
		$fields = 'article_id,title,addtime,clickdot';
		$result = $azArticle->get_shop_tutorial_source_list($limit, $order, $fields, $where);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取AZ网店通用素材列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回网店通用素材列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及asort_id为ARTICLE_SORT_SOURCE，查询表tp_article中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getAzTaobaoCourseSourceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$azArticle = new AzArticleModel();
		return $azArticle->get_taobao_course_source_list($limit, $order, $fields, $where);
    }
	
	/**
	 * 获取分页的AZ网店通用素材列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回网店通用素材列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getAzTaobaoCourseSourceListPage($where = '', $rows = 15)
	{
		$azArticle = new AzArticleModel();
		$total = $azArticle->getTotal(ARTICLE_SORT_SOURCE, $where);
		if(!total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'serial,addtime DESC';
		$fields = 'article_id,title,addtime,clickdot';
		$result = $azArticle->get_taobao_course_source_list($limit, $order, $fields, $where);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取AZ常用工具下载列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回常用工具下载列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及asort_id为ARTICLE_SORT_TOOLS，查询表tp_article中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getAzSoftwareSourceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$azArticle = new AzArticleModel();
		return $azArticle->get_software_source_list($limit, $order, $fields, $where);
    }
	
	/**
	 * 获取分页的AZ常用工具下载列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回常用工具下载列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getAzSoftwareSourceListPage($where = '', $rows = 15)
	{
		$azArticle = new AzArticleModel();
		$total = $azArticle->getTotal(ARTICLE_SORT_TOOLS, $where);
		if(!total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'serial,addtime DESC';
		$fields = 'article_id,title,addtime,clickdot';
		$result = $azArticle->get_software_source_list($limit, $order, $fields, $where);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取AZ电商资讯列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回电商资讯列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及asort_id为ARTICLE_SORT_KNOWLEDGE，查询表tp_article中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getAzInfomationSourceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$azArticle = new AzArticleModel();
		return $azArticle->get_infomation_source_list($limit, $order, $fields, $where);
    }
	
	/**
	 * 获取分页的AZ电商资讯列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回电商资讯列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getAzInfomationSourceListPage($where = '', $rows = 15)
	{
		$azArticle = new AzArticleModel();
		$total = $azArticle->getTotal(ARTICLE_SORT_KNOWLEDGE, $where);
		if(!total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'serial,addtime DESC';
		$fields = 'article_id,title,addtime,clickdot';
		$result = $azArticle->get_infomation_source_list($limit, $order, $fields, $where);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取指定分类云文章下载列表
	 *
	 * @param int $sort 分类ID
	 * @param string $field 获取字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回云文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及分类$sort，查询表tp_article中$fields字段
	 *
	 */
	public function getDownloadedInfomationSourceList($sort, $field, $where = '')
	{
		$_this = $this;
		$condition = 'article_sort_id=' . $sort;
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		return $_this->where($condition)->getField($field, true);
	}
    
    /**
	 * 下载一篇云文章
	 *
	 * @param int $id AZ文章ID
	 * @param bool $isFill 若keywords、description字段值未设置，是否根据文章标题和内容生成keywords、description字段值，默认true
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 调用ArticleBaseModel中_downloadArticle()方法下载云文章资源
	 *
	 */
    public function downloadInfomationSource($id, $isFill = true)
    {
		if($id < 0)
		{
			return false;
		}
		//是否已下载
		if(0 < $this->getTotal('az_article_id=' . $id))
		{
			return 'isDownloaded';
		}
		return $this->_downloadArticle($id, $isFill);
    }
}
?>