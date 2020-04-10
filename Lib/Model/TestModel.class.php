<?php
/**
 * 测试模型类
 */

class TestModel extends Model
{
    // 测试id
    public $test_id;
        
    protected $table_sql = "CREATE TABLE `tp_test` (
                                  `test_id` int(11) NOT NULL AUTO_INCREMENT,
                                  `username` varchar(16) NOT NULL DEFAULT '' COMMENT '用户名',
                                  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
                                  PRIMARY KEY (`test_id`)
                                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                                ";

    protected $check_fields = array(
        'test_id' => "ALTER TABLE `tp_test` ADD COLUMN `test_id` int(11) NOT NULL AUTO_INCREMENT",
        'username' =>  "ALTER TABLE `tp_test` ADD COLUMN `username`  varchar(16) NOT NULL DEFAULT '' COMMENT '用户名'",
        'password' =>  "ALTER TABLE `tp_test` ADD COLUMN `password`  varchar(32) NOT NULL DEFAULT '' COMMENT '密码'",
        'nickname' =>  "ALTER TABLE `tp_test` ADD COLUMN `nickname`  varchar(16) NOT NULL DEFAULT '' COMMENT '昵称'",
        'headimgurl' =>  "ALTER TABLE `tp_test` ADD COLUMN `headimgurl`  varchar(255) NOT NULL DEFAULT '' COMMENT '头像'",
        );

    /**
     * 构造函数
     * @author 姜伟
     * @param $test_id 测试ID
     * @return void
     * @todo 初始化测试id
     */
    public function TestModel($test_id)
    {
        
        check_table_exist(C('DB_PREFIX').'test', $this->table_sql);
        check_field_exist($this->getDbFields(), $this->check_fields);
        parent::__construct('test');
        if ($test_id = intval($test_id))
		{
            $this->test_id = $test_id;
		}
    }

    /**
     * 获取测试信息
     * @author 姜伟
     * @param int $test_id 测试id
     * @param string $fields 要获取的字段名
     * @return array 测试基本信息
     * @todo 根据where查询条件查找测试表中的相关数据并返回
     */
    public function getTestInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改测试信息
     * @author 姜伟
     * @param array $arr 测试信息数组
     * @return boolean 操作结果
     * @todo 修改测试信息
     */
    public function editTest($arr)
    {
        return $this->where('test_id = ' . $this->test_id)->save($arr);
    }

    /**
     * 添加测试
     * @author 姜伟
     * @param array $arr 测试信息数组
     * @return boolean 操作结果
     * @todo 添加测试
     */
    public function addTest($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除测试
     * @author 姜伟
     * @param int $test_id 测试ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delTest($test_id)
    {
        if (!is_numeric($test_id)) return false;
        return $this->where('test_id = ' . $test_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取测试数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的测试数量
     * @todo 根据where子句获取测试数量
     */
    public function getTestNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询测试信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 测试基本信息
     * @todo 根据SQL查询字句查询测试信息
     */
    public function getTestList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取测试列表页数据信息列表
     * @author 姜伟
     * @param array $test_list
     * @return array $test_list
     * @todo 根据传入的$test_list获取更详细的测试列表页数据信息列表
     */
    public function getListData($test_list)
    {
        
		return $test_list;
    }

}
