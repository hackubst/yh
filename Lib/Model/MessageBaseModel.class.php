<?php
/**
 *  站内消息管理类。
 *  @ File Name : MessageBaseModel.class.php
 *  @ Date : 2014/2/24
 *  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
 */

require_once 'Lib/Model/UserModel.class.php';
 class MessageBaseModel extends Model
{
    /** */
    protected $messageId;
    
    /** */
    protected $Unnamed1;
    
    protected $tableName = 'message';
    /**
     * @access public
     * @todo 新增一条消息
     * @param int    $send_user_id      发送者的用户ID。  必须,如果发送者是管理员，则该值为0
     * @param int    $reply_user_id     回复者的用户ID。  必须，如果接受者是管理员，则该值为0
     * @param string $message_title     消息标题 。       必须，当是回复消息类型时，该参数传入空字符串
     * @param string $message_contents  消息详情(长字符串)。必须
     * @param int    $message_type      消息类型，具体为：1发送，2回复，3管理员群发。   默认值为1，表示是发送一条新消息
     * @param int    $main_message_id   如果是回复消息类型，值为回复的最原始消息的ID，否则为0。 默认值为0，当消息类型（即message_type）为2时，该值为必须
     * @param int    $is_advice         是否用户建议，1是，0否。    默认为0
     * @param int    $isuse             是否显示，1显示，0不显示。  默认为1，只有在管理员群发消息时，此值才可能设置
     * @return int   如果新增消息成功，则返回该消息的ID号；否则返回-1
     *  */
    public function addMessage($send_user_id, $reply_user_id, $message_title, $message_contents, $message_type = 1, $main_message_id = 0, $is_advice = 0, $isuse = 1)
    {
        if($message_type == 2 && !$main_message_id)                             //如果消息类型为回复类型，但是没有传入原消息ID，则直接返回 -1
        {
            return -1;
        }
        $UserModel1 = new UserModel($send_user_id);
        $send_user_info = $UserModel1->getUserInfo('username,realname,linkman');               //发送者的用户信息
        
        $UserModel2 = new UserModel($reply_user_id);
        $reply_user_info = $UserModel2->getUserInfo('username,realname,linkman');             //回复者的用户信息
        
     //   $send_user_info = array('username'=>'admin@admin.com');
     //   $reply_user_info = array('username'=>'admin');
        if(!$reply_user_info)
        {
            return -1;      //接受者不存在
        }
        $addtime = time();
        /* 组装数据开始 */
        $data = array(
            'is_advice'         =>  $is_advice,
            'message_type'      =>  $message_type,
            'send_user_id'      =>  $send_user_id,
            'reply_user_id'     =>  $reply_user_id,
            'send_username'     =>  ($send_user_info['linkman'])?$send_user_info['linkman']:($send_user_info['realname']?$send_user_info['realname']:$send_user_info['username']),
            'reply_username'    =>  ($reply_user_info['linkman'])?$reply_user_info['linkman']:($reply_user_info['realname']?$reply_user_info['realname']:$reply_user_info['username']),
            'message_title'     =>  $message_title,
            'is_read'           =>  0,
            'message_contents'  =>  $message_contents,
            'isuse'             =>  $isuse,
            'addtime'           =>  $addtime
        );
        if($message_type == 1)      
        {
            $data['main_message_id'] = 0;                   //如果是主动发送一条消息,此值则为0
        }
        else                       
        {                     
            $data['main_message_id'] = $main_message_id;    //如果是回复一条消息，此值为原消息ID值
        }
       // myprint($data);
        /* 组装数据结束 */
        $last_insert_id = $this->add($data);                 //插入数据
        if($last_insert_id)
        {
            return $last_insert_id;
        }
        else
        {
            return -1;
        }
        
    }
    
