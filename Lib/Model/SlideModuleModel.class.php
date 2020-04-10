<?php

//商户前台装修,幻灯片信息封装类
require_once('Lib/Model/GeneralModuleModel.class.php');

class SlideModuleModel extends GeneralModuleModel {
    
    function _initialize() {
        parent::_initialize();
        
    }
    
    
    /** 
     * 函数名: get_itemrank_info()
     * 功能: 获取模块数据
     * @param 
     * @return array
        title   公告标题
        url     链接
     * @author <zgq@360shop.cc>
     */
    public function get_slide_info() 
    {
        //幻灯片
       
        
        return $itemrank_info;
    }
    
   
}