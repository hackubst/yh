<?php
if(is_file(CONF_PATH.'upload.php')){
    C(include CONF_PATH.'upload.php');
}

return array(

'TEMPLET_HOST'          => 'http://crm.com',//CRM host
'CRM_APPID'             => '1',     //微信APPID
'APPID'                 => 'wx0ce15601ca221f39', 	//微信APPID
'APPSECRET'             => '4e0a38f2ea4b5dc17305ee8488056a20', //微信APPSECRET
//'SECRET'                => "heEo4GmTTor1rvpj", //ERP接口socket通信secret
'SECRET'                => "heEo4GmTTor1rvpj", //ERP接口socket通信secret
'WEB_ENABLE'            => true,
'VERSION'               => 201612002,

//'配置项'                 =>'配置值'
'DB_TYPE'               => 'mysql', //数据库类型
'DB_HOST'               => 'localhost', //数据库主机200.360shop.cc
'DB_NAME'               => 'kkl28', //数据库名称
'DB_USER'               => 'root', //数据库用户名
'DB_PWD'                => '0c7a11c3953fba72', //数据库密码
'DB_PORT'               => '3306', //数据库端口
'ADDR_URL'              => 'http://' . $_SERVER['HTTP_HOST'] . '/FrontOrder/order_submit/',
'DB_PREFIX'             => 'tp_', //数据表前缀
'IS_TEST'               => 1, //是否测试版
'ITEM_NAME'             => '商品', //item对应的中文名称，如商品、植灯等；
'USER_NAME'             => '用户', //user对应的中文名称，如用户、代理商、会员等；
'REDIS_HOST'            => 'localhost', //redis域名
'REDIS_PORT'            => '6379', //redis连接端口号
#'MEMCACHE_HOST'         => 'localhost', //Memcache域名
#'MEMCACHE_PORT'         => '11211', //Memcache连接端口号
#'DATA_CACHE_TIME'       => 86400, //Memcache缓存时间，单位秒
#'MEMCACHE_QUEUE_LENGTH' => 100, //Memcache缓存队列长度，每个客户之间独立
'URL_MODEL'             => second_domain() == 'mall-api' ? 0 : 2, //网址重写模式，详见ThinkPHP帮助
'ITEM_NUM_PER_PAGE'		=> 10,
'ITEM_NUM_PER_ACCOUNT_PAGE'		=> 8, //财务，充值，标金记录每页
'MCP_NUM_PER_ACCOUNT_PAGE'	=>25,//财务，充值，标金记录每页  商家版
'SHOP_NUM_PER_PAGE'		=> 10,
'ORDER_NUM_PER_PAGE'	=> 10,
'URL_HTML_SUFFIX'       => 'html', //网址重写文件扩展名，详见ThinkPHP帮助
'URL_CASE_INSENSITIVE'  => true, //忽略网址中的大小写，详见ThinkPHP帮助
'LOG_RECORD'            => false, //是否记录日志，这里不用记录日志，开发模式下会强制出现日志，正式运行时不要求写日志，详见ThinkPHP帮助
'DEFAULT_FILTER'        => 'htmlspecialchars,trim',
'DEFAULT_AJAX_RETURN'   => 'JSON', //AJAX返回数据格式
'OUTPUT_ENCODE'         => false,
'TMPL_ENGINE_TYPE'      => 'smarty',
'TMPL_ENGINE_CONFIG'    => array(
	'caching'               => false, //默认不要缓存，想要缓存的地方加重新定义设置下即可
	'cache_lifetime'        => 3600,
	'template_dir'          => TMPL_PATH,
	'compile_dir'           => CACHE_PATH,
	'cache_dir'             => TEMP_PATH
),
'IMG_DOMAIN'            => 'http://zrllz.cn',   
'DOMAIN'		=> 'http://zrllz.cn',   
'PER_PAGE_NUM'          => 10, //每页显示数
'NEWS_NUM_PER_PAGE'          => 15, //新闻每页显示数
'MIDDLE_IMG_SIZE'		=> '400', // 中图尺寸
'SMALL_IMG_HEIGHT'		=> '300', // 小图高度
'SMALL_IMG_WIDTH'		=> '300', // 小图宽度
'MIDDLE_IMG_SUFFIX'		=> '_m', // 中图后缀
'SMALL_IMG_SUFFIX'		=> '_s', // 小图后缀
'TMPL_EXCEPTION_FILE'   => './Tpl/Public/error.tpl', // 异常页面的模板文件

'TMPL_PARSE_STRING'     => array(
	'__JS__'                => '/Public/Js', // 公用的js路径
	'__CSS__'               => '/Public/Css', // 公用的css路径
	'__IMAGES__'            => '/Public/Images', // 公用的images路径
	'__PLG__'               => '/Public/Plugins', // 公用的Plugins路径
	'__ACPJS__'             => '/Public/Js/acp', // acp的js路径
	'__ACPCSS__'            => '/Public/Css/acp', // acp的css路径
	'__ACPIMAGES__'         => '/Public/Images/acp', // acp的images路径
	'__UCPJS__'             => '/Public/Js/ucp', // ucp的js路径
	'__UCPCSS__'            => '/Public/Css/ucp', // ucp的css路径
	'__UCPIMAGES__'         => '/Public/Images/ucp', // ucp的images路径
	'__HOMEJS__'            => '/Public/Js/home', // 前台的js路径
	'__HOMECSS__'           => '/Public/Css/home', // 前台的css路径
	'__HOMEIMAGES__'        => '/Public/Images/home', // 前台的images路径
	'__COMMJS__'            => '/Public/Js/common', // 公共页面的js路径
	'__COMMCSS__'           => '/Public/Css/common', // 公共页面的css路径
	'__COMMIMG__'           => '/Public/Images/common', // 公共页面的images路径
	'__KD__'                => '/KD', // 编辑器路径
	'__UE__'                => '/UE', // 编辑器路径
	'__UPLOAD__'            => '/Uploads', // 文件上传目录
	'__CSV__'               => '/Uploads/CsvDir', // csv文件生成目录
'__ZIP__'               => '/Uploads/ZipDir', // 数据包zip目录
),
// 'APP_LIST'				=> array(
// 	'Fenxiao'	=> array(
// 		'account'	=> array(
// 			'9'		=> '一级代理提成',
// 			'10'	=> '二级代理提成',
// 			'11'	=> '三级代理提成'
// 		),
// 	),
// ),
'URL_PREG'              =>  '/^https?:\/\/[a-z0-9_\.\/\?\+\*,;&=#%@!$-]+/i',//网址正则 by zhengzhen
);

//获取二级域名
function second_domain()
{
        $domain = explode('.', $_SERVER['HTTP_HOST']);
        $domain_level = count($domain) - 1;
        $second_domain = '';
        if ($domain_level > 1)
        {
                $second_domain = $domain[0];
        }

        return $second_domain == 'www' ? '' : $second_domain;
}

