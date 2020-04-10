<?php
/**
 * ������Ѷ������Model
 *
 * @access public
 * @author zhengzhen
 * @date 2014/2/25
 *
 */
class GeneralNoticeModel extends NoticeBaseModel
{
	/**
	 * ��ȡ�����б�
	 *
	 * @param string $limit �޶���ȡ��¼��ʼ��������Ĭ��''
	 * @param string $order ����Ĭ��''
	 * @param string $where ��ѯ������Ĭ��''
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
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��15
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_notice��$fields�ֶΣ���$rows������ҳ����$order����
	 *
	 */
	public function getGeneralNoticeListPage($where = '', $rows = 15)
	{
		$total = $this->getTotal($where);
		if(!$total)
		{
			return false;
		}
		
		//��ҳ����
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
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��15
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_notice��$fields�ֶΣ���$rows������ҳ����$order����
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
		
		//��ҳ����
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
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��5
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_notice��$fields�ֶΣ���$rows������ҳ����$order����
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
		
		//��ҳ����
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
				//ʵ��ת��
				$val['contents'] = htmlspecialchars_decode($val['contents']);
				$result[$key]['contents'] = filterAndSubstr($val['contents'], 200, '<p><a><br>');
			}
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * ��ȡָ��ID��������
	 *
	 * @param int $id ����ID
	 * @return mixed
	 * @author zhengzhen
	 * @todo ��ȡ��tp_notice��notice_idΪ$id�����ֶ�ֵ��ͬʱ��ȡ��tp_notice_txt��contentsֵ
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
	 * ��ȡ������Ϣ
	 *
	 * @param int $id ����ID
	 * @param string $fields ��ȡ�ֶ��б�����԰�Ƕ��ŷָ���Ĭ��''���������ֶ�
	 * @return mixed �ɹ�����������Ϣ�����򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������notice_idΪ$id����ѯ��tp_notice����$fields�ֶ�ֵ�����keywords��description�ֶ�ֵΪ�գ����Ա������
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
	 * ������������
	 *
	 * @param int $id ����ID
	 * @param int $serial �����
	 * @return bool �ɹ�����true�����򷵻�false
	 * @author zhengzhen
	 * @todo ���±�tp_notice��notice_idΪ$id��serialΪ$serial
	 *
	 */
	public function setSerial($id, $serial)
	{
		return $this->setNoticeInfo($id, array('serial' => $serial));
	}
}
?>