    /**
     * @access public       --------->这个函数似乎没有必要存在<----------
     * @todo   新增一条回复消息（相比较addMessage()函数，此函数添加的消息数据中消息类型固定为2）
     * @param int    $send_user_id      发送者的用户ID。 必须
     * @param int    $reply_user_id     回复者的用户ID。 必须
     * @param string $message_title     消息标题 。     必须
     * @param string $message_contents  消息详情(长字符串)。必须
     * @param int    $main_message_id   如果是回复消息类型，值为回复的最原始消息的ID，否则为0。 默认值为0
     * @param int    $is_advice         是否用户建议，1是，0否。    默认为0
     * @param int    $isuse             是否显示，1显示，0不显示。  默认为1，只有在管理员群发消息时，此值才可能设置
     * @return int   如果新增消息成功，则返回该消息的ID号；否则返回-1
     *  */
    public function addReplyMessage($send_user_id, $reply_user_id, $message_title, $message_contents, $main_message_id, $is_advice = 0, $isuse = 1)
    {
        $UserModel = new UserModel();
        $send_user_info  = $UserModel->getUserInfo($send_user_id);              //发送者的用户信息
        $reply_user_info = $UserModel->getUserInfo($reply_user_id);             //回复者的用户信息
        $addtime = time();
        //组装数据
        $data = array(
            'is_advice'         =>  $is_advice,
            'message_type'      =>  2,
            'send_user_id'      =>  $send_user_id,
            'reply_user_id'     =>  $reply_user_id,
            'send_username'     =>  $send_user_info['username'],
            'reply_username'    =>  $reply_user_info['username'],
            'main_message_id'   =>  $main_message_id,
            'is_read'           =>  0,
            'message_contents'  =>  $message_contents,
            'isuse'             =>  $isuse,
            'addtime'           =>  $addtime
        );
       
        $last_insert_id = $this->add(data);                 //插入数据
        if($last_insert_id)
        {
            return $last_insert_id;
        }
        else
        {
            return -1;
        }
    }
    
