<?php
/*class OAuthException extends Exception {
     // pass
}*/
/**
* @package 自定义微信api接口
* @author 张光强
* @version 1.0
*/
class WxAuthV2 {
     public $access_token;
     public $host           = "https://api.weixin.qq.com/cgi-bin/";
     public $timeout        = 30;
     public $connecttimeout = 30;
     public $ssl_verifypeer = FALSE;
     public $format         = '?';
     public $decode_json    = TRUE;
     public $http_info;
     public static $boundary= '';


     function __construct($access_token = NULL) 
     {
        $this->access_token = $access_token;
     }


     function base64decode($str) 
     {
        return base64_decode(strtr($str.str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
     }


     /**
     * GET wrappwer for oAuthRequest.
     *
     * @return mixed
     */
     function get($url, $parameters = array()) 
     {
        $response = $this->oAuthRequest($url, 'GET', $parameters);
        if ($this->format === '?' && $this->decode_json) 
        {
          return json_decode($response, true);
        }
        return $response;
     }


     /**
     * POST wreapper for oAuthRequest.
     *
     * @return mixed
     */
     function post($url, $parameters = array(), $multi = false) 
     {
          $response = $this->oAuthRequest($url, 'POST', $parameters, $multi );
          if ($this->format === '?' && $this->decode_json) 
          {
            return json_decode($response, true);
          }
          return $response;
     }


     /**
     * DELTE wrapper for oAuthReqeust.
     *
     * @return mixed
     */
     function delete($url, $parameters = array()) {
          $response = $this->oAuthRequest($url, 'DELETE', $parameters);
          if ($this->format === 'json' && $this->decode_json) 
          {
            return json_decode($response, true);
          }
          return $response;
     }


     /**
     * Format and sign an OAuth / API request
     *
     * @return string
     * @ignore
     */
     function oAuthRequest($url, $method, $parameters, $multi = false) {

          if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) 
          {
            $url = "{$this->host}{$url}{$this->format}"."access_token=".$this->access_token;
          }

          switch ($method) 
          {
            case 'GET':
              $url = $url . '&' . http_build_query($parameters);
              return $this->http($url, 'GET');
              default:
              $headers = array();
              if (!$multi && (is_array($parameters) || is_object($parameters)) ) 
              {
                $body = $this->ch_json_encode($parameters);
              } 
              else 
              {
                $body = self::build_http_query_multi($parameters);
                $headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
              }
              return $this->http($url, $method, $body, $headers);
          }
     }


     /**
     * Make an HTTP request
     *
     * @return string API results
     * @ignore
     */
     function http($url, $method, $postfields = NULL, $headers = array()) 
     {
        #var_dump('url:' . $url);
		#echo "<br>";
        #var_dump('method:' . $method);
		#echo "<br>";
        #var_dump($postfields);
		#echo "<br>";
        #var_dump('headers:' .$headers);
		#echo "<br>";

        $this->http_info = array();
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_ENCODING, "");
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) 
        {
           case 'POST':
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($postfields)) 
            {
              curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
              $this->postdata = $postfields;
            }
            break;
        }
        curl_setopt($ci, CURLOPT_URL, $url );
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        $response = curl_exec($ci);
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        curl_close ($ci);
        return $response;
     }


     /**
     * Get the header info to store.
     *
     * @return int
     * @ignore
     */
     function getHeader($ch, $header) 
     {
        $i = strpos($header, ':');
        if (!empty($i)) 
        {
          $key   = str_replace('-', '_', strtolower(substr($header, 0, $i)));
          $value = trim(substr($header, $i + 2));
          $this->http_header[$key] = $value;
        }
        return strlen($header);
     }


     /**
     * @ignore
     */
     public static function build_http_query_multi($params) 
     {
        if (!$params) 
          return '';

        uksort($params, 'strcmp');

        $pairs          = array();
        self::$boundary = $boundary = uniqid('------------------');
        $MPboundary     = '--'.$boundary;
        $endMPboundary  = $MPboundary. '--';
        $multipartbody  = '';

        foreach ($params as $parameter => $value) 
        {

          if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) 
          {
            $url            = ltrim( $value, '@' );
            $content        = file_get_contents( $url );
            $array          = explode( '?', basename( $url ) );
            $filename       = $array[0];
            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
            $multipartbody .= "Content-Type: image/unknown\r\n\r\n";
            $multipartbody .= $content. "\r\n";
          } 
          else 
          {
            $multipartbody .= $MPboundary . "\r\n";
            $multipartbody .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
            $multipartbody .= $value."\r\n";
          }

        }

        $multipartbody .= $endMPboundary;
        return $multipartbody;
     }


     /**
      * 对数组和标量进行 urlencode 处理
      * 通常调用 wphp_json_encode()
      * 处理 json_encode 中文显示问题
      * @param array $data
      * @return string
      */
     function wphp_urlencode($data) 
     {
        if (is_array($data) || is_object($data)) 
        {
          foreach ($data as $k => $v) 
          {
            if (is_scalar($v)) 
            {
              if (is_array($data)) 
              {
                $data[$k] = urlencode($v);
              } 
              else if (is_object($data)) 
              {
                $data->$k = urlencode($v);
              }
            } 
            else if (is_array($data)) 
            {
              $data[$k] = $this->wphp_urlencode($v); //递归调用该函数
            } 
            else if (is_object($data)) 
            {
              $data->$k = $this->wphp_urlencode($v);
            }
          }
        }
        return $data;
     }


     /**
      * json 编码
      *
      * 解决中文经过 json_encode() 处理后显示不直观的情况
      * 如默认会将“中文”变成"\u4e2d\u6587"，不直观
      * 如无特殊需求，并不建议使用该函数，直接使用 json_encode 更好，省资源
      * json_encode() 的参数编码格式为 UTF-8 时方可正常工作
      *
      * @param array|object $data
      * @return array|object
      */
     public function ch_json_encode($data) 
     {
       	$ret = $this->wphp_urlencode($data);
       	$ret = json_encode($ret);
       	return urldecode($ret);
     }
}

