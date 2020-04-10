$(function(){
	$("#set_free_shipping").validate({  
	     rules: {
	         post_code:{
	        	 required: true,
	        	 number:true
	         }  
	     },  
	     messages: { 
	         post_code:{
	        	 required: '请指定额度,全场包邮填写0',
	        	 number:"请输入正确的邮编号"
	         } 
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 }); 
});