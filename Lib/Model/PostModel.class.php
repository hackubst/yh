<?php

class PostModel extends Model{

	const TABLE_NAME = 'user_posts';
	const PRIMARY_KEY = 'id';

	public function PostModel()
	{
		$this->db(0);
		$this->tableName= C('DB_PREFIX').self::TABLE_NAME; //设置表名称
	}

	//数据插入
	public function addPoat($data)
	{
		$data['creattime'] = time();
		$points_id =  $this->add($data);

		//判断是否是首日发帖,是则加10积分
		$todayStart = strtotime(date('Y-m-d'));
		$todayLast = $todayStart + 86400;
		$wher = 'user_id = '.$data['user_id'].' and creattime between '.$todayStart." and ".$todayLast;

		$is_has = $this->where($where)->count();
		if($is_has  == 1){
			$changepoints_obj = new ChangePointsModel();
			$insert['user_id'] = $data['user_id'];
			$insert['points'] = 10;
			$insert['type'] = 1;
			$insert['changtime'] = $data['creattime'];
			$insert['explain'] = "每日发帖奖励";
			$insert['increase'] = 1;  
			$insert['post_id'] = $points_id; 
			return $changepoints_obj->addData($insert);
		} else {
			return $points_id;
		}
	}

	//增加字段的值
	public function incFieldNum($field,$id,$num=1)
	{
		$where = "id = ".$id;
		$change = $this->where($where)->setInc($field,$num);
	}

	//减少字段的值
	public function decFieldNum($field,$id,$num=1)
	{
		$where = "id = ".$id;
		$change = $this->where($where)->setDec($field,$num);
	}


	//获取所有帖子的基本信息
	public function getPostsInfo($wher)
	{	
		return $this->find();
	}


	//获取帖子的所有记录数
	public function getGiftNum($where = '')
	{	
		$where = 'is_delete = 0  '.(!empty($where) ? 'and'.$where :'');
		return $count = $this->where($where)->count('id');

	}


	//获取某个帖子的字段值
	public function getFieldNum($field,$post_id)
	{
		return $this->where('id='.$post_id)->getfield($field);
	}


	/**
	 * 根据传入的查询条件查找帖子
	 * @author 姜伟
	 * @param string $fields
	 * @param string $where
	 * @param string $groupby
	 * @return array $user_list
	 * @todo 根据where子句查询订单表中的订单信息，并以数组形式返回
	 */
	public function getPostList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
	{
		return $this->field($fields)->where($where)->group($groupby)->order($order)->limit()->select();

		 // return $this->getLastSql();
	}


	/*
	帖子的字段过滤
	*/
	public function getListData($data)
	{
		 
		foreach($data as $k =>$v) {

			$data[$k]['creattime'] = date('Y-m-d H:i:s',$v['creattime']);
			$data[$k]['user_name'] = M('users')->where('user_id = 62277')->getfield('username');
			$data[$k]['content'] = $this->strContent($v['content']);

		}

		return $data;
	}


	//content 内容截取
	public function strContent($content,$num=50)
	{

		$content_02 = htmlspecialchars_decode($content);//把一些预定义的 HTML 实体转换为字符
        $content_03 = str_replace("&nbsp;","",$content_02);//将空格替换成空
        $contents = strip_tags($content_03);//函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        return mb_substr($contents, 0, $num,"utf-8").".....";//返回字符串中的前100字符串长度的字符

	}




























}