<?php

class IndexAppAction extends GlobalAction
{
    function _initialize()
    {
        parent::_initialize();

        //获取token，体验版免一键登录有用
        $token = $_REQUEST['u_token'];
        $user_obj = new UserModel();
        if ($token) {
            $res = $user_obj->getRealUserInfo($token);
            $res = json_decode($res,true);
//		    echo $res;die;
            if ($res['code'] === 0) {
                //真实用户
                $real_info = $res['data'];
                //根据token获取当前用户
                $user_info = $user_obj->getUserInfo('mobile,user_id',['token' => $token]);
                //判断当前token是否有用户
                if ($user_info && $user_info['mobile'] == $real_info['mobile']) {
                    session('user_id',$user_info['user_id']);
//                    echo session('user_id');die;
                } else {//添加用户
                    $user_id = $user_obj->addUser([
                        'mobile'    =>  $real_info['mobile'],
//                        'again_password'    =>  $real_info['again_password'],
                        'password'    =>  $real_info['password'],
                        'left_money'    =>  200000,
                        'id'    =>  $real_info['id'],
                        'nickname'    =>  $real_info['nickname'],
                        'token' =>  $real_info['token']
                    ]);
                    session('user_id',$user_id);
                }
            }

//		    echo 111;die;
        }
    }

    //首页
    public function index()
    {
        $this->display();

    }

    /**
     * 分享链接 下载app
     */
    public function share_app()
    {
        $article_obj = new ArticleModel();
        $content = $article_obj->where('article_tag = "download_app"')->getField('description');
        $download_url = $GLOBALS['config_info']['APP_DOWNLOAD_URL'];
        $content = htmlspecialchars_decode($content);
        $this->assign('download_url', $download_url);
        $this->assign('share_text', $content);
        $this->display();
    }
}
