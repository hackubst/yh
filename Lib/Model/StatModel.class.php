<?php
class StatModel extends Model
{
	//浏览器列表
	public static function getBrowserVersionList()
	{
		return array(
			'0'	=> '其他',
			'1'	=> 'MSIE6',
			'2'	=> 'MSIE7',
			'3'	=> 'MSIE8',
			'4'	=> 'MSIE9',
			'5'	=> 'MSIE10',
			'6'	=> 'Chrome',
			'7'	=> 'Firefox',
			'8'	=> 'Opera',
		);
	}

	//分辨率列表
	public static function getScreenList()
	{
		return array(
			'0'	=> '其它',
			'1'	=> '640 * 960',
			'2'	=> '800 * 600',
			'3'	=> '800 * 1200',
			'4'	=> '1024 * 600',
			'5'	=> '1024 * 768',
			'6'	=> '1024 * 1536',
			'7'	=> '1152 * 864',
			'8'	=> '1152 * 1728',
			'9'	=> '1280 * 720',
			'10'	=> '1280 * 768',
			'11'=> '1280 * 800',
			'12'=> '1280 * 900',
			'13'=> '1280 * 960',
			'14'=> '1280 * 1024',
			'15'=> '1280 * 1600',
			'16'=> '1280 * 2048',
			'17'=> '1360 * 768',
			'18'=> '1360 * 1024',
			'19'=> '1400 * 1050',
			'20'=> '1400 * 2100',
			'21'=> '1440 * 900',
			'22'=> '1600 * 600',
			'23'=> '1600 * 1024',
			'24'=> '1600 * 1200',
			'25'=> '1600 * 2400',
			'26'=> '1680 * 1050',
			'27'=> '1792 * 1344',
			'28'=> '1800 * 1440',
			'29'=> '1920 * 1080',
			'30'=> '1920 * 1200',
			'31'=> '1920 * 1440',
			'32'=> '2048 * 768',
			'33'=> '2048 * 1536',
			'34'=> '2048 * 3072',
			'35'=> '2304 * 864',
			'36'=> '2560 * 800',
			'37'=> '2560 * 1024',
			'38'=> '2800 * 1050',
			'39'=> '3200 * 1200',
			'40'=> '4096 * 1536',
		);
	}

	//操作系统列表
	public static function getOSList()
	{
		return array(
			'0'	=> '其他',
			'1'	=> 'Windows XP',
			'2'	=> 'Windows 7',
			'3'	=> 'Windows Vista',
			'4'	=> 'Windows 2000',
			'5'	=> 'Windows 2003',
			'6'	=> 'Mac',
			'7'	=> 'Unix',
			'8'	=> 'Linux',
		);
	}

	//语言列表
	public static function getLanguageList()
	{
		return array(
			'0'	=> '其他',
			'1'	=> '简体中文',		//zh-cn 汉语-中国大陆
			'2'	=> '繁体中文-中国台湾',
			'3'	=> '繁体中文-中国香港',
			'4'	=> '英语-中国香港',
			'5'	=> '英语-美国',
			'6'	=> '英语-英国',
			'7'	=> '英语-全球',
			'8'	=> '英语-加拿大',
		);
	}

	//终端列表
	public static function getTerminalList()
	{
		return array(
			'0'	=> '其他',
			'1'	=> 'ios PC',
			'2'	=> '手机',
			'3'	=> '安卓手机',
			'4'	=> 'iPhone手机',
			'5'	=> 'iPad',
		);
	}

	//浏览器ID转化文字
	public static function convertBrowserVersion($index)
	{
		$arr = self::getBrowserVersionList();
		return $arr[$index];
	}

	//分辨率ID转化文字
	public static function convertScreen($index)
	{
		$arr = self::getScreenList();
		return $arr[$index];
	}

	//操作系统ID转化文字
	public static function convertOS($index)
	{
		$arr = self::getOSList();
		return $arr[$index];
	}

	//语言ID转化文字
	public static function convertLanguage($index)
	{
		$arr = self::getLanguageList();
		return $arr[$index];
	}

	//终端ID转化文字
	public static function convertTerminal($index)
	{
		$arr = self::getTerminalList();
		return $arr[$index];
	}
}
