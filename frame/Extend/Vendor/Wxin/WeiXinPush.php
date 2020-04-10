<?php
/**
  * 微信公众平台接口封装功能类文件
  * author 张光强
  * date 2013-11-27 PRC:E+8 23:03
*/
 
header("Content-Type:text/html; charset=utf-8");
#define("TOKEN", "Mhha7fg81hh2");
#$wechatObj = new wechatCallbackapiTest();
#$wechatObj->weixin_run(); //执行接收器方法
//$wechatObj->valid();       //token第一次验证时需要打开,后面就可以注释掉了
 
class wechatCallbackapiTest
{
    private $sorry_msg = '很抱歉，未找到相关信息，请输入“世界杯”进入投票。'; private $thank_msg = "感谢您宝贵的建议，我们会提交给相关人员参考，感谢您对育儿树的支持，祝您生活愉快。"; private $world_cup_link = '点击进入：http://world-cup.yurtree.com/'; private $fromUsername;  //发送过来消息的微信openid
    private $toUsername;    //开发者微信号
    private $times;         //消息创建时间
    private $keyword;       //消息内容
    private $PicUrl;        //图片消息
    private $MediaId;       //媒体消息id，可以调用多媒体文件下载接口拉取数据。
    private $ThumbMediaId;  //视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。
    private $Format;        //语音格式
    private $Location_X;    //地理位置维度
    private $Location_Y;    //地理位置精度
    private $Scale;         //地图缩放大小
    private $Label;         //地理位置信息
    private $Title;         //连接消息标题
    private $Description;   //连接消息描述
    private $Url;           //连接消息url
    private $Latitude;      //上报地理位置时的地理位置纬度
    private $Longitude;     //上报地理位置时的地理位置经度
    private $Precision;     //上报地理位置时的地理位置精度
    private $MsgType;       //消息类型 文本txt  图片image 语音voice
                            //           视频video  地理位置location         连接link
                            //         点击event
    private $Event;         //事件类型 subscribe(订阅)、unsubscribe(取消订阅)            
                            //         LOCATION(用户同意上报地址位置)  CLICK(点击菜单)
    private $EventKey;      //事件KEY值，qrscene_为前缀，后面为二维码的参数值
                            //           或者是与自定义菜单接口中KEY值对应
    private $Ticket;        //二维码的ticket，可用来换取二维码图片
    private $Recognition;   //语音识别结果，UTF8编码
 
 
 
