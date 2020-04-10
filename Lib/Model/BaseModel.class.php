<?php
/**
 * 模型中间类，根据角色获取相应信息
 */

class BaseModel extends Model
{
	public $area_id;
    /**
     * 架构函数
     * 取得DB类的实例对象 字段检查
     * @access public
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
	public function __construct($name='',$tablePrefix='',$connection='')
	{
        parent::__construct($name, $tablePrefix, $connection);
    }

    /**
     * 指定查询条件 支持安全过滤
     * @access public
     * @param mixed $where 条件表达式
     * @param mixed $parse 预处理参数
     * @return Model
     */
    /*public function where($where,$parse=null){
        if(!is_null($parse) && is_string($where)) {
            if(!is_array($parse)) {
                $parse = func_get_args();
                array_shift($parse);
            }
            $parse = array_map(array($this->db,'escapeString'),$parse);
            $where =   vsprintf($where,$parse);
        }elseif(is_object($where)){
            $where  =   get_object_vars($where);
        }
        if(is_string($where) && '' != $where){
            $map    =   array();
            $map['_string']   =   $where;
            $where  =   $map;
        }
        if(isset($this->options['where'])){
            $this->options['where'] =   array_merge($this->options['where'],$where);
        }else{
            $this->options['where'] =   $where;
        }
        
		$area_id = intval(session('area_id'));
		$role_type = intval(session('role_type'));
		$where = $this->options['where']['_string'];
		
		if ($role_type == 1)
		{
			//管理员，取全部
		}
		elseif ($role_type == 2)
		{
			//商家，根据area_id取
			$where .= $where ? ' AND area_id = ' . $area_id : ' area_id = ' . $area_id;
		}
		elseif ($role_type == 3)
		{
			//用户，根据area_id取
			$where .= $where ? ' AND area_id = ' . $area_id : ' area_id = ' . $area_id;
		}
		elseif ($role_type == 4)
		{
			//代理商，根据province_id/city_id/area_id取，后期扩张
			#$where .= $where ? ' AND area_id = ' . $area_id : ' area_id = ' . $area_id;
		}
		#echo "<pre>";
		#print_r($user_info);
		#echo $where;
		#die;
		$this->options['where'] =   $where;

        return $this;
	}*/

    /**
     * 查询数据集
     * @access public
     * @param array $options 表达式参数
     * @return mixed
     */
    public function select($options=array()) {
		
		$area_id = intval(session('area_id'));
		$area_id = $this->area_id == 0 ? 0 : $area_id;
		$role_type = intval(session('role_type'));
		$where = $this->options['where']['_string'];
		
		if ($area_id)
		{
			if ($role_type == 1)
			{
				$tag = strpos($where, 'user_id');
				$tag = is_numeric($tag) ? $tag : strpos($where, 'admin_id');
				//管理员，取全部
			}
			elseif ($role_type == 2)
			{
				$tag = strpos($where, 'merchant_id');
				$tag = is_numeric($tag) ? $tag : strpos($where, 'user_id');
				//商家，根据area_id取
				if (!is_numeric($tag))
				{
					$where .= $where ? ' AND ' . $this->trueTableName . '.area_id = ' . $area_id : $this->trueTableName . '.area_id = ' . $area_id;
				}
			}
			elseif ($role_type == 3)
			{
				$tag = strpos($where, 'user_id');
				$tag = is_numeric($tag) ? $tag : strpos($where, 'merber_id');
				//用户，根据area_id取
				if (!is_numeric($tag))
				{
					$where .= $where ? ' AND ' . $this->trueTableName . '.area_id = ' . $area_id : $this->trueTableName . '.area_id = ' . $area_id;
				}
			}
			elseif ($role_type == 4)
			{
				//代理商，根据province_id/city_id/area_id取，后期扩张
				#$where .= $where ? ' AND ' . $this->trueTableName . '.area_id = ' . $area_id : $this->trueTableName . '.area_id = ' . $area_id;
			}
		}
		#echo "<pre>";
		#print_r($user_info);
		#echo $where;
		#die;
		$this->options['where'] =   $where;

        if(is_string($options) || is_numeric($options)) {
            // 根据主键查询
            $pk   =  $this->getPk();
            if(strpos($options,',')) {
                $where[$pk]     =  array('IN',$options);
            }else{
                $where[$pk]     =  $options;
            }
            $options            =  array();
            $options['where']   =  $where;
        }elseif(false === $options){ // 用于子查询 不查询只返回SQL
            $options            =  array();
            // 分析表达式
            $options            =  $this->_parseOptions($options);
            return  '( '.$this->db->buildSelectSql($options).' )';
        }
        // 分析表达式
        $options    =  $this->_parseOptions($options);
        $resultSet  = $this->db->select($options);
        if(false === $resultSet) {
            return false;
        }
        if(empty($resultSet)) { // 查询结果为空
            return null;
        }

        $this->_after_select($resultSet,$options);
        return $resultSet;
    }

    /**
     * 新增数据
     * @access public
     * @param mixed $data 数据
     * @param array $options 表达式
     * @param boolean $replace 是否replace
     * @return mixed
     */
    public function add($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 没有传递数据，获取当前数据对象的值
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 重置数据
                $this->data     = array();
            }else{
                $this->error    = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 分析表达式
        $options    =   $this->_parseOptions($options);
        // 数据处理
        $data       =   $this->_facade($data);
        if(false === $this->_before_insert($data,$options)) {
            return false;
        }
        // 写入数据到数据库
        $result = $this->db->insert($data,$options,$replace);
        if(false !== $result ) {
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                // 自增主键返回插入ID
                $data[$this->getPk()]  = $insertId;
                $this->_after_insert($data,$options);
                return $insertId;
            }
            $this->_after_insert($data,$options);
        }
        return $result;
    }
}
