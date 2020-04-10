// by zhengzhen

var expressBG = $('#J_ExpressBG');
$(function(){
	var imgUrl = $('#J_ImgUrl').val();
	
	if ('' != imgUrl) {
		$.jPops.showLoading();
		expressBG.attr('src', imgUrl);
		if (expressBG.prop('complete')) {
			expressBG.removeClass('default-height');
			$.jPops.hideLoading();
			initConfig();
		}
		else {
			expressBG.on('load', function(){
				$(this).removeClass('default-height');
				$.jPops.hideLoading();
				initConfig();
			}).on('error', function(){
				$.jPops.hideLoading();
			});
		}
	}
	
	$('#J_ShippingCompanyId').on('change', function(){
		if ('' == $('#J_PrintTempName').prop('defaultValue')) {
			if (0 != $(this).children(':selected').index()) {
				$('#J_PrintTempName').val($(this).children(':selected').text());
			}
			else {
				$('#J_PrintTempName').val(null);
			}
		}
	});
	
	$("#J_FormExpress").validate({
		rules: {
			shipping_company_id: {
				equal_select: true
			},
			print_temp_name: {
				required: true
			},
			printing_paper_width: {
				required: true
			},
			printing_paper_height: {
				required: true
			}
		},
		messages: {
			shipping_company_id: {
				equal_select: "请选择物流公司"
			},
			print_temp_name: {
				required: "请输入模板名称"
			},
			printing_paper_width: {
				required: "请输入快递单长度"
			},
			printing_paper_height: {
				required: "请输入快递单宽度"
			}
		},
		errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
		success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
	});
	
	new AjaxUpload("#J_Uploader", {
		action: "/AcpShippingAjax/uploadHandler",
		name: "imgFile",
		responseType: "json",
		onSubmit: function(){
			$.jPops.showLoading();
			var legacyImg = $('#J_ImgUrl').val();
			if (legacyImg != '') {
				this.setData({
					legacy_img: legacyImg
				});
			}
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
				$.jPops.hideLoading();
				$.jPops.alert({
					title:"提示",
					content:response.msg,
					okBtnTxt:"确定",
					callback:function(){
						return true;
					}
				});
			}
			else if (response.status === 1) {
				$('#J_ImgUrl').val(response.img_url);
				expressBG.attr('src', response.img_url);
				expressBG.on('load', function(){
					$(this).fadeIn().parent().removeClass('default-height');
					$.jPops.hideLoading();
				}).on('error', function(){
					$.jPops.hideLoading();
				});
			}
		}
	});
});

function initConfig() {
	$('.J_PrintItem>textarea').each(function(){
		var itemConfig = $(this).data('item_config');
		if (itemConfig != '') {
			itemConfig = JSON.parse(decodeURIComponent(itemConfig));
		//	console.log(itemConfig);
			$(this).css({
				"font-weight": itemConfig.bold,
				"font-style": itemConfig.italic,
				"font-size": itemConfig.fontSize,
				"letter-spacing": itemConfig.letterSpacing
			}).parent().css({
				"display": "block",
				"left": itemConfig.left,
				"top": itemConfig.top,
				"position": "absolute",
				"width": itemConfig.width,
				"height": itemConfig.height
			});
			if (itemConfig.value != '') {
				$(this).val(itemConfig.value);
			}
		}
	});
}