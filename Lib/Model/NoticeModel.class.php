<?php
/**
 * 通知模型类
 */

class NoticeModel extends BaseModel
{
    // 通知id
    public $notice_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $notice_id 通知ID
     * @return void
     * @todo 初始化通知id
     */
    public function NoticeModel($notice_id)
    {
        parent::__construct('notice');

        if ($notice_id = intval($notice_id))
		{
            $this->notice_id = $notice_id;
		}
    }

    /**
     * 获取通知信息
     * @author 姜伟
     * @param int $notice_id 通知id
     * @param string $fields 要获取的字段名
     * @return array 通知基本信息
     * @todo 根据where查询条件查找通知表中的相关数据并返回
     */
    public function getNoticeInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改通知信息
     * @author 姜伟
     * @param array $arr 通知信息数组
     * @return boolean 操作结果
     * @todo 修改通知信息
     */
    public function editNotice($arr)
    {
        return $this->where('notice_id = ' . $this->notice_id)->save($arr);
    }

    /**
     * 添加通知
     * @author 姜伟
     * @param array $arr 通知信息数组
     * @return boolean 操作结果
     * @todo 添加通知
     */
    public function addNotice($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除通知
     * @author 姜伟
     * @param int $notice_id 通知ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delNotice($notice_id)
    {
        if (!is_numeric($notice_id)) return false;
		return $this->where('notice_id = ' . $notice_id)->delete();
    }

    /**
     * 根据where子句获取通知数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的通知数量
     * @todo 根据where子句获取通知数量
     */
    public function getNoticeNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询通知信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 通知基本信息
     * @todo 根据SQL查询字句查询通知信息
     */
    public function getNoticeList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }
    public function getNoticeLimitList($fields = '', $where = '', $orderby = '',$start = 0,$limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($start,$limit)->select();
    }

    /**
     * 获取通知列表页数据信息列表
     * @author 姜伟
     * @param array $notice_list
     * @return array $notice_list
     * @todo 根据传入的$notice_list获取更详细的通知列表页数据信息列表
     */
    public function getListData($notice_list)
    {
		foreach ($notice_list AS $k => $v)
		{
			$notice_list[$k]['addtime'] = date('Y-m-d H:i:s',$notice_list[$k]['addtime']);
		}

		return $notice_list;
    }

     /**
     * 获取数据库查询用的公告类型列表
     * @author 姜伟
     * @param void
     * @return array $notice_type
     * @todo 获取数据库查询用的公告类型列表
     */
    public static function getNoticeTypeList()
    {
        return array(
            '1' => '商品',
            '2' => '订单',
            '3' => '纯数字',
            '4' => '其他',
            
        );
    }

    /**
     * 获取数据库查询用的公告用户类型列表
     * @author 姜伟
     * @param void
     * @return array $user_type
     * @todo 获取数据库查询用的公告用户类型列表
     */
    public static function getUserTypeList()
    {
        return array(
            '0' => '通用',
            '1' => '管理员',
            '2' => '商家',
            '3' => '用户',
            '4' => '镖师',
        );
    }

    /**
     * 获取帮助中心文章总数
     *
     * @param string $where 查询条件，默认''
     * @return mixed 成功返回文章总数，否则返回false
     * @author zhengzhen
     * @todo 获取表tp_help中help_sort_id为$id记录总数
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
     * 获取分页的帮助列表
     *
     * @param string $where 查询条件，默认''
     * @param int $rows 每页显示数，默认15
     * @return mixed 成功返回帮助列表数组，否则返回false
     * @author zhengzhen
     * @todo 通过条件$where，查询表tp_help中$fields字段，按$rows条数分页，以$order排序
     *
     */
    public function getNoticeListPage($where = '', $rows = 15)
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
        $order = $_table . '.addtime DESC';
        $fields = $_table . '.notice_id,' . $_table . '.title,'  . $_table . '.addtime,'  . $_table . '.description';
        #$join = C('DB_PREFIX') . 'help_sort AS h_s ON h_s.help_sort_id=' . $_table . '.help_sort_id';
        $result = $this->getNonNoticeList($limit, $order, $fields, $where);
        if($result)
        {
            $result[] = $pagination;
        }
        return $result;
    }

    /**
     * 获取帮助列表
     *
     * @param string $limit 限定获取记录起始及条数，默认''
     * @param string $order 排序，默认''
     * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
     * @param string $where 查询条件，默认''
     * @param string $join 联表查询，默认''
     * @return mixed 成功返回帮助列表数组，否则返回false
     * @author zhengzhen
     * @todo 通过条件$where，查询表tp_help中$fields字段，获取$limit条数，以$order排序
     *
     */
    public function getNonNoticeList($limit = '', $order = '', $fields = '', $where = '')
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
     * 删除公告
     *
     * @param int $id 文章ID
     * @return bool 成功返回true，否则返回false
     * @author zlf
     * @todo 删除表tp_notice中notice_id为$id的记录，
     * @todo 删除公告
     *
     */
    public function deleteNotice($id)
    {
        if($id < 0)
        {
            return false;
        }
        //删除文章基本信息记录
        return $this->where('notice_id=' . $id)->delete();
    }

    /**
     * 获取文章详情
     *
     * @param int $id 文章ID
     * @param bool $isFormat 是否格式化文章详情，默认false
     * @param bool $isReplace 是否替换详情中图片域名占位符'##img_domain##'为实际域名C('IMG_DOMAIN')，默认true
     * @return mixed 成功返回文章内容，否则返回false
     * @author zlf
     * @todo 通过条件article_id为$id，查询表tp_article_txt中contents字段值，若$isFormat为true，则为表tp_article_keywords中的关键字添加标签
     *
     */
    public function getArticleContents($id, $isFormat = false, $isReplace = true)
    {
        if($id < 0)
        {
            return false;
        }
        $articleTxt = M('notice_txt');
        $result = $articleTxt->where('notice_id=' . $id)->getField('contents');
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
                    //  $tag = '<a href="">' . $val['keyword'] .'</a>';
                    //  $result = str_replace($val['keyword'], $tag, $result);
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
}
