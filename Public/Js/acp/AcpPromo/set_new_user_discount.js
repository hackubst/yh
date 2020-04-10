$(function(){
	$("#set_new_user_discount").validate({  
	     rules: {
	    	 discount_total:{
	        	 required: true,
	        	 number:true
	         }  
	     },  
	     messages: { 
	    	 discount_total:{
	        	 required: '请指定折扣率(1-100)',
	        	 number:"请输入正确的邮编号"
	         } 
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 }); 
});