    //执行检测的信息
    public function weixin_run()
    {
        $this->responseMsg();
	$ids = '';
 
        switch ($this->MsgType)
        {
            case 'text':
                //关键字回复
                $wx_reply_obj = D('WxKwReply');
                $reply_info = $wx_reply_obj->getWxKwReplyInfo('keyword = "' . $this->keyword . '"');
				log_file(json_encode($reply_info), 'ca', true);
                if($reply_info){
                    switch ($reply_info['reply_type']) {
                        case 'text':
							log_file($reply_info['text_value'], 'ca', true);
							//获取media_id
                            $this->fun_xml('text', array($reply_info['text_value']));
                            break;
                        case 'news':
                            $arr = array(
                                'name'  => $reply_info['news_title'],
                                'body'  => $reply_info['text_value'],
                                'pic'   => 'http://' . $_SERVER['HTTP_HOST'] . $reply_info['img_url'],
                                'url'   => $reply_info['news_link'],
                            );
                            $this->fun_xml('news', $arr, array(1,0));
                            break;
                        case 'image':
                            #$arr = array('http://' . $_SERVER['HTTP_HOST'] . $reply_info['img_url']);
                            #$this->fun_xml('image', $arr);
							log_file('1', 'ca', true);
							$media_id = get_media_id($reply_info['wx_kw_reply_id']);
							log_file($media_id, 'ca', true);
							$this->fun_xml('image', array($media_id));
                        default:
                            # code...
                            break;
                    }
                }
				//$this->fun_xml('text', array($GLOBALS['config_info']['DEFAULT_MSG']));
				break;
            case 'voice':
				$this->fun_xml('text', array($GLOBALS['config_info']['DEFAULT_MSG']));
				break;
            case 'image':
                /*$str   = "类型:图片消息\n";
                $str  .= '图片链接:' . $this->PicUrl . "\n";
                $str  .= 'MediaId:' . $this->MediaId . "\n";
                $arr[] = $str;
				$this->fun_xml('text', $arr);*/
                $con .= "<Image>";
                $con .= "<MediaId><![CDATA[{$value_arr[0]}]]></MediaId>";
                $con .= "</Image>";
                break;
            case 'video':
                $str   = "类型:视频消息\n";
                $str  .= '图片链接:' . $this->PicUrl . "\n";
                $str  .= 'MediaId:' . $this->MediaId . "\n";
                $str  .= 'ThumbMediaId:' . $this->ThumbMediaId . "\n";
                $arr[] = $str;
                $this->fun_xml('text', $arr);
                break;
            case 'location':
                $str   = "类型:地理位置消息\n";
                $str  .= '维度:' . $this->Location_X . "\n";
                $str  .= '精度:' . $this->Location_Y . "\n";
                $str  .= '地图缩放大小:' . $this->Scale . "\n";
                $str  .= '地理位置信息:' . $this->Label . "\n";
                $arr[] = $str;
                $this->fun_xml('text', $arr);
                break;
            case 'link':
                $str   = "类型:链接消息\n";
                $str  .= '标题:' . $this->Title . "\n";
                $str  .= '描述:' . $this->Description . "\n";
                $str  .= '链接:' . $this->Url . "\n";
                $arr[] = $str;
                $this->fun_xml('text', $arr);
                break;
             case 'event':
                if($this->Event == 'subscribe')
                {
                    log_file('EventKey = ' . $this->EventKey . ', ticket = ' . $this->Ticket, 'subscribe');
                    //注册该用户，并标注为已关注
                    $arr = array(
                        'openid'				=> (string) $this->fromUsername,
                        'user_cookie'           => $GLOBALS['user_cookie'],
                        #'role_type'             => 0,
                        'is_enable'             => 1,
                        'subscribe'             => 1,
                        #'reg_time'              => 0,
                    );
                    $where = 'openid = "' . $this->fromUsername . '"';
                    log_file('openid = ' . $this->fromUsername, 'subscribe');
                    $user_model = new UserModel();
                    $user_info = $user_model->where($where)->find();
                    log_file('user_model sql0 = ' . $user_model->getLastSql() . "\n" . json_encode($user_info), 'subscribe');
					if (!$user_info)
					{
						//省份城市信息
						$area_obj = new AreaModel();
						$city_info = $area_obj->getIdByName($arr['province'], $arr['city']);
						$arr['province_id'] = $city_info ? $city_info['province_id'] : 0;
						$arr['city_id'] = $city_info ? $city_info['city_id'] : 0;
						$arr['area_id'] = 0;

						if ($this->Ticket)
						{
							//获取上级ID
							$user_obj = new UserModel();
							$rec_user_id = intval($user_obj->where('ticket = "' . $this->Ticket . '"')->getField('user_id'));
							$arr['first_agent_id'] = $rec_user_id;
                            log_file('user sql1 = ' . $user_obj->getLastSql(), 'subscribe');
                            log_file('rec_user_id = ' . $rec_user_id, 'subscribe');

							//获取上上级ID
							$user_obj = new UserModel($rec_user_id);
							$user_info2 = $user_obj->getUserInfo('first_agent_id, second_agent_id');
							$arr['second_agent_id'] = $user_info2 ? $user_info2['first_agent_id'] : 0;
							$arr['third_agent_id'] = $user_info2 ? $user_info2['second_agent_id'] : 0;
							//log_file('user sql2 = ' . $user_obj->getLastSql(), 'subscribe');
							//log_file('second_agent_id = ' . $arr['second_agent_id'], 'subscribe');
	
							//$arr['user_rank_id'] = intval($GLOBALS['config_info']['DEFAULT_USER_RANK_ID']);

						}

						$user_id = $user_model->addUser($arr);
                        log_file('user sql3 = ' . $user_model->getLastSql(), 'subscribe');
					}
					else
					{
						unset($arr['reg_time']);
						$user_model->setLimit(1);
						$user_model->where($where)->save($arr);
						$user_id = $user_info['user_id'];
					}

					$arr = array(
						'name'	=> $GLOBALS['config_info']['SUBSCRIBE_TITLE'],
						'body'	=> $GLOBALS['config_info']['SUBSCRIBE_CONTENT'],
						'pic'	=> 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['config_info']['SUBSCRIBE_PIC'],
						'url'	=> $GLOBALS['config_info']['SUBSCRIBE_LINK'],
					);
log_file(json_encode($arr), 'ceshi', true);
					$this->fun_xml('news', $arr, array(1,0));
					break;
                }
                else if($this->Event == 'unsubscribe')
                {
                    $str   = "类型:取消关注消息\n";
                }
                else if($this->Event == 'CLICK')
                {
                    $str   = "类型:点击菜单事件\n";
                    $str  .= 'EventKey:' . $this->EventKey . "\n";
			$this->fun_xml('event', array($str));
			break;
                }
 
                #$str  .= 'openid:' . $this->fromUsername . "\n";
                $arr[] = $str;
                $this->fun_xml('text', $arr);
                break;
 
            default:
               # code...
                break;
        }
	//写日志
	#$this->save_log($ids);
 
 
        //回复文本消息
        /* $arr[] = $this->keyword;
        // $arr[] = $this->MediaId;
        $this->fun_xml('text', $arr);  */
 
        //回复图片消息
        /* $arr[] = $this->MediaId;
        $this->fun_xml('image', $arr); */
 
        //回复语音消息
        /* $arr[] = $this->MediaId;
        $this->fun_xml('voice', $arr); */
 
        //回复视频消息
        /* $arr[] = $this->MediaId;
        $arr[] = $this->ThumbMediaId;
        $this->fun_xml('video', $arr); */
 
        //回复音乐消息
       /*  $arr[] = '测试音乐消息回复';
        $arr[] = "描述";
        $arr[] = "http://wx.vtaoshop.net/wll/yiqi.mp3";
        $arr[] = "http://wx.vtaoshop.net/wll/yiqi.mp3";
        $arr[] = "5Tyme685adshiJWroam_UGuywqesG2k3jGxC1NZcK6JceW9V6aHvs0VbZKvUdAvl";
        $this->fun_xml('music', $arr); */
 
        //回复图文消息
        /* $arr[] = array(
            "感谢你关注360shop",
            "谢谢",
            "http://www.360shop.com.cn/templates/default/images/about_us/360-12_05.jpg",
            "http://www.360shop.com.cn"
        );
        $arr[] = array(
            "关于我们",
            "公司介绍",
            "http://www.360shop.com.cn/templates/default/images/about_us/360-12_05.jpg",
            "http://www.360shop.com.cn/us/introduce"
        );
        $this->fun_xml("news", $arr, array(2,0));  */
 
 
 
     /* //判断有数据就返回天气数据,没有就返回富文本信息
      if(empty($t->weatherinfo->week))
      {
           //获取对方要翻译的数据
             $t = $this->fanyi($this->keyword);
 
           //获取拼音
           $py = $this->pinyin($this->keyword);
 
           $arr[]=array("感谢你关注商信圈","aa","http://1.zhangya4548.sinaapp.com/shanxinquan1.jpg","http://www.laiyuan168.com");
           $arr[]=array("没有你要查询的城市: '" . $this->keyword . "' 天气!请输入中文城市名如: '杭州' 不要带其它数字或字母字符","aa","http://1.zhangya4548.sinaapp.com/12345.jpg","http://www.laiyuan168.com");
 
           //翻译测试
           $arr[]=array("你输入的[ " . $this->keyword . " ] 英文 为: '" . $t . "'","aa","http://1.zhangya4548.sinaapp.com/fanyi.jpg","http://www.laiyuan168.com");
 
           //拼音测试
           $arr[]=array("你输入的[ " . $this->keyword . " ] 拼音 为: '" . $py . "'","aa","http://1.zhangya4548.sinaapp.com/pinyin.png","http://www.laiyuan168.com");
 
           $arr[]=array("更多功能后续开发,敬请等待!","aa","http://1.zhangya4548.sinaapp.com/12345.jpg","http://www.laiyuan168.com");
           //回复富文本消息
           $this->fun_xml("news",$arr,array(5,0)); //2控制几行输出
      }
      else
      {
            $str  =  " 你要查询的地区是: " . $t->weatherinfo->city . ",\n";
            $str .=  " 今天是: " . $t->weatherinfo->date_y . ",\n";
            $str .=  " 今天是: " . $t->weatherinfo->week . ",\n";
            $str .=  " 今天温度是: " . $t->weatherinfo->temp1 . ",\n";
            $str .=  " 今天天气状况: " . $t->weatherinfo->weather1 . ",\n";
            $str .=  " 今天穿衣指数: " . $t->weatherinfo->index . "   " . $t->weatherinfo->index_d . ",\n";
            $str .=  " 48小时穿衣指数: " . $t->weatherinfo->index48 . "   " . $t->weatherinfo->index48_d . ",\n";
            $str .=  " 紫外线及48小时紫外线: " . $t->weatherinfo->index_uv . "   " . $t->weatherinfo->index48_uv . ",\n";
            $str .=  " 洗车: " . $t->weatherinfo->index_xc . ",\n";
            $str .=  " 旅游: " . $t->weatherinfo->index_tr . ",\n";
            $str .=  " 舒适指数: " . $t->weatherinfo->index_co . ",\n";
            $str .=  " 晨练: " . $t->weatherinfo->index_cl . ",\n";
            $str .=  " 晾晒: " . $t->weatherinfo->index_ls . ",\n";
            $str .=  " 过敏: " .$t->weatherinfo->index_ag . ",\n";
            $str .=  " 从今天开始到第六天的每天的天气情况，这里的温度是摄氏温度,\n";
 
            //判断今天是星期几
            $weekarray = array("日","一","二","三","四","五","六");
            $lb = date("w");
            $j  = 1;
            for($i = 0; $i <= 6; $i++)
            {
                if($j != 7)
                {
                    $ss   = 'temp' . $j;
                    $hs   = 'tempF' . $j;
                    $zk   = 'weather' . $j;
                    $fs   = 'wind' . $j;
                    $fl   = 'fl' . $j;
                    $str .=  "  星期" . $weekarray[$lb] . "\n是: " . $t->weatherinfo->$ss . "摄氏度,\n" . $t->weatherinfo->$hs . "华氏度,\n天气状况:  " . $t->weatherinfo->$zk . ",\n风速:  " . $t->weatherinfo->$fs . " \n风力级别:  " . $t->weatherinfo->$fl . ";\n";
 
                    $lb++;
                    if($lb == 7)
                    {
                        $lb = 0;
                    }
                }
                $j++;
            }
 
              $arr[] = $str;
            //回复文本消息
            $this->fun_xml('text', $arr);
       }*/
    }
 
