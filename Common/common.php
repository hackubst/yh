<?php

/**
 * @param string $url 远程文件路径
 * @param string $filename 本地存储文件名
 */
function grabImage($url, $filename = '')
{
    if($url == '')
    {
        return false; //如果 $url 为空则返回 false;
    }

    $ext_name = strrchr($url, '.'); //获取图片的扩展名
    if($ext_name != '.gif' && $ext_name != '.jpg' && $ext_name != '.bmp' && $ext_name != '.png' && $ext_name != '.zip') {
        return false; //格式不在允许的范围
    }

    if($filename == '')
    {
        $filename = time().'.rar'; //以时间戳另起名
    }
    //开始捕获
    ob_start();
    readfile($url);
    $img_data = ob_get_contents();
    ob_end_clean();
    $size = strlen($img_data);
    $local_file = fopen($filename , 'w');
    fwrite($local_file, $img_data);
    fclose($local_file);
    return $filename;
}


/**
 * 判断文件夹是否也可写权限
 * @param  [str] $filepath 文件夹路径
 * @return [bool]
 * @author  <[23585472@qq.com]>
 */
function getChmod($filepath)
{

    if(is_dir($filepath))
    {
        //开始写入测试;
        $file = '_______' . time() . rand() . '_______';
        $file = $filepath .'/'. $file;
        if (file_put_contents($file, '//'))
        {
            unlink($file);//删除测试文件
            return true;
        }
        else
        {
            return false;
        }
    }
}

/**
 * 隐藏邮箱后面的字符(用于前台头部,防止用户名过长导致模版错位)
 * @param  [str] $email 邮箱
 * @return [str]
 * @author  <[23585472@qq.com]>
 */
function hide_mail ($email)
{
    $pattern='/\S+@(([a-z0-9]+\-)*[a-z0-9]+\.)+(com|net|cn|com.cn|org)/ix';
    if(preg_match($pattern, $email))
    {
       $email = substr($email, 0, strrpos($email, '@'));
    }
    return $email;
}


/**
* 获取用户真实 IP
* @return: str
* @author <23585472@qq.com>
*/
function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        }
    }


    return $realip;
}


/**
 * 根据 IP 获取地理位置
 * @param IP
 * @return: array
 * @author <23585472@qq.com>
*/
function getCity($ip)
{
    $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
    $ip=json_decode(file_get_contents($url));
    if((string)$ip->code=='1'){
       return false;
     }
     $data = (array)$ip->data;
    return $data;
}

//通过取浏览器的头信息，判断当前网页是在哪个APP环境中
function getAppName()
{
	$appid = 0;
	$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';

	if(preg_match("/weibo/i", $user_agent))	//新浪微博
	{
		return APP_SOURCE_WEIBO;
	}
	else if ($user_agent == 'micromessenger')	//微博
	{
		return APP_SOURCE_WEIXIN;
	}

	return APP_SOURCE_OTHER;	//异常情况
}

//分析浏览器头，判断是在哪种设备中
function getdeviceName($user_agent)
{
	if (!$user_agent) return false;

	$user_agent = strtolower($user_agent);

	if(preg_match("/iphone/i", $user_agent))	//iphone手机
	{
		return '苹果手机';
	}
	elseif (preg_match("/ipod/i", $user_agent))	//ipod
	{
		return '苹果Ipod';
	}
	elseif (preg_match("/ipad/i", $user_agent))	//ipad
	{
		return '苹果IPAD';
	}
	elseif (preg_match("/android/i", $user_agent))	//android
	{
		return '安卓设备';
	}
	elseif (preg_match("/windows phone/i", $user_agent))	//微软手机
	{
		return 'wp手机';
	}
	else
	{
		return '';
	}
}


/**
 * 2维码生成
 * @param chl 网址
 * @param widhtHeight 宽高
 * @return [str] 2维码图片
 * @author <23585472@qq.com>
 */
function generateQRfromGoogle($chl,$widhtHeight ='250',$EC_level='L',$margin='0')
{
    $url = urlencode($url);
    return '<img src="http://chart.apis.google.com/chart?chs='.$widhtHeight.'x'.$widhtHeight.'&cht=qr&chld='.$EC_level.'|'.$margin.'&chl='.$chl.'" alt="QR code" widhtHeight="'.$widhtHeight.'" widhtHeight="'.$widhtHeight.'"/>';
}

/**
 * 2为数组排序
 * @param arr 排序的数组
 * @param keys 排序的键名
 * @param type 排序方向
 * @author <23585472@qq.com>
 */
function array_sort($arr,$keys,$type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}


/**
 * 调试时候用来写日志文件的函数
 * @param filename 保存的文件名
 * @author <23585472@qq.com>
 */
function phpLog($str) {
    $time = "\n\t". date('Y-m-d H:i:s', time()) . "------------------------------------------------------------------------------------\n\t";
    file_put_contents(APP_PATH . 'log.php', $time .  $str , FILE_APPEND);
}

/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 * @author <23585472@qq.com>
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function randLenString($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {//位数过长重复字符串一定次数
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        // 中文随机字
        for($i=0;$i<$len;$i++){
          $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1,'utf-8',false);
        }
    }
    return $str;
}


/**
* 去掉字符串前一位
* @param string $str
* @author <23585472@qq.com>
*/
function left_substr($str){
    return substr($str, 1 ,strlen($str) - 1 );
}

/**
 * 金额格式化
 * @param $fee
 * @date: 2019/5/7
 * @author: hui
 * @return null|string|string[]
 */
function feeHandle($fee){
    if (is_numeric($fee)) {
        $flag = 0;
        if($fee < 0)
        {
            $flag = 1;
            $fee = -1* $fee;
        }
        list($int, $decimal) =  explode('.', $fee);
        $int = preg_replace('/(?<=[0-9])(?=(?:[0-9]{3})+(?![0-9]))/', ',',$int);
        if($flag)
        {
            $int = '-'.$int;
        }
//        if ($decimal)$int .= '.'. $decimal;
        return $int;
    }
}