    /** 
     * @access public
     * @todo 删除消息（物理性的永久删除）
     * @param int(or)array  $message_id  (两种情况：单条进行删除时，该值为消息ID；批量进行删除时，该值为消息ID组成的索引数组)。        必须
     * @param int $send_user_id 待删除消息的所属人用户ID。 必须（为确保安全性，即使是管理员做删除操作，也要传入本参数值）
     * @return bool 删除成功返回TRUE，否则返回FALSE
     */
    public function deleteMessage($message_id)
    {
        if(!$message_id || empty($message_id))   //判断参数的可用行
        {
            return FALSE;
        }
        
        $where = '';                        //组装where条件
        if(is_array($message_id))           //如果是批量删除
        {
            $m_ids = implode(',',$message_id);
            $where .= 'message_id IN('.$m_ids.') OR main_message_id IN('.$m_ids.')';
        }
        else
        {
            $where .= 'message_id = '.$message_id.' OR main_message_id = '.$message_id;
        }
        
        //执行删除操作
        if($this->where($where)->delete())
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /** 
     * @access public
     * @todo  设置消息的显示与否
     * @param int(or)array   $message_id 必须（此处有2中情况:如果是单条消息设置，则该值为单条消息ID；如果是批量设置，则该制是由消息ID组成的索引数组。     
     * @param int $isuse 要设置的值，有两种：1显示，0不显示。   必须 
     * @param bool 设置成功返回TRUE，否则返回FALSE
     */
    public function setShowHide($message_id,$isuse)
    {
        if(!$message_id || empty($message_id))              //判断参数的可用行
        {
            return FALSE;
        }
        $where = '';                        //组装where条件
        if(is_array($message_id))           //如果是批量删除
        {
            $where .= 'message_id IN('.implode(',', $message_id);
        }
        else
        {
            $where .= 'message_id = '.$message_id;
        }
        
        $data = array('isuse'=>$isuse);        //要编辑的值
        if($this->where($where)->save($data))  //执行编辑操作
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * @access public
     * @todo 编辑单条消息内容
     * @message_id int 待编辑的消息ID 
     * @param array $data 编辑后的数据组装的数组（格式参考TP框架CURD操作部分）
     * @return bool 执行成功返回TRUE,否则返回FALSE
     */
    public function editMessage($message_id,$data)
    {
        if(!$message_id)
        {
            return FALSE;
        }
        
        if($this->where('message_id = '.$message_id)->save($data))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
        
        
    }
    
    /** 
     * @access public
     * @todo 根据用户ID获取该用户的消息列表
     * @param int $user_id 用户ID。必须
     * @param int $type 要获取的消息类型：1表示主动发送的消息；2表示接受到的消息；3表示所有。默认为3
     * @param string $where 其他组装好的查询条件，比如时间段，读取状态。默认为空
     * @param string $limit 进行分页展示时的limit限制。比如"10,20"：表示条件查询条件 limit(10,20)。默认为空字符串，读取所有消息
     * 
     * @return array 返回查询结果（一维数组） 
     */
    public function getMessageListByUserId($user_id, $type = 3, $where='', $limit='')
    {
        $data = array();            //空数组
        
        if($where != '')            //如果有查询条件
        {
            $where .= ' AND ';
        }
        if($type == 1)              //获取自己发送的消息
        {
            $where .= 'send_user_id = '.$user_id;
        }
        elseif($type == 2)          //获取自己接受到的消息
        {
            $where .= 'reply_user_id = '.$user_id;
        }
        else                        //获取所有消息
        {
            $where .= 'send_user_id = '.$user_id.' OR reply_user_id = '.$user_id;
        }
        $where .= ' AND main_message_id = 0';       //不是回复消息
        
        $data = $this->where($where)->order('addtime DESC')->limit($limit)->select();
        return $data;
        
    }
    
    /**
     * @access public 
     * @todo 获取所有消息的列表
     * @param string $where 查询语句的where参数。默认为空，表示没有条件限制
     * @param string $limit 查询语句的limit参数。默认为空，表示取出所有
     * @return array 返回查询结果
     */
    public function getAllMessageList($where='')
    {
        if($where != '')
        {
            $where .= ' AND ';
        }
        $where .= 'main_message_id = 0';        //取出非回复类型的消息
        $data = $this->where($where)->order('addtime DESC')->limit()->select();
        return $data;
    }
    
    /**
     * @access public 
     * @todo 统计所有消息的数量
     * @param string $where 查询语句的where参数。默认为空，表示没有条件限制
     * @return array 返回查询结果
     */
    public function countAllMessageList($where='')
    {
        if($where != '')
        {
            $where .= ' AND ';
        }
        $where .= 'main_message_id = 0';        //取出非回复类型的消息
        $data = $this->where($where)->count();
        return $data;
    }
    
    /**
     * @access public 
     * @todo 统计需要回复的消息(我们需要统计的是需回复的原始消息的总数，比如A给B发了一条原始站内信即新的站内信，此时B给A一次性回复了有10条，那么对于A来说，待回复的算一条);
     * @param bool $need_total 默认为true,返回待回复的总数量。当传入false时，返回待回复的原始消息的id组成的索引数组
     * @param int $replay_user_id 消息的回复者ID，即接受者ID。默认为0，表示发送给管理员的信息
     */
    public function countAllMessageNeedReply($need_total = true,$reply_user_id = 0)
    { 
        $where = 'is_read = 0 AND reply_user_id = '.$reply_user_id.' ';  //所有未读的发送给管理员的
        //先统计直接发给管理员的未读原始消息
        $where1 .= $where.'AND main_message_id = 0';
        $r1 = $this->where($where1)->field('message_id')->select();
        #echo $this->getLastSql();echo '<hr/>';
        
        //统计回复类的未读消息
        $where2 = $where.'AND main_message_id != 0';
        $r2 = $this->where($where2)->group('main_message_id')->field('main_message_id')->select();
        #echo $this->getLastSql();echo '<hr/>';
        if(!$need_total)
        {
            $arr = array();
            foreach($r1 as $v)
            {
                $arr[] = $v['message_id'];
            }
            foreach ($r2 as $v)
            {
                $arr[] = $v['main_message_id'];
            }
            return array_unique($arr);      //理论上，两个查询结果不会存在重复的主消息ID
        }
        else
        {
            $count1 = $r1?count($r1):0;
            $count2 = $r2?count($r2):0;
            $count = $count1+$count2;
            return $count;
        }
    }
    
    /**
     * @access public
     * @todo 获取所有需要回复的消息（获取原始消息列表）
     * @param string $where where查询条件
     */
    public function getAllMessageNeedReply($where)
    {
        return $this->where($where)->order('addtime DESC')->limit()->select();
    }
    
    /** 
     * @access public
     * @todo 根据原始消息ID获取消息详情（包括该消息下的所有回复消息）
     * @param int $message_id 消息的ID
     * @param int $user_id  消息的关联者（验证消息的所有者）。默认为0，表示是管理员
     * @param bool $reply  是否获取该消息下的回复信息
     * @param string $limit 该消息下的所有回复消息进行分页的条件
     * @return array 返回获得的消息(包括回复消息)
     */
    public function getMessageInfoById($message_id, $user_id=0, $reply=true ,$limit='')
    {
        $where = 'message_id = '.$message_id.' AND isuse = 1';
        $where .= ' AND (send_user_id = '.$user_id.' OR reply_user_id = '.$user_id.') ';
        
        $data0 = $this->where($where)->find();             //原始消息的内容
        if(!$data0)
        {
            return false;
        }
        if($reply)          //当需要获取该消息的所有回复内容时
        {
            $data1 = $this->getReplyInfoByMessageId($message_id, $limit);           //获取消息回复
        
            $data = array(
                'message_info'  => $data0,
                'reply_info'    => $data1
            );
            return $data;
        }
        return $data0;
        
    }
    
    /**
     * @access public
     * @todo 根据消息ID获取该消息的回复信息
     * @param int $message_id 消息ID
     * @param string $limit （分页显示）消息回复的分页限制
     * @retrun array 返回消息的回复内容
     */
    public function getReplyInfoByMessageId($message_id,$limit='')
    {
        $data1 = $this->where('main_message_id = '.$message_id.' AND isuse = 1')->order('addtime ASC')->limit($limit)->select(); //该消息下所有回复的内容
        return $data1?$data1:false;
    }
    
    /** 
     * @access public
     * @todo 批量发送消息
     * @param int $send_user_id 发送者用户ID
     * @param array $reply_user_arr 接受者用户ID组成的索引数组
     * @param string $message_titile 消息标题
     * @param string $message_contents 消息内容
     * @param int $message_type 消息类型（1或者3）：1表示是普通消息；3表示是管理员群发。默认为1
     * 
     * @return bool 成功返回发送成功的消息的总条数，否则返回-1
     */
    public function batchSendMessage($send_user_id, $reply_user_arr, $message_title, $message_contents, $message_type=1)
    {
        $is_advice    = 0;          //是否是用户建议，0表示否
        $addtime      = time();     //消息发送时间
        
        $UserModel = new UserModel();
        $send_user_info = $UserModel->getUserInfo($send_user_id);               //发送者的用户信息
        $send_user_info = array('username'=>'admin@admin.com');
        /* 组装数据 */
        $data = array(
            'is_advice'         =>  $is_advice,
            'message_type'      =>  $message_type,
            'send_user_id'      =>  $send_user_id,
            'send_username'     =>  $send_user_info['username'],
            'message_title'     =>  $message_title,
            'is_read'           =>  0,                  //新消息，状态为未读，此值为0
            'main_message_id'   =>  0,                  //主动发送一条消息,此值则为0
            'message_contents'  =>  $message_contents,
            'isuse'             =>  1,
            'addtime'           =>  $addtime
        );
        $total = 0;     //计数器，用于记录发送成功的消息条数
        foreach($reply_user_arr as $k=>$v)
        {
            $reply_user_info = $send_user_info = $UserModel->getUserInfo($v);   //接受者的用户信息
            $reply_user_info = array('username'=>'admin');
            $data['reply_user_id']  = $v;                                       //接受者用户ID
            $data['reply_username'] = $reply_user_info['username'];             //接受者用户名
            if($this->add($data))                 //插入数据
            {
                $total++;                       //计数器加1
                continue;
            }
            else
            {
                return -1;
                breadk;
            }
        }
        if($total)
        {
            return $total;
        }
        else
        {
            return -1;
        }
    }

    
    /**
     * @access public
     * @todo 将消息的状态设置为已读
     * @param int $reply_user_id  这条消息的接受者ID。 必须
     * @param int $message_id 消息ID。 必须
     * @return bool 成功返回TRUE;否则返回FALSE
     */
    public function setMessageReaded($reply_user_id, $message_id)
    {
        if($message_id < 0)
        {
            return FALSE;
        }
       $data = array('is_read'=>1);
       $r =  $this->where('message_id = '.$message_id.' AND reply_user_id = '.$reply_user_id)->save($data);
       return $r?TRUE:FALSE;
    }
    
}
