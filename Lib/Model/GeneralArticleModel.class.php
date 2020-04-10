<?php
/**
 * 新闻资讯类文章Model
 *
 * @access public
 * @author zhengzhen
 * @date 2014/2/25
 *
 */
class GeneralArticleModel extends ArticleBaseModel
{
	/**
	 * 获取文章列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $where 查询条件，默认''
	 * @return mixed
	 * @author zhengzhen
	 *
	 */
    public function getGeneralArticleList($limit = '', $order = '', $where = '')
    {
		$_table = $this->getTableName();
		$fields = $_table . '.article_id,' . $_table . '.title,' . $_table . '.clickdot,' .
				$_table . '.addtime,' . $_table . '.serial,' . $_table . '.isuse,a_s.article_sort_name';
		$join = C('DB_PREFIX') . 'article_sort AS a_s ON a_s.article_sort_id=' . $_table . '.article_sort_id';
		return $this->getArticleList($limit, $order, $fields, $where, $join);
    }
	
	/**
	 * 获取分页的文章列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralArticleListPage($where = '', $rows = 15)
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
		$order = $_table . '.serial,' . $_table . '.addtime DESC';
		$fields = $_table . '.article_id,' . $_table . '.title,' . $_table . '.clickdot,' .
				$_table . '.addtime,' . $_table . '.serial,' . $_table . '.isuse,a_s.article_sort_name';
		$join = C('DB_PREFIX') . 'article_sort AS a_s ON a_s.article_sort_id=' . $_table . '.article_sort_id';
		$result = $this->getArticleList($limit, $order, $fields, $where, $join);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取分页的文章列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralArticleListUcpPage($where = '', $rows = 15)
	{
		$condition = 'isuse=1';
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		$total = $this->getTotal($condition);
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.PageUcp');
		$Page = new PageUcp($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'serial,addtime DESC';
		$fields = 'article_id,title,clickdot,addtime';
		$result = $this->getArticleList($limit, $order, $fields, $condition);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取分页的文章列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认5
	 * @return mixed 成功返回文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_article中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralArticleListFrontPage($where = '', $rows = 5)
	{
		$condition = 'isuse=1';
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		$total = $this->getTotal($condition);
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.PageFront');
		$Page = new PageFront($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$_table = $this->getTableName();
		$order = $_table . '.serial,' . $_table . '.addtime DESC';
		$fields = $_table . '.article_id,' . $_table . '.title,' . $_table . '.path_img,' . $_table . '.addtime,a_t.contents';
		$join = C('DB_PREFIX') . 'article_txt AS a_t ON a_t.article_id=' . $_table . '.article_id';
		$result = $this->getArticleList($limit, $order, $fields, $condition, $join);
		if($result)
		{
			foreach($result as $key => $val)
			{
				//实体转换
				$val['contents'] = htmlspecialchars_decode($val['contents']);
				$result[$key]['contents'] = filterAndSubstr($val['contents'], 200, '<p><a><br>');
			}
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取指定ID文章数据
	 *
	 * @param int $id 文章ID
	 * @return mixed
	 * @author zhengzhen
	 * @todo 获取表tp_article中article_id为$id所有字段值，同时获取表tp_article_txt中contents值
	 *
	 */
	public function getGeneralArticleById($id)
	{
		$articleInfo = $this->getArticleInfo($id);
		if($articleContents = $this->getArticleContents($id))
		{
			$articleInfo['contents'] = $articleContents;
		}
		return $articleInfo;
	}
	
	/**
	 * 获取文章信息
	 *
	 * @param int $id 文章ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回文章信息，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件article_id为$id，查询表tp_article表中$fields字段值，如果keywords和description字段值为空，则以标题替代
	 *
	 */
    public function getArticleInfoFill($id, $fields = '')
    {
		$articleInfo = $this->getArticleInfo($id, $fields);
		$articleContents = $this->getArticleContents($id);
		if($articleInfo)
		{
			if(!$articleInfo['keywords'])
			{
				$articleInfo['keywords'] = $articleInfo['title'];
			}
			if(!$articleInfo['description'])
			{
				if($articleContents)
				{
					$articleInfo['description'] = filterAndSubstr($articleContents, 200, '', false);
				}
			}
		}
		return $articleInfo;
    }
	
	/**
	 * 设置文章排序
	 *
	 * @param int $id 文章ID
	 * @param int $serial 排序号
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_article中article_id为$id的serial为$serial
	 *
	 */
	public function setSerial($id, $serial)
	{
		return $this->setArticleInfo($id, array('serial' => $serial));
	}
}
?>