/**
* 下载文件
* @param string $file 被下载文件的路径
* @param string $name 用户看到的文件名
* @author <23585472@qq.com>
*/
function file_download($file,$name=''){
    $fileName = $name ? $name : pathinfo($file,PATHINFO_FILENAME);
    $filePath = realpath($file);

    if(!file_exists($filePath))
    {
        header('HTTP/1.1 404 Not Found');
        exit;
    }


    $fp = fopen($filePath,'rb');


    $fileName = $fileName .'.'. pathinfo($filePath,PATHINFO_EXTENSION);
    $encoded_filename = urlencode($fileName);
    $encoded_filename = str_replace("+", "%20", $encoded_filename);

    header('HTTP/1.1 200 OK');
    header( "Pragma: public" );
    header( "Expires: 0" );
    header("Content-type: application/octet-stream");
    header("Content-Length: ".filesize($filePath));
    header("Accept-Ranges: bytes");
    header("Accept-Length: ".filesize($filePath));

    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/MSIE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $fileName . '"');
    } else {
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    }

    // ob_end_clean(); <--有些情况可能需要调用此函数
    // 输出文件内容
    fpassthru($fp);
    exit;
}


/**
* 文件大小格式化
* @param integer $size 初始文件大小，单位为byte
* @return array 格式化后的文件大小和单位数组，单位为byte、KB、MB、GB、TB
* @author <23585472@qq.com>
*/
function file_size_format($size = 0, $dec = 2) {
    $unit = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    $result['size'] = round($size, $dec);
    $result['unit'] = $unit[$pos];
    return $result['size'].$result['unit'];
}


/**
* 文件夹大小
* @param  string directoty 文件夹
* @return array 格式化后的文件大小和单位数组，单位为byte、KB、MB、GB、TB
* @author <23585472@qq.com>
*/
function dirSize($directoty){
    $dir_size=0;
    if($dir_handle=@opendir($directoty))
    {
        while($filename=readdir($dir_handle)){
            $subFile=$directoty.DIRECTORY_SEPARATOR.$filename;
            if($filename=='.'||$filename=='..'){
                continue;
            }elseif (is_dir($subFile))
            {
                $dir_size+=dirSize($subFile);
            }elseif (is_file($subFile)){
                $dir_size+=filesize($subFile);
            }
        }
        closedir($dir_handle);
    }
    return file_size_format($dir_size);
}




/**
* 生成签名
* @param $client_secret 客户端密钥
* @param $arr 要传递的数组，此数组只能是一维或二维数组，不可以超过二维，且包括系统级参数（但不包括签名本身）和应用级参数
* @return 签名字符串
* @author <23585472@qq.com>
**/
function get_sign($client_secret, $arr) {
	ksort($arr); //将数组按键名排序，按键名升序排列
	$tmp = '';
	foreach ($arr as $k => $v) {
		if (is_array($v)) { //如果当前值为数组时则该数组内的各项也要拼接到字符串中
			ksort($v); //按键名排序
			foreach ($v as $m => $n) {
				$tmp .= $m . $n; //拼接字符串
			}
		} else {
			$tmp .= $k . $v; //拼接字符串
		}
	}
	return strtoupper(md5($client_secret . $tmp . $client_secret)); //先用md5加密，再转成大写
}

/**
 * 浏览过的商品记录到cookie中
 * @param  item_info 商品数据
 * @author <23585472@qq.com>
 */
function set_item_history($item_info)
{
    //获取浏览过的商品
    $item_history  =  unserialize($_COOKIE['item_history']);
    $temp_num      = count($item_history);
    //当有六条记录时候删除最原始一条
    if($temp_num == 6)
    {
       $new_arr = array();
       $i = 1;
       foreach ($item_history as $key => $value)
       {
           if($i != 1)
               continue;

           $new_arr[$key] = $value;
           $i++;
       }
       $item_history = $new_arr;
    }

    //添加新浏览的商品
    $item_history[$item_info['item_id']]['item_id']          = $item_info['item_id'];
    $item_history[$item_info['item_id']]['item_name']        = $item_info['item_name'];
    $item_history[$item_info['item_id']]['base_pic']         = $item_info['base_pic'];
    $item_history[$item_info['item_id']]['wholesale_price']  = $item_info['wholesale_price'];
    cookie('item_history',null);    //一定要先清空
    cookie('item_history', serialize($item_history), time()+3600*24*30);
}


/**
 * 转换支付类型
 * @param $change_type 支付类型值
 * @return string 支付类型
 * @author <23585472@qq.com>
 */
function change_change_type($change_type)
{
        //操作类型
        switch ($change_type)
        {
            case 1: $change_type = '领取救济'; break;
            case 2: $change_type = '领取排行榜奖励'; break;
            case 3: $change_type = '存入银行'; break;
            case 4: $change_type = '银行取出'; break;
            case 5: $change_type = '发红包'; break;
            case 6: $change_type = '经验换豆'; break;
            case 7: $change_type = '兑换点卡'; break;
            case 8: $change_type = '领红包'; break;
            case 9: $change_type = '充值'; break;
            case 10: $change_type = '提现'; break;
            case 11: $change_type = '红包退回'; break;
            case 12: $change_type = '游戏投注'; break;
            case 13: $change_type = '充值'; break;
            case 14: $change_type = '扣减金豆'; break;
            case 15: $change_type = '提现申请'; break;
            case 16: $change_type = '拒绝提现申请'; break;
            case 17: $change_type = '游戏竞猜赢取'; break;
            case 18: $change_type = '推广新用户奖励'; break;
            case 19: $change_type = '后台加余额'; break;
            case 20: $change_type = '后台扣余额'; break;
            case 21: $change_type = '代理充值支出'; break;
            case 22: $change_type = '投注金豆返还'; break;
            case 23: $change_type = '卡密回收'; break;
            case 26: $change_type = '领取每周亏损返利'; break;
            case 27: $change_type = '领取充值返利'; break;
            case 28: $change_type = '领取下线投注返利'; break;
            case 29: $change_type = '领取有效流水返利'; break;
            case 30: $change_type = '撤销充值扣除金豆'; break;
            case 31: $change_type = '撤销充值扣除活动赠送金豆'; break;
            case 32: $change_type = '撤销充值返还金豆'; break;
            case 33: $change_type = '后台加银行分'; break;
            case 34: $change_type = '后台扣银行分'; break;
            default:    break;
        }
        return $change_type;
}


