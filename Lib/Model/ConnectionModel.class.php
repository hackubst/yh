<?php
class ConnectionModel extends Model
{

    function __construct() {}

	const CURL_TIMEOUT = 30;

    const OP_CARD_QUERY = "00";
    const OP_INTEGRAL_INCREASE = "01";
    const OP_INTEGRAL_DECREASE = "02";
    const OP_CREATE_MEMBER = "03";
    const OP_PAY = "05";
    const OP_REFUND = "06";
    const OP_GET_CAT_INFO = "08";
    const OP_GET_ITEM_INFO = "09";
    const OP_TEST = "10";
    const OP_SYNC_ORDER_INFO = "11";
    const OP_GET_DEPT_INFO = "12";
    const OP_BIND_CARD = "13";
    const OP_GET_ORDER_STATUS = "16";
    const OP_SYNC_ORDER_STATUS = "17";
    const OP_GET_TICKET_LIST = "18";
    const OP_GET_TICKET_INFO = "19";
    const OP_TICKET_PAY = "20";

    // 发送接受 socket 数据
    // @author wsq
    protected function sendToSocket($msg, $debug=false) 
    {
        // 调试信息
        if ($debug) var_dump($msg);
        trace($msg, '----------------socket-------------');

        if (!$msg) return false;

        // 从接口请求数据
        // WARN 
        // 注意 nginx_lua 那边key的设置，
        // 否则无法从接口获取数据
        $url = C('IMG_DOMAIN') . '/socket?msg='.$msg;

        // 初始化一个 cURL 对象
        $curl = curl_init(); 
        // 设置你需要抓取的URL
        //curl_setopt($curl, CURLOPT_URL, 
        curl_setopt($curl, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::CURL_TIMEOUT); 
        // 运行cURL，请求网页
        $data = curl_exec($curl);
        // 关闭URL请求
        $status = curl_getinfo($curl,CURLINFO_HTTP_CODE); 
        curl_close($curl);

        // 调试信息显示获得的数据
        if ($debug) var_dump($data);
        trace($data, '----------------socket-------------');

        if (intval($status) == 200) {
            return json_decode($data, true);
        } else {
            return false;
        }
    }

    // 创建通信数据格式
    // @author wsq
    protected function createData($params) {
        // 请求数据包长度不超过99999、返回数据包长度不超过99999字符，
        // 加密字符串为RJ2AlQddJfyVZeUa15qL，
        // MD5值	=	MD5函数（请求类型 +	请求内容	+	加密字符串）
        if (is_array($params) && $params['type'] && $params['msg']) {

            // todo: 上线从配置中获取
            $secret = C('SECRET');

            $content = $params['type'].$params['msg'].$secret;
            $result = md5($content).$params['type'].$params['msg'];
            $length = sprintf("%'.05d", strlen($result));

            return $length.$result;
        } else {
            return false;
        }
    }

    // 获取查询结果
    // @author wsq
    public function getResult($params) {
        return $this->sendToSocket($this->createData($params));
    }

    // 解压缩数据
    // @author wsq
    public static function decodeCompressData($data) {
        return json_decode(gzdecode(hex2bin($data)), true);
    }

}
