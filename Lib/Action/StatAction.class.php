<?php
//  统计基类，所有统计相关的类都继承该类
class StatAction extends GlobalAction {
	function valid_user($Key)
	{
	}

	/**
	 * 写日志文件，一般用于调试，log.txt文件在根目录
	 * 
	 * $str	写入的内容
	 */
	function log_result($str)
	{
		$file = fopen('log.txt', 'a');
		fwrite($file, $str . "\n" . date('Y-m-d H:i:s') . "\n\n");
		fclose($file);
	}
}
