<?php
/**
 * ������Ѷ������Model
 *
 * @access public
 * @author zhengzhen
 * @date 2014/2/25
 *
 */
class GeneralArticleModel extends ArticleBaseModel
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
    public function getGeneralArticleList($limit = '', $order = '', $where = '')
    {
		$_table = $this->getTableName();
		$fields = $_table . '.article_id,' . $_table . '.title,' . $_table . '.clickdot,' .
				$_table . '.addtime,' . $_table . '.serial,' . $_table . '.isuse,a_s.article_sort_name';
		$join = C('DB_PREFIX') . 'article_sort AS a_s ON a_s.article_sort_id=' . $_table . '.article_sort_id';
		return $this->getArticleList($limit, $order, $fields, $where, $join);
    }
	
	/**
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��15
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_article��$fields�ֶΣ���$rows������ҳ����$order����
	 *
	 */
	public function getGeneralArticleListPage($where = '', $rows = 15)
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
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��15
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_article��$fields�ֶΣ���$rows������ҳ����$order����
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
		
		//��ҳ����
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
	 * ��ȡ��ҳ�������б�
	 *
	 * @param string $where ��ѯ������Ĭ��''
	 * @param int $rows ÿҳ��ʾ����Ĭ��5
	 * @return mixed �ɹ����������б����飬���򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������$where����ѯ��tp_article��$fields�ֶΣ���$rows������ҳ����$order����
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
		
		//��ҳ����
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
	 * @todo ��ȡ��tp_article��article_idΪ$id�����ֶ�ֵ��ͬʱ��ȡ��tp_article_txt��contentsֵ
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
	 * ��ȡ������Ϣ
	 *
	 * @param int $id ����ID
	 * @param string $fields ��ȡ�ֶ��б�����԰�Ƕ��ŷָ���Ĭ��''���������ֶ�
	 * @return mixed �ɹ�����������Ϣ�����򷵻�false
	 * @author zhengzhen
	 * @todo ͨ������article_idΪ$id����ѯ��tp_article����$fields�ֶ�ֵ�����keywords��description�ֶ�ֵΪ�գ����Ա������
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
	 * ������������
	 *
	 * @param int $id ����ID
	 * @param int $serial �����
	 * @return bool �ɹ�����true�����򷵻�false
	 * @author zhengzhen
	 * @todo ���±�tp_article��article_idΪ$id��serialΪ$serial
	 *
	 */
	public function setSerial($id, $serial)
	{
		return $this->setArticleInfo($id, array('serial' => $serial));
	}
}
?>