/**
 * 处理json传值中文不显示
 * @param:   $data 字符串
 * @return: string 字符串
 * @Author: 张光强
 * @Date:  Wed Jan 15 06:31:57 GMT 2014
 */
function ch_json_encode($data)
{
    /**
    * 将中文编码
    * @param array $data
    * @return string
    */
    function ch_urlencode($data)
    {
        if (is_array($data) || is_object($data))
        {
            foreach ($data as $k => $v)
            {
                if (is_scalar($v))
                {
                    if (is_array($data))
                    {
                        $data[$k] = urlencode($v);
                    }
                    elseif (is_object($data))
                    {
                        $data->$k = urlencode($v);
                    }
                }
                elseif (is_array($data))
                {
                    $data[$k] = ch_urlencode($v);//递归调用该函数
                }
                elseif (is_object($data))
                {
                    $data->$k = ch_urlencode($v);
                }
            }
        }
        return$data;
    }
    $ret = ch_urlencode($data);
    $ret = json_encode($ret);
    return urldecode($ret);
}



/**
 * 日期转换时间戳
 * @param:   字符串
 * @return:
 * @Author: 张光强
 * @Date:  Wed Jan 15 06:31:57 GMT 2014
 */
function str_format_time($timestamp = '')
{
    if (preg_match("/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/i", $timestamp))
    {
        list($date,$time)=explode(" ",$timestamp);
        list($year,$month,$day)=explode("-",$date);
        list($hour,$minute,$seconds )=explode(":",$time);
        $timestamp=gmmktime($hour,$minute,$seconds,$month,$day,$year);
    }
    else
    {
        $timestamp=time();
    }
    return ($timestamp-28800);
}


/**
 *     过滤所有空格，回车，换行
 * @param:   字符串
 * @return:
 * @Author: 张光强
 * @Date:  Wed Jan 15 06:31:57 GMT 2014
 */
function loseSpace($str){
	//$str = preg_replace("/ /","",$str);
	//$str = preg_replace("/&nbsp;/","",$str);
	//$str = preg_replace("/　/","",$str);
	$str = preg_replace("/\r\n/","",$str);
	$str = str_replace(chr(13),"",$str);
	$str = str_replace(chr(10),"",$str);
	$str = str_replace(chr(9),"",$str);
	return $str;
}

/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @return mixed
 */
function CCC($name=null, $value=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        if(!empty($value) && $array = S('c_'.$value)) {
            $_config = array_merge($_config, array_change_key_case($array));
        }
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : null;
            $_config[$name] = $value;
            return;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtolower($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : null;
        $_config[$name[0]][$name[1]] = $value;
        return;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name));
        if(!empty($value)) {// 保存配置值
            S('c_'.$value,$_config);
        }
        return;
    }
    return null; // 避免非法参数
}




/**
 *     删除指定文件夹(包括文件夹内所有文件与目录)
 * @param:    文件夹绝对路径
 * @return:  无
 * @Author: 张光强
 * @Date:  Tue Jan 14 03:44:15 GMT 2014
 */
function delDirAndFile($dirName)
{
	if($handle = opendir("$dirName")){
		while(false !== ($item = readdir($handle))){
			if($item != "." && $item != ".."){
				if(is_dir("$dirName/$item")){
					delDirAndFile("$dirName/$item");
				}else{
					//if(unlink("$dirName/$item"))echo"成功删除文件： $dirName/$item<br />\n";
					unlink("$dirName/$item");
				}
			}
		}
		closedir($handle);
		//if(rmdir($dirName))echo"成功删除目录： $dirName<br />\n";
		rmdir($dirName);
	}
}

/**
 * 上传图片
 * @param string $fileBoxName file输入框的名称
 * @return array
 */
function uploadImg($fileBoxName = 'imageUp') {
    if(!empty($_FILES[$fileBoxName]['name'])){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();                       // 实例化上传类
        $upload->autoSub = true;                          // 是否有子目录保存
        $upload->subType = 'date';
        $upload->maxSize  = 3145728;// 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath =  UPLOAD_IMAGE_PATH; // 设置附件上传目录
        $upload->dateFormat = DATE_FORMAT;                      // 保存的日期子目录格式
        if(!$upload->upload()) {                          // 上传错误提示错误信息
            $data['error_msg'] = $upload->getErrorMsg();
        }else{
            // 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
            // 图片名称
            $data['pic_name']  =  end(explode('/', $info[0]['savename']));
            // 扩展名
            $data['extension'] =  $info[0]['extension'];
            // 图片访问路径
            $data['pic_url']   =  substr(UPLOAD_IMAGE_PATH, 1) . $info[0]['savename'];
            // 图片存放目录
            $data['pic_path']  =  str_replace($data['pic_name'], '', $data['pic_url']);
        }
        return $data;
    }

    return null;
}

