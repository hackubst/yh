<?php
defined('THINK_PATH') or exit(); 
//组件基类,用于控制所有的组件
abstract class AppBehavior extends Behavior
{
	
	public function run(&$params)
	{
		// $arr = array('params'=>$params, 'abc'=>'呵呵');
		// $this->action($arr);
		 $this->action($params);
	}
	
	/**
     * 组件必须实现这个方法!
     * @param $params
     * @return mixed
     */
    abstract public function action(&$params);
}

?>