class WxApi
{
	 protected $oauth;

    /**
      * 生成带参数二维码
	  *  https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
      * 根据ticket换取二维码
	  *  https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=TICKET
	*/
     function generate_qr_code($data)
     {
     	$result = $this->oauth->post('qrcode/create', $data);
	return $result;
     }
	 
     /**
     * 静态方法，获取access_token
     *
     * @access public
     * @param string $appid
     * @param string $secret
     * @return void
     */
     public static function getAccessToken($appid, $secret)
     {
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		#echo "<pre>";
		#print_r($result);
		#echo "</pre>";
		return isset($result['access_token']) ? $result['access_token'] : null;
     }

     /**
     * 构造函数
     *
     * @access public
     * @param mixed $access_token OAuth认证返回的token
     * @return void
     */
     function __construct($access_token)
     {
        $this->oauth = new WxAuthV2($access_token);
     }

     /**
     * 上传多媒体文件
     *
     * @access public
     * @param string $access_token 访问令牌
     * @param string $filepath 文件绝对路径
     * @param string $type 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @return 成功返回多媒体文件media_id，失败返回0
     */
     function uploadMedia($access_token, $filepath, $type)
     {
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
		$data = array("media"  => "@" . $filepath);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);

		return $output;
     }

	function downloadWeixinFile($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);    
		curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$package = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		curl_close($ch);
		$imageAll = array_merge(array('header' => $httpinfo), array('body' => $package)); 
		return $imageAll;
	}
	 
	function saveWeixinFile($filename, $filecontent)
	{
		$local_file = fopen($filename, 'w');
		if (false !== $local_file){
			if (false !== fwrite($local_file, $filecontent)) {
				fclose($local_file);
			}
		}
	}

     /**
     * 下载多媒体文件
     *
     * @access public
     * @param string $media_id 要下载的多媒体文件ID
     * @param string $filename 保存的文件名
     * @return 成功返回多媒体文件media_id，失败返回0
     */
     function getMedia($media_id, $filename = '')
     {
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=oFEASzAKBvwGOdQYCeC2Rdeb5yyv7EB8ZzgvMsnbJXNTzqKASv8U_dmVXtc6rvOX3Ow1Ia0Bly1zB0nzm6HhrlgB2znJP3nUo4oP0wBYgLK1WggDg8YzFrC3gGWr7DECJYVjsR0MqhPCwrBVOHicFA&media_id=y-4YcnM4AOGyMZK3NGS84ryt1rBoXCRIUZbb3U6X_79o0fsyUI8lvVxjKsSZJ5WK";
		$fileinfo = $this->downloadWeixinFile($url);
		$filename = time() . '.jpg';
		$this->saveWeixinFile($filename, $fileinfo["body"]);
	 }

     /**
      * 查询分组 
      * https://api.weixin.qq.com/cgi-bin/groups/get?access_token=ACCESS_TOKEN
     */
     function groups_get()
     {
        $params = array();
        return $this->oauth->get('groups/get', $params);
     }


     /**
      * 创建分组  
      * https://api.weixin.qq.com/cgi-bin/groups/create?access_token=ACCESS_TOKEN
      * $name  分组名字（30个字符以内）
      */
     function groups_create($name)
     {
     	  $params = array();
     	  $params['group']['name'] = trim($name);
     	  return $this->oauth->post('groups/create',$params );
     }


     /**
      * 修改分组名   
      * https://api.weixin.qq.com/cgi-bin/groups/update?access_token=ACCESS_TOKEN
      * $id 分组id，
      * $name 分组名字（30个字符以内）
      */
     function groups_update($id,$name)
     {
     	$params = array();
      $params['group']['id'] = trim($id);
     	$params['group']['name'] = trim($name);
     	return $this->oauth->post('groups/update',$params);
     }


     /**
      * 移动用户分组   
      *  https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=ACCESS_TOKEN
      *  $openid 用户唯一标识符
      *  $to_groupid 分组id
      */
     function groups_members_update($openid,$to_groupid){
     	$params = array();
     	$params['openid'] = trim($openid);
     	$params['to_groupid'] = trim($to_groupid);
     	return $this->oauth->post('groups/members/update',$params);
     }


     /**
      * 获取关注列表 一次最多返回1万
      * https://api.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&next_openid=NEXT_OPENID
      */
     function user_get($next_openid="")
     {
     	$params = array();
     	$params['next_openid'] = $next_openid;
     	return $this->oauth->get('user/get', $params);
     }


     /**
      * 获取用户基本信息
      * https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID
      * $openid 用户唯一标识符
      */
     function user_info($openid="")
     {
     	$params = array();
     	$params['openid'] = $openid;
     	return $this->oauth->get('user/info', $params);//可能是接口的bug不能补全
     }


     /*
      * 发送客服文本消息 
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
      * $content 内容
      */
     function message_custom_send_text($touser,$content)
     {
     	$params = array();
     	$params['touser'] = trim($touser);
     	$params['msgtype'] = "text";
     	$params['text']["content"] = $content;
     	return $this->oauth->post('message/custom/send',$params);
     }
     

     /*
      * 发送客服图片消息
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
      * $media_id 内容id
      */
     function message_custom_send_image($touser,$media_id)
     {
     	$params = array();
     	$params['touser'] = trim($touser);
     	$params['msgtype'] = "image";
     	$params['image']["media_id"] = $media_id;
     	return $this->oauth->post('message/custom/send',$params);
     }


     /*
      * 发送客服语音消息
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
      * $media_id 内容id
      */
     function message_custom_send_voice($touser,$media_id)
     {
     	$params = array();
     	$params['touser'] = trim($touser);
     	$params['msgtype'] = "voice";
     	$params['voice']["media_id"] = $media_id;
     	return $this->oauth->post('message/custom/send',$params);
     }
     

     /*
      * 发送客服视频消息
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
        $media_id 内容id 
        $thumb_media_id 内容缩略图id
      */
     function message_custom_send_video($touser,$media_id, $thumb_media_id)
     {
      $params = array();
      $params['touser'] = trim($touser);
      $params['msgtype'] = "video";
      $params['voice']["media_id"] = $media_id;
      $params['voice']["thumb_media_id"] = $thumb_media_id;
      return $this->oauth->post('message/custom/send',$params);
     }


      /*
      * 发送客服音乐消息
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
        musicArray
          "title":"MUSIC_TITLE",
          "description":"MUSIC_DESCRIPTION",
          "musicurl":"MUSIC_URL",
          "hqmusicurl":"HQ_MUSIC_URL",
          "thumb_media_id":"THUMB_MEDIA_ID" 
      */
     function message_custom_send_music($touser, $musicArray)
     {
      $params = array();
      $params['touser'] = trim($touser);
      $params['msgtype']= "music";
      $params['music']  = $musicArray;
      return $this->oauth->post('message/custom/send',$params);
     }


     /*
      * 发送客服图文消息 图文消息条数限制在10条以内。
      * https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN
      * $touser 用户唯一标识符openid
      * $newsArray 
             "title":"Happy Day",
             "description":"Is Really A Happy Day",
             "url":"URL",
             "picurl":"PIC_URL"
      */
     function message_custom_send_news($touser,$newsArray)
     {
     	$params = array();
     	$params['touser']  = trim($touser);
     	$params['msgtype'] = "news";
     	$params['news']["articles"] = $newsArray;
     	return $this->oauth->post('message/custom/send',$params);
     }
     

     function message_mass_send($params)
     {
      
      return $this->oauth->post('message/mass/sendall',$params);
     }

     /*
      * 创建临时二维码ticket 
      * http://mp.weixin.qq.com/wiki/index.php?title=%E7%94%9F%E6%88%90%E5%B8%A6%E5%8F%82%E6%95%B0%E7%9A%84%E4%BA%8C%E7%BB%B4%E7%A0%81
      * expire_seconds	 该二维码有效时间，以秒为单位。 最大不超过1800。
	    *	action_name	 二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久
	    *	scene_id	 场景值ID，临时二维码时为32位整型，永久二维码时最大值为1000
      */
     function qrcode_create_scene($expire_seconds="900",$scene_id)
     {
     	$params = array();
     	$params['expire_seconds'] = $expire_seconds;
     	$params['action_name'] = "QR_SCENE";
      $params['action_info']["scene"]["scene_id"] = $scene_id;
     	return $this->oauth->post('qrcode/create',$params);
     }


     /*
      * 创建永久二维码ticket
     * http://mp.weixin.qq.com/wiki/index.php?title=%E7%94%9F%E6%88%90%E5%B8%A6%E5%8F%82%E6%95%B0%E7%9A%84%E4%BA%8C%E7%BB%B4%E7%A0%81
     * expire_seconds	 该二维码有效时间，以秒为单位。 最大不超过1800。
     *	action_name	 二维码类型，QR_LIMIT_SCENE为永久
     *	scene_id	 场景值ID，临时二维码时为32位整型，永久二维码时最大值为1000
     */
     function qrcode_create_forever($scene_id)
     {
     	$params = array();
     	$params['action_name'] = "QR_LIMIT_SCENE";
     	$params['action_info']["scene"]["scene_id"] = $scene_id;
     	return $this->oauth->post('qrcode/create',$params);
     }


     /*
      * 通过ticket换取二维码
      * 
      */
     function showqrcode($ticket)
     {
      return "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . $ticket;
     }

    
    /*
    * 获取菜单信息
    * https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN
    */
    function menu_get()
    {
      $params = array();
      return $this->oauth->get('menu/get',$params);
    }


     /*
    * 创建菜单信息
    * https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN
    */
    function menu_create($params)
    {
      return $this->oauth->post('menu/create',$params);
    }

    
     /*
    * 删除菜单信息
    * https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=ACCESS_TOKEN
    */
    function menu_delete()
    {
      $params = array();
      return $this->oauth->get('menu/delete',$params);
    }


     /**
      * 发送模板消息
	  * https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN
      */
     function send_template_msg($data)
     {
     	return $this->oauth->post('message/template/send', $data);
     }

     //小程序模板消息
     function send_template_msg_xcx($data)
     {
      return $this->oauth->post('message/wxopen/template/send', $data);
     }


    protected function id_format(&$id) {
      if ( is_float($id) ) 
      {
        $id = number_format($id, 0, '', '');
      } 
      elseif ( is_string($id) ) 
      {
        $id = trim($id);
      }
     }


    //获取素材列表
    function get_material_list($appid, $secret, $type, $offset){
      $access_token = $this->getAccessToken($appid, $secret);
      $url ='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access_token;
        $curl = curl_init ();
        // 设置你需要抓取的URL
        curl_setopt ( $curl, CURLOPT_URL, $url );
        // 设置header
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );
         curl_setopt($curl, CURLOPT_NOBODY, 0); 
           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
        'type' => $type,
        'offset' => $offset,
        'count' => C('PER_PAGE_NUM'),
        )));
        // 运行cURL，请求网页
       $data =  curl_exec ( $curl );
        // 关闭URL请求
        curl_close ( $curl );
        $data = json_decode($data,true);
        return $data;
    }


    function get_user_summary($params){
        $appid = C('APPID');
        $secret = C('APPSECRET');
        $access_token = $this->getAccessToken($appid, $secret);
        $url = 'https://api.weixin.qq.com/datacube/getusersummary?access_token='.$access_token;
        $res = http_curl_post($url, $params);
        $res = json_decode($res, true);
        return $res;
    }

    function get_user_cumulate($params){
        $appid = C('APPID');
        $secret = C('APPSECRET');
        $access_token = $this->getAccessToken($appid, $secret);
        $url = 'https://api.weixin.qq.com/datacube/getusercumulate?access_token='.$access_token;
        $res = http_curl_post($url, $params);
        $res = json_decode($res, true);
        return $res;
    }
}

