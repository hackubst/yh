<?php
class JSSDK {
    private $appId;
    private $appSecret;
    public function __construct($appId, $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        //log_file('11 signPackage = ' . json_encode($signPackage), 'share');
        return $signPackage; 
    }
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function getJsApiTicket() {
        $config_obj = new ConfigBaseModel();
        $jsapi_ticket = $config_obj->getConfig('jsapi_ticket');
        $jsapi_ticket_expire_time = intval($config_obj->getConfig('jsapi_ticket_expire_time'));
        if ($jsapi_ticket && $jsapi_ticket_expire_time && ($jsapi_ticket_expire_time - 300) > time())
        {
            return $jsapi_ticket;
        }
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch);
        $result = json_decode($result, true);

        //保存ACCESS_TOKEN到数据库
        $config_obj->setConfig('jsapi_ticket', $result['ticket']);
        $config_obj->setConfig('jsapi_ticket_expire_time', NOW_TIME + 7000);
        #echo "<pre>";
        #print_r($result);
        #echo "</pre>";
        return isset($result['ticket']) ? $result['ticket'] : null;
    }

    public function getAccessToken()
    {
        $appid  = $this->appId;
        $secret = $this->appSecret; 
        $config_obj = new ConfigBaseModel();
        $access_token = $config_obj->getConfig('access_token');
        $access_token_expire_time = intval($config_obj->getConfig('access_token_expire_time'));
        if ($access_token && $access_token_expire_time && ($access_token_expire_time - 300) > time())
        {
            return $access_token;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $result = curl_exec($ch);
        $result = json_decode($result, true);

        //保存ACCESS_TOKEN到数据库
        $config_obj->setConfig('access_token', $result['access_token']);
        $config_obj->setConfig('access_token_expire_time', NOW_TIME + 7000);
        #echo "<pre>";
        #print_r($result);
        #echo "</pre>";
        return isset($result['access_token']) ? $result['access_token'] : null;
    }

    private function getAccessToken2() {
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("access_token.json"));
        if ($data->expire_time < time()) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    private function downloadWeixinFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        curl_close($ch);
        $imageAll = array_merge(array('header' => $httpinfo), array('body' => $package)); 
        return $imageAll;
    }

    private function saveWeixinFile($filename, $filecontent)
    {
        $local_file = fopen($filename, 'w');
        if (false !== $local_file){
            if (false !== fwrite($local_file, $filecontent)) {
                fclose($local_file);
            }
        }
    }

    public function getMedia($media_id, $filename = '')
    {
        if (!$filename) return false;
        $access_token = $this->getAccessToken();
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token .
            "&media_id=" . $media_id;
        $fileinfo = $this->downloadWeixinFile($url);
        if (strlen($fileinfo['body']) < 100) {
            return false;
        }
        $this->saveWeixinFile($filename, $fileinfo["body"]);
        return true;
    }
}
