<?php
/**
 * 即时通讯
 * Created by PhpStorm.
 * User: lkp
 * Date: 2017/8/25
 * Time: 13:18
 */
class AcpZhishidianAction extends AcpAction{


    public function __construct()
    {
        parent::_initialize();

    }

    public function add_dynamic()
    {
        $config_obj = new ConfigBaseModel();
        $config_watermark = $config_obj->getConfig('config_watermark');
        $this->assign('config_watermark',$config_watermark);
//        $industry_id = $user_info['industry'];
        if (IS_POST)
        {
            $img_path = $this->_post('pic_arr');
            //七牛添加水印就是在utl后面添加参数就行了，水印的图片地址需要经过base64编码
            $shuiyin = 'http://'.$_SERVER['HTTP_HOST'].'/Public/Images/front/watermark/shuiyin@3x.png';
            $shuiyin = base64_encode($shuiyin);
            if (!strpos($img_path,'?watermark/1/image/')){
                $img_path = $img_path.'?watermark/1/image/'.$shuiyin;
            }
            $res = $config_obj->setConfig('config_watermark',$img_path);
            if ($res!==FALSE){
                $this->success('操作成功');
            }
            else
            {
                $this->error('操作失败');
            }
        }
//        var_dump(array_values($result[0]));die;
        $this->assign('qr_code_data', array(
            'name'  => 'pic_arr',
            'url'   => $config_watermark,
//            'pic_arr'=>array_column($result,'path_img'),
            'title' => '水印添加',
            'batch' =>  false
//            'help'  => '宽高600*375最佳',
        ));
        $this->display();
    }

    //高并发处理同一个用户的账户时建议使用
    public function redis_use()
    {
        echo '举例：如果说碰到高并发的，先将数据处理的订单id放入队列，然后写一个定时脚本，每秒去跑一次，获取队列中的订单id，然后进行处理就行了，'.'</br>';
        echo '<span style="color: red">注意，队列不同项目的队列名字不能重复，取队列名字的时候带上项目名字以及队列用户比如：mpwh_order_list(名品尾货的订单队列)</span>'.'</br>';
        //i可用订单id
        $i='250';
        echo '推入队列内容：'.$i.'</br>';
        $redis = new Redis();
        $redis->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        //将数据推入redis队列
        $redis->rPush('test_list',$i);

        //获取该队列长度,秒杀等进来可判断队列长度，
        $test_list_length = $redis->lSize('test_list');
        echo '队列长度：'.$test_list_length.'</br>';

        //将数据从队列中取出，右进左出
        //获取到id之后就可以对id进行操作，这样来保证一个数据的正确性，秒杀一样可以用，奖所有用户的点击都放入redis，只获取前多少，
        echo '获取队列内容：'.$redis->lpop('test_list').'</br>';
        //再次获取该队列长度，
        $test_list_length = $redis->lSize('test_list');
        echo '队列长度：'.$test_list_length.'</br>';

        //举例：如果说碰到高并发的，先将数据处理的订单id放入队列，然后写一个定时脚本，每秒去跑一次，获取队列中的订单id，
        //然后进行处理就行了，
        //注意，队列不同项目的队列名字不能重复，取队列名字的时候带上项目名字以及队列用户比如：mpwh_order_list(名品尾货的订单队列)
    }

    /**
     * @todo ajax 长轮循测试
     * @author lye
     */
    public function ajax_round_robin()
    {
        $this->display();
    }

    /**
     * @todo 发送邮件
     * @author lye
     */
    public function send_email()
    {
        // 引入邮件
        // 例子 163邮箱发送
        import('ORG.Util.Email');
        $mail = new MySendMail();
        /**
         * 参数1: 163 SMTP
         * 参数2: 邮箱账号
         * 参数3: 163客户端授权密码
         */
        $mail->setServer("smtp.163.com", "m15057369158", "yuanen123", 465, true); //设置smtp服务器，到服务器的SSL连接
        $mail->setFrom("15057369158@163.com"); //设置发件人
        $mail->setReceiver("775344992@qq.com"); //设置收件人，多个收件人，调用多次
        $mail->setMail('金龙28', '哈哈哈哈哈哈哈'); //设置邮件主题、内容
        $mail->sendMail(); //发送
    }

    /**
     * @todo redis 队列红包
     * @author lye
     */
    public function redis_red_pack()
    {
        $redis = new Redis();
        $redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
        $res = $this->random_points(100, 10, 0.01); // 1金额, 2几份, 最少得到多少
        foreach($res AS $v)
        {
            $redis->lpush('shejiao_red_pack_list_1', $v); // 在名称为key的list头添加一个值为value的 元素
        }
    }

