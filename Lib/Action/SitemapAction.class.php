<?php

// 网站地图(网站导航)
class SitemapAction extends FrontAction {

    function _initialize() {
        parent::_initialize();
    }

    
    /**
    * 网站地图
    * @param string 
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 显示网站所有的url连接
    */
    public function index() {
        
        
        $head_title = $this->get_header_title('网站地图页');
        $this->assign('head_title', $head_title);
        $this->display();
    }
    
    
}

