<?php
class ApiAction extends GlobalAction
{
    private $api_type;
    private $api_name;

    public function _initialize()
    {
        $user_id = intval(session('user_id'));
        $session_id = session_id();
        $push_obj = new PushModel();
        $user_session = $push_obj->redisGet('push_' . $user_id . '_session');
        if ($user_session && $session_id) {
            if ($session_id != $user_session) {
                session('user_id', null);
                session('role_type', null);
                ApiModel::returnResult(40022, '','您的账号已在其他地方登录，已被迫下线');
            }
        }
        parent::_initialize();


    }

    /**
     * API接口器
     * @author 姜伟
     * @param void
     * @return void
     * @todo 过程如下：
     * 1、判断必要参数合法性；
     * 2、判断API接口权限；
     * 3、解析请求的接口类型和接口名；
     * 4、判断API接口参数合法性；
     * 5、调用相应接口；
     * 6、返回结果。
     */
    public function api()
    {
        // session('user_id','145230');

        #$Content = ob_get_contents();
        header("Content-Type:text/html; charset=utf-8");
//        log_file('in', 'api');

        //session有效期设置
        $user_id = intval(session('user_id'));
        if (isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] && $user_id) {
            $user_obj  = new UserModel();
            $user_info = $user_obj->getUserInfo('role_type');
            $role_type = $user_info['role_type'];
        }

        //存最后调接口时间
        $last_use_time = session('last_use_time');
        if($last_use_time == null){
            session('last_use_time',time());
        }elseif($last_use_time + 1800 < time()){
            session('last_use_time',null);
            session('user_id',null);
        }else{
            session('last_use_time',time());
        }



//        log_file('call api: 0' . arrayToString($_POST));
        $api_obj = new ApiModel();

        /** 1、判断必要参数合法性begin **/
        $params = $api_obj->getRequiredParams();
//        log_file('call api: 1' . $_SERVER['QUERY_STRING']);
        ini_set("display_errors", 1);
        error_reporting(E_ALL);
//        log_file('1PHPSESSID = ' . $_REQUEST['PHPSESSID']);
//        log_file('user_id = ' . session('user_id'));
//        log_file('file_data = ' . json_encode($_FILES));
        $api_obj = new ApiModel();
        $_REQUEST['PHPSESSID'] = Cookie('PHPSESSID');
        $res = $this->checkParamsValid($params);
//        log_file('call api: 2' . arrayToString($_POST));

        /** 1、判断必要参数合法性end **/

        /** 2、判断API接口权限begin **/
        //模拟请求
        $data = array(
            'api_name'  => 'msd.footManOrder.robOrder',
            #'api_name'            => 'msd.merchantOrder.replyOrder',
            'appid'     => '1',
            'order_id'  => '190',
            #'reply_state'            => '1',
            #'promise_time'            => '100',
            'PHPSESSID' => 'arb8htn4ilsa08vc1i337m9bs4',
        );
        $token         = $api_obj->generateSign($_POST);
        log_file($token.'----'.json_encode($_POST),'token');
        if ($token !=$_POST['token'] && $_POST['api_name'] != 'kkl.user.getRealInfo') {
            log_file($token.'----'.$_POST['token'],'token');
//            log_file('111','111');
            ApiModel::returnResult(-1,'','请求错误啦~');
        }
        $data['token'] = $token;
        #echo $token;die;
        #echo "<pre>";
        #print_r($data);
        #print_r($_REQUEST);
//        log_file('call api: 3' . arrayToString($_POST));

        if (PHP_OS == "LINUX" && !$api_obj->checkPriv()) {
            $api_obj->permissionDeny();
            exit;
        }
//        log_file('call api: 4' . arrayToString($_POST));
        /** 2、判断API接口权限end **/

        /** 3、解析请求的接口类型和接口名begin **/
        $this->parseRequest();
        /** 3、解析请求的接口类型和接口名end **/

        /** 4、判断API接口参数合法性begin **/
        $new_api_obj = new $this->api_type();//实例化接口请求的相关model类
        $params      = $new_api_obj->getParams($this->api_name);
//        log_file('call api: 5' . arrayToString($_POST));
        $params = $this->checkParamsValid($params);

        
        /** 4、判断API接口参数合法性end **/

//        $push_obj  = new PushModel();
//        $redis_obj = $push_obj->getRedisObj();
//        $session   = $redis_obj->get('pdl_user_' . $user_id . '_session');
//        log_file('user_id:' . $user_id .' session :' .  $session, 'redis');
//        log_file('session_id:' . session_id(), 'redis');
//        if ($session != session_id()) {
//            ApiModel::returnResult(1, null, 'PHPSESSID is not vaild');
//        }



