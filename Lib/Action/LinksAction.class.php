<?php

// 友情链接
class LinksAction extends FrontAction {

    function _initialize() {
        parent::_initialize();
    }

    
    /**
    * 友情链接
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 取出tp_link表isuse == 1 的数据内容
    */
    public function friendlinks()
	{
        $fields = 'link_name,link_url';
		$where = 'isuse=1';
		$link = new LinkModel();
		$linkList = $link->getAllLinkList('', '', $fields, $where);
		$this->assign('link_list', $linkList);
		
		$this->assign('head_title', '友情链接');
		$this->display();
    }
    
    
}

