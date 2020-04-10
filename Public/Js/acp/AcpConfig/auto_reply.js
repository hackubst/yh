
function delImage() {
	var param = {};
	var _id = $('#J_ImgUrl').data('id');
	var imgUrl = $('#J_ImgUrl').val();

	if (_id != '') {
		param.id = _id;
	}
	if (imgUrl != '') {
		param.img_url = imgUrl;
	}
	$.ajax({
		url: '/AcpArticleAjax/delImage',
		type: 'post',
		data: param,
		dataType: 'json',
		beforeSend: function(){
			$('#J_AjaxLoading').show();
		},
		success: function(data){
		//	console.log(data);
			if (data.status === 1) {
				$('#J_ImgUrl').attr('data-id', '').val(null);
				$('#J_Del').off('click', delImage);
				$('#J_Preview').removeAttr('src').parent().parent().addClass('hide');
				$('#J_Uploader').parent().removeClass('hide');
			}
			$('#J_AjaxLoading').fadeOut();
		}
	});
}


$(function(){
	var imgUrl = $('#J_ImgUrl').val();
	if ('' != imgUrl) {
		$('#J_AjaxLoading').show().parent().removeClass('hide');
		$('#J_Preview').attr('src', imgUrl);
		if ($('#J_Preview').prop('complete')) {
			_emuMaxWidth($('#J_Preview'));
			$('#J_AjaxLoading').fadeOut();
			$('#J_Del').on('click', delImage);
			$('#J_Preview').fadeIn().parent().parent().hover(
				function(){
					if ($.browser.msie) {
						$('#J_Mask').show();
					}
					else {
						$('#J_Mask').fadeIn(300);
					}
				}, function(){
					if ($.browser.msie) {
						$('#J_Mask').hide();
					}
					else {
						$('#J_Mask').fadeOut(300);
					}
				}
			);
		}
		else {
			$('#J_Preview').on({
				load: function(){
					_emuMaxWidth($(this));
					$('#J_AjaxLoading').fadeOut();
					$('#J_Del').on('click', delImage);
					$('#J_Preview').fadeIn().parent().parent().hover(
						function(){
							if ($.browser.msie) {
								$('#J_Mask').show();
							}
							else {
								$('#J_Mask').fadeIn(300);
							}
						}, function(){
							if ($.browser.msie) {
								$('#J_Mask').hide();
							}
							else {
								$('#J_Mask').fadeOut(300);
							}
						}
					);
				},
				error: function(){
					$('#J_AjaxLoading').fadeOut();
				}
			});
		}
	}





	var html = '';
	var foot ='<div class="formitems inline"><label class="fi-name"></label><div class="form-controls"><input type="hidden" name="act" value="add" /><button type="submit" class="btn btn-blue"><i class="gicon-check white"></i>添加</button></div></div>';
	$('#reply_type').on('change', function(){
		var reply_type = $(this).val();
		/*if(reply_type == 'text'){
			return;
		}*/

				switch(reply_type){
					case 'news':
						html = '';
						$('#type').nextAll().remove();
						html+= '<div class="formitems inline"><label class="fi-name"><span class="colorRed">*</span>图文标题：</label><div class="form-controls"><input type="text" placeholder="" class="xlarge" name="news_title" id="news_title" value=""> <span class="fi-help-text hide"></span></div></div>';
						html += '<div class="formitems inline"><label class="fi-name"><span class="colorRed">*</span>图文链接：</label><div class="form-controls"><input type="text" placeholder="" class="xlarge" name="news_link" id="news_link" value=""><span class="fi-help-text hide"></span></div></div>';
						html += '<div class="formitems inline" id="img_url" data-dir="" data-module="AcpConfig"><label class="fi-name"><span class="colorRed">*</span>缩略图：</label><div class="form-controls"><ul class="fi-imgslist"><li class="preview fi-imgslist-item pic hide" id="preview" style=""><div><img id="J_Preview" style="height:69px;" src=""/></div><input class="xxlarge" type="hidden" name="img_url" id="J_ImgUrl" value="" /><a href="javascript:;" class="del" id="J_Del" title="删除这张图"><i class="gicon-remove" onclick="delete_single_image(\'img_url\');"></i></a><img src="/Public/Images/popup-loading.gif" id="J_AjaxLoading" class="pic-loading" style="width: 32px; height: 32px;" /><div class="pic-mask" id="J_Mask"></div></li><li class="fi-imgslist-item" id="add_li"><a href="javascript:;" class="add" title="上传一张新的图片" id="img_url_uploader">+</a></li></ul><span class="fi-help-text ">系统支持扩展名为(*.jpg,*.jpeg,*.png,*.gif)的2M以内图片</span></div></div>';
						html += '<div class="formitems inline"><label class="fi-name"><span class="colorRed">*</span>图文简述：</label><div class="form-controls"><textarea name="text_value" id="text_value"></textarea></div></div>';
						html += foot;
						$('#type').after(html);
						upload_single_image('img_url');
						break;
					case 'image':
						html = '';
						$('#type').nextAll().remove();
						html += '<div class="formitems inline" id="img_url" data-dir="" data-module="AcpConfig"><label class="fi-name"><span class="colorRed">*</span>缩略图：</label><div class="form-controls"><ul class="fi-imgslist"><li class="preview fi-imgslist-item pic hide" id="preview" style=""><div><img id="J_Preview" style="height:69px;" src=""/></div><input class="xxlarge" type="hidden" name="img_url" id="J_ImgUrl" value="" /><a href="javascript:;" class="del" id="J_Del" title="删除这张图"><i class="gicon-remove" onclick="delete_single_image(\'img_url\');"></i></a><img src="/Public/Images/popup-loading.gif" id="J_AjaxLoading" class="pic-loading" style="width: 32px; height: 32px;" /><div class="pic-mask" id="J_Mask"></div></li><li class="fi-imgslist-item" id="add_li"><a href="javascript:;" class="add" title="上传一张新的图片" id="img_url_uploader">+</a></li></ul><span class="fi-help-text ">系统支持扩展名为(*.jpg,*.jpeg,*.png,*.gif)的2M以内图片</span></div></div>';
						html += foot;
						$('#type').after(html);
						upload_single_image('img_url');
						break;
					case 'text':
						html = '';
						$('#type').nextAll().remove();
						html += '<div class="formitems inline"><label class="fi-name"><span class="colorRed">*</span>文本内容：</label><div class="form-controls"><textarea name="text_value" id="text_value"></textarea></div></div>';
						html += foot;
						$('#type').after(html);
						break;
					default:
						break;
				}

	});


});


