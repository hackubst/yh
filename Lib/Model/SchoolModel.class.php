<?php
//学校模型
class SchoolModel extends Model{

	

	/**
	 * 获取学校列表
	 * @param  string $where [条件]
	 * @return [array]        [学校列表]
	 */
	public function getSchoolList($where = '', $fields='', $order ='serial', $limit= ''){
		return $this->field($fields)->where($where)->order($order)->limit($limit)->select();
	}

	//添加学校
	public function addSchool($data){
		$result = $this->add($data);
		return $result;
	}


	public function getSchoolNum($where =''){
		$num = $this->where($where)->count();
		return $num;
	}

	/**
	 * 获取学校
	 * @param  int $school_id 学校id
	 * @param  string $field   查询的字段名，默认为空，取全部
	 * @return array           学校
	 */
	public function getSchool($school_id, $field= ''){
		$school_info = $this->where('school_id ='. $school_id)->field($field)->find();
		return $school_info;
	}

	/**
	 * 修改学校
	 * @param  int $school_id 学校id
	 * @param  array $data     学校数组
	 * @return [type]           [description]
	 */
	public function editSchool($school_id, $data){
		if (!is_numeric($school_id) || !is_array($data)) return false;
		return $this->where('school_id ='.$school_id)->save($data);
	}

	/**
	 * 删除学校
	 * @param  int $school_id 学校id
	 * @return [type]           [description]
	 */
	public function delSchool($school_id){
		if (!is_numeric($school_id)) return false;
		return $this->where('school_id = ' . $school_id)->delete();
	}


	//生成计算距离的sql字段
	public static function getDistanceSql($lon, $lat, $return_where = false)
	{
		return $return_where ? ' SQRT(POWER(center_lng - 120.01147, 2) + POWER(center_lat - 30, 2)) <= ' : 'center_lng, center_lat, SQRT(POWER(center_lng - ' . $lon . ', 2) + POWER(center_lat - ' . $lat . ', 2)) AS distance';
	}
}