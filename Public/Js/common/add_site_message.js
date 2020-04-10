$(function(){
	//找回密码表单验证
	$("#add_site_message").validate({
		rules: {
			title: {
				required: true
			},
			contents: {
				required: true
			},
			code:{
				required: true
			}
		},
		messages: {
			title: {
				required: "请添加消息的标题"
			},
			contents: {
				required: "消息不能为空"
			},
			code: {
				required: '请输入验证码'
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

//重载验证码
function fleshVerify(){ 
  var time = new Date().getTime().toString();
  //console.log(verify+time);
  document.getElementById('verifyImg').src = verify+time;
}