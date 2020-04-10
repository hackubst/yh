<?php
class ChangeMoneyModel extends Model{

	const TABLE_NAME = 'user_change_moneys';
	const PRIMARY_KEY = 'id';

	public function ChangeMoneyModel()
	{
		$this->db(0);
		$this->tableName= C('DB_PREFIX').self::TABLE_NAME; //设置表名称

	}


	public function addData($data)
	{
		$gift_obj = new GiftModel($data['gift_id']);

		$where = 'gift_id = '.$data['gift_id'];
		$result = $gift_obj->getGiftInfo($where,'money');
		$money = $result['money'];
		$data['moneys'] = $data['num'] * $money;  //获取到的总礼物的价值
		$user_obj = new UserModel();
		$user_money = $user_obj->gettoLeftMoney($data['user_id']);
		
		if($user_money < $data['moneys']){

			ApiModel::returnResult(-1, [], '金额不足');
		}
		$data['changetime'] = time();
		$data['type'] = 1;
		$data['increase'] = 0;
		$data['explain'] = '打赏帖子支出';
		$res = $this->add($data);   //资金明细

		//return $this->getLastSql();
		$user_obj = new UserModel();
		$user_obj -> operationMoney($data['user_id'],$data['moneys']); //扣除用户金额

		return $res;
	}

	//查找相关的帖子的打赏记录
	public function getRewordList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
	{

		return $this->field($fields)->where($where)->group($groupby)->order($order)->limit()->select();

		// return $this->getLastSql();
	
	}


	

	//数据过滤
	public function getRewordData($data)
	{
		foreach ($data as $key => $value) {
			$data[$key]['username'] = M('users')->where('user_id ='.$value['user_id'])->getField('username');
			$data[$key]['giftname'] = M('gift')->where('gift_id ='.$value['gift_id'])->getField('gift_name');
			$data[$key]['changetime'] = date('Y-m-d H:i:s',$value['changetime']);
		}

		return $data;

	}

	//获取打赏总记录数
	public function getRecordtNum($id)
	{
		$where = 'post_id = '.$id. ' and type=1';

		return $this->where($where)->count();


		//return $this->getLastSql();
	}
















}