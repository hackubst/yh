<?php
/**
 * 移动端导航、友情链接购买Model
 *
 * @author zhengzhen
 * @date 2014/2/24
 *
 */
class LinkModel extends Model
{
    /**
	 * 添加友情链接
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_link插入一条记录
	 *
	 */
    public function addLink(array $data)
    {
		if(isset($data['link_logo']) && false !== strpos($data['link_logo'], C('IMG_DOMAIN')))
		{
			$data['link_type'] = 2;
			$data['link_logo'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['link_logo']);
		}
		else
		{
			$data['link_type'] = 1;
		}
		$this->create($data);
		return $this->add();
    }
    
    /**
	 * 修改友情链接
	 *
	 * @param int $id 友情链接ID
	 * @param array $data 更新数据
	 * @return mixed 成功返回受影响记录数，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_link中link_id为$id的数据为$data
	 *
	 */
    public function editLink($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		if(isset($data['link_logo']) && false !== strpos($data['link_logo'], C('IMG_DOMAIN')))
		{
			$data['link_type'] = 2;
			$data['link_logo'] = str_replace(C('IMG_DOMAIN') . '/Uploads', '##img_domain##', $data['link_logo']);
		}
		else
		{
			$data['link_type'] = 1;
		}
		return $this->where('link_id=' . $id)->setField($data);
    }
    
    /**
	 * 获取友情链接信息
	 *
	 * @param int $id 友情链接ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回友链信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_link中link_id为$id的信息
	 *
	 */
    public function getLink($id, $fields = '')
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
		$result = $_this->where('link_id=' . $id)->find();
		if($result)
		{
			if($result['link_logo'] && false !== strpos($result['link_logo'], '##img_domain##'))
			{
				$result['link_logo'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $result['link_logo']);
			}
		}
		return $result;
    }
    
    /**
	 * 删除友情链接
	 *
	 * @param int $id 友情链接ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_link中link_id为$id的记录，同时删除logo图片（如果有）
	 *
	 */
    public function deleteLink($id)
    {
		if($id < 0)
		{
			return false;
		}
		$linkLogoPath = $this->getLink($id, 'link_logo');
		$result = $this->where('link_id=' . $id)->delete();
		if($result)
		{
			if($linkLogoPath['link_logo'])
			{
				$linkLogoFile = str_replace('##img_domain##', APP_PATH . 'Uploads', $linkLogoPath['link_logo']);
				@unlink($linkLogoFile);
			}
		}
		return $result;
    }
	
	/**
	 * 设置友情链接排序号
	 *
	 * @param int $id 友情链接ID
	 * @param bool $serial 排序号
	 * @return mixed 成功返回受影响记录数，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_link中link_id为$id的serial为$serial
	 *
	 */
    public function setSerial($id, $serial)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('link_id=' . $id)->setField('serial', $serial);
    }
    
    /**
	 * 设置是否在前台页面底部显示
	 *
	 * @param int $id 友情链接ID
	 * @param bool $isuse 是否显示
	 * @return mixed 成功返回受影响记录数，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_link中link_id为$id的isuse为$isuse
	 *
	 */
    public function setIsuse($id, $isuse)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('link_id=' . $id)->setField('isuse', $isuse);
    }
    
    /**
	 * 获取页面底部链接列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回底部链接列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及is_bottom_show=1，查询表tp_link，获取$limit条数
	 *
	 */
    public function getBottomLinkList($limit = '', $where = '')
    {
		$condition = 'is_bottom_show=1';
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		return $this->getAllLinkList($limit, $condition);
    }
    
    /**
	 * 获取所有链接列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $where 查询条件，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回所有链接列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_link，获取$limit条数
	 *
	 */
    public function getAllLinkList($limit = '', $where = '', $fields = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		if($where)
		{
			$_this = $_this->where($where);
		}
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		$result = $_this->order('serial')->select();
		if($result)
		{
			if(!$fields || ($fields && false !== strpos($fields, 'link_logo')))
			{
				foreach($result as $key => $val)
				{
					if($val['link_logo'])
					{
						$result[$key]['link_logo'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $val['link_logo']);
					}
				}
			}
		}
		return $result;
    }
	
	/**
	 * 获取分页的链接列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回用户建议列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_link中所有字段，按$rows条数分页
	 *
	 */
	public function getAllLinkListPage($where, $rows = 15)
	{
		$total = $this->where($where)->count();
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$result = $this->getAllLinkList($limit);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
	}
	
	/**
	 * 下载链接资源，即将AZ上表tp_link相应记录拷贝到本地对应表中
	 *
	 * @param int $id 文章ID
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 读取AZ上表tp_link中link_id,link_name,link_url，保存至本地表tp_link中
	 *
	 */
    public function downloadLinkSource($id)
    {
		if($id < 0)
		{
			return false;
		}
		$operatingSource = new OperatingSourceModel();
		$linkInfo = $operatingSource->getLinkPurchaseSiteSource($id);
		
		if(!$linkInfo)
		{
			return false;
		}
		
		//插表tp_link
		$data = array();
		$data['az_link_id'] 	= $linkInfo['link_id'];
		$data['link_type'] 		= 1;//文字型
		$data['is_bottom_show'] = 1;
		$data['link_name'] 		= $linkInfo['link_name'];
		$data['link_url'] 		= $linkInfo['link_url'];
		$data['isuse'] 			= 1;
		
		return $this->addLink($data);
    }
}
?>