   //获取天气数据
    public function t($n)
    {
        include("t_api.php");
        $c_name = $t_api[$n];
        $json   = file_get_contents("http://m.weather.com.cn/data/" .  $c_name . ".html");
        return json_decode($json);
    }
 
 
    //获取翻译
    public function fanyi($n)
    {
        include("fanyi.php");
        $fanyi         = new BaiduFanyi();
        $fanyi->appkey = "UMCUG6znEOc10S4D72AuGeXN";
        $re=$fanyi->fanyi($n,"zh","en");
        if(!$re)
        {
            return $fanyi->geterror();
        }
        else
        {
            return $re;
        }
    }
 
 
    //获取拼音
    public function pinyin($n)
    {
        include("hz_zhuan_py.php");
        $pin = new pin();
        return $pin->Pinyin($n,'UTF8');
 
    }
 
 
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature())
        {
            echo $echoStr;
            exit;
        }
    }
 
	function send_service()
	{
		$kefu_arr = array(
			'0'     => 'jwtest@wsjfwh',
		);
		$rand = rand(0, count($kefu_arr) - 1);
		$KfAccount = $kefu_arr[$rand];
		$kefu_arr = array(
			'KfAccount'     => $KfAccount
		);
		$this->fun_xml('transfer_customer_service', $kefu_arr);
	}
 
    public function responseMsg()
    {
        #$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	$postStr = file_get_contents("php://input");
		
        if (!empty($postStr))
        {
	        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$file = fopen('Log/event.log', 'a');
		fwrite($file, date('Y-m-d H:i:s') . "\n");
		fwrite($file, 'FromUserName = ' . $postObj->FromUserName . "\n");
		foreach ($postObj AS $k => $v)
		{
			$str = $k . ' => ' . $v . "\n";
			fwrite($file, $str);
		}
		fwrite($file, "\n");
		fclose($file);
            $this->fromUsername = $postObj->FromUserName;
            $this->toUsername   = $postObj->ToUserName;
            $this->keyword      = trim($postObj->Content);
            $this->MsgType      = $postObj->MsgType;
            $this->PicUrl       = $postObj->PicUrl;
            $this->MediaId      = $postObj->MediaId;
            $this->ThumbMediaId = $postObj->ThumbMediaId;
            $this->Format       = $postObj->Format;
            $this->Location_X   = $postObj->Location_X;
            $this->Location_Y   = $postObj->Location_Y;
            $this->Scale        = $postObj->Scale;
            $this->Label        = $postObj->Label;
            $this->Title        = $postObj->Title;
            $this->Description  = $postObj->Description;
            $this->Url          = $postObj->Url;
            $this->Latitude     = $postObj->Latitude;
            $this->Longitude    = $postObj->Longitude;
            $this->Precision    = $postObj->Precision;
            $this->Event        = $postObj->Event;
            $this->EventKey     = $postObj->EventKey;
            $this->Ticket       = $postObj->Ticket;
            $this->Recognition  = $postObj->Recognition;
            $this->times        = time();
        }
        else
        {
            echo "this a file for weixin API!";
            exit;
        }
    }
 
 
    /* 微信发送消息封装
    type         文本txt  图片image 语音voice  视频video  音乐music  图文news
    value_arr (内容)  注意: 多图要小于10条
              多图时候的格式
              value_arr(array(标题,介绍,图片,超链接),...小于10条)
    array     (条数,ID)
    */
    private function fun_xml($type, $value_arr, $o_arr = array(0))
    {
        //=================xml header============
        $con="<xml>
        <ToUserName><![CDATA[{$this->fromUsername}]]></ToUserName>
        <FromUserName><![CDATA[{$this->toUsername}]]></FromUserName>
        <CreateTime>{$this->times}</CreateTime>
        <MsgType><![CDATA[{$type}]]></MsgType>";
 
        //=================type content============
        switch($type)
        {
            case "event" :
				if ($this->EventKey == 'customer_service')
				{
					$this->send_service();

					//发送提示消息
					$appid = C('APPID');
					$secret = C('APPSECRET');
					vendor('Wxin.WeiXin');
					$access_token = WxApi::getAccessToken($appid, $secret);
					$weixin_obj = new WxApi($access_token);
					$weixin_obj->message_custom_send_text($this->fromUsername, $GLOBALS['config_info']['DEFAULT_MSG']);

					#$this->fun_xml('text', array($GLOBALS['config_info']['DEFAULT_MSG']));
				}
            case "text" :
				$con .= "<Content><![CDATA[{$value_arr[0]}]]></Content>
						<FuncFlag>{$o_arr}</FuncFlag>";
				#$this->send_service();
				break;

            case "transfer_customer_service" :
		$KfAccount = $value_arr['KfAccount'];
                $con .= "<TransInfo><KfAccount>{$KfAccount}</KfAccount></TransInfo>";
                break;
 
            case "image" :
                $con .= "<Image>";
                $con .= "<MediaId><![CDATA[{$value_arr[0]}]]></MediaId>";
                $con .= "</Image>";
                break;
 
            case "voice" :
                $con .= "<Voice>";
                $con .= "<MediaId><![CDATA[{$value_arr[0]}]]></MediaId>";
                $con .= "</Voice>";
                break;
 
            case "video" :
                $con .= "<Video>";
                $con .= "<MediaId><![CDATA[{$value_arr[0]}]]></MediaId>";
                $con .= "<ThumbMediaId><![CDATA[{$value_arr[1]}]]></ThumbMediaId>";
                $con .= "</Video>";
                break;
 
            case "music" :
                $con .= "<Music>";
                $con .= "<Title><![CDATA[{$value_arr[0]}]]></Title>";
                $con .= "<Description><![CDATA[{$value_arr[1]}]]></Description>";
                $con .= "<MusicUrl><![CDATA[{$value_arr[2]}]]></MusicUrl>";
                $con .= "<HQMusicUrl><![CDATA[{$value_arr[3]}]]></HQMusicUrl>";
                $con .= "<ThumbMediaId><![CDATA[{$value_arr[4]}]]></ThumbMediaId>";
                $con .= "</Music>";
                break;
 
            case "news" :
                $con .= "<ArticleCount>{$o_arr[0]}</ArticleCount>
                <Articles>";
				$con .= "<item>
				<Title><![CDATA[{$value_arr['name']}]]></Title>
				<Description><![CDATA[{$value_arr['body']}]]></Description>
				<PicUrl><![CDATA[{$value_arr['pic']}]]></PicUrl>
				<Url><![CDATA[{$value_arr['url']}]]></Url>
				</item>";
                $con .= "</Articles>
                <FuncFlag>{$o_arr[1]}</FuncFlag>";
                break;
 
        }
 
        //=================end return============
        echo $con."</xml>";
    }
 
 
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];
        $token     = TOKEN;
        $tmpArr    = array($token, $timestamp, $nonce);
 
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
 
        if( $tmpStr == $signature )
        {
          return true;
        }
        else
        {
          return false;
        }
    }

	function show_link()
	{
		$user_obj = new UserModel();
		$user_info = $user_obj->getUserInfoByUserOpenid('user_id');
		if ($user_info)
		{
			session('user_id', $user_info['user_id']);
		}
		else
		{
			$user_id = $user_obj->registerUserByUserCookie();
			session('user_id', $user_id);
		}
		if (strpos($this->keyword, '世界杯') === 0)
		{
			$this->fun_xml('text', array($this->world_cup_link . "\n" . $this->fromUsername));
			break;
		}
		else
		{
			$this->fun_xml('text', array($this->sorry_msg));
		}
	}

	function save_log($ids)
	{
		$postStr = file_get_contents("php://input");
	        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		$key_str = '';
		$val_str = '';
		foreach ($postObj AS $k => $v)
		{
			if ($k == 'Precision') $k = 'MPrecision';
			if ($k == 'Content') $k = 'keyword';
			if ($k == 'MsgId') $k = 'MediaId';
			$key_str .= $k . ',';
			$val_str .= '"' . $v . '"' . ',';
		}
		$key_str .= 'send_ids';
		$val_str .= '"' . $ids . '"';
		$sql = 'INSERT INTO GDN_EventLog(' . $key_str . ') VALUES(' . $val_str . ')';
		$conn = mysql_connect('localhost', 'root', '1moodadmin');
		mysql_select_db('yurtree_20140421', $conn);
		mysql_query('SET NAMES UTF8', $conn);
		mysql_query($sql);
		mysql_close($conn);
		log_file($sql);
	}
}

