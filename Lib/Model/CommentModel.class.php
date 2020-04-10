<?php
class CommentModel extends Model{

	const TABLE_NAME = 'user_posts_comments';
	const PRIMARY_KEY = 'id';

	public $post; //PostModel的模型类;

	public function CommentModel()
	{
		$this->db(0);
		$this->tableName= C('DB_PREFIX').self::TABLE_NAME; //设置表名称

		if(empty($this->post)){
			$this->post  = new PostModel();
		}
	}



	//评论数据插入
	public function addData($data)
	{
		$data['creattime'] = time();
		//增加帖子的评论数
		$this->post->incFieldNum('commentNum',$data['post_id']);
		//输入插入
		$result_one = $this->add($data);
		if($result_one) {
			return true;
		} else {
			return false;
		}
	}


	//查找相关的帖子的评论记录
	public function getRewordList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
	{
		return  $this->field($fields)->where($where)->group($groupby)->order($order)->limit()->select();
	}


	//数据过滤
	public function getRewordData($data)
	{
		foreach ($data as $key => $value) {
			$data[$key]['username'] = M('users')->where('user_id ='.$value['user_id'])->getField('username');
			$data[$key]['creattime'] = date('Y-m-d H:i:s',$value['creattime']);
		}
		return $data;

	}









}