/*验证手机号*/
function checkMobile($mobile)
{
    if(preg_match('/^(0|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57]|19[0-9])[0-9]{8}$/',$mobile))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/*验证电话号*/
function checkTel($tel)
{
        return preg_match('/^(0[0-9]{2,3}[\-]?)?([0-9]{6,8})+(\-[0-9]{1,4})?$/',$tel);
}

/*验证邮编号*/
function checkPost($C_post){
    $C_post=trim($C_post);
    if (strlen($C_post) == 6){//若校验邮编的话此值为6、电话号码或为11位或7位、区号则4位，视情况定
        if(!ereg('/^[1-9]\d{5}$/',$C_post)){
            return true;
        }else{
            return false;
        }
    }else{
        return false;;
    }
}

/*验证邮箱*/
function checkEmail($email)
{
	return preg_match('/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/',$email);
}

/*获取当前完整路径*/
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

function getImageRealPath($relative_path)
{
	return $relative_path?C('IMG_DOMAIN').$relative_path:'';
}

/**隐藏手机的中间部分号码**/
function hideMobile($phone)
{
	$pattern = "/(1\d{1,2})\d\d(\d{0,3})/";		//隐藏手机号码的中间部分
	$replacement = "\$1****\$3";
	return preg_replace($pattern, $replacement, $phone);
}

/**
 * 过滤字符串中空白字符以及HTML标签并截取指定长度字符
 *
 * @param string $src_str 源字符串
 * @param int $trunc 截取字符数，默认200
 * @param string $tag 保留的HTML标签
 * @param string $fill 若超出指定长度时的填充字符串，默认'...'，若不希望填充则设置false
 * @author zhengzhen
 * @return string 处理后字符串
 * @todo 去除$tag以外的所有HTML标签和\t,\n,\r空白字符
 * @todo 以及空的HTML元素，即起始标签和结束标签之间只包含空白字符（包括\t,\n,\r,WhiteSpace,全角空格(Unicode码为/u3000)）
 * @todo 截取$trunc个字符，多余字符以$fill替换
 *
 */
function filterAndSubstr($src_str, $trunc = 200, $tag = '', $fill = '...')
{
	$plain = strip_tags($src_str, $tag);
	$plain = preg_replace('/<([a-z]+)[^>]*>\s*\x{3000}*<\/\1[^>]*>|<[a-z]+\s*\/>/ismuU', '', $plain);
	$plain = str_replace(array(chr(9), chr(10), chr(13)), '', $plain);
	$dst = mb_substr($plain, 0, $trunc, 'UTF-8');
	if($fill !== false && mb_strlen($plain, 'UTF-8') > $trunc)
	{
		$dst .= $fill;
	}
	return $dst;
}

/**
 * 多字节安全的字符串截取，需开启mbstring扩展
 *
 * @param string $src_str 源字符串
 * @param int $limit 截取字符数
 * @param string $fill 省略字符串的填充字符串，默认'...'，可以设置$fill为false禁用
 * @author zhengzhen
 * @return mixed 若mbstring扩展未开启，则返回false，否则返回截取后字符串
 * @todo 截取源字符串$src中$limit长度后以$fill字符串补充
 *
 */
function mbSubStr($src_str, $limit, $fill = '...')
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
}

function mb_trim($src_str)
{
	preg_match_all('/^[\x{9}\x{10}\x{13}\x{3000}]$/u', $src_str, $matches);
}

/**
 * Unicode编码
 *
 * @param string $src_str 源字符串
 * @param string $src_encode 源字符串编码，默认'UTF-8'
 * @param int $mode 返回字符串表现形式，默认1
 * @author zhengzhen
 * @return mixed 返回编码字符串
 * @todo 转换源字符串为Unicode编码，以指定形式返回
 *
 */
function unicode_encode($src_str, $src_encode = 'UTF-8', $mode = 1)
{
	switch($mode)
	{
		case 1:
			$prefix = '\u';
			$suffix = '';
			break;
		case 2:
			$prefix = '\x';
			$suffix = '';
			break;
		case 3:
			$prefix = '\x{';
			$suffix = '}';
			break;
		default:
			$prefix = '\u';
			$suffix = '';
			break;
	}

	$dst = iconv($src_encode, 'UCS-2', $src_str);
	$len = strlen($dst);
	$uni = '';
	for($i = 0; $i < $len; $i += 2)
	{
		$byte_h = $dst[$i];//高位字节
		$byte_l = $dst[$i + 1];//低位字节

		if(ord($byte_h) == 0)
		{
			//若高位字节ASCII编码值为0，则为ASCII字符
			$uni .= $byte_l;
		}
		elseif(ord($byte_h) > 0)
		{
			$uni .= $prefix;
			$uni .= base_convert(ord($byte_h), 10, 16);//高位字节转十六进制
			$uni .= base_convert(ord($byte_l), 10, 16);//低位字节转十六进制
			$uni .= $suffix;
		}
	}
	return $uni;
}

/**
 * HTML实体编码
 *
 * @param string $src_str 源字符串
 * @param string $src_encode 源字符串编码，默认'UTF-8'
 * @param int $dst_base 进制数，默认16，可选10
 * @author zhengzhen
 * @return mixed 返回编码字符串
 * @todo 以指定进制转换源字符串为HTML实体编码格式
 *
 */
function html_entity_encode($src_str, $src_encode = 'UTF-8', $dst_base = 16)
{
	$dst = iconv($src_encode, 'UCS-2', $src_str);
	$len = strlen($dst);
	$entity = '';

	for($i = 0; $i < $len; $i += 2)
	{
		$byte_h = $dst[$i];//高位字节
		$byte_l = $dst[$i + 1];//低位字节
		$byte_h = base_convert(ord($byte_h), 10, 2);
		$byte_l = base_convert(ord($byte_l), 10, 2);
		$byte_h = str_repeat('0', (8 - strlen($byte_h))) . $byte_h;//不足8位补0
		$byte_l = str_repeat('0', (8 - strlen($byte_l))) . $byte_l;//不足8位补0

		switch($dst_base)
		{
			case 10:
				$prefix = '&#';
				$suffix = ';';
				$entity .= $prefix;
				$entity .= base_convert($byte_h . $byte_l, 2, 10);
				$entity .= $suffix;
				break;
			case 16:
			default:
				$prefix = '&#x';
				$suffix = ';';
				$entity .= $prefix;
				$entity .= base_convert($byte_h . $byte_l, 2, 16);
				$entity .= $suffix;
				break;
		}
	}
	return $entity;
}

function makeTag($src, $keyword)
{
	$keyword = unicode_encode($keyword, 'UTF-8', 3);
	return preg_replace_callback(
		'/>[^<>]*(' . $keyword . ')[^<>]*<(?!\/a)/ismuU',
		'_replace',
		$src
	);
}

function _replace($matches)
{
	return str_replace($matches[1], '<a href="">' . $matches[1] . '</a>', $matches[0]);
}

