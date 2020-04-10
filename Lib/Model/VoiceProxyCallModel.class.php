<?php
class VoiceProxyCallModel extends Model
{
    function __construct() {
    }


    const REQUEST_URL = 'https://api.ucpaas.com/2014-06-30/Accounts/bcadd12199e3497e8fb8a5d08ae8870e/safetyCalls/';//请求url
    const APPID = '937c016a1416464c8a86b292e160f07f';
    const TOKEN = '7ac6088e207240409657eccb56281983';

    function curl_post($url, $fields)
    {  

        $ch = curl_init();
        $postvars = '';
        
        $datetime = date('YmdHis');
        $Authorization = base64_encode('bcadd12199e3497e8fb8a5d08ae8870e:'.$datetime);
        
        $SigParameter = 'bcadd12199e3497e8fb8a5d08ae8870e'.self::TOKEN.$datetime;
        // echo $SigParameter;die;
        $SigParameter = strtoupper(MD5($SigParameter));
        $url .= '?sig='.$SigParameter;
        // echo $url.'<br>';
        // echo $Authorization.'<br>';
        // dump($fields);die;
        $fields = json_encode( $fields );
        // dump($fields);die;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json; charset=utf-8', 'Authorization: '.$Authorization, 'Accept:application/json'));
        curl_setopt($ch,CURLOPT_TIMEOUT, 30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        // if($response === false){
        //     echo curl_errno($ch);
        //     exit();
        // }
        // var_dump($response);die;
        curl_close ($ch);
        log_file('voice_call='.json_encode($response), 'voice_call', true);
        return $response;
    }

    //隐号绑定
    function binding($caller, $callee, $cityid){
        $post_data = array(
            'appId' => self::APPID,
            'caller' => $caller,
            'callee' => $callee,
            'cityId' => $cityid,
        );
        $result = $this->curl_post(self::REQUEST_URL.'allocNumber', $post_data);
        $result = json_decode($result, true);
        return $result;
    }

    //隐号解绑
    function unbundling($bindid, $cityid){
        $post_data = array(
            'appId' => self::APPID,
            'bindId' => $bindid,
            'cityId' => $cityid,
        );
        $result = $this->curl_post(self::REQUEST_URL.'freeNumber', $post_data);
        $result = json_decode($result, true);
        return $result;
    }

}