function upload_file()
    {
        // ajax上传图片
	new AjaxUpload("#J_Uploader", {
		action: "/AcpArticleAjax/uploadHandler",
		name: "imgFile",
		responseType: "json",
		onSubmit: function(){
			$('#J_AjaxLoading').show().parent().removeClass('hide');
			$('#J_Uploader').parent().addClass('hide');
		},
		onChange: function(file, extension){
			if (extension && /^(jpg|png|jpeg|gif)$/.test(extension)) {
				return true;
			}
			else {
				$.jPops.alert({
					title:"提示",
					content:'暂不支持该图片格式！',
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
				return false;
			}
		},
		onComplete: function(file, response){
		//	console.log(response);
			if (response.status === 0) {
				$.jPops.alert({
					title:"提示",
					content:response.msg,
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
				$('#J_AjaxLoading').parent().addClass('hide');
				$('#J_Uploader').parent().removeClass('hide');
			}
			else if (response.status === 1) {
				$('#J_ImgUrl').val(response.img_url);
				$('#J_Preview').attr('src', response.img_url);
				//等待图片加载完成
				$('#J_Preview').on({
					'load': function(){
						_emuMaxWidth($(this));
						$('#J_AjaxLoading').fadeOut();
						$('#J_Del').on('click', delImage);
						$('#J_Preview').fadeIn().parent().parent().hover(
							function(){
								if ($.browser.msie) {
									$('#J_Mask').show();
								}
								else {
									$('#J_Mask').fadeIn(300);
								}
							}, function(){
								if ($.browser.msie) {
									$('#J_Mask').hide();
								}
								else {
									$('#J_Mask').fadeOut(300);
								}
							}
						);
					},
					'error': function(){
						$('#J_AjaxLoading').fadeOut();
					}
				});
			}
		}
	});
}

function _emuMaxWidth(_this) {
		//IE6,7,8不支持CSS的max-width属性的临时解决办法--!
		if ($.browser.msie) {
			var width = _this.width();
			var height = _this.height();
			var pWidth = _this.parent().width();
			var pHeight = _this.parent().height();

			if (width / height > pWidth / pHeight) {
				_this.css('width', _this.css('max-width'));
			}
			else {
				_this.css('width', '');
			}
		}
	}