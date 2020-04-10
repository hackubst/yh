<?php
/**
 *数组和语言包
 */

/*引用方式DEMO begin*/
#require_lang();
#global $lang;
#var_dump($lang);
/*引用方式DEMO end*/

//顶部菜单的类型
$GLOBALS['lang']['top_menu_type'][MENU_TYPE_ITEMS_CLASS] = '商品分类';
$GLOBALS['lang']['top_menu_type'][MENU_TYPE_ARTICLE_CLASS] = '文章分类';
$GLOBALS['lang']['top_menu_type'][MENU_TYPE_OUT_LINK] = '自定义链接';

//无线分销平台
$GLOBALS['lang']['app_name'][APP_SOURCE_PC] = '移动端订单';
$GLOBALS['lang']['app_name'][APP_SOURCE_WEIXIN] = '微信';
$GLOBALS['lang']['app_name'][APP_SOURCE_WEIBO] = '微博';
$GLOBALS['lang']['app_name'][APP_SOURCE_TXWEIBO] = '腾讯微博';
$GLOBALS['lang']['app_name'][APP_SOURCE_LAIWANG] = '来往';
$GLOBALS['lang']['app_name'][APP_SOURCE_OTHER] = '';