<?php

// 用于需要用户验证的全局类
class GlobalAction extends Action
{
    private $key = '@J4*A9N7&B^A9Y7j6sWv8m5%q_p+z-h=';

    function _initialize()
    {
        header("Content-Type:text/html; charset=utf-8");

        //图片上传路径
        define('DS', DIRECTORY_SEPARATOR);
        define('UPLOAD_IMAGE_PATH', './Uploads/image/');

        //清除缓存 (开发模式下不需要缓存)
        if (PHP_OS != "LINUX") {
            $cache = Cache::getInstance();
            $cache->clear();
        }

        //取得系统配置
        $system_config = array();
        $Mconfig = M("Config");
        $config = $Mconfig->cache(true, 86400)->select();
        if (is_array($config)) {
            foreach ($config as $k => $v) {
                $system_config[strtoupper($v['config_name'])] = html_entity_decode($v['config_value']);
            }
        }

        $system_config['CUR_TIME'] = time();
        $system_config['WEBSITE_DOMAIN'] = $this->_server('HTTP_HOST');
        $this->assign("system_config", $system_config);
        $this->system_config = $system_config;

        $GLOBALS['config_info'] = $system_config;

        //用户IP
        $this->user_ip = get_client_ip();
        $this->assign("user_ip", $this->user_ip);

        //加密当前网址
        if ($this->parameter && is_string($this->parameter)) {
            parse_str($this->parameter, $parameter);
        } elseif (is_array($this->parameter)) {
            $parameter = $this->parameter;
        } elseif (empty($this->parameter)) {
            unset($_GET[C('VAR_URL_PARAMS')]);
            $var = $_GET;
            if (empty($var)) {
                $parameter = array();
            } else {
                $parameter = $var;
            }
            if ($_POST) {
                foreach ($_POST as $k => $v) {
                    $parameter[$k] = $v;
                }
            }
        }

        $this->cur_url = url_jiami(U('', $parameter));
        $this->assign("cur_url", $this->cur_url);

        $this->mod = MODULE_NAME;
        $this->do = ACTION_NAME;
        $this->assign("mod", MODULE_NAME);
        $this->assign("do", ACTION_NAME);
        $this->assign("SYSTEM_MONEY_NAME", $system_config['SYSTEM_MONEY_NAME']);

        //加载公用常量
        include_once(CONF_PATH . 'constants.php');

        //qianniu
        $this->assign('up_token', get_qiniu_uploader_up_token());
        $this->assign('image_domain', C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_IMAGES_DOMAIN'));

        //种COOKIE
        if (isset($_COOKIE['user_cookie'])) {
            $GLOBALS['user_cookie'] = $_COOKIE['user_cookie'];

        } else {
            $cookie_value = md5(get_client_ip() . time());
            cookie('user_cookie', null);    //一定要先清空
            cookie('user_cookie', $cookie_value, time() + 3600 * 24 * 3650);    //默认30天有效期
            $GLOBALS['user_cookie'] = $cookie_value;
        }
        $version = C("VERSION");
        $version = $version ? $version : 201612201545;
        $this->assign('version', $version);
//        date_default_timezone_set('Asia/Shanghai');
        $is_web_enable = C("WEB_ENABLE");
//        PC&微信&APP区
        $USER_AGENT = $_REQUEST['user_agent'];
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $request_token_str = $_REQUEST['u_token']?'?u_token='.$_REQUEST['u_token']:'';

        if($USER_AGENT != 'jlapp'){
            $this->assign('JL_APP', 0);
            if( strtolower(MODULE_NAME) != 'api'){

                $redis_obj = new Redis();
                $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
                $is_mobile_wap = $redis_obj->get('is_mobile_wap'.session('user_id'));//1代表手机看也是pc显示

                if (($is_web_enable && !strpos($agent, 'micromessenger') && !strpos($agent, 'iphone') && !strpos($agent, 'ipad') && !strpos($agent, 'android')) || $is_mobile_wap == 1) {
                    if (strpos(strtolower(MODULE_NAME), 'global') !== false) return;
                    if (strpos(strtolower(MODULE_NAME), 'acp') !== false) return;
                    if (strpos(strtolower(MODULE_NAME), 'mcp') !== false) return;
                    if (strpos(strtolower(MODULE_NAME), 'logout') !== false) return;
                    if (strpos(strtolower(MODULE_NAME), 'login') !== false) return;

                    if (!strpos(strtolower(MODULE_NAME), 'pc')) {
                        $module_name = str_replace('Front', 'FrontPc', MODULE_NAME);
                        $module_name = str_replace('Index', 'IndexPc', $module_name);
                        $module_name = str_replace('index', 'indexPc', $module_name);
                        $module_name = str_replace('Ucp', 'UcpPc', $module_name);
                        $module_name = str_replace('ucp', 'ucpPc', $module_name);

                        $request_uri = $_SERVER['REQUEST_URI'];
                        $request_uri = str_replace('/' . MODULE_NAME . '/' . ACTION_NAME, '', $request_uri);
                        #die($request_uri);

                        $url = '/' . $module_name . '/' . ACTION_NAME . $request_uri;
                        trace($url, '-------- url -------');
                        #die($url);
                        redirect($url);
                    }
                } else {
                    #dump(strpos(strtolower(MODULE_NAME)));
                    #dump(strpos(strtolower(MODULE_NAME), 'IndexApp'));die;

                    if (strpos(strtolower(MODULE_NAME), 'indexapp') === false) redirect('/IndexApp/index'.$request_token_str);

                }
        }

        }else{
            if (strpos(strtolower(MODULE_NAME), 'indexapp') === false) redirect('/IndexApp/index'.$request_token_str);
            $this->assign('JL_APP', 1);

        }
    }

    public function _empty()
    {
    }

    // 图片上传
    public function upload_image()
    {
        $dir = I('get.dir', C('DEFAULT_IMAGE_UPLOAD_DIR'));
        $result = auto_upload_handler($_FILES['imgFile'], $dir);
        echo json_encode($result);
        exit;
    }

    protected function _ajaxFeedback($status, $data = null, $msg = '')
    {
        $data['status'] = $status;
        $data['msg'] = $msg;
        echo json_encode($data);
        exit;
    }

    /**
     * 记录日志
     *
     * op_type            操作方式，1增加、2修改、3删除、4访问
     * mark                日志说明
     * tb_name            操作表名
     * tb_id            操作表中的ID号
     * op_user_id        操作者用户ＩＤ
     * change_user_id    被修改的用户ＩＤ
     */
    protected function save_logs($op_type, $mark, $tb_name = '', $tb_id = 0, $op_user_id = 0, $change_user_id = 0)
    {
        if (!(isset($op_user_id) && $op_user_id > 0)) $op_user_id = $this->login_user['user_id'];

        if (!in_array($op_type, array(1, 2, 3, 4))) return '';

        if (!$mark) return '';

        $s_arr = array(
            'op_user_id' => $op_user_id,
            'change_user_id' => $change_user_id,
            'ip' => $this->user_ip,
            'op_date_time' => date('Y-m-d H:i:s'),
            'op_time' => time(),
            'op_type' => $op_type,
            'tb_name' => $tb_name,
            'tb_id' => $tb_id,
            'linkman' => (isset($this->login_user['linkman'])) ? $this->login_user['linkman'] : '',
            'mark' => $mark
        );

        $Log = M('Logs');
        $Log->add($s_arr);
    }

    public function test_memcache()
    {
        S(MD5('a'), '1111111111111111');
        echo 'a = ' . S(MD5('a')) . "<br>";
        S(MD5('b'), '222222');
        $cache = Cache::getInstance();
        echo 'b = ' . S(MD5('b')) . "<br>";
        $cache->clear();
    }

    public function test_redis()
    {
        error_reporting(E_ALL);
        $obj = new Redis();
        $obj->connect('localhost', 6379);
        $planter_id = session('planter_id');
        $planter_id = 2008;
        $command_info = $obj->get('command_' . $planter_id);
        if (!$command_info) {
            die('对不起，当前未登录或未绑定种植机');
        } else {
            echo "<pre>";
            print_r(PlanterModel::parseCommand($command_info));
        }
    }

    /**
     * 获取sign
     * @author 姜伟
     * @param array $params 请求参数列表
     * @return string sign
     * @todo 排序，组成请求字符串后，连接key进行MD5加密，返回
     */
    function generateSign($data)
    {
        ksort($data);

        $sign = '';
        foreach ($data as $key => $val) {
            if ($key != 'token') {
                $sign .= "&$key=$val";
            }
        }

        $sign = md5(substr($sign, 1) . $this->key);
        return $sign;
    }

    function test_curl()
    {
        $url = 'http://api.pandorabox.mobi/?m=api&a=api';
        $vars = array(
            'appid' => '11',
            'api_name' => 'plant.user.register',
            'mobile' => '15158131315',
            'nickname' => '诸葛青云',
            'password' => '698d51a19d8a121ce581499d7b701668',
            'PHPSESSID' => '',
            'username' => 'jiangwei',
        );
        $token = $this->generateSign($vars);
        $vars['token'] = $token;

        echo "<pre>";
        $result = $this->getData($url, $vars);
        print_r($result);
        var_dump(json_decode($result));
    }

    //获取https的get请求结果
    function getData($c_url, $vars = '')
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_REFERER, 'https://mp.weixin.qq.com/cgi-bin/singlemsgpage?t=wxm-singlechat&lang=zh_CN');
        curl_setopt($curl, CURLOPT_URL, $c_url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer

        #curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转

        $postfields = '';
        foreach ($vars as $key => $value) {
            $postfields .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, substr($postfields, 1)); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

    public function test_db_cache()
    {
        require_once('Lib/Model/AzTemplateModel.class.php');
        $aztemplate_obj = new AzTemplateModel();
        $aztemplate_obj->get_module_list();
        echo "<br>db cache: ";
        var_dump(S(MD5('SELECT * FROM `tp_template_model` WHERE ( isuse = 1 ) ORDER BY serial ')));

        //清除缓存
        $cache = Cache::getInstance();
        $cache->clear();
        echo "<br>after flushing cache: ";
        var_dump(S(MD5('SELECT * FROM `tp_template_model` WHERE ( isuse = 1 ) ORDER BY serial ')));

        //测试对空数组执行empty函数的返回值
        $a = array();
        var_dump(empty($a));

        echo "<hr>";
        $user = M('users');
        var_dump($user);
    }

    /**
     * 根据条件把文章表
     * @param string
     * @return void
     * @author <zgq@360shop.cc>
     * @todo 根据条件把文章表tp_article数据取出来
     */
    function get_header_title($str)
    {
        return $str . '-' . $this->system_config['SHOP_TITLE'];
    }

    function test_sms()
    {
        $UserModel = new UserModel();
        $mobile = '15158131315';
        $time = time();
        if ($time - session('code_time_mark') < 60)    //60s内不重新发送
        {
            exit;
        }
        session('verify_phone', null);
        $username = $this->_post('user');    //加密后的用户名
        $username = $username ? url_jiemi($username) : '';
        $username = 'aaa';

        if (!$username) {
            exit(json_encode(array('type' => -3, 'message' => '请求出错')));
        }
        if (!check_mobile($mobile)) {
            exit(json_encode(array('type' => -2, 'message' => '请输入正确的手机号')));
        }
        //检查该手机号是否已经绑定过账号
        $check = $UserModel->getUserList('mobile = ' . $mobile);    //一个手机号只能绑定一个账号
        if ($check)    //如果该手机号已经有账号绑定过了，提示不能绑定
        {
            exit(json_encode(array('type' => -2, 'message' => '对不起，该手机号已经被绑定了,请更换一个手机号！')));
        }
        session('phone', $mobile);
        session('code_time_mark', time());    //标记发送时间

        //生成验证码数字
        $codeSet = '0123456789';
        for ($i = 0; $i < 6; $i++) {    //6位数字
            $code[$i] = $codeSet[mt_rand(0, 9)];
        }
        // 保存验证码
        $str = join('', $code);
        $str = strtoupper($str);    //生成的验证码
        require_once('Lib/Model/SMSModel.class.php');
        $SMSModel = new SMSModel();
        $result = $SMSModel->sendCode($mobile, $str);        //发送短信
        $_SESSION['verify_phone'] = md5($str);            //session存储验证码
        if ($result['status']) {
            exit(json_encode(array('type' => 1)));        //验证码发送成功
        } else if ($result['wrong']) {
            exit(json_encode(array('type' => -1, 'message' => $result['message'])));
        } else {
            exit(json_encode(array('type' => -1, 'message' => '发送失败')));
        }
    }

    function test_csv()
    {
        //初始化预约对象
        $agent_appoint_obj = new AppointModel();
        $agent_appoint_obj->setStart(0);
        //一次性最多可导出1000条数据
        $agent_appoint_obj->setLimit(1000);

        $fields = '';
        $arr = $agent_appoint_obj->getAppointListByQueryString($fields, $where);

        //引入PHPExcel类库
        vendor('Excel.PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle("预约报表")
            ->setSubject("预约报表")
            ->setDescription("预约报表")
            ->setKeywords("预约,报表")
            ->setCategory("预约报表");

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet(0)->setTitle('预约报表');          //标题
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);      //单元格宽度
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小
        $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '预约号');
        $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '手机号');
        $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '预约状态');
        $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '预约时间');

        $i = 2;
        //每行数据的内容
        foreach ($arr as $value) {
            for ($j = 0; $j < 10; $j++) {
                //预约号
                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, " " . $value['appoint_number']);

                //商品总金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['mobile']);

                //商品总成本价
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, AppointModel::convertAppointStatus($value['appoint_status']));

                //商品总折扣
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, date('Y-m-d H:i:s', $value['appoint_time']));
                $i++;
            }
        }

        //直接输出文件到浏览器下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="预约报表_' . date("YmdHis") . '.xls"');
        header('Cache-Control: max-age=0');
        ob_clean(); //关键
        flush(); //关键
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    //测试发送验证码
    function test_send_vc()
    {
        //获取验证码
        $verify_code_obj = new VerifyCodeModel();
//短信数量
        $sms_obj = new SMSModel();
        $sms_num = $sms_obj->getSMSLeftNumber();
        var_dump($sms_num);
        $mobile = $this->_request('mobile');
        $verify_code = $verify_code_obj->generateVerifyCode($mobile);
        var_dump($verify_code);
        die;
        if ($verify_code) {
            $sms_obj = new SMSModel();
            $send_state = $sms_obj->sendVerifyCode('15158131315', $verify_code);
            var_dump($send_state);
        }
    }

    function mp_event()
    {
        if (isset($_GET['signature'])) {
            $postStr = file_get_contents("php://input");
            if (empty($postStr) && isset($_GET['signature']) && $_GET['signature']) {
                ob_clean();
                echo $_GET['echostr'];

                exit;
            } else {
                require('frame/Extend/Vendor/Wxin/WeiXinPush.php');
                $wechatObj = new wechatCallbackapiTest();
                $wechatObj->weixin_run(); //执行接收器方法
            }
        }
    }

    function generate_planter()
    {
        die;
        $planter_obj = new PlanterModel();
        $num = 0;
        for ($i = 0; $i < 1000; $i++) {
            $arr = array(
                'planter_code' => sprintf("%06d", $i),
                'serial_num' => sprintf("%06d", $i),
                'addtime' => time(),
                'last_visit_time' => time(),
            );
            $success = $planter_obj->add($arr);
            echo $planter_obj->getLastSql() . "<br>";
            $num += $success ? 1 : 0;
        }
        echo $num;
    }

    //接收推送消息，返回命令
    function receive_push()
    {
        if (isset($_REQUEST['IO'])) {
            $planter_id = intval($_REQUEST['planter_id']);

            $planter_obj = new PlanterModel($planter_id);
            if (!$planter_obj->checkVisitValid()) {
                exit('invalid visit');
            }
            //保存温度、湿度、光照度
            $arr = array();
            $str = '';
            if (isset($_REQUEST['ADC1'])) {
                $arr['temperature'] = $_REQUEST['ADC1'];
            }
            if (isset($_REQUEST['ADC2'])) {
                $arr['outside_temperature'] = $_REQUEST['ADC2'];
            }
            if (isset($_REQUEST['ADC3'])) {
                $arr['humidity'] = $_REQUEST['ADC3'];
            }
            if (isset($_REQUEST['ADC4'])) {
                $arr['illuminance'] = $_REQUEST['ADC4'];
            }
            if (isset($_REQUEST['ADC5'])) {
                $arr['alarm'] = $_REQUEST['ADC5'];
            }
            if (!empty($arr)) {
                $planter_obj->editPlanter($arr);
            }

            $planter_obj = new PlanterModel($planter_id);
            $planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'ton, toff, box_state, temperature, humidity, illuminance, outside_temperature, alarm');
            $command = $planter_info['box_state'];

            $str .= '&ADC1=' . $planter_info['temperature'];
            $str .= '&ADC2=' . $planter_info['outside_temperature'];
            $str .= '&ADC3=' . $planter_info['humidity'];
            $str .= '&ADC4=' . $planter_info['illuminance'];
            $str .= '&ADC5=' . $planter_info['alarm'];
            $ton = sprintf('%04d', $planter_info['ton']);
            $toff = sprintf('%04d', $planter_info['toff']);
            $pre_str = 'IO=' . $_REQUEST['IO'] . "\n";
            $str = 'GPIO=' . $command . '&T1=' . $ton . '&T2=' . $toff . $str . 'END';
            #log_file($pre_str . $str . "\nIP:" . get_client_ip());
            echo $str;

            if ($command[4] == 1) {
                $success = $planter_obj->sendCommand(5, 0, $planter_id);
            }
        }

        if (!empty($_FILES)) {
            echo "<pre>";
            print_r($_FILES);
            echo "</pre>";
        }
    }

    //接收推送消息，返回命令
    function receive_img()
    {
        #log_file("receive_img\n" . $_SERVER['QUERY_STRING'] . "\nIP:" . get_client_ip());
        $en = explode('image/', $_SERVER['QUERY_STRING']);
        log_file("en\n" . $en[1] . "\nIP:" . get_client_ip());
        $en = str_replace('_', '+', $en[1]);
        $en = str_replace('%2b', '+', $en);
        $en = str_replace('#a', '//', $en);
        $en = base64_decode('/' . $en);
        file_put_contents('Uploads/img.jpg', $en);

        /*foreach ($_REQUEST AS $k => $v)
        {
            $en = str_replace('_', '+', $k);
            $en = base64_decode($en);
            file_put_contents('Uploads/img.jpg', $en);
            break;
        }*/
        #$en = str_replace('_', '+', $_REQUEST['data']);
        #echo "11111<br>";
        #var_dump($en);
        #$en = base64_decode($en);
        #echo "22222<br>";
        #var_dump($en);
        #file_put_contents('Uploads/img.jpg', $en);
        echo "<pre>";
        print_r($_REQUEST);
        print_r($_FILES);
        echo "</pre>";
    }

    function take_photo()
    {
        $user_id = intval($_REQUEST['user_id']);
        if (!$user_id) {
            exit;
        }

        $user_obj = new UserModel($user_id);
        $success = $user_obj->sendCommand(5, 1, $user_id);
        echo $success ? 'success' : 'failure';
    }

    function show_img()
    {
        echo "原图<br><img src='/Uploads/img.jpg'>";
        echo "<br>参照图<br><img src='/Uploads/tomato1.jpg'>";
    }

    function cal_express_fee()
    {
        //获取当前IP所在省份城市
        $area_info = getIPSource(get_client_ip());

        //调用物流模型ShippingCompanyModel的calculateShippingFee
        $default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
        $shipping_company_obj = new ShippingCompanyModel();
        var_dump($shipping_company_obj->calculateShippingFee($default_express_company, $area_info['province_id'], 3000, 30));
    }

    function import_area()
    {
        set_time_limit(0);
        $province_obj = M('address_province');
        $city_obj = M('address_city');
        $area_obj = M('address_area');

        $file = fopen('doc/area.txt', 'r');
        $line = 0;
        $province_id = 0;
        $city_id = 0;
        $is_zhixia = 0;
        while (!feof($file)) {
            $line++;
            $str = fgets($file, 1024);
            $code = substr($str, 0, 6);
            $last4code = substr($code, 2, 4);
            $last2code = substr($code, 4, 2);
            echo "$last4code, $last2code<br>";
            if ($last4code == '0000') {
                //省份
                $province_name = substr($str, 10, -1);
                echo "省份：$province_name<br>";
                $arr = array(
                    'province_id' => $code,
                    'province_name' => $province_name,
                );
                $province_id = $province_obj->add($arr);
                if (!$province_id) {
                    echo $province_obj->getLastSql() . "<br>";
                    echo "省份$province_name插入失败<br>";
                }
            } elseif ($last2code == '00') {
                //城市
                $city_name = substr($str, 12, -1);
                $is_zhixia = $city_name == '市辖区' || $city_name == '县' ? 1 : 0;
                echo "城市：$city_name<br>";
                if (!$is_zhixia) {
                    $arr = array(
                        'city_id' => $code,
                        'province_id' => $province_id,
                        'city_name' => $city_name,
                    );
                    $city_id = $city_obj->add($arr);
                    if (!$city_id) {
                        echo $city_obj->getLastSql() . "<br>";
                        echo "城市$city_name插入失败<br>";
                    }
                }
            } elseif ($is_zhixia) {
                //城市
                $city_name = substr($str, 14, -1);
                echo "城市：$city_name<br>";
                $arr = array(
                    'city_id' => $code,
                    'province_id' => $province_id,
                    'city_name' => $city_name,
                );
                $city_id = $city_obj->add($arr);
                if (!$city_id) {
                    echo $city_obj->getLastSql() . "<br>";
                    echo "城市$city_name插入失败<br>";
                }
            } else {
                //地区
                $area_name = substr($str, 14, -1);
                if ($code == 0) {
                    var_dump($code = substr($str, 14, -1));
                    echo "code invalid.<br>";
                }
                echo "地区：$area_name<br>";
                $arr = array(
                    'area_id' => $code,
                    'city_id' => $city_id,
                    'area_name' => $area_name,
                );
                $area_id = $area_obj->add($arr);
                if (!$area_id) {
                    echo $area_obj->getLastSql() . "<br>";
                    echo "地区$area_name插入失败<br>";
                }
            }
        }
    }

    function test_get_item()
    {
        $item_id = 502;
        $item_obj = new ItemModel($item_id);
        $item_info = $item_obj->getItemInfo('item_id = ' . $item_id, 'item_name, t_desc, h_desc, i_desc, item_sn, has_sku, base_pic, mall_price, market_price, stock, weight, sales_num, class_id');
        if (!$item_info) {
            ApiModel::returnResult(42025, null, '商品不存在');
        }

        //是否种子
        $class_obj = new ClassModel();
        $class_info = $class_obj->getClassInfo('class_id = ' . $item_info['class_id'], 'class_tag');
        $is_seed = 0;
        if ($class_info && $class_info['class_tag'] == 'seed') {
            $is_seed = 1;
        }
        $item_info['is_seed'] = $is_seed;

        echo "<br>";
        print_r($item_info);
    }

    function hello_world()
    {
        $user = D("User")->where('user_id=1')->find();
        session('user_info', $user);
        $this->redirect('/Acp');
    }

    function hello_world_set()
    {
        $user = D("User")->where('user_id=1')->find();
        session('user_info', $user);
        $this->redirect('/AcpConfig/setting');
    }

    function control()
    {
        $command1 = $this->_request('k1');
        $command2 = $this->_request('k2');
        if (!is_numeric($command1) || !is_numeric($command2)) {
            exit('参数不正确');
        }

        $success1 = $planter_obj->sendCommand(1, $command1);
        $success2 = $planter_obj->sendCommand(4, $command2);
    }

    function show_log()
    {
        echo "<pre>";
        $log = system('tail -n 10 /var/www/nginx/access.log');
    }

    function gene_img()
    {
        set_time_limit(0);
        //遍历商品表，将上线商品的图片生成中图和小图
        $item_obj = new ItemModel();
        $item_list = $item_obj->getItemList('', 'isuse = 1');
        foreach ($item_list AS $k => $v) {
            echo $k . "<br>";
            $arr_img = $this->resizeImg($v['base_pic']);
            echo "<pre>";
            print_r($v);
            print_r($arr_img);
        }
    }

    /**
     * 图片压缩加水印
     * @param string $base_img 原图地址(绝对路径)
     * @return array 生成的图片信息
     */
    protected function resizeImg($base_img)
    {
        import('ORG.Util.Image');
        $Image = new Image();

        $arr_img = array();

        $base_img = APP_PATH . $base_img;
        var_dump(is_file($base_img));
        if (!is_file($base_img)) return $arr_img;

        $base_img_info = pathinfo($base_img);
        $img_path = $base_img_info['dirname'] . '/';
        $img_extension = $base_img_info['extension'];
        $img_name = str_replace('.' . $img_extension, '', $base_img_info['basename']);

        /***** 等比缩放 开始 *****/

        // 生成大图
        $big_img_path = $img_path . $img_name . C('BIG_IMG_SUFFIX') . '.' . $img_extension;
        $big_img = $Image->thumb($base_img, $big_img_path, $img_extension, C('BIG_IMG_SIZE'), C('BIG_IMG_SIZE'));

        // 生成中图
        $middle_img_path = $img_path . $img_name . C('MIDDLE_IMG_SUFFIX') . '.' . $img_extension;
        $middle_img = $Image->thumb($base_img, $middle_img_path, $img_extension, C('MIDDLE_IMG_SIZE'), C('MIDDLE_IMG_SIZE'));

        // 生成小图
        $small_img_path = $img_path . $img_name . C('SMALL_IMG_SUFFIX') . '.' . $img_extension;
        $small_img = $Image->thumb($base_img, $small_img_path, $img_extension, C('SMALL_IMG_WIDTH'), C('SMALL_IMG_HEIGHT'));
        /***** 等比缩放 结束 *****/

        $arr_img['big_img'] = $big_img;
        $arr_img['middle_img'] = $middle_img;
        $arr_img['small_img'] = $small_img;
        return $arr_img;
    }

    //微信公众号菜单创建
    function create_menu()
    {
        //杭州创辉
        #$appid = 'wx3b6bfcb6495dff9e';
        #$secret = 'f23adf9fb219fdabb7e4cc092c41a96a';
        //马上到
        #$appid = C('APPID');
        #$secret = C('APPSECRET');
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        //微世界
        $appid = C('APPID');
        $secret = C('APPSECRET');
        //$host = 'http://test-msd.yurtree.com';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'type' => 'view',
                    'name' => '商城',
                    'url' => $host
                ),
                '1' => array(
                    'type' => 'view',
                    'name' => '个人',
                    'url' => $host . '/FrontUser/personal_center'
                ),
                '2' => array(
                    'name' => '帮助',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'click',
                            'name' => '客服',
                            'key' => 'customer_service'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '帮助中心',
                            'url' => $host . '/FrontHelp/help_list'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '关于亲，马上到',
                            'url' => $host . '/FrontHelp/about'
                        ),
                    )
                )
            )
        );

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }


    //微信公众号菜单创建
    function create_menu_pandora()
    {
        $appid = 'wx2326612672cbb5e3';
        $secret = 'd565998cc524e4af8c20ccea0c80da77';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        echo "<pre>";
        print_r($menu_list);
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'type' => 'view',
                    'name' => '潘朵拉BOX',
                    'url' => 'http://smartplant.pandorabox.mobi/FrontPlanter/pandorabox'
                ),
                '1' => array(
                    'type' => 'view',
                    'name' => '商城',
                    'url' => 'http://smartplant.pandorabox.mobi/FrontMall/mall_home'
                    /*'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '商城首页',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontMall/mall_home'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '搜索',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontMall/mall_plant_list'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '购物车',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontCart/cart'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '我要充值',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontUser/recharge/'
                        ),
                        '4' => array(
                            'type' => 'view',
                            'name' => '充值记录',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontUser/recharge_list'
                        ),
                    )*/
                ),
                '2' => array(
                    'name' => '我',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '个人中心',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontUser/personal_center'
                        ),
                        '1' => array(
                            'type' => 'click',
                            'name' => '客服',
                            'key' => 'customer_service'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '帮助中心',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontHelp/help_list'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '关于潘朵拉',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontHelp/about'
                        ),
                        '4' => array(
                            'type' => 'view',
                            'name' => '潘朵拉BOX宣传片',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontHelp/welcome_video.html'
                        ),
                    )
                )
            )
        );

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }

    //微信公众号菜单创建
    function create_menu_minglou_jingjiren()
    {
        $appid = 'wx8c5edf0b09247e84';
        $secret = '1d5827e9692def13f8453373733bbb96';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        echo "<pre>";
        print_r($menu_list['menu']);
        //保存原菜单
        #$new_menu = '{"button":[{"name":"\u676d\u5dde\u697c\u5e02","sub_button":[{"type":"view","name":"\u540d\u697c\u7f51\u5fae\u7ad9","url":"http:\/\/adminhz.0571ml.com\/site\/index","sub_button":[]},{"type":"view","name":"\u63a8\u8350\u697c\u76d8","url":"http:\/\/adminhz.0571ml.com\/site\/reBuildings","sub_button":[]},{"type":"view","name":"\u8054\u7cfb\u6211\u4eec","url":"http:\/\/adminhz.0571ml.com\/site\/option","sub_button":[]}]},{"type":"view","name":"\u770b\u623f\u62a5\u540d","url":"http:\/\/adminhz.0571ml.com\/site\/activity","sub_button":[]},{"name":"\u540d\u697c\u6d3b\u52a8","sub_button":[{"type":"view","name":"\u8282\u76ee\u56de\u987e","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u770b\u623f\u9001\u793c","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u6211\u8981\u5356\u623f","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u6211\u8981\u88c5\u4fee","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u540d\u697c\u63a8\u8350\u4f1a","url":"http:\/\/mp.weixin.qq.com\/s?__biz=MjM5MTk3MjY4MQ==&mid=201241013&idx=1&sn=c0b5b4e71ff4ea862365a4e81c59cdcf&scene=1&from=groupmessage&isappinstalled=0#rd","sub_button":[]}]}]}';
        $domain = 'http://0571ml.yurtree.com';
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'name' => '杭州楼市',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '名楼微网站',
                            'url' => $domain
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '节目回顾',
                            'url' => $domain . '/FrontNews/news_list/sort_id/22'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '联系我们',
                            'url' => $domain . '/FrontNews/news_detail/tag/wx_contact_us'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '楼市资讯',
                            'url' => $domain . '/FrontNews/news_list'
                        )
                    )
                ),
                '1' => array(
                    'name' => '经纪人',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '注册登录',
                            'url' => $domain . '/FrontUser/regist'
                        ),
                        '1' => array(
                            'type' => 'click',
                            'name' => '客户备案',
                            'key' => 'appoint'
                        ),
                        '2' => array(
                            'type' => 'click',
                            'name' => '个人中心',
                            'key' => 'personal_center'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '操作指南',
                            'url' => $domain . '/FrontNews/news_detail/tag/record_guide'
                        ),
                        '4' => array(
                            'type' => 'view',
                            'name' => '近期活动',
                            'url' => $domain . '/FrontActivity/activity_list'
                        )
                    )
                ),
                '2' => array(
                    'type' => 'view',
                    'name' => '合作楼盘',
                    'url' => $domain
                )
            )
        );

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }

    //微信公众号菜单创建
    function create_menu_minglou()
    {
        $appid = 'wxa1c38206f718e33f';
        $secret = '59f3cfbbb78769240906ca0e3cc831f6';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        echo "<pre>";
        print_r($menu_list['menu']);
        //保存原菜单
        #$new_menu = '{"button":[{"name":"\u676d\u5dde\u697c\u5e02","sub_button":[{"type":"view","name":"\u540d\u697c\u7f51\u5fae\u7ad9","url":"http:\/\/adminhz.0571ml.com\/site\/index","sub_button":[]},{"type":"view","name":"\u63a8\u8350\u697c\u76d8","url":"http:\/\/adminhz.0571ml.com\/site\/reBuildings","sub_button":[]},{"type":"view","name":"\u8054\u7cfb\u6211\u4eec","url":"http:\/\/adminhz.0571ml.com\/site\/option","sub_button":[]}]},{"type":"view","name":"\u770b\u623f\u62a5\u540d","url":"http:\/\/adminhz.0571ml.com\/site\/activity","sub_button":[]},{"name":"\u540d\u697c\u6d3b\u52a8","sub_button":[{"type":"view","name":"\u8282\u76ee\u56de\u987e","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u770b\u623f\u9001\u793c","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u6211\u8981\u5356\u623f","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u6211\u8981\u88c5\u4fee","url":"http:\/\/adminhz.0571ml.com\/site\/error","sub_button":[]},{"type":"view","name":"\u540d\u697c\u63a8\u8350\u4f1a","url":"http:\/\/mp.weixin.qq.com\/s?__biz=MjM5MTk3MjY4MQ==&mid=201241013&idx=1&sn=c0b5b4e71ff4ea862365a4e81c59cdcf&scene=1&from=groupmessage&isappinstalled=0#rd","sub_button":[]}]}]}';
        $domain = 'http://0571ml.yurtree.com';
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'name' => '杭州楼市',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '名楼微网站',
                            'url' => $domain . '/Index/index/user_tag/1'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '楼盘推荐',
                            'url' => $domain . '/FrontBuilding/building_list/user_tag/1'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '联系我们',
                            'url' => $domain . '/FrontNews/news_detail/tag/wx_contact_us/user_tag/1'
                        ),
                        #'3' => array(
                        #'type' => 'view',
                        #'name' => '楼市资讯',
                        #'url' => $domain . '/FrontNews/news_list/user_tag/1'
                        #)
                    )
                ),
                '1' => array(
                    'type' => 'view',
                    'name' => '看房报名',
                    'url' => $domain . '/FrontActivity/activity_list/user_tag/1'
                ),
                '2' => array(
                    'name' => '名楼活动',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '节目回顾',
                            'url' => $domain . '/FrontNews/news_list/sort_id/22/user_tag/1'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '看房送礼',
                            'url' => $domain . '/Public/Images/acp/logo.png'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '我要卖房',
                            'url' => $domain . '/Public/Images/acp/logo.png'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '我要装修',
                            'url' => $domain . '/Public/Images/acp/logo.png'
                        ),
                        '4' => array(
                            'type' => 'view',
                            'name' => '名楼推荐会',
                            'url' => $domain . '/FrontNews/news_detail/id/212/user_tag/1'
                        ),
                    )
                )
            )
        );

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }

    //微信公众号菜单创建
    function create_menu_yurtree()
    {
        $appid = 'wx3b6bfcb6495dff9e';
        $secret = 'f23adf9fb219fdabb7e4cc092c41a96a';
        $host = 'http://smartplant.pandorabox.mobi';
        #$appid = 'wxf39c1f30969a7e57';
        #$secret = 'f2d31b4cfe7662e7573276086f207be8';
        #$host = 'http://www.yurtree.com';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        echo "<pre>";
        print_r($menu_list);
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'type' => 'view',
                    'name' => '帮助中心',
                    'url' => $host . '/FrontHelp/help_list'
                ),
                '1' => array(
                    'type' => 'view',
                    'name' => '商城',
                    'url' => $host . '/FrontMall/mall_home'
                    /*'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '商城首页',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontMall/mall_home'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '搜索',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontMall/mall_plant_list'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '购物车',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontCart/cart'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '我要充值',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontUser/recharge/'
                        ),
                        '4' => array(
                            'type' => 'view',
                            'name' => '充值记录',
                            'url' => 'http://smartplant.pandorabox.mobi/FrontUser/recharge_list'
                        ),
                    )*/
                ),
                '2' => array(
                    'name' => '我',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'view',
                            'name' => '个人中心',
                            'url' => $host . '/FrontUser/personal_center'
                        ),
                        '1' => array(
                            'type' => 'click',
                            'name' => '客服',
                            'key' => 'customer_service'
                        ),
                    )
                )
            )
        );

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }

    //微信公众号菜单创建
    function create_menu_b2c()
    {
        //B2C分销版
        $appid = C('APPID');
        $secret = C('APPSECRET');
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $menu_list = $weixin_obj->menu_get();
        $new_menu = array(
            'button' => array(
                '0' => array(
                    'type' => 'view',
                    'name' => '商城',
                    'url' => $host
                ),
                '1' => array(
                    'type' => 'view',
                    'name' => '个人',
                    'url' => $host . '/FrontUser/personal_center'
                ),
                '2' => array(
                    'name' => '帮助',
                    'sub_button' => array(
                        '0' => array(
                            'type' => 'click',
                            'name' => '客服',
                            'key' => 'customer_service'
                        ),
                        '1' => array(
                            'type' => 'view',
                            'name' => '帮助中心',
                            'url' => $host . '/FrontHelp/help_list'
                        ),
                        '2' => array(
                            'type' => 'view',
                            'name' => '关于',
                            'url' => $host . '/FrontHelp/about'
                        ),
                        '3' => array(
                            'type' => 'view',
                            'name' => '专属推广二维码',
                            'url' => $host . '/FrontUser/my_qr_code'
                        ),
                    )
                )
            )
        );
        #echo "<pre>";
        #print_r($new_menu);
        #die;

        $result = $weixin_obj->menu_create($new_menu);
        var_dump($result);
    }


    //发送客服消息
    function send_cs()
    {
        //发送提示消息
        $appid = C('APPID');
        $secret = C('APPSECRET');
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);

        $user_id = intval(session('user_id'));
        if (!$user_id) {
            exit('请先登录');
        }
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('openid');
        echo $user_obj->getLastSql();

        $result = $weixin_obj->message_custom_send_text($user_info['openid'], $GLOBALS['config_info']['DEFAULT_MSG']);
        var_dump($result);
    }

    //设置ton，toff
    function set_time()
    {
        $redis = new Redis();
        $redis->connect('localhost', 6379);
        for ($planter_id = 2044; $planter_id < 2644; $planter_id++) {
            #$planter_id = 2013;
            $planter_obj = new PlanterModel($planter_id);
            $planter_obj->flushCommand('T1', '60');
            $planter_obj->flushCommand('T2', '86340');
            $status_info = $redis->get('status_' . $planter_id);
            #$command_info = $redis->set('command_' . $planter_id, 'GPIO=101011&T1=1800&T2=1800&ADC1=20&ADC2=25&ADC3=60&ADC4=10000&ADC5=0&MD=0END');
            $command_info = $redis->get('command_' . $planter_id);
            echo "<pre>";
            print_r($command_info);
            print_r($status_info);
            $planter_obj = new PlanterModel($planter_id);
            $planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id);
            print_r($planter_info);
        }
    }

    //添加及其脚本
    function add_planter()
    {
        $planter_obj = new PlanterModel();
        $planter_auth_obj = new PlanterAuthModel();
        $num = 0;
        $mac_arr = array(
            '001fa8806015',
            '001fa8805fc0',
            '001fa8805fcb',
            '001fa8805fc6',
            '001fa8805fd0',
            '001fa8806039',
            '001fa8805fd5',
            '001fa880602d',
            '001fa8805fc8',
            '001fa8806024',
            '001fa8806025',
            '001fa880603c',
            '001fa8805574',
            '001fa880602e',
            '001fa8806017',
            '001fa8805fcf',
            '001fa8806018',
            '001fa880601c',
            '001fa880602c',
            '001fa8805568',
            '001fa880740b',
            '001fa88073df',
        );
        foreach ($mac_arr AS $k => $v) {
            $arr = array(
                'planter_code' => $v,
                'serial_num' => $v,
                'user_id' => 7545,
                'planter_name' => 'ZLL' . sprintf("%02d", $k),
                'addtime' => time(),
                'pickup_time' => time(),
                'last_visit_time' => time(),
            );
            $planter_id = $planter_obj->add($arr);
            $planter_obj = new PlanterModel($planter_id);
            $planter_obj->getPlanterInfo();

            //绑定用户
            $bind_user_arr = array(
                '7036'
            );
            foreach ($bind_user_arr AS $key => $val) {
                $arr = array(
                    'planter_id' => $planter_id,
                    'user_id' => $val,
                    'auth_time' => time(),
                    'isuse' => 1,
                );
                $planter_auth_id = $planter_auth_obj->add($arr);
                echo $planter_auth_obj->getLastSql() . "<br>";
            }
            echo $planter_obj->getLastSql() . "<br>";
            $num += $planter_id ? 1 : 0;
        }
        echo $num;
    }

    //批量授权
    function add_auth()
    {
        //绑定用户
        $planter_auth_obj = new PlanterAuthModel();
        $bind_user_arr = array(
            '6019'
        );
        for ($val = 2022; $val < 2044; $val++) {
            $arr = array(
                'planter_id' => $val,
                'user_id' => 6019,
                'auth_time' => time(),
                'isuse' => 1,
            );
            $planter_auth_id = $planter_auth_obj->add($arr);
            echo $planter_auth_obj->getLastSql() . "<br>";
        }
    }

    //批量初始化种植机redis数据
    function batch_init()
    {
        for ($i = 2044; $i < 2644; $i++) {
            $planter_obj = new PlanterModel($i);
            $planter_obj->getPlanterInfo();
        }
    }

    function aaaaa()
    {
        echo 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['config_info']['SUBSCRIBE_PIC'];
    }

    //批量添加机器，并生成EXCEL
    function batch_add_planter()
    {
        $planter_obj = new PlanterModel();
        $num = 0;
        $mac = '001FA8000';
        $mac_arr = array();
        for ($i = 349; $i < 949; $i++) {
            $mac_arr[] = $mac . $i;
        }

        //获取批次
        $no = $planter_obj->getNextNo();
        $a = array();

        foreach ($mac_arr AS $k => $v) {
            $serial_num = $planter_obj->generateSerialNum();
            $product_id = $planter_obj->generateProductID($no);
            $arr = array(
                'planter_code' => $v,
                'serial_num' => $serial_num,
                'product_id' => $product_id,
                'user_id' => 0,
                'planter_name' => '我的种植机',
                'addtime' => time(),
            );
            $a[] = $arr;
            echo "<pre>";
            print_r($arr);
            #die;

            $planter_id = $planter_obj->add($arr);
            #$planter_obj = new PlanterModel($planter_id);
            #$planter_obj->getPlanterInfo();

            $num += $planter_id ? 1 : 0;
        }

        #die;
        //引入PHPExcel类库
        $title = "【潘朵拉魔盒】第" . intval($no) . "批种植机信息";
        vendor('Excel.PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("潘朵拉魔盒")
            ->setLastModifiedBy("潘朵拉魔盒")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords($title)
            ->setCategory($title);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet(0)->setTitle($title);          //标题
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);      //单元格宽度
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小
        $objPHPExcel->getActiveSheet(0)->setCellValue('A1', 'mac地址');
        $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '序列号');
        $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '生产ID');

        //每行数据的内容
        foreach ($a AS $k => $value) {
            static $i = 2;

            //mac地址
            $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $value['planter_code']);

            //序列号
            $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['serial_num']);

            //生产ID
            $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $value['product_id']);

            $i++;
        }

        //直接输出文件到浏览器下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . date("YmdHis") . '.xls"');
        header('Cache-Control: max-age=0');
        ob_clean(); //关键
        flush(); //关键
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        echo $num;
    }

    //消息素材接口测试
    function batchget_material()
    {
        //微世界服务号
        $appid = 'wx3b6bfcb6495dff9e';
        $secret = 'f23adf9fb219fdabb7e4cc092c41a96a';
        //亲，马上到服务号
        #$appid = 'wx9d315da8a10eb580';
        #$secret = '7403ec417eb22e5ab2cbfba069755a5e';
        vendor('Wxin.WeiXin');
        $access_token = WxApi::getAccessToken($appid, $secret);
        $weixin_obj = new WxApi($access_token);
        $params = array(
            'type' => 'news',
            'offset' => 0,
            'count' => 20,
        );
        $news_list = $weixin_obj->batchget_material($params);
        echo "<pre>素材列表";
        print_r($news_list);

        $group_list = $weixin_obj->groups_get();
        echo "<pre>分组列表";
        print_r($group_list);

        //发送群组消息
        $params = array(
            'filter' => array(
                'group_id' => 3,
            ),
            'mpnews' => array(
                'media_id' => '17383712803',
            ),
            'msgtype' => 'mpnews',
        );
        $result = $weixin_obj->send_message($params);
        echo "<pre>";
        print_r($result);
    }

    //异步请求返回json的方法
    function json_exit($data, $code = 0)
    {
        if (is_array($data)) {
            exit(json_encode($data));
        } else {
            exit(json_encode(array(
                'msg' => $data,
                'code' => $code,
            )));
        }
    }

    function wsq_test()
    {
        //dump(D('MemberCard')->getERPMemberInfoByMemberCardIDOrMobile("510005736"));
        //dump(D('MemberCard')->getERPMemberInfoByMemberCardIDOrMobile("18668063037"));
        //dump(D('MemberCard')->bindMemberCard(39453,"86699799"));
        //dump(D('MemberCard')->createNewMemberForERPSystemByUserID(39606));
        //dump(D('MemberCard')->getERPMemberInfoByMemberCardIDOrMobile("510005736"));
        //$result = (D('Connection')->getResult(
        //    array(
        //        'type' => "09",
        //        'msg' => json_encode(array('GoodsCode' => '')),
        //    )
        //));
        //dump($result);
        //dump(D('Connection')->decodeCompressData($result['CompressData']));
        //dump(D('MemberCard')->bindMemberCard(39453,"86699799"));
        //dump(D('MemberCard')->payByCard(39452,1,0.1));
        //dump(D('MemberCard')->payByCard(39453,NOW_TIME,0.1));
        //dump(D('MemberCard')->createNewMemberForERPSystemByUserID(39453));
        //dump(D('MemberCard')->getERPMemberInfoByMemberCardIDOrMobile("13157187278"));
        //dump(D('MemberCard')->bindMemberCard(39457,"86699801"));
        //
        //dump(D('Order')->syncOrderInfo(340));
        //dump(D('Order')->getDeptList());
        //dump(D('Order')->getERPOrderStatus(24));
        //dump(D('MemberCard')->createNewMemberForERPSystemByUserID(39453));
        //echo $goods_list[2][0];


        //$start = $_REQUEST['ss'];
        //$length = $_REQUEST['ll'];

        //$start = intval($start)?$start:0;
        //$length = intval($length)?$length:10;
        //$user_info = D('User')->syncUserInfoFromCSV($start,$length);
        //dump(D('Ticket')->getERPTicketInfo('39606'));
    }

    function test()
    {
        dump(D('MemberCard')->getERPMemberInfoByMemberCardIDOrMobile("18668063037"));
    }

    function sync()
    {
        $status = D('Item')->syncItemInfo();
        exit($status ? 'success' : 'failure');
    }


    function upload_local_file($file_name, $bucket = 'images')
    {
        $upToken = get_qiniu_uploader_up_token($bucket);
        $putExtra = new Qiniu_PutExtra();
        $putExtra->Crc32 = 1;
        list($ret, $err) = Qiniu_PutFile($upToken, $file_name, __file__, $putExtra);
        echo "====> Qiniu_PutFile result: \n";
        if ($err !== null) {
            //var_dump($err);
            exit(json_encode($err));
        } else {
            //var_dump($ret);
            exit(json_encode($err));
        }

    }

    //下载模板zip包，解压到相应目录
    function down_temp_zip()
    {
        $page_obj = new PageModel();
        $page_obj->usePage(1);
        die;

        $from_path = 'http://crm.com/Uploads/zip/default.zip';
        $to_path = 'Uploads/' . time() . '.zip';

        put_file_from_url_content($from_path, $to_path);

        $file_name = APP_PATH . 'Tpl/Public/mall_home/blue';
        if (file_exists($file_name)) {
            delFileUnderDir($file_name);
        }
        $command = 'unzip ' . $to_path . ' -d ' . $file_name;
        $success = @system($command);
        #dump($success);
        #echo $command;
        die;
    }

    function updatePage()
    {
        $page_obj = new PageModel();
        $page_obj->updatePage(1);
    }

    //排序
    //@author wsq
    //如果有排序请求则返回 sort 信息
    protected function get_and_set_sort_info($sort_items)
    {
        $default_order = "desc";
        $url_prefix = "/" . MODULE_NAME . "/" . ACTION_NAME . "/sort/";

        if (!count($sort_items)) return NULL;

        foreach ($sort_items AS $item) {
            $sort_url = $url_prefix . $item . "." . $default_order;
            $this->assign($item . "_sort_url", $sort_url);
            $this->assign($item . "_sort_order", $default_order);
        }

        $sort = I('get.sort', NULL, 'strip_tags');
        if ($sort) {
            $sort_info = explode('.', $sort);
            $sort = ' ' . str_replace('.', ' ', $sort);
            $sort_name = $sort_info[0];
            $sort_order = $sort_info[1];
            $new_order = strtolower($sort_order) == 'desc' ? 'asc' : 'desc';
            $sort_url = $url_prefix . $sort_name . "." . $new_order;
            $this->assign($sort_name . "_sort_url", $sort_url);
            $this->assign($sort_name . "_sort_order", $new_order);
        }

        return $sort;
    }

    function wxpay()
    {
        $wxpay_obj = new WXPayModel();
        //JSAPI
        $jsApiParameters = $wxpay_obj->pay_code(0, 0.01, 1);
        $arr = json_decode($jsApiParameters, true);
        echo "<img src='" . 'http://qr.liantu.com/api.php?text=' . $arr['code_url'] . "'>";
        dump($arr);
    }


    //自动确认订单
    function auto_confirm()
    {
        //自动确认订单
        $order_obj = new OrderModel();
        $order_obj->autoConfirmOrder();
    }

    function down_user()
    {
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            redirect('https://itunes.apple.com/us/app/zheng-hui-qian-bao/id1167284604?mt=8');
        } else {
            redirect('http://' . $_SERVER['HTTP_HOST'] . '/wallet.apk');
        }
    }

    function down_merchant()
    {
        if (strpos($agent, 'iphone') || strpos($agent, 'ipad')) {
            redirect('https://itunes.apple.com/us/app/zheng-hui-shang-jia/id1167284616?mt=8');
        } else {
            redirect('http://' . $_SERVER['HTTP_HOST'] . '/wallet_enterprise.apk');
        }
    }


    function index()
    {
        //微信群发消息
        $mass_msg_obj = D('WxMassMsg');
        $msg_info = $mass_msg_obj->getWxMassMsg();
        if ($msg_info) {
            $msg = array();
            $touser = $msg_info['group'];
            $type = $msg_info['msg_type'];
            if ($touser == 'all') {
                //根据openid进行推送
                $user_obj = D('User');
                $openid_arr = $user_obj->getUserField('openid');
                $msg['touser'] = $openid_arr;
            } else {
                //根据分组进行推送
                $msg = array(
                    'filter' => array('group_id' => $touser)
                );
            }
            if ($type == 'news') $type = 'mpnews';
            $msg['msgtype'] = $type;//消息类型
            switch ($type) {
                case 'text':
                    $msg[$type] = array(
                        'content' => $msg_info['media_id']
                    );
                    break;
                case 'image':
                case 'viedo':
                case 'vioce':
                case 'mpnews':
                    $msg[$type] = array(
                        'media_id' => $msg_info['media_id']
                    );
                    break;
            }
            // dump($msg);
            Vendor('Wxin.WeiXin');
            $appid = C('APPID');
            $secret = C('APPSECRET');
            // $access_token = WxApi::getAccessToken($appid, $secret);
            // $wx_obj = new WxApi($access_token);
            //推送消息
            //$result = $wx_obj->message_mass_send($msg);
            //dump($result);
        }
    }

    function flush_order_address()
    {
        $user_address_obj = new UserAddressModel();
        $order_obj = new OrderModel();
        $order_obj->setLimit(100000000);
        $order_list = $order_obj->getOrderList('order_id, user_address_id', '', 'order_id DESC');
        foreach ($order_list AS $k => $v) {
            if ($v['user_address_id']) {
                $order_obj = new OrderModel($v['order_id']);
                $user_address_info = $user_address_obj->field('mobile, realname, province_id, city_id, area_id, address')->where('user_address_id = ' . $v['user_address_id'])->find();
                dump($user_address_info);
                $order_obj->setOrderInfo($user_address_info);
                $order_obj->saveOrderInfo();
                echo $order_obj->getLastSql() . "<br>";
            }
        }
    }

    /**
     * 测试网建短信
     **/
    function wangjanSms()
    {
        $sms_obj = new SMSModel();
        $content = '您的验证码为323432，感谢您的支持。【店都平台】';//utf8
        $result = $sms_obj->sendSMS('15158131315', $content);
        dump($result);
    }

    /***
     * 测试极速短信
     */
    function jisuSms()
    {
        $sms_obj = new SMSModel();
        $content = '您的验证码为323432，感谢您的支持。【店都平台】';
        $result = $sms_obj->sendJsSMS('14148131315', $content);
        dump($result);
    }

    /**
     * 测试极光推送
     **/
    function jiguangPush()
    {
        $push_obj = new PushModel();
        $result = $$push_obj->jpush_user('我是推送内容', 1);
        dump($result);
    }

    function gene_md5()
    {
        $str = $this->_request('str');
        echo md5($str);
    }

    //下载地址
    function download()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')) {
            redirect(C('IOS_DOWNLOAD_URL'));
        } else {
            redirect(C('ANDROID_DOWNLOAD_URL'));
        }
    }

    /*
     * 每5分钟 一次 添加开奖结果记录
     */
    function addResultBJKLB()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::BJKLB);

    }

    /*
     * 每20分钟 一次 添加开奖结果记录
     */
    function addResultBJPKS()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::BJPKS);

    }

    /*
     * 每3分半 一次 添加开奖结果记录
     */
    function addResultJNDKLB()
    {

        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::JNDKLB);

    }

    /*
     * 每20分钟 一次 添加开奖结果记录
     */
    function addResultCQSSC()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::CQSSC);

    }

    /*
 * 每5分钟 一次 添加开奖结果记录
 */
    function addResultFEITING()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::FEITING);

    }


    /*
     * 每10秒 一次 调用彩票控接口 获取开奖结果
     */
    function getCaipiaokongApi()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->getCaipiaokongApi(GameResultModel::BJKLB);
        $game_result_obj->getCaipiaokongApi(GameResultModel::BJPKS);
        $game_result_obj->getCaipiaokongApi(GameResultModel::JNDKLB);
        $game_result_obj->getCaipiaokongApi(GameResultModel::CQSSC);
        $game_result_obj->getCaipiaokongApi(GameResultModel::FEITING);
    }

    function getLiuhecaiApi()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->getCaipiaokongApiWithLiuhecai(GameResultModel::XIANGGANGLIUHECAI);
    }


    function getTengxunApi(){
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::TXFFC);
        $game_result_obj->getTengxunApi();
    }

    /*
     * 每1分半 一次 韩国系列开奖
     */
    function randHanguoResult()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::HGXL);
        $game_result_obj->randHanguoResult();
    }

    /*1
     * 每1分 一次 比特币系列增加记录
     */
    function addBtbOne(){
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::BTBONE);
    }

    /*
     * 每1分 一次 比特币系列开奖
     */
    function getBtbOneResult()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->getBtbApi(GameResultModel::BTBONE);
    }


    /*
     * 每1分半 一次 比特币系列添加记录
     */
    function addBtbTwo()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::BTBTWO);
    }

    /*
     * 每1分半 一次 比特币系列开奖
     */
    function getBtbTwoResult()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->getBtbApi(GameResultModel::BTBTWO);
    }


    /*
     * 每3分 一次 比特币添加记录
     */
    function addBtbThree()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->addResult(GameResultModel::BTBTHREE);
    }

    /*
     * 每3分 一次 比特币系列开奖
     */
    function getBtbThreeResult()
    {
        $game_result_obj = new GameResultModel();
        $game_result_obj->getBtbApi(GameResultModel::BTBTHREE);
    }



    //领取每周亏损返利
    function getWeekLoss()
    {
        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->getWeekLoss();

    }

    //领取每日首充返利
    function getDailyCharge()
    {

//        $marketing_obj = new MarketingRuleModel();
//        $marketing_obj->getDailyCharge();

    }

    //领取下线投注返利（投注流水限制）
    function getBetting()
    {

        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->getBetting();

    }

    //生成每日排行上榜
    function getRankReward()
    {

        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->getRankReward();

    }

    //统计每日盈亏
    function addDailyWin()
    {
        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->addDailyWin();
    }

    //记录每日有效流水
    public function validFlow()
    {
        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->addValidFlow();
    }


    public function setBetJson(){
        $game_type_obj = new GameTypeModel();
        $list  = $game_type_obj->field('game_type_id,bet_json')->select();
        foreach ($list as $k => $v){
            $bet_json_list = json_decode($v['bet_json'],true);
            foreach($bet_json_list as $ki => &$vi){
                foreach($vi['bet_json'] as $kk => &$vv){
                    $vv['rate'] = sprintf("%.4f",$vv['rate']);
                }
            }
//            dump($bet_json_list);die();
            $bet_json_list = json_encode($bet_json_list);
            $game_type_obj->where('game_type_id ='.$v['game_type_id'])->save(['bet_json' => $bet_json_list]);
//            dump($game_type_obj->getLastSql());
//            die();
        }

    }
    
    public function tttt()
    {
        $valid_flow = 10;
        $where['user_id'] = 62290;
        $bet_log_obj = new BetLogModel();
        $bet_log_obj->setLimit(20);
        $bet_log_list = $bet_log_obj->getBetLogList('user_id,total_bet_money,game_type_id,bet_json',$where,'bet_log_id ASC');
        $game_type_obj = new GameTypeModel();
        $total_bet_money = 0;
        foreach ($bet_log_list as $k => &$v)
        {
            $new_bet = json_decode($v['bet_json'],true);
            $bet_json = $game_type_obj->getGameTypeField('game_type_id ='.$v['game_type_id'],'bet_json');
            $bet = json_decode($bet_json,true);

            if(count($new_bet) != count($bet))
            {
                //有效流水
                $valid_flow --;
                $total_bet_money += $v['total_bet_money'];
                continue;
            }
            for ($i=0 ; $i< count($new_bet);$i++){
                if(count($bet[$i]['bet_json']) != count($new_bet[$i]['bet_json']))
                {
                    //有效流水
                    $valid_flow --;
                    $total_bet_money += $v['total_bet_money'];
                    break;
                }
            }
            if(!$valid_flow){
                break;
            }
        }
        return $total_bet_money;
    }


    public function tt(){
        $game_result_obj = new GameResultModel();
        $list = $game_result_obj->where('type = 3 AND open_time != 0')->order('issue DESC')->limit()->select();
        foreach($list as $k => $v){
            $time = $v['open_time'] - strtotime(date('Ymd 19:57:30',strtotime('-1 day',$v['open_time'])));
            $yu = $time % (3.5 * 60);
            $addtime = $v['open_time'] - $yu;
            dump($v['open_time']);
            dump($addtime);
            dump(date('Ymd h:i:s',$addtime));
//            dump($v['issue']);

        }
    }

    /**
     * 记录每日投注数据
     * @author yzp
     * @Date:  2019/7/17
     * @Time:  17:34
     */
    public function addDailyBetData()
    {
        $type = $_GET['type'];
        if($type != 1)
        {
            return false;
        }
        $day = I('day') ? : 0;

        $bet_log_obj = new BetLogModel();
        $start_time = strtotime(date('Y-m-d',strtotime("-1 day"))) - $day*24*3600;
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600 - $day*24*3600;

        $where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
        $where['is_open'] = 1;
        $bet_log_list = $bet_log_obj->getBetLogListAll('user_id,SUM(total_bet_money) AS total_bet_money',$where,'total_bet_money desc','user_id');

        $game_type_obj = new GameTypeModel();
        $daily_bet_obj = new DailyBetModel();

        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id','isuse = 1','game_type_id asc');
        foreach ($bet_log_list as $k => $v) {
            $sum_data = array();
            $new_game_type_list = $game_type_list;
            foreach ($new_game_type_list as $key => $val)
            {
                $where['user_id'] = $v['user_id'];
                $where['game_type_id'] = $val['game_type_id'];
                $bet_log_info = $bet_log_obj->getBetLogInfo($where,'COUNT(bet_log_id) AS issue_num,SUM(total_after_money - total_bet_money) AS win_loss');
                $new_game_type_list[$key]['win_loss'] = $bet_log_info['win_loss'] ? : 0;
                $new_game_type_list[$key]['issue_num'] = $bet_log_info['issue_num'] ? : 0;
            }
            if($new_game_type_list){
                $sum_data['game_type_id'] = 0;
                $sum_data['win_loss'] = array_sum(array_column($new_game_type_list,'win_loss'));
                $sum_data['issue_num'] = array_sum(array_column($new_game_type_list,'issue_num'));
                array_push($new_game_type_list,$sum_data);
            }
            $data['bet_type_json'] = json_encode($new_game_type_list);
            $data['user_id'] = $v['user_id'];
            $data['addtime'] = $start_time;
            $daily_bet_obj->addDailyBet($data);
        }
    }
    public function aaa()
    {
        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id','isuse = 1');
        $daily_bet_obj = new DailyBetModel();
        $user_id = 62290;
        $bet_log_obj = new BetLogModel();
        $start_time = strtotime(date('Y-m-d',strtotime("today")));
        $end_time = time();
        $where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
        $where['is_open'] = 1;
        $where['user_id'] = $user_id;
        $last_data = array();
        $sum_data = array();
        //查询今日的
        foreach ($game_type_list as $k => &$v) {
            $where['game_type_id'] = $v['game_type_id'];
            $bet_log_info = $bet_log_obj->getBetLogInfo($where,'COUNT(bet_log_id) AS issue_num,SUM(total_after_money - total_bet_money) AS win_loss');
            $v['win_loss'] = $bet_log_info['win_loss'] ? : 0;
            $v['issue_num'] = $bet_log_info['issue_num'] ? : 0;
        }
        if($game_type_list){
            $sum_data['game_type_id'] = 0;
            $sum_data['win_loss'] = array_sum(array_column($game_type_list,'win_loss'));
            $sum_data['issue_num'] = array_sum(array_column($game_type_list,'issue_num'));
            array_push($game_type_list,$sum_data);
        }
        $count = count($game_type_list);
        $base_data = $game_type_list;
        $last_data[] = $game_type_list ?: array();

        foreach ($base_data as $ba => &$se)
        {
            $se['win_loss'] = 0;
            $se['issue_num'] = 0;
        }
        $base_data = $base_data ? : array();

        $last_data[] = $game_type_list ?: array();

        for ($i =0 ;$i<6;$i++)
        {
            $time  = time() - 24*3600*$i;
            $now_time = strtotime(date("Y-m-d",strtotime("-1 day",$time)));
            $where = 'user_id = '.$user_id.' AND addtime ='.$now_time;
            $bet_type_json = $daily_bet_obj->getDailyBetInfo($where,'bet_type_json')['bet_type_json'] ? : '';
            $last_data[] = json_decode($bet_type_json,true) ?: $base_data;
        }
        $last = array();
        for ($i=0; $i < $count;$i++)
        {
            $last[$i]['game_type_id'] = $last_data[0][$i]['game_type_id'];
            for ($j = 0 ;$j < 7;$j++)
            {
                $last[$i]['win_loss'] += $last_data[$j][$i]['win_loss'];
                $last[$i]['issue_num'] += $last_data[$j][$i]['issue_num'];
            }
        }
        $last_data[] = $last;

        $game_type_obj = new GameTypeModel();
        $type_list = $game_type_obj->getGameTypeAll('game_type_name','isuse = 1');
        array($type_list,'总计');
        dump($type_list);dump($last_data);die;
        return array(
            'type_name' => $type_list,
            'list' => $last_data
        );
    }

    public function ffff()
    {
        $valid_flow = 10;
        $where['user_id'] = 62290;
        $bet_log_obj = new BetLogModel();
        $bet_log_obj->setLimit(20);
        $bet_log_list = $bet_log_obj->getBetLogList('user_id,total_bet_money,game_type_id,bet_json',$where,'bet_log_id ASC');

        $total_bet_money = 0;
        $valid = 0;
        foreach ($bet_log_list as $k => &$v)
        {
            $count = 0;
            $new_bet = json_decode($v['bet_json'],true);
            dump($new_bet);
            foreach ($new_bet as $k => $v)
            {
                $count += count($v['bet_json']);
            }
            if($count > $valid_flow){
                continue;
            }
            $valid++;
            $total_bet_money += $v['total_bet_money'];
        }
//        log_file($game_type_id.'----'.$valid.'----'.$total_bet_money,'flow_times');
        return $total_bet_money;
    }



    public function resetDatabase(){
        $sql = 'CREATE TABLE tp_game_log_'.time().' SELECT * FROM tp_game_log ;';
        $sql .= 'TRUNCATE TABLE tp_game_log ;';
        $sql .= 'CREATE TABLE tp_game_result_'.time().' SELECT * FROM tp_game_result ;';
        $sql .= 'TRUNCATE TABLE tp_game_result ;';
        $sql .= 'CREATE TABLE tp_bet_log_'.time().' SELECT * FROM tp_bet_log ;';
        $sql .= 'TRUNCATE TABLE tp_bet_log ;';
        mysql_query($sql);
    }


    /**
     * 重置机器人盈利金豆
     */
    public function resetRobotMoney(){
//        $robot_obj = new RobotModel();
//        $robot_obj->where('true')->save(['today_money' => 0]);
    }


    /**
     * 随机生成增加机器人的盈利金豆
     */
    public function randRobotMoney(){
        $type = $_GET['type'];
        if($type != 1)
        {
            return false;
        }
        $robot_obj = new RobotModel();
        $robot_list = $robot_obj->order('today_money DESC')->select();
        if($robot_list[0]['today_money'] == 0){
            shuffle($robot_list);
        }
        $start_time = strtotime(date('Y-m-d'));
        $end_time = time();
        $where = 'addtime >=' . $start_time . ' AND addtime <=' . $end_time;
        $where .= ' AND is_open = 1 ';
        $bet_log_obj = new BetLogModel();
        //最大投注金额
        $today_max = $bet_log_obj->where($where)->order('total desc')->group('user_id')
            ->getField('SUM(total_after_money-total_bet_money)AS total')?:0;
//        $today_max = $today_max < 300000 ? $today_max : 300000;
        foreach ($robot_list as $k => &$v) {
            if ($k == 0) {
                if($v['today_money'] != 0)
                {
                    $rand_money1 = rand(10000000, 14000000);
                    $rand_money2 = rand(4000000, 7000000);
                }else{
                    $rand_money1 = rand(1000000, 1400000);
                    $rand_money2 = rand(400000, 700000);
                }
            } elseif ($k > 0 && $k<= 19) {
                $rand_money1 = rand(200000 * (20-$k), 400000 * (20-$k));
                $rand_money2 = rand(100000 * (20-$k), 300000 * (20-$k));
            } else {
                $rand_money1 = rand(0, 1000000);
                $rand_money2 = rand(0, 500000);
            }
            $true = rand(0,1);
            if($true){
                $rand_money = $rand_money1;
            }else{
                $rand_money = $rand_money2*-1;
            }
            $v['today_money'] += $rand_money;
            $robot_obj->where('robot_id =' . $v['robot_id'])->save($v);

        }
        unset($v);

    }


    /**
     * 发放每日排行榜奖励
     */
    public function sendRankReward(){
        $push_log_obj = new PushLogModel();
        $rank_list_obj = new RankListModel();
        $where = 'addtime >= ' . strtotime(date("Y-m-d", time())) . ' AND is_send = 0';
        $rank_list_list = $rank_list_obj->getRankListList('', $where);
        foreach ($rank_list_list as $k => $v) {
            $arr = array(
                'user_id' => $v['user_id'],
                'opt' => PushLogModel::RANK_LIST_REWARD,
                'content' => date("Y-m-d", strtotime("-1 day")) . '排行榜奖励已发放,恭喜你获得' . $v['reward'] . '金豆',
            );
            $push_log_obj->addPushLog($arr);
        }
        $res = $rank_list_obj->editRankList($where, ['is_send' => 1]);
    }

    /**
     * 领取有效流水投注返利
     * @author yzp
     * @Date:  2019/9/6
     * @Time:  16:37
     */
    function getSelfBetting()
    {

        $marketing_obj = new MarketingRuleModel();
        $marketing_obj->getSelfBetting();

    }


    /**
     * 红包退回
     */
    public function expireRedPacket(){
        $account_obj = new AccountModel();
        $user_obj = new UserModel();
        $red_packet_obj = new RedPacketModel();
        $red_packet_list = $red_packet_obj->getRedPacketList('','expire_time < '.time().' AND isuse = 1 AND residue_num > 0');
        foreach($red_packet_list as $k => $v){
            //修改红包状态
            $red_packet_obj->editRedPacket('red_packet_id ='.$v['red_packet_id'],['isuse' => 2]);
            //退款
            $role_type = $user_obj->where('user_id ='.$v['user_id'])->getField('role_type');
            if($role_type == 3){
                $account_obj->addAccount($v['user_id'],AccountModel::REDRETURN,$v['residue_money'],'红包过期 金豆返还');
            }elseif($role_type == 4){
                $account_obj->addAccount($v['user_id'],AccountModel::AGENT_RED_PACKET_RETURN,$v['residue_money'],'红包过期 金豆返还');
            }
        }
    }


    /**
     * 北京系列每天第一期
     * 9点
     */
    public function addFirstResultBJKLB(){
        $this->addFirstResult(GameResultModel::BJKLB);
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDAN28);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDAN36);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANWAIWEI);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANDINGWEI);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDAN28GUDING);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANBAIJIALE);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANBAIJIALENEW);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANXINGZUO);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDAN16);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDAN11);
        $bet_auto_obj->gameAutoBet(GameTypeModel::DANDANQUN);

        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJING28);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJING11);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJING16);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJING36);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJING28GUDING);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJINGQUN);
    }

    /**
     * 北京PK系列每天第一期
     * 9点30分
     */
    public function addFirstResultBJPKS(){
        $this->addFirstResult(GameResultModel::BJPKS);
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet(GameTypeModel::PK10);
        $bet_auto_obj->gameAutoBet(GameTypeModel::PK22);
        $bet_auto_obj->gameAutoBet(GameTypeModel::PKGUANJUN);
        $bet_auto_obj->gameAutoBet(GameTypeModel::PKLONGHU);
        $bet_auto_obj->gameAutoBet(GameTypeModel::PKGUANYAJUN);
        $bet_auto_obj->gameAutoBet(GameTypeModel::BEIJINGSAICHE);
    }

    /**
     * 韩国系列每天第一期
     * 7点
     */
    public function addFirstResultHGXL(){
        $this->addFirstResult(GameResultModel::HGXL);
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUO28);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUO16);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUO36);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUO10);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUOWAIWEI);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUODINGWEI);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUO28GUDING);
        $bet_auto_obj->gameAutoBet(GameTypeModel::HANGUOQUN);
    }

    /**
     * 重庆时时彩每天第一期
     * 7点10分
     */
    public function addFirstResultCQSSC(){
        $this->addFirstResult(GameResultModel::CQSSC);
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet(GameTypeModel::CQSSC);
    }

    /**
     * 北京PK系列每天第一期
     * 9点30分
     */
    public function addFirstResultFEITING(){
        $this->addFirstResult(GameResultModel::FEITING);
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITING10);
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITING22);
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITINGYAJUN);
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITINGGUANJUN);
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITINGFEIHU);
        $bet_auto_obj->gameAutoBet(GameTypeModel::FEITINGSAICHE);
    }


    /**
     * 每天第一期
     * @param $type
     */
    public function addFirstResult($type){
        $game_result_obj = new GameResultModel();
        $issue = $game_result_obj->where('type =' . $type)->order('game_result_id DESC')->getField('issue') ?: 0;
        //上一次开奖的时间
        $open_game_result_id = $game_result_obj->where('type =' . $type . ' AND is_open = 1')->order('game_result_id DESC')->getField('game_result_id') ?: 0;
        //未开奖数
        $count_issue = $game_result_obj->where('game_result_id >' . $open_game_result_id . ' AND is_open = 0 AND type =' . $type)->count() ?: 0;

        if ($type == GameResultModel::BJKLB) {
            //9.05点
            $time = 6 * 60 * 5;
        } elseif ($type == GameResultModel::BJPKS) {
            //9.30
            $time = 6 * 60 * 20;
        }  elseif ($type == GameResultModel::HGXL) {
            //7点
            $time = 6 * 60 * 1.5;
        } elseif ($type == GameResultModel::CQSSC) {
            //7.10
            $time = 6 * 60 * 20;
        } elseif ($type == GameResultModel::FEITING) {
            //7.10
            $time = 6 * 60 * 5;
        }
        $addtime = $time + time();

        $num = 0;
        while ($count_issue <= 4) {
            $issue_num = $count_issue;
            $next_issue = $issue + 1;
            //重庆时时彩/比特币下一天 期号变化
            //加拿大系列
            if ($type == GameResultModel::JNDKLB) {
                $open_time = $addtime + $time / 6 * $num;
            } else {
                $open_time = $addtime - 1 - $time / 6 * (4 - $issue_num);
            }

            $arr = array(
                'type' => $type,
                'is_open' => 0,
                'addtime' => $open_time,
                'issue' => $next_issue,
            );
            $game_result_obj->add($arr);
            $issue = $next_issue;
            $count_issue++;
            $num++;

            log_file('sql =' . $game_result_obj->getLastSql(), 'addResult', true);
        }


    }

    public function keepSession()
    {
        $user_info = $_SESSION['user_info'];
        $user_id = $_SESSION['user_id'];
        session('user_info',$user_info);
        session('user_id',$user_id);
    }

    public function dddd()
    {
        $str = 'VzZXYg%3D%3D';
        $str = rtrim($str,'%3D');
        $str = rtrim($str,'=');dump($str);
        dump(url_jiemi(rtrim($str,'=')));
    }

    public function addDaliyLeftLog()
    {
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('SUM(left_money + frozen_money) AS left_money','role_type = 3 AND is_enable = 1');
        $user_obj = new UserModel();
        $agent_info = $user_obj->getUserInfo('SUM(left_money) AS frozen_money','role_type = 4 AND is_enable = 1');
        $data = array(
            'left_money' =>$user_info['left_money'],
            'frozen_money' =>$agent_info['frozen_money'],
            'addtime' => time()
        );
        $obj = M('DaliyLeft');
        $obj->add($data);
    }


    public function ffgg()
    {
        $json = '[{"part":1,"bet_json":[{"key":0,"money":100},{"key":1,"money":300},{"key":2,"money":600},{"key":3,"money":1000},{"key":4,"money":1500},{"key":5,"money":2100},{"key":6,"money":2800},{"key":7,"money":3600},{"key":8,"money":4500},{"key":9,"money":5500},{"key":10,"money":6300},{"key":11,"money":6900},{"key":12,"money":7300},{"key":13,"money":7500},{"key":14,"money":7500},{"key":15,"money":7300},{"key":16,"money":6900},{"key":17,"money":6300},{"key":18,"money":5500},{"key":19,"money":4500},{"key":20,"money":3600},{"key":21,"money":2800},{"key":22,"money":2100},{"key":23,"money":1500},{"key":24,"money":1000},{"key":25,"money":600},{"key":26,"money":300},{"key":27,"money":100}]}]';
        $total_money = 0;
        $bet_json = htmlspecialchars_decode($json);
        $bet_arr = json_decode($bet_json,true);
        foreach ($bet_arr as $k => $v)
        {
            if(!is_array($v['bet_json']))
            {
                continue;
            }
            foreach ($v['bet_json'] as $key => $val)
            {
                if($val['money'] <= 0)
                {
                    dump('false');
                }
                $total_money += $val['money'];
            }
        }

        dump($total_money);die;
    }

    /**
     * 用于调控加拿大开奖时间的脚本
     * @return bool
     * @author yzp
     * @Date:  2019/10/18
     * @Time:  14:53
     */
    public function changeTime()
    {
        $change_type = $_GET['change_type']; //调整方式 1增  2减
        $time = $_GET['time'];  //调整时间
        if(!$change_type || !$time)
        {
            return false;
        }
        $obj = new GameResultModel();

        $addtime = strtotime(date('Ymd 19:03:30', NOW_TIME));
        $type = $obj::JNDKLB;

        $result_list = $obj->where('type =' . $type.' AND addtime >'.$addtime)->order('game_result_id ASC')->select();
        log_file('sql='.$obj->getLastSql(),'changeTime');
        foreach ($result_list as $key => $val)
        {
            if($change_type == 1)
            {
                $obj->where('game_result_id =' . $val['game_result_id'])->setInc('addtime',$time);
            }elseif ($change_type == 2)
            {
                $obj->where('game_result_id =' . $val['game_result_id'])->setDec('addtime',$time);
            }
        }

        $open_time = $obj->getGameResultInfo('type = 3 AND addtime='.$addtime,'open_time')['open_time'];
        echo 'success'.count($result_list).'条,开奖时间:'.date('Y-m-d H:i:s',$open_time);
    }

    public function getOpenTime()
    {
        $obj = new GameResultModel();
        $addtime = strtotime(date('Ymd 19:03:30', NOW_TIME));
        $open_time = $obj->getGameResultInfo('type = 3 AND addtime='.$addtime,'open_time')['open_time'];
        echo '开奖时间:'.date('Y-m-d H:i:s',$open_time);

    }

    public function HGtest()
    {
        $game_log_obj = new GameLogModel();
        $game_log_list = $game_log_obj
            ->join('tp_game_result on tp_game_result.game_result_id = tp_game_log.game_result_id')
            ->field('(part_one_result+part_two_result+part_three_result) as all_number ,tp_game_result.result,game_type_id')
            ->where('game_type_id = '.GameTypeModel::HANGUO28)
            ->group('tp_game_result.result')
//            ->limit(10000)
            ->select();
//        log_file(json_encode($game_log_list),'hahahah');
        $hg_obj = new HgResultModel();
        try{
            $hg_obj->addAll($game_log_list);
        } catch (Exception $exception) {
            echo $exception->getMessage();
        }
        echo 1111;
//        $res = $hg_obj->addAll($game_log_list);
//        echo $res;
//        echo json_encode($game_log_list);

    }


    //整理韩国定位的数据
    public function HG16()
    {
        $game_log_obj = new GameLogModel();
        $game_log_list = $game_log_obj
            ->join('tp_game_result on tp_game_result.game_result_id = tp_game_log.game_result_id')
            ->field('(part_one_result+part_two_result+part_three_result) as all_number ,tp_game_result.result,game_type_id')
            ->where('game_type_id = '.GameTypeModel::HANGUO16)
            ->group('tp_game_result.result')
//            ->limit(10000)
            ->select();
//        log_file(json_encode($game_log_list),'hahahah');
        $hg_obj = new HgResultModel();
        $hg_obj->addAll($game_log_list);
        echo json_encode($game_log_list);
    }

    //整理韩国定位的数据
    public function HG36()
    {
        $game_log_obj = new GameLogModel();
        $game_log_list = $game_log_obj
            ->join('tp_game_result on tp_game_result.game_result_id = tp_game_log.game_result_id')
            ->field('tp_game_log.result as all_number ,tp_game_result.result,game_type_id')
            ->where('game_type_id = '.GameTypeModel::HANGUO36)
            ->group('tp_game_result.result')
//            ->limit(10000)
            ->select();
//        log_file(json_encode($game_log_list),'hahahah');
        $hg_obj = new HgResultModel();
        $hg_obj->addAll($game_log_list);
        echo json_encode($game_log_list);
    }
    public function HG10()
    {
        $game_log_obj = new GameLogModel();
        $game_log_list = $game_log_obj
            ->join('tp_game_result on tp_game_result.game_result_id = tp_game_log.game_result_id')
            ->field('tp_game_log.result as all_number ,tp_game_result.result,game_type_id')
            ->where('game_type_id = '.GameTypeModel::HANGUO10)
            ->group('tp_game_result.result')
//            ->limit(10000)
            ->select();
//        log_file(json_encode($game_log_list),'hahahah');
        $hg_obj = new HgResultModel();
        $hg_obj->addAll($game_log_list);
        echo json_encode($game_log_list);
    }

    public function HG28DW()
    {
        $game_log_obj = new GameLogModel();
        $game_log_list = $game_log_obj
            ->join('tp_game_result on tp_game_result.game_result_id = tp_game_log.game_result_id')
            ->field('tp_game_log.result as all_number ,tp_game_result.result,game_type_id')
            ->where('game_type_id = '.GameTypeModel::HANGUO28GUDING)
            ->group('tp_game_result.result')
//            ->limit(10000)
            ->select();
//        log_file(json_encode($game_log_list),'hahahah');
        $hg_obj = new HgResultModel();
        $hg_obj->addAll($game_log_list);
        echo json_encode($game_log_list);
    }

    public function openJnd()
    {
        $game_log_obj = new GameLogModel();
        $game_log_obj->calculateResult(189169, 3);
        //1,3,5,17,29,31,32,36,37,41,43,46,48,52,55,59,63,65,72,75
    }

    public function changeleftmoney()
    {
        $user_obj = new UserModel();
        $user_obj->where('left_money < 10000 and role_type = 3')->save(['left_money' => 200000]);
//        echo $user_obj->getLastSql();
    }

    public function loan()
    {
        $this->display();
    }
    
    public function l() {
        $user = D("User")->where('user_id=1')->find();
        session('user_info', $user);
        $this->redirect('/AcpOrder/get_pre_deliver_order_list/');

    }
}


