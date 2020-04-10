<?php
/**
 * 消息推送模型类
 */

class PushModel extends BaseModel
{
    // 消息id
    public $push_id;

	// redis对象
    public $redis_obj;

	// 频道
    const CHANNEL = 'msg';

	// APP
    const APP = 'msd';

	// type
    const PUSH_TYPE = 'broadcast';

	#private $app_key = '2f0aa92e091ab19c24930acb';        //待发送的应用程序(appKey)，只能填一个。
	#private $master_secret = 'd41782350a98a44a35a6e1ab';    //主密码
	private $url = "https://api.jpush.cn/v3/push";      //推送的地址

    /**
     * 构造函数
     * @author 姜伟
     * @param $push_id 消息推送ID
     * @return void
     * @todo 初始化消息推送id
     */
    public function PushModel($push_id)
    {
        if ($push_id = intval($push_id))
		{
            $this->push_id = $push_id;
		}
    }

    /**
     * 获得redis对象
     * @author 姜伟
     * @param void
     * @return $redis_obj
     * @todo 获得redis对象
     */
    public function getRedisObj()
    {
		if ($this->redis_obj)
		{
			return $this->redis_obj;
		}
		$redis = new Redis();
		$redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
		$this->redis_obj = $redis;
		return $redis;
	}

    /**
     * 缓存数据
     * @author 姜伟
     * @param string $key
     * @param mixed $value
     * @return boolean
     * @todo 缓存数据
     */
    public function redisSet($key, $value)
    {
		$redis = $this->getRedisObj();
		$success = $redis->set($key, $value);
		return $success;
    }

    /**
     * 获取缓存数据
     * @author 姜伟
     * @param string $key
     * @param mixed $value
     * @return boolean
     * @todo 获取缓存数据
     */
    public function redisGet($key)
    {
		$redis = $this->getRedisObj();
		return $redis->get($key);
    }

    /**
     * 推送一条消息
     * @author 姜伟
     * @param int $user_id 用户ID
     * @param string $msg 消息内容，json字符串
     * @param string $channel 频道
     * @param string $type broadcast
     * @param string $app APP名称，如msd-mcp，msd-ucp，msd-fcp
     * @return void
     * @todo 推送一条消息
     */
    public function push($user_id, $msg, $channel = '', $app = '', $type = '')
    {
		$channel = $channel ? $channel : PushModel::CHANNEL;
		$app = $app ? $app : PushModel::APP;
		$type = $type ? $type : PushModel::PUSH_TYPE;
		$mid	= self::generateMessageId();
		$msg['mid']	= $mid;

		//除推送镖师待抢订单消息和订单被抢推送其他镖师消息外，其他消息均存数据库备案，发生异常情况时，可以恢复
		//if ($msg['opt'] != 'order_push' && $msg['opt'] != 'order_robed')
log_file('push_log: ' . json_encode($msg['msg']));
		{
			$log_arr = array(
				'opt'		=> $msg['msg']['opt'],
				'content'	=> json_encode($msg['msg']),
				'user_id'	=> $user_id,
				'mid'		=> $mid,
			);
			$push_log_obj = new PushLogModel();
			$push_log_obj->addPushLog($log_arr);
		}

		$push_time = $msg['push_time'];
#var_dump($user_id);
#echo "<pre>";
#print_r($msg);

		$redis = $this->getRedisObj();
		if ($push_time)
		{
			//若非即时推送，将消息push到缓存中的消息队列
			$msg['app'] = $app;
			$msg['mid'] = $mid;
			$msg['msg']['mid'] = $mid;
			$msg = json_encode($msg);
			$success = $redis->RPUSH('fm_push_list_' . $user_id, $msg);
			#var_dump($success);
		}
		else
		{
			//即时推送
			/*$msg = array(
				"app"	=> $app,
				"type"	=> $type,
				"mid"	=> $mid,
				"user_id"=> $user_id,
				"msg"	=> $msg
			);     // msg in json format*/
			$msg['app'] = $app;
			$msg['type'] = $type;
			$msg['mid'] = $mid;
			$msg['msg']['mid'] = $mid;
			$msg['user_id'] = $user_id;
			$msg = json_encode($msg);
			$success = $redis->publish($channel, $msg);
		}

		//写日志
		log_file('推送：user_id = ' . $user_id . '; msg = ' . json_encode($msg), 'push_log', true);
if (ACTION_NAME == 'add_freight' && isset($_GET['pay_password']))
{
echo '推送：user_id = ' . $user_id . '; msg = ' . json_encode($msg) . "<br>";
}
		//var_dump($success);
#var_dump($push_time);
    }

