<?php
// 空模块
class EmptyAction extends FrontAction {
	function _initialize() {
		parent::_initialize();
		
		//前台不用，直接跳转至淘宝专家服务上即可
		redirect('http://' . $_SERVER['HTTP_HOST']);
	}
}