/**
 * 更通用的图片上传处理
 *
 * @param array $src_img 上传的图片资源，为$_FILES['filename']格式，一般地filename为img标签的name属性值
 * @param string $sub_path 图片保存子路径，以'/'开头，但不以'/'结尾，如设置友情链接logo图片子路径为'/other/link'，根路径为/Uploads/image
 * @param string $img_domain 图片域名，结尾无'/'，未设置则取配置项中IMG_DOMAIN值
 * @param int $limit_size 图片尺寸限制，如未设置默认2MB
 * @author zhengzhen
 * @return string $imgUrl
 * @todo 首先判断图片大小是否超出预设值，然后判断图片格式是否支持
 * @todo 创建相应路径并保存图片，转换图片路径为图片URL通过JSON输出到终端（一般为浏览器）
 *
 */
function upImageHandler($src_img, $sub_path, $img_domain = null, $limit_size = null)
{
	$tmpFile = $src_img['tmp_name'];
// echo "<pre>";
// var_dump($src_img);
// echo $tmpFile;
	$tmpFileSize = $src_img['size'];
	$maxSize = isset($limit_size) ? $limit_size : 2 * pow(1024, 2);
	if($tmpFileSize > $maxSize)
	{
		echo json_encode(array('status' => 0, 'msg' => '图片过大，请上传2MB以内大小图片！'));
		exit;
	}

	switch($src_img['type'])
	{
		case 'image/gif':
			$imgExt = '.gif';
			break;
		case 'image/jpeg':
		case 'image/pjpeg'://IE
			$imgExt = '.jpg';
			break;
		case 'image/png':
		case 'image/x-png'://IE
			$imgExt = '.png';
			break;
		default:
			break;
	}
	if(!isset($imgExt))
	{
		echo json_encode(array('status' => 0, 'msg' => '暂不支持该图片格式！'));
		exit;
	}

	$savePath = APP_PATH . 'Uploads/image' . $sub_path . '/' . date('Y-m');
	$saveFile = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . $imgExt;

	//确认保存路径，没有则创建
	if(!is_dir($savePath))
	{
		if(!@mkdir($savePath, 0700, true))
		{
			echo json_encode(array('status' => 0, 'msg' => '上传目录创建失败！'));
			exit;
		}
	}
	//移动文件
	if(move_uploaded_file($tmpFile, $saveFile) === false)
	{
		echo json_encode(array('status' => 0, 'msg' => '图片上传失败！'));
		exit;
	}
	if(!isset($img_domain))
	{
		$img_domain = C('IMG_DOMAIN');
	}
   // $imgUrl = str_replace(APP_PATH . 'Uploads', $img_domain . '/Uploads', $saveFile);
	$imgUrl = str_replace(APP_PATH . 'Uploads', '/Uploads', $saveFile);
	echo json_encode(array('status' => 1, 'img_url' => $imgUrl));
	//exit;
    return $imgUrl;
}

/**
 * 更通用的附件上传处理
 *
 * @param array $src_img 上传的图片资源，为$_FILES['filename']格式，一般地filename为img标签的name属性值
 * @param string $sub_path 图片保存子路径，以'/'开头，但不以'/'结尾，如设置友情链接logo图片子路径为'/other/link'，根路径为/Uploads/image
 * @param string $img_domain 图片域名，结尾无'/'，未设置则取配置项中IMG_DOMAIN值
 * @param int $limit_size 图片尺寸限制，如未设置默认2MB
 * @author zlf
 * @return string $imgUrl
 * @todo 首先判断图片大小是否超出预设值，然后判断图片格式是否支持
 * @todo 创建相应路径并保存图片，转换图片路径为图片URL通过JSON输出到终端（一般为浏览器）
 *
 */
function upFileHandler($src_img, $sub_path, $img_domain = null, $limit_size = null)
{
    $tmpFile = $src_img['tmp_name'];
// echo "<pre>";
// var_dump($src_img);
 #print_r($src_img);die;
    $tmpFileSize = $src_img['size'];
    $maxSize = isset($limit_size) ? $limit_size : 2 * pow(1024, 2);
    if($tmpFileSize > $maxSize)
    {
        echo json_encode(array('status' => 0, 'msg' => '附件过大，请上传2MB以内大小附件！'));
        exit;
    }

    switch($src_img['type'])
    {
        case 'image/gif':
            $imgExt = '.gif';
            break;
        case 'image/jpeg':
        case 'image/pjpeg'://IE
            $imgExt = '.jpg';
            break;
        case 'image/png':
        case 'image/x-png'://IE
            $imgExt = '.png';
            break;
        case 'application/vnd.ms-excel':
        case 'application/msexcel':
        case 'application/octet-stream':
            $imgExt = '.xls';
            break;
        case 'application/msword':
            $imgExt = '.doc';
            break;
        case 'application/pdf':
            $imgExt = '.pdf';
            break;
        case 'application/zip':
            $imgExt = '.zip';
            break;
        default:
            break;
    }
    if(!isset($imgExt))
    {
        echo json_encode(array('status' => 0, 'msg' => '暂不支持该附件格式！'));
        exit;
    }

    $savePath = APP_PATH . 'Uploads/image' . $sub_path . '/' . date('Y-m');
    $saveFile = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . $imgExt;

    //确认保存路径，没有则创建
    if(!is_dir($savePath))
    {
        if(!@mkdir($savePath, 0700, true))
        {
            echo json_encode(array('status' => 0, 'msg' => '上传目录创建失败！'));
            exit;
        }
    }
    //移动文件
    if(move_uploaded_file($tmpFile, $saveFile) === false)
    {
        echo json_encode(array('status' => 0, 'msg' => '附件上传失败！'));
        exit;
    }
    if(!isset($img_domain))
    {
        $img_domain = C('IMG_DOMAIN');
    }
   // $imgUrl = str_replace(APP_PATH . 'Uploads', $img_domain . '/Uploads', $saveFile);
    $imgUrl = str_replace(APP_PATH . 'Uploads', '/Uploads', $saveFile);
    echo json_encode(array('status' => 1, 'img_url' => $imgUrl));
    //exit;
    return $imgUrl;
}

/**
 * BMP 创建函数(解决thinkphp不能使用ImageCreateFrombmp创建图片)
 * @param string $filename 文件地址
 * @return resource of GD
 * @author 23585472@qq.com
 */
