<?php
/**
 * 文章分类Model
 *
 * @author zhengzhen
 * @date 2014/2/25
 *
 */
class NoticeCategoryModel extends Model
{
	protected $tableName = 'notice_sort';
	
	
	/**
	 * 添加文章分类
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_notice_sort表插入一条记录
	 *
	 */
	public function addNoticeCategory(array $data)
	{
		$this->create($data);
		return $this->add();
	}
	
	/**
	 * 修改文章分类
	 *
	 * @param int $id 文章分类ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_notice中notice_sort_id为$id的信息为$data
	 *
	 */
	public function setNoticeCategory($id, array $data)
	{
		if($id < 0)
		{
			return false;
		}
		return $this->where('notice_sort_id=' . $id)->setField($data);
	}
	
	/**
	 * 删除文章分类，若该分类下有文章时不能删除
	 *
	 * @param int $id 文章分类ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_notice_sort中notice_sort_id为$id的记录
	 *
	 */
	public function deleteNoticeCategory($id)
	{
		if($id < 0)
		{
			return false;
		}
		$notice = new NoticeBaseModel();
		$noticeList = $notice->getNoticeList(1, '', 'notice_id', 'asort_id=' . $id);
		if($noticeList)
		{
			return false;
		}
		return $this->where('notice_sort_id=' . $id)->delete();
	}
	
	/**
	 * 获取文章分类列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回文章分类列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_notice_sort中$fields字段，获取$limit条数，以$order排序
	 *
	 */
	public function getNoticeCategoryList($limit = '', $order = '', $fields = '', $where = '')
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
		
		return $_this->select();
	}
	
	/**
	 * 获取分页的文章分类列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认10
	 * @return mixed 成功返回文章分类列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_notice_sort中$fields字段，获取$limit条数，以$order排序
	 *
	 */
	public function getNoticeCategoryListPage($where = '', $rows = 10)
	{
		$_this = $this;
		
		$total = $_this->getTotal($where);
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		
		if($where)
		{
			$_this = $_this->where($where);
		}
		
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		
		$result = $_this->order('serial')->select();
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 获取指定ID文章分类信息
	 *
	 * @param int $id 文章分类ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回文章分类信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_sort中notice_sort_id为$id的数据
	 *
	 */
	public function getNoticeCategory($id, $fields = '')
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
		return $_this->where('notice_sort_id=' . $id)->find();
	}
	
	/**
	 * 获取指定ID文章分类名称
	 *
	 * @param int $id 文章分类ID
	 * @return mixed 成功返回文章分类名称，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_sort中notice_sort_id为$id的notice_sort_name值
	 *
	 */
	public function getNoticeCategoryName($id)
	{
		if($id < 0)
		{
			return false;
		}
		return $this->where('notice_sort_id=' . $id)->getField('notice_sort_name');
	}
	
	/**
	 * 获取指定ID文章分类启用状态
	 *
	 * @param int $id 文章分类ID
	 * @return mixed 成功返回文章分类启用状态，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_sort中notice_sort_id为$id的isuse值
	 *
	 */
	public function getNoticeCategoryState($id)
	{
		if($id < 0)
		{
			return false;
		}
		return $this->where('notice_sort_id=' . $id)->getField('isuse');
	}
	
	/**
	 * 获取文章分类总数
	 *
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回文章分类总数，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_notice_sort中记录总数
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
	
    /**
     * 获取一级分类某个字段的信息
     * @author 张勇
     * @param int $class_id 一级分类ID
     * @param string $field 查询的字段名
     * @return array 一级分类
     * @todo 获取一级分类某个字段的信息
     */
    public function getClassField($class_id, $field)
    {
        if (!is_numeric($class_id))   return false;
        return $this->where('notice_sort_id = ' . $class_id)->getField($field);
    }
}
?>