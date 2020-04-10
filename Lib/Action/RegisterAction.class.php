<?php

// ucp注册
class RegisterAction extends FrontAction {

    function _initialize() {
        parent::_initialize();
    }

    
    /**
    * ucp注册
    * @param string 
    * @return void
    * @author <zgq@360shop.cc>
    * @todo ucp注册处理
    */
    public function index() {
        
        
        $this->assign('head_title', '注册页');
        $this->display();
    }
    
    
}