    /**
     * @todo 把X数分成N个随机数 总和=X
     * @author lye
     * @param $total int 总数
     * @param $num int 分成几份
     * @param $min int 虽少能分到多少
     * @return array
     */
    function random_points($total ,$num , $min){
        $arr = [];
        for ($i=1;$i<$num;$i++)
        {
            $safe_total=($total-($num-$i)*$min)/($num-$i);//随机安全上限
            $money=mt_rand($min*100,$safe_total*100)/100;
            $total=$total-$money;
            array_push($arr, $money);
        }
        array_push($arr, $total);
        return $arr;
    }

    /**
     * @todo 拆红包
     * @author lye
     */
    public function split_red_pack()
    {
        $redis = new Redis();
        $redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
        // 返回名称为key的list中start至end之间的元素
        $list = $redis->lrange('shejiao_red_pack_list_1', 0, -1);
        if($list)
        {
            // 取出金额 $num就是拆开的数量
            $num = $redis->lpop('shejiao_red_pack_list_1');
            // 项目逻辑逻辑..........
        }
        else
        {

        }
    }

    /**
     * @todo 退回红包
     * @author lye
     */
    public function refund_red_pack()
    {
        $redis = new Redis();
        $redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
        // 返回名称为key的list中start至end之间的元素
        $list = $redis->lrange('shejiao_red_pack_list_1', 0, -1);
        if($list)
        {
            // 计算剩余所有余额
            $num = $this->getRemainingRedPack($list);
            // 返回.........

            // 删除队列
            $redis->del('shejiao_red_pack_list_1'); //销毁红包记录
        }
        else
        {

        }
    }

    /**
     * @todo 剩余群红包余额
     * @author lye
     * @param $list array 剩余群红包余额数组
     * @return v
     */
    public function getRemainingRedPack($list)
    {
        if(!$list || !is_array($list)) return 0;
        $num = '';
        foreach($list AS $v)
        {
            $num += $v;
        }
        return $num;
    }

    /**
     * @todo 根据车票图片 获取车票信息
     * @author lye
     * @param string $file 文件路径
     * @return array
     */
    function aliTicketIdentify($file = '')
    {
        $url = "https://ocrhcp.market.alicloudapi.com/api/predict/ocr_train_ticket";

        $config_info = $GLOBALS['config_info'];
        $appcode = $config_info['ALI_APPCODE']; // 阿里云appCode

        //如果输入带有inputs, 设置为True，否则设为False
        $is_old_format = false;
        //如果没有configure字段，config设为空
        //$config = array(
        //    "side" => "face"
        //);
        $config = array();

        // base64编码
        $base64 = base64_encode(file_get_contents($file));


        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        if($is_old_format == TRUE){
            $request = array();
            $request["image"] = array(
                "dataType" => 50,
                "dataValue" => "$base64"
            );

            if(count($config) > 0){
                $request["configure"] = array(
                    "dataType" => 50,
                    "dataValue" => json_encode($config)
                );
            }
            $body = json_encode(array("inputs" => array($request)));
        }else{
            $request = array(
                "image" => "$base64"
            );
            if(count($config) > 0){
                $request["configure"] = json_encode($config);
            }
            $body = json_encode($request);
        }
        $method = "POST";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$url, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader = substr($result, 0, $header_size);
        $rbody = substr($result, $header_size);

        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        if($httpCode == 200){
            if($is_old_format){
                $output = json_decode($rbody, true);
                $result_str = $output["outputs"][0]["outputValue"]["dataValue"];
            }else{
                $result_str = $rbody;
            }
            log_file('ali_ticket='.json_encode($result_str), 'chepiao');
            return json_decode($result_str, true);
        }else{
            printf("Http error code: %d\n", $httpCode);
            printf("Error msg in body: %s\n", $rbody);
            printf("header: %s\n", $rheader);
            log_file('ali_Http_error_code='.json_encode($httpCode), 'chepiao');
            log_file('ali_Error_msg_in_body='.json_encode($rbody), 'chepiao');
            log_file('ali_header='.json_encode($rheader), 'chepiao');

            return false;
        }

        #返回下图效果
//  'date' => string '2014年08月19日17:00' (length=22)
//  'destination' => string '桂林' (length=6)
//  'level' => string '.等座' (length=7)
//  'number' => string 'D8260' (length=5)
//  'origin' => string '北海' (length=6)
//  'place' => string '03车118号' (length=11)
//  'price' => int 166
//  'request_id' => string '20180504131453_28c7a5901d7fb44860f23f8265fe9ece' (length=47)
//  'success' => boolean true
    }

    /**
     * @todo 查看车次
     * @author Time
     * @param string $trains 车次名称
     * @return array
     */
    function getTrains($trains)
    {
        $config_info = $GLOBALS['config_info'];
        $juhe_key = $config_info['JUHE_KEY']; // 聚合数据的key

        $url = 'http://apis.juhe.cn/train/s?name='.$trains.'&key='.$juhe_key;
        $result = json_decode(file_get_contents($url), true);
        log_file('ali_header='.json_encode($result), 'chepiao');
        return $result;
    }

}