function imagecreatefrombmp( $filename ){
    if ( !$f1 = fopen( $filename, "rb" ) )
        return FALSE;

    $FILE = unpack( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread( $f1, 14 ) );
    if ( $FILE['file_type'] != 19778 )
        return FALSE;

    $BMP = unpack( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread( $f1, 40 ) );
    $BMP['colors'] = pow( 2, $BMP['bits_per_pixel'] );
    if ( $BMP['size_bitmap'] == 0 )
        $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
    $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
    $BMP['bytes_per_pixel2'] = ceil( $BMP['bytes_per_pixel'] );
    $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
    $BMP['decal'] -= floor( $BMP['width'] * $BMP['bytes_per_pixel'] / 4 );
    $BMP['decal'] = 4 - (4 * $BMP['decal']);
    if ( $BMP['decal'] == 4 )
        $BMP['decal'] = 0;

    $PALETTE = array();
    if ( $BMP['colors'] < 16777216 ){
        $PALETTE = unpack( 'V' . $BMP['colors'], fread( $f1, $BMP['colors'] * 4 ) );
    }

    $IMG = fread( $f1, $BMP['size_bitmap'] );
    $VIDE = chr( 0 );

    $res = imagecreatetruecolor( $BMP['width'], $BMP['height'] );
    $P = 0;
    $Y = $BMP['height'] - 1;
    while( $Y >= 0 ){
        $X = 0;
        while( $X < $BMP['width'] ){
            if ( $BMP['bits_per_pixel'] == 32 ){
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) );
                $B = ord(substr($IMG, $P,1));
                $G = ord(substr($IMG, $P+1,1));
                $R = ord(substr($IMG, $P+2,1));
                $color = imagecolorexact( $res, $R, $G, $B );
                if ( $color == -1 )
                    $color = imagecolorallocate( $res, $R, $G, $B );
                $COLOR[0] = $R*256*256+$G*256+$B;
                $COLOR[1] = $color;
            }elseif ( $BMP['bits_per_pixel'] == 24 )
                $COLOR = unpack( "V", substr( $IMG, $P, 3 ) . $VIDE );
            elseif ( $BMP['bits_per_pixel'] == 16 ){
                $COLOR = unpack( "n", substr( $IMG, $P, 2 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 8 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, $P, 1 ) );
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 4 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 2) % 2 == 0 )
                    $COLOR[1] = ($COLOR[1] >> 4);
                else
                    $COLOR[1] = ($COLOR[1] & 0x0F);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }elseif ( $BMP['bits_per_pixel'] == 1 ){
                $COLOR = unpack( "n", $VIDE . substr( $IMG, floor( $P ), 1 ) );
                if ( ($P * 8) % 8 == 0 )
                    $COLOR[1] = $COLOR[1] >> 7;
                elseif ( ($P * 8) % 8 == 1 )
                    $COLOR[1] = ($COLOR[1] & 0x40) >> 6;
                elseif ( ($P * 8) % 8 == 2 )
                    $COLOR[1] = ($COLOR[1] & 0x20) >> 5;
                elseif ( ($P * 8) % 8 == 3 )
                    $COLOR[1] = ($COLOR[1] & 0x10) >> 4;
                elseif ( ($P * 8) % 8 == 4 )
                    $COLOR[1] = ($COLOR[1] & 0x8) >> 3;
                elseif ( ($P * 8) % 8 == 5 )
                    $COLOR[1] = ($COLOR[1] & 0x4) >> 2;
                elseif ( ($P * 8) % 8 == 6 )
                    $COLOR[1] = ($COLOR[1] & 0x2) >> 1;
                elseif ( ($P * 8) % 8 == 7 )
                    $COLOR[1] = ($COLOR[1] & 0x1);
                $COLOR[1] = $PALETTE[$COLOR[1] + 1];
            }else
                return FALSE;
            imagesetpixel( $res, $X, $Y, $COLOR[1] );
            $X++;
            $P += $BMP['bytes_per_pixel'];
        }
        $Y--;
        $P += $BMP['decal'];
    }
    fclose( $f1 );

    return $res;
}

/**
 * 更通用的图片上传处理
 *
 * @param array $src_img 上传的图片资源，为$_FILES['filename']格式，一般地filename为img标签的name属性值
 * @param string $sub_path 图片保存子路径，以'/'开头，但不以'/'结尾，如设置友情链接logo图片子路径为'/other/link'，根路径为/Uploads/image
 * @param string $img_domain 图片域名，结尾无'/'，未设置则取配置项中IMG_DOMAIN值
 * @param int $limit_size 图片尺寸限制，如未设置默认2MB
 * @author zhengzhen
 * @return void
 * @todo 首先判断图片大小是否超出预设值，然后判断图片格式是否支持
 * @todo 创建相应路径并保存图片，转换图片路径为图片URL通过JSON输出到终端（一般为浏览器）
 *
 */
