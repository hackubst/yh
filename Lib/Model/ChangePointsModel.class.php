<?php
class ChangePointsModel extends Model{

	//积分明细模型
	const TABLE_NAME = 'user_change_points';
	const PRIMARY_KEY = 'id';
	public function ChangePointsModel()
	{
		$this->db(0);
		$this->tableName= C('DB_PREFIX').self::TABLE_NAME; //设置表名称

	}

	//增加用户积分并且记录
	public function addData($data)
	{
		$user_obj = new UserModel();
		$user_obj ->incIntegral($data['user_id'],10);
		return $this->add($data);
	}

}