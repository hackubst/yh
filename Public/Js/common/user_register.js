$(function(){
	//会员注册表单验证
	$("#form_register").validate({
		rules: {
			username: {
				required: true,
				minlength: 5
			},
			password: {
				required: true,
				minlength: 6
			},
			confirm_password: {
				required: true,
				equalTo: "#password"
			},
			code:{
				required: true
  			},
  			readme: {
  				required: true
  			}
		},
		messages: {
			username: {
				required: "请输入账号",
				minlength: "长度最少为5位"
			},
			password: {
				required: "请输入密码",
				minlength: "长度最少为6位"
			},
			confirm_password: {
				required: "请再次输入密码",
				equalTo: "两次密码不一致"
			},
			code: {
				required: '请输入验证码'
			},
  			readme: {
  				required: "请仔细阅读服务条款"
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
	
	
	//验证用户名是否可以注册
	$('.check_user').click(function(){
		var name = $('#username').val();
		if(!name)
		{
			$('.flag_u').html('请填写您的账号').addClass('error');
		}else if(name.length < 5){
			$('.flag_u').html('长度不能少于5').addClass('error').show();
		}else{
			$('.flag_u').html('<img src="/Public/Images/ajax-loading.gif" />').removeClass('hide');
			$.ajax({
		        type        : "POST",
		        url         : '/Common/user_register',
		        data		: {'u':name,'act':'checkUser'},
		        dataType	: "json",
		        beforeSend  : function (XMLHttpRequest) {
		            XMLHttpRequest.setRequestHeader("request_type","ajax");
		        },
		        success     : function(data){
			    	  if(data.type == 1)
			   		  {
			   			  $('.flag_u').html('可以注册').removeClass('error');
			   		  }else{
			   			  $('.flag_u').html('该账号已经存在').addClass('error');
			   		  }
		        }
		    });
		      
		}
	});
	
	$('#username').keyup(function(){
		this.value=this.value.replace(/(^\s+)|\s+$/g,""); 
	});
	
//	$.validator.addMethod("check_username", function(value) {
//		var passed=false;
//		$('.flag_u').html('<img src="/Public/Images/ajax-loading.gif" />').show();
////		var name = $('#username').val();
//		if(!value || value.length < 5)
//		{
//			return false;
//		}
//		$('#username').focus(function(){
//			return true;
//		});
////		console.log(name);
//	    $.ajax({
//	        type        : "POST",
//	        url         : '/Common/user_register',
//	        async		: false,
//	        data		: {'u':name,'act':'checkUser'},
//	        dataType	: "json",
//	        beforeSend  : function (XMLHttpRequest) {
//	            XMLHttpRequest.setRequestHeader("request_type","ajax");
//	        },
//	        success     : check
//	    });
//	       function check(data){
//	   		  var info = $.parseJSON(data);
//	   		  console.log(data);
//	   		  if(data.type != -1)
//	   		  {
//	   			  log('周涛');
//	   			 passed=true;
//	   		  }else{
//	   			  log('wocao');
//	   		  }
//	       }
//	    log('hello');
//	    return passed;
//		
//	}, "该用户名已经被注册");

	$('.agreement').click(function(){
		$('.agreement_content').slideToggle();
	});
	
	
});

function log(msg)
{
	console.log(msg);
}

/*******************************************************/


//重载验证码
function fleshVerify(){ 
    var time = new Date().getTime();
//    console.log(verify+time);
    document.getElementById('verifyImg').src = '/Public/verify/'+time;
}
