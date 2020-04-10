<?php
/**
 * 消息模型类
 */

class MessageModel extends Model
{
    // 消息id
    public $message_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $message_id 消息ID
     * @return void
     * @todo 初始化消息id
     */
    public function MessageModel($message_id)
    {
        parent::__construct('message');

        if ($message_id = intval($message_id))
		{
            $this->message_id = $message_id;
		}
    }

    /**
     * 获取消息信息
     * @author 姜伟
     * @param int $message_id 消息id
     * @param string $fields 要获取的字段名
     * @return array 消息基本信息
     * @todo 根据where查询条件查找消息表中的相关数据并返回
     */
    public function getMessageInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 添加消息
     * @author 姜伟
     * @param array $arr 消息信息数组
     * @return boolean 操作结果
     * @todo 添加消息
     */
    public function addMessage($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
		$arr['IP'] = get_client_ip();
		$arr['area'] = getIPSource($arr['IP']);

        return $this->add($arr);
    }

    /**
     * 删除消息
     * @author 姜伟
     * @param int $message_id 消息ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delMessage($message_id)
    {
        if (!is_numeric($message_id)) return false;
        return $this->where('message_id = ' . $message_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取消息数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的消息数量
     * @todo 根据where子句获取消息数量
     */
    public function getMessageNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询消息信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 消息基本信息
     * @todo 根据SQL查询字句查询消息信息
     */
    public function getMessageList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 修改消息
     * @author 姜伟
     * @param array $arr 消息信息数组
     * @return boolean 操作结果
     * @todo 修改消息信息
     */
    public function editMessage($arr)
    {
        return $this->where('message_id = ' . $this->message_id)->save($arr);
    }
}
