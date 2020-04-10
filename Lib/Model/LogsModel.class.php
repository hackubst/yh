<?php
/**
 * 日志模型类
 */

class LogsModel extends Model
{
    // 日志id
    public $logs_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $logs_id 日志ID
     * @return void
     * @todo 初始化日志id
     */
    public function LogsModel($logs_id)
    {
        parent::__construct('logs');

        if ($logs_id = intval($logs_id))
		{
            $this->logs_id = $logs_id;
		}
    }

    /**
     * 获取日志信息
     * @author 姜伟
     * @param int $logs_id 日志id
     * @param string $fields 要获取的字段名
     * @return array 日志基本信息
     * @todo 根据where查询条件查找日志表中的相关数据并返回
     */
    public function getLogsInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改日志信息
     * @author 姜伟
     * @param array $arr 日志信息数组
     * @return boolean 操作结果
     * @todo 修改日志信息
     */
    public function editLogs($arr)
    {
        return $this->where('logs_id = ' . $this->logs_id)->save($arr);
    }

    /**
     * 添加日志
     * @author 姜伟
     * @param array $arr 日志信息数组
     * @return boolean 操作结果
     * @todo 添加日志
     */
    public function addLogs($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除日志
     * @author 姜伟
     * @param int $logs_id 日志ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delLogs($logs_id)
    {
        if (!is_numeric($logs_id)) return false;
        return $this->where('logs_id = ' . $logs_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取日志数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的日志数量
     * @todo 根据where子句获取日志数量
     */
    public function getLogsNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询日志信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 日志基本信息
     * @todo 根据SQL查询字句查询日志信息
     */
    public function getLogsList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取日志列表页数据信息列表
     * @author 姜伟
     * @param array $logs_list
     * @return array $logs_list
     * @todo 根据传入的$logs_list获取更详细的日志列表页数据信息列表
     */
    public function getListData($logs_list)
    {
		foreach ($logs_list AS $k => $v)
		{
			$logs_list[$k]['link_logs'] = U('/FrontLogs/logs_detail/logs_id/' . $v['logs_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$logs_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$logs_list[$k]['status'] = $status;
		}

		return $logs_list;
    }
}
