<?php

//商户前台装修,文章数据获取封装类
require_once('Lib/Model/GeneralModuleModel.class.php');

class ArticleModuleModel extends GeneralModuleModel {
    
    function _initialize() {
        parent::_initialize();
        
    }
    
     //文章模块id
    private $article_module_model_id = null;
    
    /** 
     * 函数名: get_article_sort_list
     * 功能: 获取文章分类
     * @param 无
     * @return array
         sort_type_id 分类id
         sort_type_name 分类名称
     * @author <zgq@360shop.cc>
     */
    public function get_article_sort_list()
    {
        return M('article_sort')->where('isuse = 1')->field('article_sort_id AS sort_type_id, article_sort_name AS sort_type_name')->order('serial')->select();
    }
       
    
 
}