    /**
     * 更新镖师消息队列
     * @author 姜伟
     * @param int $user_id 用户ID
     * @param int $message_id 消息ID
     * @param string $opt 操作，1add，2remove
     * @return void
     * @todo 更新镖师消息队列
     */
    public function updateMessageQueue($user_id, $message_id, $opt = 1)
    {
		if ($opt == 1)
		{
			//添加新的消息到队列
		}
		else
		{
		}
    }

    /**
     * 获取消息推送信息
     * @author 姜伟
     * @param int $push_id 消息推送id
     * @param string $fields 要获取的字段名
     * @return array 消息推送基本信息
     * @todo 根据where查询条件查找消息推送表中的相关数据并返回
     */
    public function getPushInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改消息推送信息
     * @author 姜伟
     * @param array $arr 消息推送信息数组
     * @return boolean 操作结果
     * @todo 修改消息推送信息
     */
    public function editPush($arr)
    {
        return $this->where('push_id = ' . $this->push_id)->save($arr);
    }

    /**
     * 添加消息推送
     * @author 姜伟
     * @param array $arr 消息推送信息数组
     * @return boolean 操作结果
     * @todo 添加消息推送
     */
    public function addPush($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除内存中信息
     * @author 姜伟
     * @param string $key
     * @return boolean 操作结果
     * @todo 删除内存中信息
     */
    public function delete($key)
    {
	$redis_obj = $this->getRedisObj();
	return $redis_obj->delete($key);
    }

    /**
     * 根据where子句获取消息推送数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的消息推送数量
     * @todo 根据where子句获取消息推送数量
     */
    public function getPushNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询消息推送信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 消息推送基本信息
     * @todo 根据SQL查询字句查询消息推送信息
     */
    public function getPushList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取消息推送列表页数据信息列表
     * @author 姜伟
     * @param array $push_list
     * @return array $push_list
     * @todo 根据传入的$push_list获取更详细的消息推送列表页数据信息列表
     */
    public function getListData($push_list)
    {
		foreach ($push_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic');
			$push_list[$k]['item_name'] = $item_info['item_name'];
			$push_list[$k]['mall_price'] = $item_info['mall_price'];
			$push_list[$k]['small_pic'] = $item_info['base_pic'];

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$push_list[$k]['status'] = $status;
		}

		return $push_list;
    }

    /**
     * 获取内存中某键对应值
     * @author 姜伟
     * @param string $key
     * @return string $value
     * @todo 获取内存中某键对应值
     */
    public function getMemoryData($key)
    {
		$redis = $this->getRedisObj();
		$value = json_decode($redis->get($key), true);
		return $value;
	}

    /**
     * 生成消息ID
     * @author 姜伟
     * @param void
     * @return int $message_id
     * @todo 生成消息ID
     */
    public function generateMessageId()
    {
		$push_log_obj = new PushLogModel();
		$push_log_info = $push_log_obj->getPushLogInfo('', 'push_log_id', 'push_log_id DESC');
		$message_id = $push_log_info ? $push_log_info['push_log_id'] : 0;
		return $message_id + 1;
	}

    /**
     * 推送消息给用户
     * @author 姜伟
     * @param int $user_id
     * @param string $opt refund订单退款(退款通知模板)，accept商家接单/镖师抢单(服务接单通知模板)，reject商家拒单/无镖师抢单(预定失败通知模板)
     * @param array $msg
     * @return int $message_id
     * @todo 推送消息给用户
     */
    public static function wxPush($user_id, $opt, $msg)
    {
		log_file('user_id = ' . $user_id, 'push');
		//获取用户微信openid
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('openid');
		$openid = $user_info['openid'];
		$template_id = '';
		$url = $msg['order_id'] ? C('DOMAIN') . '/FrontOrder/order_detail/order_id/' . $msg['order_id'] : $msg['url'];
		$order_id  = intval($msg['order_id']);
		#$url = 'http://' . 'msd.yurtree.com' . '/FrontOrder/order_detail/order_id/' . $msg['order_id'];
		$topcolor = '#FF0000';
		$remark = isset($msg['remark']) ? $msg['remark'] : '点击查看详情';

		$push_obj = new PushModel();
		$wx_templates = C('TEMPLATES');
		switch($opt)
		{
			case 'refund':
				#$template_id = 'sWViZCQUbemOp-oDle4fthuCGrziM-qS9EGOVDy3QkE';
				//推微信
				$template_id = $wx_templates['refund'];	//退款申请审核结果
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						/*'reason'	=> array(
							'value'	=> $msg['reason'],
							'color'	=> '#FF0000',
						),
						'refund'	=> array(
							'value'	=> $msg['refund'],
							'color'	=> '#FF0000',
						),*/
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'keyword3'	=> array(
							'value'	=> $msg['keyword3'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_refund',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_refund',
						'order_id'		=> $order_id,
						'msg'			=> $msg['keyword1'],
						'refund_num'	=> $msg['keyword2'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'accept':
				$template_id = $wx_templates['accept'];	//服务接单通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'keyword3'	=> array(
							'value'	=> $msg['keyword3'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_accept',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_accept',
						'order_id'		=> $order_id,
						'msg'			=> $msg['first'] . "\n" . $msg['remark'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'reject':
				$template_id = $wx_templates['reject'];		//订单取消通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_reject',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_reject',
						'order_id'		=> $order_id,
						'msg'			=> $msg['first'] . "\n" . $msg['remark'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'no_response':
				$template_id = $wx_templates['no_response'];		//服务未响应通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_no_response',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_no_response',
						'order_id'		=> $order_id,
						'msg'			=> $msg['first'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'confirm':
				$template_id = $wx_templates['confirm'];		//订单确认收货通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'keyword3'	=> array(
							'value'	=> $msg['keyword3'],
							'color'	=> '#FF0000',
						),
						'keyword4'	=> array(
							'value'	=> $msg['keyword4'],
							'color'	=> '#FF0000',
						),
						'keyword5'	=> array(
							'value'	=> $msg['keyword5'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_confirm',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_confirm',
						'order_id'		=> $order_id,
						'msg'			=> $msg['first'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'coupon':
				$template_id = $wx_templates['coupon'];	//优惠券到账通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'orderTicketStore'	=> array(
							'value'	=> $msg['orderTicketStore'],
							'color'	=> '#FF0000',
						),
						'orderTicketRule'	=> array(
							'value'	=> $msg['orderTicketRule'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_coupon',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_coupon',
						'msg'			=> $msg['first'] . "\n" . $msg['orderTicketStore'] . "\n" . $msg['orderTicketRule'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			case 'cancel':
				$template_id = $wx_templates['cancel'];	//订单取消通知
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'url'			=> $url,
					'topcolor'		=> $topcolor,
					'data'			=> array(
						'first'	=> array(
							'value'	=> $msg['first'],
							'color'	=> '#FF0000',
						),
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
						'remark'	=> array(
							'value'	=> $remark,
							'color'	=> '#FF0000',
						),
					),
				);

				//推APP
				$push_arr = array(
					'opt'		=> 'user_cancel',
					'push_time'	=> 0,
					'msg'		=> array(
						'opt'			=> 'user_cancel',
						'msg'			=> $msg['first'] . "\n" . $msg['keyword1'],
					),
				);
				$push_obj->push($user_id, $push_arr);

				break;
			default:
				break;
		}

		log_file('push_user: ' . 'opt = ' . $opt . ', data = ' . json_encode($data));
		$appid = C('APPID');
		$secret = C('APPSECRET');
		vendor('Wxin.WeiXin');
		$access_token = WxApi::getAccessToken($appid, $secret);
		$weixin_obj = new WxApi($access_token);
		$result = $weixin_obj->send_template_msg($data);
		log_file('access_token = ' . $access_token . ', result = ' . json_encode($result), 'addMerchantOrder');
		$success = $result['errcode'] == 0 ? true : false;

		return $success;
	}

	//小程序消息推送
	public static function wxXcxPush($user_id, $opt, $msg)
    {
		log_file('user_id = ' . $user_id, 'push');
		//获取用户微信openid
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('openid');
		$openid = $user_info['openid'];
		$template_id = '';
		$topcolor = '#FF0000';
		$push_obj = new PushModel();
		$wx_templates = C('TEMPLATES');
		switch($opt)
		{
			case 'notice':
				#$template_id = 'sWViZCQUbemOp-oDle4fthuCGrziM-qS9EGOVDy3QkE';
				//推微信
				$template_id = $wx_templates['notice'];	//退款申请审核结果
				$data = array(
					'touser'		=> $openid,
					'template_id'	=> $template_id,
					'page'			=> $msg['page'],
					'form_id' 		=> $msg['form_id'],
					'color'		=> $topcolor,
					'data'			=> array(
						'keyword1'	=> array(
							'value'	=> $msg['keyword1'],
							'color'	=> '#FF0000',
						),
						'keyword2'	=> array(
							'value'	=> $msg['keyword2'],
							'color'	=> '#FF0000',
						),
					),
				);

				break;
			default:
				break;
		}

		log_file('push_user: ' . 'opt = ' . $opt . ', data = ' . json_encode($data), 'xcx_push');
		$appid = C('APPID');
		$secret = C('APPSECRET');
		vendor('Wxin.WeiXin');
		$access_token = WxApi::getAccessToken($appid, $secret);
		$weixin_obj = new WxApi($access_token);
		$result = $weixin_obj->send_template_msg_xcx($data);
		log_file('access_token = ' . $access_token . ', result = ' . json_encode($result), 'xcx_push');
		$success = $result['errcode'] == 0 ? true : false;

		return $success;
	}

	/**
	 * $receiver 接收者的信息
	 * all 字符串 该产品下面的所有用户. 对app_key下的所有用户推送消息
	 * tag(20个)Array标签组(并集): tag=>array('昆明','北京','曲靖','上海');
	 * tag_and(20个)Array标签组(交集): tag_and=>array('广州','女');
	 * alias(1000)Array别名(并集): alias=>array('93d78b73611d886a74*****88497f501','606d05090896228f66ae10d1*****310');
	 * registration_id(1000)注册ID设备标识(并集): registration_id=>array('20effc071de0b45c1a**********2824746e1ff2001bd80308a467d800bed39e');
	 * $content 推送的内容。
	 * $m_type 推送附加字段的类型(可不填) http,tips,chat....
	 * $m_txt 推送附加字段的类型对应的内容(可不填) 可能是url,可能是一段文字。
	 * $m_time 保存离线时间的秒数默认为一天(可不传)单位为秒
	 */
	public function jpush($receiver='all',$content='',$m_type='http',$m_txt='', $m_time='864000'){
		$app_key = C('JPUSH_APPID');        //待发送的应用程序(appKey)，只能填一个。
		$master_secret = C('JPUSH_SECRET');    //主密码
		$base64=base64_encode("$app_key:$master_secret");
		$header=array("Authorization:Basic $base64","Content-Type:application/json");
		$data = array();
		$data['platform'] = 'all';          //目标用户终端手机的平台类型android,ios,winphone
		$data['audience'] = $receiver;      //目标用户
		$data['notification'] = array(
			//统一的模式--标准模式
			"alert"=>$content,
			//安卓自定义
			"android"=>array(
				"alert"=>$content,
				"title"=>"",
				"builder_id"=>1,
				"extras"=>array("type"=>$m_type, "txt"=>$m_txt)
			),
			//ios的自定义
			"ios"=>array(
				"alert"=>$content,
				"badge"=>"1",
				"sound"=>"default",
				"extras"=>array("type"=>$m_type, "txt"=>$m_txt)
			)
		);
		//苹果自定义---为了弹出值方便调测
		$data['message'] = array(
			"msg_content"=>$content,
			"extras"=>array("type"=>$m_type, "txt"=>$m_txt)
		);

		//附加选项
		$data['options'] = array(
			"sendno"=>time(),
			"time_to_live"=>$m_time, //保存离线时间的秒数默认为一天
			"apns_production"=>false, //布尔类型   指定 APNS 通知发送环境：0开发环境，1生产环境。或者传递false和true
		);
		$param = json_encode($data);
		$res = $this->push_curl($param,$header);

		if($res){       //得到返回值--成功已否后面判断
			return $res;
		}else{          //未得到返回值--返回失败
			return false;
		}
	}

	//jpush所有用户
	function jpush_all($content, $m_type = 'http', $m_txt = '')
	{
		$result = $this->jpush('all', $content, $m_type = 'http', $m_txt);
		return $result;
	}

	//jpush某个标签组用户
	function jpush_tag($content, $tag)
	{
		$receiver = array(
			'tag'	=> array(
				$tag
			)
		);
		$result = $this->jpush($receiver, $content);
		return $result;
	}

	//jpush指定用户
	function jpush_user($content, $user_id, $m_type = 'http', $m_txt = '')
	{
		//获取用户reg_id
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('jpush_reg_id');
		$jpush_reg_id = $user_info['jpush_reg_id'];
		$receiver = array(
			'registration_id'	=> array(
				$jpush_reg_id
			)
		);
		$result = $this->jpush($receiver, $content, $m_type, $m_txt);
		return $result;
	}

	//推送的Curl方法
	public function push_curl($param="",$header="") {
		if (empty($param)) { return false; }
			$postUrl = $this->url;
		$curlPost = $param;
		$ch = curl_init();                                      //初始化curl
		curl_setopt($ch, CURLOPT_URL,$postUrl);                 //抓取指定网页
		curl_setopt($ch, CURLOPT_HEADER, 0);                    //设置header
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);            //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, 1);                      //post提交方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);           // 增加 HTTP Header（头）里的字段
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);        // 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$data = curl_exec($ch);                                 //运行curl
		curl_close($ch);
		return $data;
	}

	//极光推送的类
	//文档见：http://docs.jpush.cn/display/dev/Push-API-v3

		/**组装需要的参数
		 $receive = 'all';//全部
		 $receive = array('tag'=>array('2401','2588','9527'));//标签
		 $receive = array('alias'=>array('93d78b73611d886a74*****88497f501'));//别名
		 $content = '这是一个测试的推送数据....测试....Hello World...';
		 $m_type = 'http';
		 $m_txt = 'http://www.iqujing.com/';
		 $m_time = '600';        //离线保留时间
		**/
	//调用推送方法
	public function send_pub($receive,$content,$m_type,$m_txt,$m_time){
		$m_type = 'http';//推送附加字段的类型
		$m_txt = 'http://www.groex.cn/';//推送附加字段的类型对应的内容(可不填) 可能是url,可能是一段文字。
		$m_time = '86400';//离线保留时间

		$message="";//存储推送状态
		#echo $receive;
		$result = $this->jpush($receive,$content,$m_type,$m_txt,$m_time);
		#dump($result);
		#die;
		if($result){
			$res_arr = json_decode($result, true);
			if(isset($res_arr['error'])){                       //如果返回了error则证明失败
				echo $res_arr['error']['message'];          //错误信息
				$error_code=$res_arr['error']['code'];             //错误码
				switch ($error_code) {
				case 200:
					$message= '发送成功！';
					break;
				case 1000:
					$message= '失败(系统内部错误)';
					break;
				case 1001:
					$message = '失败(只支持 HTTP Post 方法，不支持 Get 方法)';
					break;
				case 1002:
					$message= '失败(缺少了必须的参数)';
					break;
				case 1003:
					$message= '失败(参数值不合法)';
					break;
				case 1004:
					$message= '失败(验证失败)';
					break;
				case 1005:
					$message= '失败(消息体太大)';
					break;
				case 1008:
					$message= '失败(appkey参数非法)';
					break;
				case 1020:
					$message= '失败(只支持 HTTPS 请求)';
					break;
				case 1030:
					$message= '失败(内部服务超时)';
					break;
				default:
					$message= '失败(返回其他状态，目前不清楚额，请联系开发人员！)';
					break;
				}
			}else{
				$message="发送成功！";
			}
		}else{      //接口调用失败或无响应
			$message='接口调用失败或无响应';
		}
		echo  "<script>alert('推送信息:{$message}')</script>";
	}
}
