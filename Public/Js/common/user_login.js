$(function(){
	//登录表单验证
	$("#form_login").validate({
		rules: {
			username: {
				required: true
			},
			password:{
				required: true
			}
		},
		messages: {
			username: {
				required: "请输入账号"
			},
			password: {
				required: "请输入密码"
			}
		},
		//显示出错信息
		errorPlacement: function(error, element) {
			var msg=error.text();
			element.siblings('.fi-help-text').text(msg).addClass('error').removeClass('hide');
		},
		//验证成功隐藏错误信息
		success: function(element) {
			element.siblings('.fi-help-text').text("").removeClass('error').addClass('hide');
		}
	});
	
});