function checkSignature()
{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
	$token = '9c95062add550a28bf5b3c9ab6a87ce0';
	$tmpArr = array($token, $timestamp, $nonce);
	sort($tmpArr, SORT_STRING);
	$tmpStr = implode( $tmpArr );
	$tmpStr = sha1( $tmpStr );
	
	if( $tmpStr == $signature ){
		return true;
	}else{
		return false;
	}
}

/*echo "<pre>";
$arr = getArticles('妈妈');
print_r(count($arr['articles']));
print_r($arr['articles']);
echo "</pre>";*/
function getArticles($keywords)
{
	$conn = mysql_connect('localhost', 'root', '1moodadmin');
	mysql_select_db('yurtree_20140421', $conn);
	mysql_query('SET NAMES UTF8', $conn);
	//先根据文章标题匹配，若取够了10条，不再继续取；若不足10条，根据文章内容匹配取剩余条数
	$sql = 'SELECT DiscussionID, Name, Body FROM GDN_Discussion WHERE Name LIKE "%' . $keywords . '%" LIMIT 10';
	$result = mysql_query($sql);
	$articles = array();
	$ids = '';
	while ($row = mysql_fetch_assoc($result))
	{
		$row['Body'] = mbSubStr($row['Body'], 60);
		$articles[] = $row;
		$ids .= $row['DiscussionID'] . ',';
	}

	//截掉最后一个逗号，用于过滤已取过的文章
	$ids = substr($ids, 0, -1);
	
	$num = count($articles);
	if ($num < 10)
	{
		$where = ' Body LIKE "%' . $keywords . '%" ';
		$where .= $ids ? ' AND DiscussionID NOT IN (' . $ids . ')' : '';
		$sql = 'SELECT DiscussionID, Name, Body FROM GDN_Discussion WHERE ' . $where . ' LIMIT ' . (10 - $num);
		$result = mysql_query($sql);
		while ($row = mysql_fetch_assoc($result))
		{
			$row['Body'] = mbSubStr($row['Body'], 60);
			$articles[] = $row;
			$ids .= $row['DiscussionID'] . ',';
		}
	}
	$ids = substr($ids, 0, -1);
	mysql_close($conn);

	return array('articles' => $articles, 'ids' => $ids);
}

/**
 * 多字节安全的字符串截取，需开启mbstring扩展
 *
 * @param string $src_str 源字符串
 * @param int $limit 截取字符数
 * @param string $fill 省略字符串的填充字符串，默认'...'，可以设置$fill为false禁用
 * @return mixed 若mbstring扩展未开启，则返回false，否则返回截取后字符串
 * @todo 截取源字符串$src中$limit长度后以$fill字符串补充
 *
 */
/*function mbSubStr($src_str, $limit, $fill = '...')
{
	if(!extension_loaded('mbstring')){
		return false;
	}
	if(mb_strlen($src_str, 'UTF-8') > $limit)
	{
		$src_str = mb_substr($src_str, 0, $limit, 'UTF-8');
		if(false !== $fill)
		{
			$src_str .= $fill;
		}
	}
	return $src_str;
}*/
