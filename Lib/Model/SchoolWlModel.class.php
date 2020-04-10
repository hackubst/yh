<?php
//学校地理围栏模型
class SchoolWlModel extends Model{

	//设置围栏
	public function setWl($school_id, $data){
		$num = $this->where('school_id ='.$school_id)->count();
		if($num){
			return $this->where('school_id ='.$school_id)->save($data);
		}else{
			return $this->add($data);
		}
	}

	//获取围栏
	public function getWl($where){
		return $this->where($where)->find();
	}
}