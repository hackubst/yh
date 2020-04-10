<?php
/**
 * 消息记录ID模型类
 */

class PushLogModel extends BaseModel
{
    // 消息记录IDid
    public $push_log_id;

    const ONLINE_PAY = 1;
    const RANK_LIST_REWARD = 2;
    /**
     * 构造函数
     * @author 姜伟
     * @param $push_log_id 消息记录IDID
     * @return void
     * @todo 初始化消息记录IDid
     */
    public function PushLogModel($push_log_id)
    {
        parent::__construct('push_log');

        if ($push_log_id = intval($push_log_id))
		{
            $this->push_log_id = $push_log_id;
		}
    }

    /**
     * 获取消息记录ID信息
     * @author 姜伟
     * @param int $push_log_id 消息记录IDid
     * @param string $fields 要获取的字段名
     * @return array 消息记录ID基本信息
     * @todo 根据where查询条件查找消息记录ID表中的相关数据并返回
     */
    public function getPushLogInfo($where, $fields = '', $order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改消息记录ID信息
     * @author 姜伟
     * @param array $arr 消息记录ID信息数组
     * @return boolean 操作结果
     * @todo 修改消息记录ID信息
     */
    public function editPushLog($arr)
    {
        return $this->where('push_log_id = ' . $this->push_log_id)->save($arr);
    }

    /**
     * 添加消息记录
     * @author 姜伟
     * @param array $arr 消息记录
     * @return boolean 操作结果
     * @todo 添加消息记录
     */
    public function addPushLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除消息记录ID
     * @author 姜伟
     * @param int $push_log_id 消息记录IDID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPushLog($push_log_id)
    {
        if (!is_numeric($push_log_id)) return false;
		return $this->where('push_log_id = ' . $push_log_id)->delete();
    }

    /**
     * 根据where子句获取消息记录ID数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的消息记录ID数量
     * @todo 根据where子句获取消息记录ID数量
     */
    public function getPushLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询消息记录ID信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 消息记录ID基本信息
     * @todo 根据SQL查询字句查询消息记录ID信息
     */
    public function getPushLogList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取消息记录ID列表页数据信息列表
     * @author 姜伟
     * @param array $push_log_list
     * @return array $push_log_list
     * @todo 根据传入的$push_log_list获取更详细的消息记录ID列表页数据信息列表
     */
    public function getListData($push_log_list)
    {
		return $push_log_list;
    }
}
