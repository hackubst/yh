<?php
class MallApiModel extends ApiModel
{
	/**
	 * 获取商城首页轮播图片列表
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$img_list，失败退出返回错误码
	 * @todo 获取商城首页轮播图片列表
	 */
	function getMallHomeImgList($params)
	{
		return $GLOBALS['config_info']['CUST_FLASH_LIST'];
	}

	/**
	 * 获取系统热门搜索词(plant.mall.getHotKeywords)
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$hot_keywords，失败退出返回错误码
	 * @todo 获取系统热门搜索词(plant.mall.getHotKeywords)
	 */
	function getHotKeywords($params)
	{
		return $GLOBALS['config_info']['HOT_KEYWORDS'];
	}

	/**
	 * 获取参数列表
	 * @author 姜伟
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		return false;
	}
}