function uploadImage($src_img, $sub_path, $img_domain = null, $limit_size = null)
{
	$tmpFile = $src_img['tmp_name'];
	$tmpFileSize = $src_img['size'];
	$maxSize = isset($limit_size) ? $limit_size : 2 * pow(1024, 2);
	if($tmpFileSize > $maxSize)
	{
		#echo json_encode(array('status' => 0, 'msg' => '图片过大，请上传2MB以内大小图片！'));
		#exit;
		return array('status' => 0, 'msg' => '图片过大，请上传2MB以内大小图片！');
	}

log_file('upload-api: src_img = ' . json_encode($src_img));
	switch($src_img['type'])
	{
		case 'image/gif':
			$imgExt = '.gif';
			break;
		case 'image/jpeg':
		case 'image/pjpeg'://IE
			$imgExt = '.jpg';
			break;
		case 'image/png':
		case 'image/x-png'://IE
			$imgExt = '.png';
			break;
		default:
			break;
	}
	if(!isset($imgExt))
	{
		$imgExt = strrchr($src_img['name'], '.'); //获取图片的扩展名
		$allowExts  = array('.jpg', '.gif', '.png', '.jpeg');// 设置附件上传类型
log_file('imgExt = ' . $imgExt);
		if (!in_array($imgExt, $allowExts))
		{
			return array('status' => 0, 'msg' => '暂不支持该图片格式！');
		}
		#echo json_encode(array('status' => 0, 'msg' => '暂不支持该图片格式！'));
		#exit;
	}

	$savePath = APP_PATH . 'Uploads/image' . $sub_path . '/' . date('Y-m');
	$saveFile = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . $imgExt;

	//确认保存路径，没有则创建
	if(!is_dir($savePath))
	{
		if(!@mkdir($savePath, 0700, true))
		{
			#echo json_encode(array('status' => 0, 'msg' => '上传目录创建失败！'));
			#exit;
			return array('status' => 0, 'msg' => '上传目录创建失败！');
		}
	}
	//移动文件
	if(move_uploaded_file($tmpFile, $saveFile) === false)
	{
		#echo json_encode(array('status' => 0, 'msg' => '图片上传失败！'));
		#exit;
		return array('status' => 0, 'msg' => '图片上传失败！');
	}
	if(!isset($img_domain))
	{
		$img_domain = C('IMG_DOMAIN');
	}
   // $imgUrl = str_replace(APP_PATH . 'Uploads', $img_domain . '/Uploads', $saveFile);
	$imgUrl = str_replace(APP_PATH . 'Uploads', '/Uploads', $saveFile);

	return $imgUrl;
}

/**
 * 上传文件
 *
 * @param array $src_img 上传的图片资源，为$_FILES['filename']格式，一般地filename为img标签的name属性值
 * @param string $sub_path 图片保存子路径，以'/'开头，但不以'/'结尾，如设置友情链接logo图片子路径为'/other/link'，根路径为/Uploads/image
 * @param string $img_domain 图片域名，结尾无'/'，未设置则取配置项中IMG_DOMAIN值
 * @param int $limit_size 图片尺寸限制，如未设置默认2MB
 * @author 姜伟
 * @return void
 * @todo 首先判断图片大小是否超出预设值，然后判断图片格式是否支持
 * @todo 创建相应路径并保存图片，转换图片路径为图片URL通过JSON输出到终端（一般为浏览器）
 *
 */
function uploadFile($src_img, $version, $img_domain = null)
{
	$tmpFile = $src_img['tmp_name'];
	$tmpFileSize = $src_img['size'];
	$maxSize = isset($limit_size) ? $limit_size : 30 * pow(1024, 2);
	if($tmpFileSize > $maxSize)
	{
		#echo json_encode(array('status' => 0, 'msg' => '图片过大，请上传2MB以内大小图片！'));
		#exit;
		return array('status' => 0, 'msg' => '文件过大，请上传30MB以内大小文件！');
	}

log_file('upload-api: src_img = ' . json_encode($src_img));
	switch($src_img['type'])
	{
		case 'doc':
			$imgExt = '.doc';
			break;
		case 'apk':
			$imgExt = '.apk';
			break;
		case 'ipa':
			$imgExt = '.ipa';
			break;
		default:
			break;
	}
	if(!isset($imgExt))
	{
		$imgExt = strrchr($src_img['name'], '.'); //获取图片的扩展名
		$allowExts  = array('.doc', '.apk', '.ipa');// 设置附件上传类型
log_file('imgExt = ' . $imgExt);
		if (!in_array($imgExt, $allowExts))
		{
			return array('status' => 0, 'msg' => '暂不支持该文件格式！');
		}
		#echo json_encode(array('status' => 0, 'msg' => '暂不支持该图片格式！'));
		#exit;
	}

	$savePath = APP_PATH . 'Uploads/';
	$saveFile = $savePath . 'msd_' . $version . $imgExt;

	//确认保存路径，没有则创建
	if(!is_dir($savePath))
	{
		if(!@mkdir($savePath, 0700, true))
		{
			#echo json_encode(array('status' => 0, 'msg' => '上传目录创建失败！'));
			#exit;
			return array('status' => 0, 'msg' => '上传目录创建失败！');
		}
	}
	//移动文件
	if(move_uploaded_file($tmpFile, $saveFile) === false)
	{
		#echo json_encode(array('status' => 0, 'msg' => '图片上传失败！'));
		#exit;
		return array('status' => 0, 'msg' => '图片上传失败！');
	}
	if(!isset($img_domain))
	{
		$img_domain = C('IMG_DOMAIN');
	}
   // $imgUrl = str_replace(APP_PATH . 'Uploads', $img_domain . '/Uploads', $saveFile);
	$imgUrl = str_replace(APP_PATH . 'Uploads', '/Uploads', $saveFile);

	return $imgUrl;
}


/****七牛图片上传 start*****/
    // 获取上传的token
    // @author wsq
    function get_qiniu_uploader_up_token($bucket='kkl')
    {
        Vendor('qiniu.rs');
        Vendor('qiniu.io');
        // 用于签名的公钥和私钥
        $secretKey = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_SECRET_KEY');
        $accessKey = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_ACCESS_KEY');

        Qiniu_SetKeys($accessKey, $secretKey);
        $putPolicy = new Qiniu_RS_PutPolicy($bucket);
        $upToken = $putPolicy->Token(null);

        return $upToken;
    }

    //改为图片大小
    //qiniu
    //param $image_domain 图片所在域名
    //param $key 上传图片后返回的key值
    //param $width 图片处理后的宽度
    //param $height 图片处理后的高度
    //@author wzg
    function resizePic($image_domain, $key, $width, $height)
    {
        $image_domain = $image_domain ? $image_domain : C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_IMAGES_DOMAIN');
        if (!$key) false;

        return $image_domain . $key . '?imageView2/1/w/' . $width .'/h/' . $height;
    }
/*******end*********/



/**
 * 地理围栏
 * @param  array  $polygon 组成围栏的点坐标  二维数组
 * @param  array  $lnglat  要判断的点
 * @return boolean          在围栏内返回true，围栏外返回false
 */
