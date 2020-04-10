<?php
/**
 * Created by PhpStorm.
 * User: xyc
 * Date: 2018/9/6
 * Time: 10:31
 */
    class AcpWxMerchantAction extends AcpAction{

    public function merchant_list(){

        $merchant_obj = new WxMerchantModel();
        //分页处理
        import('ORG.Util.Pagelist');
        $count = $merchant_obj->getWxMerchantNum();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $merchant_obj->setStart($Page->firstRow);
        $merchant_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);
        $merchant_list = $merchant_obj->getWxMerchantList('name, mcid, wx_merchant_id');
        $this->assign('merchant_list', $merchant_list);

        $this->display();
    }

    public function add_merchant(){

        $id = I('id', 0, 'intval');
        if($id){
            $merchant_obj = new WxMerchantModel();
            $info = $merchant_obj->getWxMerchantInfo('wx_merchant_id ='.$id);
            $this->assign('info', $info);
        }

        if(IS_POST){
            $appid = I('appid');
            $appsecret = I('appsecret');
            $mcid = I('mcid');
            $pay_key = I('pay_key');
            $name = I('name');
            $price_fixed = I('price_fixed');
            if(!$appid){
                $this->error('请填写appid');
            }
            if(!$appsecret){
                $this->error('请填写appsecret');
            }
            if(!$mcid){
                $this->error('请填写mcid');
            }
            if(!$pay_key){
                $this->error('请填写商户密钥');
            }
            if(!$name){
                $this->error('请填写商户名称');
            }
            $arr = array(
                'appid' => $appid,
                'appsecret' => $appsecret,
                'mcid' => $mcid,
                'pay_key' => $pay_key,
                'name' => $name,
                'price_fixed' => $price_fixed
            );
            $merchant_obj = new WxMerchantModel();
            if($id){
                $res = $merchant_obj->editWxMerchant('wx_merchant_id ='.$id, $arr);
                if($res !== false){
                    $this->success('修改成功');
                }
                $this->error('修改失败');
            }else{
                $res = $merchant_obj->addWxMerchant($arr);
                if($res){
                    $this->success('添加成功');
                }
                $this->error('添加失败');
            }
        }

        $this->display();
    }
}