        /** 5、调用相应接口start **/
        $result = $this->callApi($params);
//        log_file('call api: 6' . arrayToString($_POST));
        /** 5、调用相应接口end **/

        /** 6、返回结果start **/
        ApiModel::returnResult(0, $result);
        /** 6、返回结果end **/
    }

    //uploadImage
    public function uploadImage()
    {
        header("Content-Type:text/html; charset=utf-8");
        // $appid = $this->_request('appid');
        // // ApiModel::checkAppid($appid, '47008');

        $dir    = I('get.dir', 'app');
        $result = auto_upload_handler($_FILES['upfile'], $dir);
        if ($result['status']) {
            $arr = array(
                'code'      => 0,
                'file_path' => $result['img_url'],
            );
        } else {
            $arr = array(
                'code' => -1,
                'msg'  => $result['msg'],
            );
        }
        echo json_encode($arr);
        exit;}

    /**
     * 调用接口
     * @author 姜伟
     * @param void
     * @return void
     * @todo 调用接口，$this->api_type为类名，$this->api_name为方法名
     */
    public function callApi($params)
    {
        $obj       = new $this->api_type();
        $func_name = $this->api_name;
        return $obj->$func_name($params);
    }

    /**
     * 解析请求的接口类型和接口名
     * @author 姜伟
     * @param void
     * @return void
     * @todo 解析请求的接口类型和接口名，并赋值到$this->api_type和$this->api_name字段
     */
    public function parseRequest()
    {
        $requests       = explode('.', I('request.api_name'));
        $this->api_type = ucfirst($requests[1]) . 'ApiModel'; //首字母大写然后拼接ApiModel;
        $this->api_name = $requests[2];
    }

    /**
     * 判断特定API参数合法性
     * @author 姜伟
     * @param array $params 参数列表
     * @return boolean
     * @todo 判断特定API参数合法性，遍历$params，根据其设置的条件判断参数是否合法
     */
    public function checkParamsValid($params = array())
    {
        $new_params = array();
        foreach ($params as $k => $v) {
            //是否传值
            if (isset($v['required']) && $v['required'] && !isset($_REQUEST[$v['field']])) {
                ApiModel::returnResult($v['miss_code'], null, '缺少' . $v['field'] . '参数');
            }
            $param = $this->_request($v['field']);

            //是否为空
            if (isset($v['required']) && $v['required'] && $param == '' && isset($v['empty_code'])) {
                ApiModel::returnResult($v['empty_code'], null, '参数' . $v['field'] . '为空');
            }

            //类型
            if (isset($v['required']) && $v['required'] && $v['type'] && $v['type'] == 'file') {
                //文件，上传之
                $user_id = intval(session('user_id'));
                $param   = uploadImage($_FILES[$v['field']], '/user/' . $user_id);
                log_file('file_data: file_path = ' . $param);
            } elseif (isset($_REQUEST[$v['field']]) && isset($v['required']) && $v['required'] && $v['type'] && $v['type'] != 'string') {
                if ($v['type'] == 'int' || $v['type'] == 'float') {
                    //是否为数字
                    if (!ApiModel::checkNumeric($param)) {
                        //不是则退出返回错误码
                        ApiModel::returnResult($v['type_code'], null, '参数' . $v['field'] . '必须为数字');
                    }
                }
            }

            if (isset($_REQUEST[$v['field']])) {
                //最小长度
                if (isset($v['min_len']) && $v['type'] == 'string') {
                    //字符串最小长度
                    if (utf8_strlen($param) < $v['min_len']) {
                        //字符串长度小于最小长度，报错退出
                        ApiModel::returnResult($v['len_code'], null, '参数' . $v['field'] . '长度不得小于' . $v['min_len'] . '位');
                    }
                }

                //最大长度
                if (isset($v['max_len']) && $v['type'] == 'string') {
                    //字符串最大长度
                    if (utf8_strlen($param) > $v['max_len']) {
                        //字符串长度大于最大长度，报错退出
                        ApiModel::returnResult($v['len_code'], null, '参数' . $v['field'] . '长度不得大于' . $v['max_len'] . '位');
                    }
                }

                //当类型为整型或浮点型时，min_len的意义为最小数值
                if (isset($v['min_len']) && ($v['type'] == 'int' || $v['type'] == 'float')) {
                    //数字最小值
                    if ($param < $v['min_len']) {
                        //数字长度小于最小值，报错退出
                        ApiModel::returnResult($v['len_code'], null, '参数' . $v['field'] . '最小值不得小于' . $v['min_len']);
                    }
                }

                //当类型为整型或浮点型时，max_len的意义为最大数值
                if (isset($v['max_len']) && ($v['field'] == 'type' || $v['type'] == 'float')) {
                    //数字最大值
                    if ($param > $v['max_len']) {
                        //数字长度大于最大值，报错退出
                        ApiModel::returnResult($v['len_code'], null, '参数' . $v['field'] . '最大值不得大于' . $v['max_len']);
                    }
                }

                //函数验证
                if (isset($v['func']) && $v['func']) {
                    ApiModel::$v['func']($param, $v['func_code']);
                }

                $new_params[$v['field']] = $param;
            }
        }
        #echo "<pre>";
        #print_r($new_params);
        #static $i = 0;
        #$i ++;
        #if ($i == 2)
        #{
        #die;
        #}

        return $new_params;
    }

    public function login()
    {
        session('user_id', 10000);
        $arr = array(
            'session' => 10000,
        );
        echo json_encode($arr);
    }

    public function getItemList()
    {
        $user_id = session('user_id');
        log_file('user_id = ' . $user_id);
        $arr = array(
            'session' => $user_id,
        );
        echo json_encode($arr);
    }

    public function test1()
    {
        #$url = 'http://smartplant.yurtree.com/Api/login';
        $url    = 'http://world_cup.test.com/Api/login';
        $result = $this->getData($url);
        $ses    = explode('PHPSESSID=', $result);
        $ses    = explode(';', $ses[1]);
        $ses    = $ses[0];
        echo "<pre>";
        var_dump($result);
        echo 'aaa';

        //第二次请求
        #$url = 'http://smartplant.yurtree.com/Api/getItemList';
        $url = 'http://world_cup.test.com/Api/getItemList';
        var_dump($this->getData($url, $ses));
    }

    public function test2()
    {
        $url = 'http://smartplant.yurtree.com/Api/getItemList';
        var_dump($this->getData($url));
    }

    //获取https的get请求结果
    public function getData($c_url, $ses = '')
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
        //登录模拟
        #curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=" . $ses);
        #curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        #curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

        $vars = array(
            'PHPSESSID' => $ses,
        );
        $postfields = '';
        foreach ($vars as $key => $value) {
            $postfields .= urlencode($key) . '=' . urlencode($value);
        }

        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl); //捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        //echo 'PHPSESSID = ' . $_COOKIE['PHPSESSID'];
        return $tmpInfo; // 返回数据
    }

    public function bbb()
    {
        $this->animate_login();
    }

    public function animate_login()
    {
        $arr = array(
            'username' => '396162022@qq.com', //wx公众帐号
            'pwd'      => md5('bbw2014success'), //wx公众帐号密码
            'f'        => 'json',
        );

        if (isset($_POST['code'])) {
            $arr['imgcode'] = $_POST['code'];
        }

        $file = dirname(__FILE__) . '/cookie/cookie_' . $arr['username'] . '.txt';

        $headers = array(
            'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36',
            'Referer:https://mp.weixin.qq.com/',
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://mp.weixin.qq.com/cgi-bin/login');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arr));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if (!empty($arr['imgcode'])) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $file);
        }

        $result = curl_exec($curl);
        curl_close($curl);
        $token = explode('token=', $result);
        $token = explode('"', $token[1]);
        $token = $token[0];
        echo $result;
        echo "<br>";
        echo $token;
        $result1 = $this->getData('https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN&token=' . $token, $token);
        var_dump($result1);
    }

    public function get_wx_data()
    {
        $arr = array(
            'username' => '396162022@qq.com', //wx公众帐号
            'pwd'      => md5('bbw2014success'), //wx公众帐号密码
            'f'        => 'json',
        );

        if (isset($_POST['code'])) {
            $arr['imgcode'] = $_POST['code'];
        }

        $file = dirname(__FILE__) . '/cookie/cookie_' . $arr['username'] . '.txt';

        $headers = array(
            'Mozilla/5.0 (Linux; U; Android 2.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 MicroMessenger/4.5.255',
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://mp.weixin.qq.com/cgi-bin/login');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arr));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if (!empty($arr['imgcode'])) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, $file);
        }

        $result = curl_exec($curl);
        curl_close($curl);
        $token = explode('token=', $result);
        $token = explode('"', $token[1]);
        $token = $token[0];
        echo $result;
        echo "<br>";
        echo $token;
        $result1 = $this->getData('https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN&token=' . $token, $token);
        var_dump($result1);
    }
}
