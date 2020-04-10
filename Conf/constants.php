<?php
//是否可用
define('CL_IS_ENABLE_YES', 1); //可用
define('CL_IS_ENABLE_NO', 2); //禁用
define('CL_IS_ENABLE_DEL', 3); //已删除

//是否默认
define('CL_IS_DEFAULT_YES', 1); //默认
define('CL_IS_DEFAULT_NO', 2); //不默认

//日志操作方式
define('CL_OP_TYPE_ADD', 1); //增加
define('CL_OP_TYPE_EDIT', 2); //修改
define('CL_OP_TYPE_DEL', 3); //删除
define('CL_OP_TYPE_SHOW', 4); //访问

//用户类型
define('CL_USER_TYPE_ADMIN', 1); //管理员
define('CL_USER_TYPE_USER', 2); //会员

//XAjax的JS路径
define("AJAX_PATH", "/Public/Js");//指定xajax_js目录所在目录

// 保存的日期子目录格式
define('DATE_FORMAT', 'Y-m');

/***** 文章分类定义 *****/
define('AZ_LINK_WEIXIN', 1);	//<移动端站收录>
define('AZ_LINK_WEBSITE', 2);	//<网站收录>
define('AZ_LINK_LINK', 3);		//<友链收录>

define('ARTICLE_SORT_TECH', 1);		//<开网店教程>
define('ARTICLE_SORT_SOURCE', 2);	//<网店通用素材>
define('ARTICLE_SORT_TOOLS', 3);	//<常用工具下载>
define('ARTICLE_SORT_KNOWLEDGE', 4);//<电商资讯>
define('ARTICLE_SORT_SEO', 5);		//<SEO知识库>
define('ARTICLE_SORT_NOTICE', 6);	//<网站公告>
define('ARTICLE_SORT_WEIXIN_FX', 7);//<移动端分销教程>
define('ARTICLE_SORT_FAQ', 8);		//<新手常见问题>

//特定文章标记
define('TAG_ABOUT_US', 'about_us');             //关于我们
define('TAG_PRIVACY_POLICY', 'privacy_policy'); //隐私条款
define('TAG_CONTACT_US', 'contact_us');         //联系我们
define('TAG_SITE_MAP', 'site_map');             //网站地图

//菜单类型常量
define('MENU_TYPE_ITEMS_CLASS', 1); //商品分类
define('MENU_TYPE_ARTICLE_CLASS', 2); //文章分类
define('MENU_TYPE_OUT_LINK', 3); //自定义链接

//无线APP端的id
define('APP_SOURCE_PC', 0); //PC端
define('APP_SOURCE_WEIXIN', 1); //微信
define('APP_SOURCE_WEIBO', 2); //新浪微博
define('APP_SOURCE_TXWEIBO', 3); //微讯微博
define('APP_SOURCE_LAIWANG', 4); //来往
define('APP_SOURCE_OTHER', 9); //其它

?>