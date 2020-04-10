<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */

/**
 * 时间转化工具，将数字转化为时间，仅限由当天秒数或当天分数转化为当天的“时：分：秒”或“时：分”格式，此工具仅做24小时制转化
 * 
 * Type:     modifier<br>
 * Name:     totime<br>
 * 
 * @author He Yubing
 * @param num $time_num 时间数
 * @param string $to_s  是否精确到秒（注：由分钟转化来的数字是不可以精确到秒的），比如：09:14:00，它转化为分钟的数字时为9 * 60 + 14 = 554，所以它不可以精确到秒，如果精确到秒后就会出错，比如此例如果精确到秒，它将得到00:09:14
 * @return string 
 */
function smarty_modifier_totime($time_num, $to_s = true)
{
	if ($to_s == true) {
		$hour = floor($time_num / 3600);
		$str = ($hour < 10) ? '0' . $hour : $hour;
		$minute = floor(($time_num % 3600) / 60);
		$str .= ($minute < 10) ? ':0' . $minute : ':' . $minute;
		$second = ($time_num % 3600) % 60;
		$str .= ($second < 10) ? ':0' . $second : ':' . $second;
	} else {
		$hour = floor($time_num / 60);
		$str = ($hour < 10) ? '0' . $hour : $hour;
		$minute = $time_num % 60;
		$str .= ($minute < 10) ? ':0' . $minute : ':' . $minute;
	}
	
	return $str;
} 

?>