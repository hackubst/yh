<?php
class MerchantApiModel extends ApiModel
{

    /**
     * 获取店铺信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回店铺信息，失败退出返回错误码
     * @todo 获取店铺信息(merchant.merchant.getShopInfo)
     */
    function getShopInfo($params)
    {

        $where = 'user_id = ' . session('user_id');
        $merchant_obj = new MerchantModel();
        $merchant_info = $merchant_obj->getMerchantInfo(
            $where,
            'merchant_id, shop_name, logo, share_img, share_desc'
        );

        // 店铺不存在，注册店铺
        if (!$merchant_info['merchant_id']) {

            $merchant_obj = new MerchantModel();
            $arr = array(
                user_id => session('user_id')
            );
            $merchant_obj->addMerchant($arr);

            $merchant_info = $merchant_obj->getMerchantInfo(
                $where,
                'merchant_id, shop_name, logo, share_img, share_desc'
            );
            return $merchant_info;
        }

        return $merchant_info;
    }


    /**
     * 获取店铺分享信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回店铺分享信息，失败退出返回错误码
     * @todo 获取店铺信息(merchant.merchant.getShareInfo)
     */
    function getShareInfo($params)
    {
        $where = 'user_id = ' . session('user_id');
        $merchant_obj = new MerchantModel();
        $merchant_info = $merchant_obj->getMerchantInfo(
            $where,
            'shop_name, share_img, share_desc'
        );

        $share_info = array(
            title => $merchant_info['shop_name'],
            desc => $merchant_info['share_desc'],
            link => 'http://www.beyondin.com',
            img  => $merchant_info['share_img']
        );

        return $share_info;
    }

    /**
     * 修改店铺信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回成功，失败退出返回错误码
     * @todo 修改店铺信息(merchant.merchant.updateShopInfo)
     */
    function updateShopInfo($params)
    {
        log_file(json_encode($params));
        if (empty($params))
        {
            return '没有任何修改';
        }

        //获取店铺基本信息
        $where = 'user_id = ' . session('user_id');
        $merchant_obj = new MerchantModel();
        $merchant_info = $merchant_obj->getMerchantInfo(
            $where,
            'merchant_id'
        );

        // 保存店铺信息
        $merchant_obj = new MerchantModel($merchant_info['merchant_id']);
        $merchant_obj->editMerchant($params);
        $merchant_obj->save();

        return '修改成功';
    }


    /**
     * 获取参数列表
     * @author zlf
     * @param
     * @return 参数列表
     * @todo 获取参数列表
     */

    function getParams($func_name)
    {
        $params = array(

                'updateShopInfo' => array(
                    array(
                        'field'		=> 'shop_name',
                        'type'		=> 'string',
                        'required'	=> false,
                        'miss_code'	=> 41014,
                        'empty_code'=> 44014,
                        'type_code'	=> 45014,
                    ),
                    array(
                        'field'		=> 'share_img',
                        'type'		=> 'string',
                        'required'	=> false,
                        'miss_code'	=> 41014,
                        'empty_code'=> 44014,
                        'type_code'	=> 45014,
                    ),
                    array(
                        'field'		=> 'share_desc',
                        'type'		=> 'string',
                        'required'	=> false,
                        'miss_code'	=> 41014,
                        'empty_code'=> 44014,
                        'type_code'	=> 45014,
                    ),
                    array(
                        'field'		=> 'lgo',
                        'type'		=> 'string',
                        'required'	=> false,
                        'miss_code'	=> 41014,
                        'empty_code'=> 44014,
                        'type_code'	=> 45014,
                    ),
                )

        );

        return $params[$func_name];
    }

}
