<?php
class LikeModel extends Model{

	const TABLE_NAME = 'user_posts_like';
	const PRIMARY_KEY = 'id';

	public $post; //PostModel的模型类;

	public function LikeModel()
	{
		$this->db(0);
		$this->tableName= C('DB_PREFIX').self::TABLE_NAME; //设置表名称

		if(empty($this->post)){
			$this->post  = new PostModel();
		}
		
	}

	//点赞还是即取消点赞
	public function likeOperation($data)
	{
		$where = "post_id = ".$data['post_id']." and user_id = ".$data['user_id'];
		$is_has = $this->where($where)->find();
		//return $this->getLastSql();
		if($is_has) {
			$this->post->decFieldNum('likeNum',$data['post_id']); //自增一
			return $this->where($where)->delete();
		} else {
			$this->post->incFieldNum('likeNum',$data['post_id']);  //自减一
			$data['creattime'] = time();
			return $this->add($data);
		}
	}




}