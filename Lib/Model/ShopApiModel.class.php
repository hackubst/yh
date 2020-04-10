<?php
class ShopApiModel extends ApiModel
{

    /**
     * 获取店铺信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回店铺信息，失败退出返回错误码
     * @todo 获取店铺信息(merchant.shop.getShopInfo)
     */
    function getShopInfo($params)
    {
        $config = new ConfigBaseModel();

        return array(

            shop_name  => $config->getConfig('shop_name'),
            logo       => $config->getConfig('shop_logo'),
            share_img  => $config->getConfig('shop_share_img'),
            share_desc => $config->getConfig('shop_share_desc'),
        );
    }


    /**
     * 获取店铺分享信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回店铺分享信息，失败退出返回错误码
     * @todo 获取店铺信息(merchant.shop.getShareInfo)
     */
    function getShareInfo($params)
    {

        $config = new ConfigBaseModel();

        $share_info = array(
            title => $config->getConfig('shop_name'),
            desc  => $config->getConfig('shop_share_desc'),
            link  =>'http://www.beyondin.com',
            img   => $config->getConfig('shop_share_img'),
        );

        return $share_info;
    }

    /**
     * 修改店铺信息
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回成功，失败退出返回错误码
     * @todo 修改店铺信息(merchant.shop.updateShopInfo)
     */
    function updateShopInfo($params)
    {
        log_file(json_encode($params));
        if (empty($params))
        {
            return '没有任何修改';
        }

        $config = new ConfigBaseModel();
        $config->setConfigs($params);

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
                    'field'		=> 'shop_share_img',
                    'type'		=> 'string',
                    'required'	=> false,
                    'miss_code'	=> 41014,
                    'empty_code'=> 44014,
                    'type_code'	=> 45014,
                ),
                array(
                    'field'		=> 'shop_share_desc',
                    'type'		=> 'string',
                    'required'	=> false,
                    'miss_code'	=> 41014,
                    'empty_code'=> 44014,
                    'type_code'	=> 45014,
                ),
                array(
                    'field'		=> 'shop_logo',
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
