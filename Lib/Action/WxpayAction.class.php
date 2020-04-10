<?php
/**
 * Created by PhpStorm.
 * User: xyc
 * Date: 2018/9/6
 * Time: 13:40
 */
class WxpayAction extends GlobalAction{

    public function wxpay(){
        $id = I('id');
        $merchant_obj = new WxMerchantModel();
        $info = $merchant_obj->getWxMerchantInfo('wx_merchant_id ='.$id);
        $qr_pay_mode = 1;
        vendor("wxpay.WxPayPubHelper");
        //初始化
        $trade_type = $qr_pay_mode ? 'NATIVE' : 'JSAPI';
        if($info['price_fixed']){
            $total_fee = 0.01;
        }else{
            $total_fee = round(randomFloat(0.01, 2), 2);
        }
        
        $GLOBALS['wx_merchant_id'] = $id;
        $out_trade_no 	= time() . '_pay_' . $id;
        $body			= '支付';
        //使用jsapi接口
        $jsApi = new JsApi_pub();

        //=========步骤1：网页授权获取用户openid============
        $user_id = intval(session('user_id'));
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('openid');
        $openid = $user_info['openid'];

        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $unifiedOrder = new UnifiedOrder_pub();
        $notify_url = 'http://' . $_SERVER['HTTP_HOST'] . '/Front/wxpay_response';
        if ($trade_type != 'NATIVE')
        {
            $unifiedOrder->setParameter("openid","$openid");//商品描述
        }
        $unifiedOrder->setParameter("body", $body);//商品描述
        //自定义订单号，此处仅作举例
        $timeStamp = time();
        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $unifiedOrder->setParameter("total_fee", floatval($total_fee) * 100);//总金额
        $unifiedOrder->setParameter("notify_url",$notify_url);//通知地址
        $unifiedOrder->setParameter("trade_type", $trade_type);//交易类型


        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();
        if ($trade_type == 'NATIVE')
        {
            $code_url = $unifiedOrder->getCodeUrl();
            $arr = json_decode($jsApiParameters, true);
            $arr['code_url'] = $code_url;
            $jsApiParameters = json_encode($arr);
        }
//        dump(json_decode($jsApiParameters));die;
//        return $jsApiParameters;
        $jsApiParameters = json_decode($jsApiParameters, true);
        $this->assign('code_url', 'http://qr.liantu.com/api.php?text='.urlencode($jsApiParameters['code_url']));
        $this->display();
    }


    //微信转账到银行卡
    //    $bank_code
    //银行名称    银行ID
    //工商银行    1002
    //农业银行    1005
    //中国银行    1026
    //建设银行    1003
    //招商银行    1001
    //邮储银行    1066
    //交通银行    1020
    //浦发银行    1004
    //民生银行    1006
    //兴业银行    1009
    //平安银行    1010
    //中信银行    1021
    //华夏银行    1025
    //广发银行    1027
    //光大银行    1022
    //北京银行    1032
    //宁波银行    1056
    public function transfer_to_bank(){
        $bank_no = '这是银行开好';
        $realname = '持卡人姓名';
        $bank_code = '银行id，参照上面的注释';//银行id
        $amount = 1;//转账金额，单位：分
        $wxpay_obj = new WXPayModel();
        $res = $wxpay_obj->transferToBank($bank_no, $realname, $bank_code, $amount);
    }


    //微信支付 --- 走授权
    public function wx_auth_pay(){
        $id = I('id', 0, 'intval');
        if(!session('openid_'.$id)){
            //授权
            $merchant_obj = new WxMerchantModel();
            $info = $merchant_obj->getWxMerchantInfo('wx_merchant_id ='.$id);
            $appid = $info['appid'];
            $secret = $info['appsecret'];
            if (!isset($_GET['code']))
            {
                $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                log_file($redirect_uri);
                //获取授权码的接口地址
                $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($redirect_uri) .'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
                redirect($url);
            }
            else
            {
                $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $_GET['code'] .  "&grant_type=authorization_code";
                Vendor('Wxin.WeiXin');
                $data = file_get_contents($url);
                $data = json_decode($data, true);
                session('openid_'.$id, $data['openid']);
            }
        }
//        dump( session('openid_'.$id));
//        dump($id);
        $this->assign('id', $id);
        $this->display();
    }

    public function wx_mp_pay(){
//        if(IS_POST){
            $id = I('id', 0, 'intval');
            log_file($id, 'wx_mp_pay');
            if(!$id){
                $this->ajaxReturn(['code'=>1, 'msg'=>'支付失败']);
            }
            vendor("wxpay.WxPayPubHelper");
            //初始化
            $trade_type = 'JSAPI';
            $total_fee = 78;
            $GLOBALS['wx_merchant_id'] = $id;
            $out_trade_no 	= time() . '_pay_' . $id;
            $body			= '支付';
            //使用jsapi接口
            $jsApi = new JsApi_pub();

            //=========步骤1：网页授权获取用户openid============
            $openid = session('openid_'.$id);

            //=========步骤2：使用统一支付接口，获取prepay_id============
            //使用统一支付接口
            $unifiedOrder = new UnifiedOrder_pub();
            $notify_url = 'http://' . $_SERVER['HTTP_HOST'] . '/Front/wxpay_response';
            if ($trade_type != 'NATIVE')
            {
                $unifiedOrder->setParameter("openid","$openid");//商品描述
            }
            $unifiedOrder->setParameter("body", $body);//商品描述
            //自定义订单号，此处仅作举例
            $timeStamp = time();
            $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
            $unifiedOrder->setParameter("total_fee", floatval($total_fee) * 100);//总金额
            $unifiedOrder->setParameter("notify_url",$notify_url);//通知地址
            $unifiedOrder->setParameter("trade_type", $trade_type);//交易类型


            $prepay_id = $unifiedOrder->getPrepayId();
            //=========步骤3：使用jsapi调起支付============
            $jsApi->setPrepayId($prepay_id);

            $jsApiParameters = $jsApi->getParameters();
            $this->ajaxReturn(['code'=>0, 'jsApiParameters' => $jsApiParameters]);
//            $jsApiParameters = json_decode($jsApiParameters, true);

//        }
    }
}
