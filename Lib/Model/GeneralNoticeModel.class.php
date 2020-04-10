<?php
/**
 * 新闻资讯类文章Model
 *
 * @access public
 * @author zhengzhen
 * @date 2014/2/25
 *
 */
class GeneralNoticeModel extends NoticeBaseModel
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
    public function getGeneralNoticeList($limit = '', $order = '', $where = '')
    {
		$_table = $this->getTableName();
		$fields = $_table . '.notice_id,' . $_table . '.title,' . $_table . '.clickdot,' .
				$_table . '.addtime,' . $_table . '.serial,' . $_table . '.isuse,a_s.notice_sort_name';
		$join = C('DB_PREFIX') . 'notice_sort AS a_s ON a_s.notice_sort_id=' . $_table . '.notice_sort_id';
		return $this->getNoticeList($limit, $order, $fields, $where, $join);
    }
	
	/**
	 * 获取分页的文章列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回文章列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_notice中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralNoticeListPage($where = '', $rows = 15)
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
		$fields = $_table . '.notice_id,' . $_table . '.title,' . $_table . '.clickdot,' .
				$_table . '.addtime,' . $_table . '.serial,' . $_table . '.isuse,a_s.notice_sort_name';
		$join = C('DB_PREFIX') . 'notice_sort AS a_s ON a_s.notice_sort_id=' . $_table . '.notice_sort_id';
		$result = $this->getNoticeList($limit, $order, $fields, $where, $join);
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
	 * @todo 通过条件$where，查询表tp_notice中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralNoticeListUcpPage($where = '', $rows = 15)
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
		$fields = 'notice_id,title,clickdot,addtime';
		$result = $this->getNoticeList($limit, $order, $fields, $condition);
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
	 * @todo 通过条件$where，查询表tp_notice中$fields字段，按$rows条数分页，以$order排序
	 *
	 */
	public function getGeneralNoticeListFrontPage($where = '', $rows = 5)
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
		$fields = $_table . '.notice_id,' . $_table . '.title,' . $_table . '.path_img,' . $_table . '.addtime,a_t.contents';
		$join = C('DB_PREFIX') . 'notice_txt AS a_t ON a_t.notice_id=' . $_table . '.notice_id';
		$result = $this->getNoticeList($limit, $order, $fields, $condition, $join);
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
	 * @todo 获取表tp_notice中notice_id为$id所有字段值，同时获取表tp_notice_txt中contents值
	 *
	 */
	public function getGeneralNoticeById($id)
	{
		$noticeInfo = $this->getNoticeInfo($id);
		if($noticeContents = $this->getNoticeContents($id))
		{
			$noticeInfo['contents'] = $noticeContents;
		}
		return $noticeInfo;
	}
	
	/**
	 * 获取文章信息
	 *
	 * @param int $id 文章ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回文章信息，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件notice_id为$id，查询表tp_notice表中$fields字段值，如果keywords和description字段值为空，则以标题替代
	 *
	 */
    public function getNoticeInfoFill($id, $fields = '')
    {
		$noticeInfo = $this->getNoticeInfo($id, $fields);
		$noticeContents = $this->getNoticeContents($id);
		if($noticeInfo)
		{
			if(!$noticeInfo['keywords'])
			{
				$noticeInfo['keywords'] = $noticeInfo['title'];
			}
			if(!$noticeInfo['description'])
			{
				if($noticeContents)
				{
					$noticeInfo['description'] = filterAndSubstr($noticeContents, 200, '', false);
				}
			}
		}
		return $noticeInfo;
    }
	
	/**
	 * 设置文章排序
	 *
	 * @param int $id 文章ID
	 * @param int $serial 排序号
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_notice中notice_id为$id的serial为$serial
	 *
	 */
	public function setSerial($id, $serial)
	{
		return $this->setNoticeInfo($id, array('serial' => $serial));
	}
}
?>