function isPointInPolygon($polygon,$lnglat){
        $count = count($polygon);
        $px = $lnglat['lng'];
        $py = $lnglat['lat'];
        $flag = FALSE;
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i, $i++) {
            $sx = $polygon[$i]['lng'];
            $sy = $polygon[$i]['lat'];
            $tx = $polygon[$j]['lng'];
            $ty = $polygon[$j]['lat'];
            if ($px == $sx && $py == $sy || $px == $tx && $py == $ty)
                return TRUE;
            if ($sy < $py && $ty >= $py || $sy >= $py && $ty < $py) {
                $x = $sx + ($py - $sy) * ($tx - $sx) / ($ty - $sy);
                if ($x == $px)
                    return TRUE;
                if ($x > $px)
                    $flag = !$flag;
            }
        }
        return $flag;
}

/**
 * 渲染输出 UploadImageWidget (图片上传控件)
 * @param array $data 传入的模版参数
 * (具体参数请参考：Lib/Widget/UploadImageWidget.class.php)
 * @param boolean $return 是否返回内容
 * @author tale
 * @return mixed
 */
function upload_image_widget($data, $return = false)
{
    return W('UploadImage', $data, $return);
}

function auto_upload_handler($src_img, $sub_path, $limit_size = null)
{
    if (!$src_img) {
        return array('status' => 0, 'msg' => '上传时出错！');
    }
    $tmpFile     = $src_img['tmp_name'];
    $tmpFileSize = $src_img['size'];
    $maxSize     = isset($limit_size) ? $limit_size : 2 * pow(1024, 2);
    if ($tmpFileSize > $maxSize) {
        return array('status' => 0, 'msg' => '图片过大，请上传2MB以内大小图片！');
    }

    switch ($src_img['type']) {
        case 'image/gif':
            $imgExt = '.gif';
            break;
        case 'image/jpeg':
        case 'image/pjpeg': //IE
            $imgExt = '.jpg';
            break;
        case 'image/png':
        case 'image/x-png': //IE
            $imgExt = '.png';
            break;
        default:
            break;
    }
    if (!isset($imgExt)) {
        $imgExt    = strrchr($src_img['name'], '.'); //获取图片的扩展名
        $allowExts = array('.jpg', '.gif', '.png', '.jpeg'); // 设置附件上传类型
        if (!in_array($imgExt, $allowExts)) {
            return array('status' => 0, 'msg' => '暂不支持该图片格式！');
        }
    }

    $sub_path = $sub_path ? $sub_path : C('DEFAULT_IMAGE_UPLOAD_DIR');
    $savePath = 'Uploads/image/' . $sub_path . '/' . date('Y-m');
    $fullName = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . $imgExt;

    // 判断驱动类型
    $driver = C('DEFAULT_IMAGE_UPLOAD_DRIVER');
    switch ($driver) {
        case 'local':
            return local_handler($fullName, $tmpFile, $savePath);
        case 'qiniu':
            return qiniu_handler($fullName, $tmpFile);
        default:
            return array('status' => 0, 'msg' => '未知上传驱动！');
    }
}

function local_handler($fullName, $tmpFile, $savePath)
{
    //确认保存路径，没有则创建
    if (!is_dir($savePath)) {
        if (!@mkdir($savePath, 0777, true)) {
            return array('status' => 0, 'msg' => '上传目录创建失败！');
        }
    }

    //移动文件
    if (move_uploaded_file($tmpFile, $fullName) === false) {
        return array('status' => 0, 'msg' => '图片上传失败！');
    }

    #$imgUrl = C('LOCAL_UPLOAD_DRIVER_CONFIG.IMAGE_DOMAIN') . ltrim($fullName, '/');
	//姜伟修改：本地图片去除域名
    $imgUrl = '/'.ltrim($fullName, '/');
    return array('status' => 1, 'img_url' => $imgUrl);
}

function qiniu_handler($key, $tmpFile)
{

    vendor('qiniu7.autoload');

    // 需要填写你的 Access Key 和 Secret Key
    $accessKey = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_ACCESS_KEY');
    $secretKey = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_SECRET_KEY');

    // 构建鉴权对象
    $auth = new \Qiniu\Auth($accessKey, $secretKey);

    // 要上传的空间
    $bucket = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_IMAGES_BUCKET');

    // 生成上传 Token
    $token = $auth->uploadToken($bucket);

    // 初始化 UploadManager 对象并进行文件的上传。
    $uploadMgr = new \Qiniu\Storage\UploadManager();

    // 调用 UploadManager 的 putFile 方法进行文件的上传。
    list($ret, $err) = $uploadMgr->putFile($token, $key, $tmpFile);

    if ($err !== null) {
        return array('status' => 0, 'msg' => '图片上传失败！');
    }

    $img_url = C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_IMAGES_DOMAIN') . $ret['key'];
    
    return array('status' => 1, 'img_url' => $img_url);
}



function check_table_exist($table_name, $sql){

    $rs = M()->query("SHOW TABLES LIKE '".$table_name."'");
    if(!$rs){
        M()->execute($sql);
    }
}

function check_field_exist($fields, $check_fields){
    // dump($fields);dump($check_fields);die;
    $r = false;
    foreach ($check_fields as $k => $v) {
        if(!in_array($k, $fields)){
            M()->execute($v);
        }
    }
}

//注册限制
function check_reg_limit(){
    $limit_reg_open = $GLOBALS['config_info']['LIMIT_REG_OPEN'];
    if(!$limit_reg_open) return array('code'=>0);
    //tp_users的用户数
    $user_num = D('User')->getUserNum();
    if($user_num >= $GLOBALS['config_info']['LIMIT_REG_NUM']){
        return array(
            'code' => 1,
            'limit_reg_desc' => $GLOBALS['config_info']['LIMIT_REG_DESC']
            );
    }else{
        return array('code'=>0);
    }
}

function http_curl_post($url, $params){
    $ch = curl_init();
    $params = json_encode($params);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
    curl_setopt($ch,CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json; charset=utf-8','Content-Length:' . strlen($params) ));
    curl_setopt($ch,CURLOPT_TIMEOUT, 30);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, 0);
    $res = curl_exec($ch);

    curl_close ($ch);
    return $res;
}

function randomFloat($min = 0, $max = 100) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);





}
