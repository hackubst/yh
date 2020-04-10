<?php
// 公共文件
class PublicAction extends Action {
	
	/**
	 * 根据商品货号生成对应库存图片
	 *
	 * @param void
	 * @author zhengzhen
	 * @return void
	 * @todo 获取URL中商品货号sn参数，查询对应库存；
	 * @todo 若失败，则中止；若库存为0，则直接返回静态图片；
	 * @todo 否则以模板图片生成库存图片输出到终端
	 *
	 */
	public function get_stock()
	{
		$sn = $this->_get('sn');
		$item = new ItemBaseModel();
		$stock = $item->getStcokBySn($sn);
		if($stock)
		{
			$handle = imagecreatefromjpeg('Public/Images/ucp/in_stock.jpg');
			if(!$handle)
			{
				exit;
			}
			$imgWidth = imagesx($handle);
			$imgHeight = imagesy($handle);
			$size = 16;
			$angle = 0;
			$color = imagecolorallocatealpha($handle, 255, 255, 255, 0);//白色不透明
			$font = APP_PATH . 'Public/font/vcode/1.ttf';
			//获取字符串尺寸
			$bbox = imagettfbbox($size, $angle, $font, $stock);
			$strWidth = abs($bbox[2] - $bbox[0]);
			$strHeight = abs($bbox[7] - $bbox[1]);
			//字符串居中处理
			$x = ($imgWidth - $strWidth) / 2;
			$y = $imgHeight - ($imgHeight - $strHeight) / 2;
			imagettftext($handle, $size, $angle, $x, $y, $color, $font, $stock);
			
			header('Content-Type: image/jpeg');
			imagejpeg($handle);
			imagedestroy($handle);
		}
		else
		{
			echo 'Public/Images/ucp/no_stock.jpg';
		}
	}
	
    /**
     * 库存数量生成图片函数
     * @author 23585472@qq.com
      * 使用: <img src="{:U('Public/CreateImg')}" alt="" />
     */
    public function get_stock2()
    {
        $id = I('id', 0);
        //获取商品总库存
        $ItemBaseModel = new ItemBaseModel();
        $authnum = $ItemBaseModel->getStockById($id);

        Header("Content-type: image/JPEG");                //生成验证码图片
        $im      = imagecreate(80,30);                        //建立空白图片
        $black   = ImageColorAllocate($im, 49,52,55);         //背景色 黑色
        $white   = ImageColorAllocate($im, 255,255,255);     //字体色 白色
        //$gray    = ImageColorAllocate($im, 200,200,200);     //干扰色 灰色
        
        $ttf = APP_PATH . 'Public/font/vcode/5.ttf'; //字体
        //可设定字体样式 25是字体大小 0倾斜度 15左右  25上下
        ImageTTFText($im, 14, 0, 15, 25, $white, $ttf, $authnum);
        //加入干扰象素
        //for($i=0;$i<200;$i++)
        //{
        //imagesetpixel($im, rand()%70, rand()%30, $gray);
        //}
        ImageJPEG($im);
        ImageDestroy($im);
    }

    //验证码
	public function verify()
	{
        import('ORG.Util.VerifyCode');
        $VerifyCode = new VerifyCode();
        echo $VerifyCode->createImage();

		/*import('ORG.Util.Image');
		$w = $this->_get('w');
		$h = $this->_get('h');
		if(isset($w) && isset($h) && ctype_digit($w) && ctype_digit($h))
		{
			Image::buildImageVerify(4, 2, 'png', $w, $h);
		}
        else
		{
			Image::buildImageVerify(4, 2, 'png');
		}*/
    }
    
        
    /**
     * 公用上传图片
     * @param int
     * @return int
     * @author <zgq@360shop.cc>
     */
    public function common_upload_img() 
    {
        if (!empty($_FILES)) 
        {
           import('ORG.Net.UploadFile');
           $upload            = new UploadFile();                                 // 实例化上传类
           $upload->maxSize   = 3145728;                                 // 设置附件上传大小
           $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');    // 设置附件上传类型
           $dir_time          = date('Ymd', time());                            // 设置附件上传目录
           $upload->savePath  = APP_PATH .  'Uploads/image/' . $dir_time . '/'; // 设置附件上传目录

           if (!$upload->upload()) 
           {
               exit(json_encode(array('code'=>400, 'msg'=>$upload->getErrorMsg())));
           } 
           else 
           {
               $info     = $upload->getUploadFileInfo();
               $path_img =  '/Uploads/image/' .$dir_time . '/' . $info[0]['savename'];
           }
           
           exit(json_encode(array('code'=>200, 'msg'=>'ok', 'path_img'=>$path_img)));
       }
    }
   
    
    
