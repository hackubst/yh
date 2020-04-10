<?php
class MonentApiModel extends ApiModel
{
	/**
	 * 发帖
	 * @author cai
	 * @param content string
	 * @return pic string
	 * @todo 验证必要参数合法性
	 */
	public function posting($params)
	{
		$data['user_id'] = intval(session('user_id')) ? ntval(session('user_id'))  : 62277;
		$data['province_id'] = $params['province_id'] ? $params['province_id'] : 0;
		$data['city_id'] = $params['city_id'] ? $params['city_id'] : 0;
		$data['area_id'] = $params['area_id'] ? $params['area_id'] : 0;
		$data['pics'] = $params['pics'] ? $params['pics'] : '';

		if($data['pics'])
		{
			$length = count(explode(',', $data['pics']));
			if($length > 9){
				ApiModel::returnResult(-1, [], '图片数量超出');exit();	
			}
		}
		$data['content'] = $params['content'];
		$post_obj = new PostModel();
		$result = $post_obj->addPoat($data);
		if($result) {
			ApiModel::returnResult(0, '成功');
		} else {
			ApiModel::returnResult(-1, [], '失败');
		}	
	}



	//对帖子进行点赞或是取消点赞
	public function giveLike($params)
	{
		$data['post_id'] = $params['post_id'];
		$data['user_id'] = intval(session('user_id')) ? ntval(session('user_id'))  : 62277;
		$like_obj = new LikeModel();
		if($like_obj->likeOperation($data)) {
			ApiModel::returnResult(0, '成功');
		} else {
			ApiModel::returnResult(-1, [], '失败');
		}		
	}


	//对帖子进行评论
	public function commentToPost($params)
	{
		$data ['user_id'] = intval(session('user_id')) ? ntval(session('user_id'))  : 62277;
		$data['post_id'] = intval($params['post_id']); //帖子id
		$data['p_id'] = $params['p_id'] ? $params['p_id'] : 0;
	
		$data['content'] = htmlspecialchars($params['content']);
		$commentPost_obj = new CommentModel();

		if($commentPost_obj->addData($data))
		{
			ApiModel::returnResult(0, '成功');
		} else {
			ApiModel::returnResult(-1, [], '失败');
		}


	}


	//余额打赏帖子
	public function rewardPost($params)
	{
		$data['user_id'] = intval(session('user_id')) ? ntval(session('user_id'))  : 62277;
		$data['gift_id'] = intval($params['gift_id']);
		$data['num'] = $params['num'];
		$data['post_id'] = $params['post_id'];

		$changeMoney = new ChangeMoneyModel();

		if($result = $changeMoney ->addData($data)){

			ApiModel::returnResult(0, '成功');

		} else {
			ApiModel::returnResult(-1, [], '打赏失败');
		}

	}



	//获取礼物接口
	public function getGifts()
	{
		$gift_obj = new GiftModel();
		$result = $gift_obj -> getAllGiftInfo('isuse = 1','gift_name,gift_id,pic,money');
		ApiModel::returnResult(0, $result);
	}


	









	 /**
     * 获取参数列表
     * @author clk
     * @param
     * @return 参数列表
     * @todo 获取参数列表
     */
    function getParams($func_name)
    {
        $params = array(
            'posting'	=> array(
                array(
                    'field'		=> 'content',
                    'type'		=> 'string',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),

                array(
                    'field'		=> 'pics',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'province_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'city_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'area_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
            ),
            'giveLike'	=> array(
                array(
                    'field'		=> 'post_id',
                    'type'		=> 'string',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
            ),
            'commentToPost'	=> array(
                array(
                    'field'		=> 'content',
                    'type'		=> 'string',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
                array(
                    'field'		=> 'post_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
                array(
                    'field'		=> 'p_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
                
            ),

            'rewardPost'	=> array(
                array(
                    'field'		=> 'num',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
                array(
                    'field'		=> 'gift_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
                array(
                    'field'		=> 'post_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ), 
            ),

        );

        return $params[$func_name];
    }






}


