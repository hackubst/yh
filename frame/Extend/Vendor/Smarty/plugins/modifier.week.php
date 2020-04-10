<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * Smarty replace modifier plugin
 * 
 * Type:     modifier<br>
 * Name:     week<br>
 * Purpose:  simple search/replace
 * 
 * @author He Yubing
 * @param num $week_n 1~7七个数，分别对应星期一至星期天
 * @param string $pre  前缀，如：“星期”或“周”
 * @param string $ri 星期天用“天”还是用“日”
 * @return string 
 */
function smarty_modifier_week($week_n, $pre = '周', $ri = '日')
{
	if ($ri == '天') {
		$week_str = array('1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五', '6' => '六', '7' => '天');
	} else {
		$week_str = array('1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五', '6' => '六', '7' => '日');
	}
	
	$week_n = trim($week_n);
	
	if (!in_array($week_n, array('1', '2', '3', '4', '5', '6', '7'))) {
		return '';
	}
	
	return $pre . $week_str[$week_n];
} 

?>