    /**
     * 删除export数据包
     * @param int
     * @return int
     * @author <zgq@360shop.cc>
     */
    public function delExportZip() 
    {
        $zip_url = $this->_request('zip_url');
        //echo APP_PATH . $zip_url;
        if($zip_url && file_exists(APP_PATH . $zip_url))
        {
            unlink(APP_PATH . $zip_url);
            echo 'ok';
        }
            
    }
    
    
    /**
     * 功能:  清空缓存文件
     * 参数:   无
     * 返回值:  无
     * 作者: 张光强
     * 时间:  Tue Jan 14 06:32:47 GMT 2014
     */
    public function delcache($is_msg = false)
    {
    	if(is_dir(APP_PATH . 'Runtime'))
    	{
    		delDirAndFile(APP_PATH . 'Runtime');
            if(!$is_msg)
    		  exit(json_encode(array('code'=>200, 'msg'=>'恭喜您,缓存已清空!')));
    	}
    }
    
    /**
     * 功能: 编辑器调用
     * 实例:   
    	$kd = new PublicAction;
    	$this->assign('KD', $kd->getKD());
    	
    	模版的form名称要为form2  textarea名称要为contents
    	<form id="form2" name="form2" method="post" action="">
			<textarea name="contents" id="" cols="30" rows="10"></textarea>
		</form>
     * 返回值:  实例化后的编辑器
     * 作者: 张光强
     * 时间:  Tue Jan 14 03:02:46 GMT 2014
     */
    public function getKD()
    {
$str = <<< ABC
<link rel="stylesheet" href="__KD__/themes/default/default.css" />
<script charset="utf-8" src="__KD__/kindeditor.js" > </script>
<script charset="utf-8" src="__KD__/lang/zh_CN.js" > </script>
<script >
KindEditor.ready( function (K) {
	 var editor1 = K.create( 'textarea[name="contents"]' , {
		uploadJson : '__KD__/php/upload_json.php' ,
		fileManagerJson : '__KD__/php/file_manager_json.php' ,
		width : '498px' ,
		height : '200px' ,
		minWidth : '498px' ,
		minHeight : '200px' ,
		fillDescAfterUploadImage : true,
		allowFileManager : true,
		items : [ 'bold' , 'italic' , 'underline' , '|' , 'insertorderedlist' , 'insertunorderedlist' , 'image' , 'multiimage' , 'link' , '|' , 'unlink' , 'removeformat' , 'forecolor' , 'hilitecolor' , 'fullscreen' ],
		afterCreate : function () {
			 var self = this;
			K.ctrl(document, 13 , function () {
				self.sync();
				K( 'form[name=form12]' )[ 0 ].submit();
			});
			K.ctrl(self.edit.doc, 13 , function () {
				self.sync();
				K( 'form[name=form12]' )[ 0 ].submit();
			});
                         K('#submit').click(function(e) {
                                alert(editor1.html());
                        });

		}
	});
});
</script>
ABC;
	return $str;
	}
        
    /**
     * 功能: 获取编辑器上传图片功能
     * 实例:   
     * 返回值:  实例化后的编辑器
     * 作者: 张光强
     * 时间:  Tue Jan 14 03:02:46 GMT 2014
     */
    public function getKDUpload()
    {
$str = <<< ABC
<script>
			KindEditor.ready(function(K) {
				var editor = K.editor({
					allowFileManager : true
				});
				
				K('#image3').click(function() {
					editor.loadPlugin('image', function() {
						editor.plugin.imageDialog({
							showRemote : false,
							imageUrl : K('#url3').attr('src'),
							clickFn : function(url, title, width, height, border, align) {
								K('#url3').attr('src', url);
								editor.hideDialog();
							}
						});
					});
				});
                               
			});
		</script>
ABC;
	return $str;
	}
        
	function error()
	{
		$this->display();
	}
}
