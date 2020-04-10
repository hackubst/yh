<?php
class HelpApiModel extends ApiModel
{
	/**
	 * 获取“关于潘朵拉”文章信息(plant.help.getAboutInfo)
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$about_info，失败退出返回错误码
	 * @todo 获取“关于潘朵拉”文章信息(plant.help.getAboutInfo)
	 */
	function getAboutInfo($params)
	{
		$generalArticle = new GeneralArticleModel();
		$help_info = $generalArticle->getArticleIdByTag('about', 'article_id, title, addtime');
	
		$article_txt_obj = D('article_txt');
		$article_txt = $article_txt_obj->where('article_id = ' . $help_info['article_id'])->find();
		$help_info['help_title'] = $help_info['title'];
		unset($help_info['title']);
	
		$help_info['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $help_info['path_img']);

		$help_info['help_contents'] = $article_txt['contents'];
		
		return $help_info;
	}

	/**
	 * 获取按分类分组的帮助标题列表(plant.help.getHelpList)
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$help_list，失败返回错误码
	 * @todo 获取按分类分组的帮助标题列表(plant.help.getHelpList)
	 */
	function getHelpList($params)
	{
		$helpCenter = new HelpCenterModel();
		$fields = 'help_sort_id,help_sort_name';
		$where = 'isuse=1';
		$helpCenterCategory = new HelpCenterCategoryModel();
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where);
		
		foreach($helpCenterCategoryList as $key => $val)
		{
			$fields = 'help_id,title';
			$where = 'isuse=1 AND help_sort_id=' . $val['help_sort_id'];
			$helpCenterCategoryList[$key]['article_list'] = $helpCenter->getHelpList('', '', $fields, $where);
		}
		
		return $helpCenterCategoryList;
	}

	/**
	 * 获取某帮助文章信息(plant.help.getHelpInfo)
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$help_info，失败返回错误码
	 * @todo 获取某帮助文章信息(plant.help.getHelpInfo)
	 */
	function getHelpInfo($params)
	{
		$id = intval($params['help_id']);
		$helpCenter = new HelpCenterModel();
		if(!$helpCenter->getTotal('help_id=' . $id))
		{
			returnResult(42042, null, '文章不存在');
		}

		$helpInfo = $helpCenter->getHelpInfo($id, 'title, addtime');
		#echo $helpCenter->getLastSql();
		#die;
		$helpInfo['help_contents'] = $helpCenter->getHelpContents($id);

		return $helpInfo;
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
		$params = array(
			'getHelpInfo'	=> array(
				array(
					'field'		=> 'help_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41044, 
					'empty_code'=> 44044, 
					'type_code'	=> 45044, 
				),
			),
		);

		return $params[$func_name];
	}
}
