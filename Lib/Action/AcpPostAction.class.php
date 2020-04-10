<?php
class AcpPostAction extends AcpAction{

	//帖子列表
	public function post_list()
	{

		$where = 'is_delete = 0';
		$post_obj = new PostModel();

		import('ORG.Util.Pagelist');
        $count = $post_obj->getGiftNum();
      
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));

        $post_obj->setStart($Page->firstRow);
        $post_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

  		$post_list = $post_obj->getPostList('', $where,'stick desc,creattime desc');
        $post_list = $post_obj->getListData($post_list);


        $this->assign('post_list', $post_list);
		$this->assign('head_title','帖子列表');
		$this->display();
	}

	//删除帖子
	public function delete_post()
	{

		$id = I('post.id',0,'int');

		if($id){

			$where = 'id ='.$id;

			$change = M('user_posts')->where($where)->save(['is_delete'=>1]);

			if($change){

				exit('success');
			}

			exit('faile');

		}
	}


	//置顶帖子
	public function set_post()
	{

		$id = I('post.id','','int');

		$tid = M('user_posts')->where('is_delete = 0 and stick = 1')->getfield('id');  //获取目前置顶的id

		$change = M('user_posts')->where('id = '.$tid)->save(['stick'=>0]); 

		if($id != $tid){

			$change_two = M('user_posts')->where('id = '.$id)->save(['stick'=>1]);
		} 

		if($change_two || $change){
			exit('success');
		}
		exit('faile');
		
	}	


	//相关帖子的打赏记录
	public function reward_list()
	{
		$post_id = I('get.post_id','','int');

		if($post_id) {

			// var_dum($my_menu_list);
			$where = 'post_id ='.$post_id.' and type=1';
			//查找相关的打赏记录
			$change_money = D('ChangeMoney');

			import('ORG.Util.Pagelist');
			$count = $change_money->getRecordtNum($post_id);
			$Page = new Pagelist($count,C('PER_PAGE_NUM'));
			$change_money->setStart($Page->firstRow);
	        $change_money->setLimit($Page->listRows);
	        $show = $Page->show();
	        $this->assign('show', $show);

	        $reword_list = $change_money->getRewordList('', $where,'changetime desc');
        	$reword_list = $change_money->getRewordData($reword_list);
        	$this->assign('reword_list',$reword_list);
        	$this->assign('head_title','打赏记录');

        	$this->display();

		}
	}



	//相关帖子的评论列表
	public function comment_list()
	{
		$post_id = I('get.post_id','','int');



		if($post_id) {

			// var_dum($my_menu_list);
			$where = 'post_id ='.$post_id.' and is_delete=0 ';
			//查找相关的打赏记录
			$comment_obj = D('Comment');

			import('ORG.Util.Pagelist');
			$count = D('Post')->getFieldNum('commentNum',$post_id);
		
			$Page = new Pagelist($count,C('PER_PAGE_NUM'));

			$comment_obj->setStart($Page->firstRow);
	        $comment_obj->setLimit($Page->listRows);
	        $show = $Page->show();
	        $this->assign('show', $show);

	        
	        $reword_list = $comment_obj->getRewordList('', $where,'creattime desc');
	       
        	$reword_list = $comment_obj->getRewordData($reword_list);


        	$this->assign('reword_list',$reword_list);
        	$this->assign('head_title','评论记录');
        	$this->display();

		}

	}



	//礼物列表
	public function gift_list()
	{
		$where = '';
		$gift_obj = new GiftModel();

		import('ORG.Util.Pagelist');
        $count = $gift_obj->getGiftNum();
      
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));

        $gift_obj->setStart($Page->firstRow);
        $gift_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

  		$gift_list = $gift_obj->getGiftList('', $where,'serial desc');
  		
        $gift_list = $gift_obj->getListData($gift_list);
        $this->assign('post_list', $gift_list);
		$this->assign('head_title','帖子列表');
		$this->display();

	}


	//下架礼物
	public function undercarriage()
	{
		$id = I('post.id','','int');
		if($id) {

			$isuse = M('gift')->where('gift_id ='.$id)->getField('isuse');
			if($isuse == 1){
				$isuse = 0;
			} elseif($isuse == 0) {
				$isuse = 1;
			}
			$change  = M('gift')->where('gift_id ='.$id)->save(['isuse'=>$isuse]);
			if($change){
				exit('success');
			}
			exit('faile');
		}
	}



	//批量下架
	public function set_all_input()
	{
		$arr = I('post.all_id');
		foreach ($arr as $key => $value) {
			$str .=$value.','; 
		}
		$str  = rtrim($str,',');
		$where = "gift_id in ( ".$str." ) ";
		$change = M('gift')->where($where)->save(['isuse'=>0]);

		if($change) {
			exit('success');
		} else{
			exit('faile');
		}
		
	
	}


	//批量上架
	public function set_all_out()
	{

		$arr = I('post.all_id');
		foreach ($arr as $key => $value) {
			$str .=$value.','; 
		}
		$str  = rtrim($str,',');
		$where = "gift_id in ( ".$str." ) ";
		$change = M('gift')->where($where)->save(['isuse'=>1]);
		if($change) {
			exit('success');
		} else{
			exit('faile');
		}
	}


	//批量删除
	public function delete_checkbox()
	{
		$arr = I('post.all_id');
		foreach ($arr as $key => $value) {
			$str .=$value.','; 
		}
		$str  = rtrim($str,',');

		$where = "gift_id in ( ".$str." ) ";
		$delete = M('gift')->where($where)->delete();
		if($delete) {
			exit('success');
		} else{
			exit('faile');
		}

	}


	//添加礼物
	public function add_gift()
	{

		$method = strtolower($_SERVER['REQUEST_METHOD']);

		if($method == 'post'){
			$data['gift_name'] = I('post.gift_name');
			$data['isuse'] = I('post.isuse');
			$data['money'] = I('post.money');
			$data['desc'] = I('post.desc');
			$add = D('Gift')->addGift($data);

			if($add) {
				$this->success('添加成功','/AcpPost/gift_list');
			} else {
				$this->error('添加失败');
			}
		} else {
			$this->assign('action','add');
			$this->assign('head_title','添加礼物');
			$this->display();

		}
	
	}


	//编辑礼物
	public function editor_gift()
	{
		$gift_id = I('get.gift_id','int');

		if($gift_id){
			$where = 'gift_id = '.$gift_id;
			$giftinfo = D('Gift')->getGiftInfo($where);
			$this->assign('post_list', $giftinfo);
			$this->assign('action','editor');
			$this->display();	exit;
		}

		$gift_id = I('post.gift_id','int');

		if($gift_id) {

			var_dump($_POST);

		}

	}


























}