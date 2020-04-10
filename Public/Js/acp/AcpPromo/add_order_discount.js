$(function(){
	//表单验证
	$("#add_order_discount").validate({
//		debug:true, 
		 ignore: "",
	     rules: {  
	    	 dis_type: {  
	        	 required: true,   
	         },  
	         discount:{  
	        	 required: true,
	        	 number:true 
	         },  
	         title:{  
	        	 required: true  
	         },
	         total:{
	        	 required: true,
	        	 number:true
	         }
	     },  
	     messages: {  
	    	 dis_type: {  
	        	 required: "请选择一种促销类型"
	         },  
	         discount: {  
	        	 required: "请输入活动的促销幅度",
	        	 number:"请输入正确的数字" 
	         },  
	         title:{  
	        	 required: "请给活动指定一个名称"  
	         },
	         total:{
	        	 required: '请指定一个参加活动的订单额度',
	        	 number:'请输入正确的数字额度'
	         } 
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 }); 

	
});	