class WeiXinUser
{
	protected $appid = '';
	protected $secret = '';
	protected $parentId = 0;

	 /**
	  * 构造函数
	  * @author 姜伟
	  * @param $appid
	  * @param $secret 
	  * @return void
	  * @todo
	  */ 
	 function __construct($appid, $secret, $first_agent_id=0)
	 {
		 $this->appid = $appid;
		 $this->secret = $secret;
         $this->parentId = $first_agent_id;
	 }

	//获取微信的openid
	function GetOpenid($c_code)
	{
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->secret . "&code=" . $c_code .  "&grant_type=authorization_code";
		//$url = "http://www.baidu.com";
		$result = $this->getData($url);

		$jsondecode = json_decode($result);
		if ($jsondecode != null)
		{
			if (property_exists ( $jsondecode, "openid" ) )
			{
				return $jsondecode->{"openid"};
			}
			else
			{
				return "code is invalid.";
			}
		}

		return null;
	}

	//用刷新令牌获取ACCESS_TOKEN
	function getAccessToken($url, $rec_user_id = 0)
	{
		$result = $this->getData($url);

		$jsondecode = json_decode($result);

    if($jsondecode->{"errcode"} == 40163  && $GLOBALS['config_info']['LIMIT_REG_OPEN']){
        die($GLOBALS['config_info']['LIMIT_REG_DESC']);
    }
    
		if($jsondecode != null)
		{
			if (property_exists($jsondecode, "errcode"))
			{
				if ($jsondecode->{"errcode"} == "40030")
				{
					$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appid . '&redirect_uri=' . urlencode($redirect_uri) .'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
					redirect($url);
				}
			}
			if(property_exists($jsondecode, "openid"))
			{
				 #$_SESSION['openid'] = $jsondecode->{"openid"};
				 #$_SESSION['refresh_token'] = $jsondecode->{"refresh_token"};
				 #$_SESSION['expires_in'] = $jsondecode->{"expires_in"};
				 #$_SESSION['access_token'] = $jsondecode->{"access_token"};
				//获取用户信息
				$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $jsondecode->{"access_token"} . '&openid=' . $jsondecode->{"openid"} . '&lang=zh_CN';
				$result = $this->getData($url);
				log_file('user_info:'.$result,'user_info');
				$userinfo = json_decode($result);
				//写数据库
				$user_model = new UserModel();
				$arr = array(
					'openid'				=> $jsondecode->{"openid"},
					'refresh_token'			=> $jsondecode->{"refresh_token"},
					'access_token'			=> $jsondecode->{"access_token"},
					'token_expires_time'	=> time() + intval($jsondecode->{"expires_in"}),
					'nickname'				=> $userinfo->{"nickname"},
					'sex'					=> $userinfo->{"sex"},
					'city'					=> $userinfo->{"city"},
					'province'				=> $userinfo->{"province"},
					'headimgurl'			=> urldecode($userinfo->{"headimgurl"}),
					#'privilege'				=> serialize($userinfo->{"privilege"}),
					'user_cookie'           => $GLOBALS['user_cookie'],
					'role_type'             => 3,
					'is_enable'             => 1,
					'reg_time'              => time(),
				);
				#echo "<pre>";
				#print_r($arr);
				#echo "</pre>";
				$where = 'openid = "' . $jsondecode->{"openid"} . '"';
				#$where .= ' OR user_cookie = "' . $GLOBALS['user_cookie'] . '"';
				$user_info = $user_model->field('user_id')->where($where)->find();
				if (!$user_info)
				{
					//省份城市信息
					$area_obj = new AreaModel();
					$city_info = $area_obj->getIdByName($arr['province'], $arr['city']);
					$arr['province_id'] = $city_info ? $city_info['province_id'] : 0;
					$arr['city_id'] = $city_info ? $city_info['city_id'] : 0;
					$arr['area_id'] = 0;

					$arr['first_agent_id'] = intval($rec_user_id);
					$user_obj = new UserModel($rec_user_id);
					$user_info2 = $user_obj->getUserInfo('first_agent_id, second_agent_id');
					$arr['second_agent_id'] = $user_info2 ? $user_info2['first_agent_id'] : 0;
					$arr['third_agent_id'] = $user_info2 ? $user_info2['second_agent_id'] : 0;
					$user_id = $user_model->addUser($arr);
#log_file($user_model->getLastSql(), 'user');
#echo $user_model->getLastSql();

					if ($arr['first_agent_id'])
					{
						//推送消息给上级
$user_obj2 = new UserModel($arr['first_agent_id']);
$user_info3 = $user_obj2->getUserInfo('id');
						$msg = array(
							'first'		=> '有一个新的会员通过您分享的链接加入了平台，感谢您的推荐！',
							'keyword1'	=> $user_info3['id'],
							'keyword2'	=> date('Y-m-d H:i:s', time()),
							'url'		=> 'http://' . $_SERVER['HTTP_HOST'] . '/FrontUser/partner/',
						);
						PushModel::wxPush($arr['first_agent_id'], 'new_rec_user', $msg);
					}
				}
				else
				{
					unset($arr['reg_time']);
					$user_id = $user_info['user_id'];
					$user_info['last_login_time'] = time();
					$user_model->where($where)->save($arr);
				}
				session('user_id', $user_id);
			}
		}
	}

	//获取https的get请求结果
	function getData($c_url)
	{
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $c_url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
		#curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
		#curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($curl); // 执行操作
		if (curl_errno($curl))
		{
			echo 'Errno'.curl_error($curl);//捕抓异常
		}
		curl_close($curl); // 关闭CURL会话
		return $tmpInfo; // 返回数据
	}

    
}
