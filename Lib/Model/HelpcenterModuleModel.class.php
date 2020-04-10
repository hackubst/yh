<?php

//商户前台装修,帮助中心封装类
require_once('Lib/Model/GeneralModuleModel.class.php');
require_once('Lib/Model/HelpCenterCategoryModel.class.php');
require_once('Lib/Model/HelpCenterModel.class.php');
require_once('Lib/Model/ConfigBaseModel.class.php');

class HelpcenterModuleModel extends GeneralModuleModel {
    
    function _initialize() {
        parent::_initialize();
        
    }
    
    
    /** 
     * 功能: 获取帮助中心信息
     * @param 无
     * @return array
     * @author <zgq@360shop.cc>
     */
    public function get_helpcenter_info()
    {
        $HelpCenterCategoryModel = new HelpCenterCategoryModel();
        $helpcenter_info = $HelpCenterCategoryModel->getHelpCenterCategoryList('', 'serial', 'help_sort_id,help_sort_name', 'isuse = 1');
        
        $HelpCenterModel = new HelpCenterModel();
        foreach ($helpcenter_info as $key => $value) 
        {
            $helpcenter_info[$key]['help_list'] = $HelpCenterModel->getHelpList('',  'serial', 'help_id,title,help_sort_id,help_tag', 'isuse = 1 AND help_sort_id = ' . $value['help_sort_id']);
        }
      
        //获取配置信息
        $ConfigBaseModel = new ConfigBaseModel();
        $helpcenter_info['customer_service_telephone']      = $ConfigBaseModel->getConfig('customer_service_telephone');
        $customer_service_working_time   = $ConfigBaseModel->getConfig('customer_service_working_time');
        $customer_service_working_time = json_decode($customer_service_working_time,true);
        $helpcenter_info['customer_service_working_time']   = '工作日:'. $customer_service_working_time['w1'] .' - '.
                                                              $customer_service_working_time['w2'] .' AM:'.
                                                              $customer_service_working_time['t1'] .' - '. $customer_service_working_time['t2'];
        $helpcenter_info['customer_service_email']          = $ConfigBaseModel->getConfig('customer_service_email');
        $helpcenter_info['customer_service_address']        = $ConfigBaseModel->getConfig('customer_service_address');
        //dump($helpcenter_info);
       
        return $helpcenter_info;
    }
}
