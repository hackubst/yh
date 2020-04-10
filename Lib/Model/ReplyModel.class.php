<?php
/**
 * 环信信息模型类
 */

class ReplyModel extends Model
{
    // 消息id
    public $reply_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $reply_id 消息ID
     * @return void
     * @todo 初始化消息id
     */

    //回复类型
    const USER_REPLAY  = 3;//用户
    const TEACHER_REPLAY  = 2;//讲师

    public function ReplyModel($reply_id)
    {
        parent::__construct('reply');

        if ($reply_id = intval($reply_id))
		{
            $this->reply_id = $reply_id;
		}
    }

    /**
     * 获取消息信息
     * @author 姜伟
     * @param int $reply_id 消息id
     * @param string $fields 要获取的字段名
     * @return array 消息基本信息
     * @todo 根据where查询条件查找消息表中的相关数据并返回
     */
    public function getReplyInfo($where, $fields = '')
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
    public function addReply($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
//		$arr['IP'] = get_client_ip();
//		$arr['area'] = getIPSource($arr['IP']);

        return $this->add($arr);
    }

    /**
     * 删除消息
     * @author 姜伟
     * @param int $reply_id 消息ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delReply($reply_id)
    {
        if (!is_numeric($reply_id)) return false;
        return $this->where('reply_id = ' . $reply_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取消息数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的消息数量
     * @todo 根据where子句获取消息数量
     */
    public function getReplyNum($where = '')
    {
        return $this->where($where)->count();
    }


    //联表统计数目
    public function getReplyWithUserNum($where = '')
    {
        $reply_num = $this
            ->field('u.nickname,tp_reply.addtime,tp_reply.message,tp_reply.to_user_id,tp_reply.from_user_id')
            ->join('tp_users as u on u.user_id = tp_reply.from_user_id')//发起留言个人信息
            ->join('tp_users as tu on tu.user_id = tp_reply.to_user_id')//收到留言的个人信息
            ->where($where)
            ->limit()
            ->count();
        return $reply_num;
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
    public function getReplyList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }


    public function getLastReplyList($where = '')
    {
        //取用户离现在最近的一条回复
        $fields = 'substring_index(group_concat(reply_id order by addtime desc),",",1) as reply_id';
        $group = 'from_user_id';
        $ids = $this->field($fields)->where($where)->group($group)->select();
        $ids_str = '';
        foreach($ids as $k=>$v){
            if($k==0){
                $ids_str.=$v['reply_id'];
            }else{
                $ids_str.=','.$v['reply_id'];
            }
        }
        $reply_list = $this->field('nickname,headimgurl,tp_reply.addtime,message,user_id,tp_reply.from_user_id')->join('tp_users on from_user_id=user_id')->where('reply_id in ('.$ids_str.')')->order('addtime desc')->limit()->select();

        foreach($reply_list as $k=>$v){
            if($reply_list[$k]['from_user_id'] == 0){
                $reply_list[$k]['nickname'] = "客服";
            }

            if(UserModel::checkVip($v['user_id'])){
                $reply_list[$k]['is_vip'] = 1;
            }else{
                $reply_list[$k]['is_vip'] = 0;
            }
        }



//        //拼接用户信息
//        $user_obj = new UserModel();
//        foreach($reply_list as $k=>$v){
//            $user_info = $user_obj->field('realname,headimgurl')->where('user_id='.$v['from_user_id'])->find();
//            if($user_info){
//                $reply_list[$k]['realname'] = $user_info['realname'];
//            }
//        }

        return $reply_list;
    }

    public function getReplyListByUsername($where){
        $reply_info = $this
            ->field('u.user_id as f_uid,u.nickname,u.headimgurl,tp_reply.addtime,u.username as f_uname,tu.username as t_uname,tp_reply.message,tp_reply.to_user_id,tp_reply.from_user_id,tp_reply.reply_id,tp_reply.message_id')
            ->join('tp_users as u on u.user_id = tp_reply.from_user_id')//发起留言个人信息
            ->join('tp_users as tu on tu.user_id = tp_reply.to_user_id')//收到留言的个人信息
            ->where($where)
            ->order('addtime desc')
            ->limit('0,5')
            ->select();

        //var_dump($this->getLastSql());

        $reply_info = array_reverse($reply_info);

        foreach($reply_info as $k=>$v){
            $reply_info[$k]['time'] = date('Y-m-d H:i:s',$reply_info[$k]['addtime']);
            if(empty($reply_info[$k]['realname'])){
                $reply_info[$k]['realname'] = $reply_info[$k]['mobile'];
            }
        }

        return $reply_info;
    }


    //获取用户信息和留言信息
    public function getMessageUserInfo($where,$orderby){
        $reply_info = $this
            ->field('u.nickname,tp_reply.addtime,tp_reply.message,tp_reply.to_user_id,tp_reply.from_user_id')
            ->join('tp_users as u on u.user_id = tp_reply.from_user_id')//发起留言个人信息
            ->join('tp_users as tu on tu.user_id = tp_reply.to_user_id')//收到留言的个人信息
            ->where($where)
            ->order($orderby)
            ->limit()
            ->select();
        return $reply_info;
    }

    //获取用户信息和留言信息
    public function getMessageUserListNum($where){
        $user = new ReplyModel();
        $count = $user
            ->field('u.nickname,tp_reply.addtime,tp_reply.message,tp_reply.to_user_id,tp_reply.from_user_id')
            ->join('tp_users as u on u.user_id = tp_reply.from_user_id')//发起留言个人信息
            ->join('tp_users as tu on tu.user_id = tp_reply.to_user_id')//收到留言的个人信息
            ->where($where)
//            ->order($orderby)
            ->count();
        return $count;
    }

    //获取用户信息和留言信息
    public function getMessageWithData($reply_list){
        foreach($reply_list as $k=>$v){
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname','user_id='.$v['to_user_id']);
            //var_dump($user_info);
            if($user_info){
                $reply_list[$k]['to_user_nickname'] = $user_info['nickname'];
            }

            if($v['to_user_id'] == 0){
                $reply_list[$k]['to_user_nickname'] = '客服';
            }

            if($v['from_user_id'] == 0){
                $reply_list[$k]['nickname'] = '客服';
            }
        }

        return $reply_list;
    }

    public function getLastReplyListNum($where = '')
    {
        //取用户离现在最近的一条回复
        $fields = 'substring_index(group_concat(reply_id order by addtime desc),",",1) as reply_id';
        $group = 'from_user_id';
        $ids = $this->field($fields)->where($where)->group($group)->select();
        $ids_str = '';
        foreach($ids as $k=>$v){
            if($k==0){
                $ids_str.=$v['reply_id'];
            }else{
                $ids_str.=','.$v['reply_id'];
            }
        }
        $count = $this->field('realname,headimgurl,tp_reply.addtime,message,user_id')->join('tp_users on from_user_id=user_id')->where('reply_id in ('.$ids_str.')')->order('addtime desc')->count();

        return $count;
    }

    /**
     * 修改消息
     * @author 姜伟
     * @param array $arr 消息信息数组
     * @return boolean 操作结果
     * @todo 修改消息信息
     */
    public function editReply($arr)
    {
        return $this->where('reply_id = ' . $this->reply_id)->save($arr);
    }

    public function setRead($where,$arr){
        return $this->where($where)->save($arr);
    }
}
