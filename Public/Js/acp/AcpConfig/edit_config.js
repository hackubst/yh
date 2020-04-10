$(function(){
	$("#base_config").validate({  
	     rules: { 
	    	 s_name: {  
	             required: true,
	         },
	         s_title: {  
	             required: true, 
	         },
	         s_desc:{  
	             required: true  
	         },
	         s_keywords:{  
	             equal_select: true  
	         },  
	         wholesale_price_name:{  
	             required: true  
	         },
	         real_price_name:{  
	             required: true  
	         },
	         license_no:{
	        	 required: true
	         }
	     },  
	     messages: { 
	    	 s_name: {
	             required: "请填写您的店铺名称",
	         },
	         s_title: {  
	             required: "店铺标题不能为空"
	         },  
	         s_desc:{  
	             required: "店铺描述不能为空"  
	         }, 
	         s_keywords:{  
	        	 required: "填写关键词，利于SEO优化"  
	         }, 
	         wholesale_price_name:{  
	             required: "批发价前台显示名称不能为空"  
	         }, 
	         real_price_name:{  
	             required: "实际拿货价前台显示名称不能为空"  
	         },
	         license_no:{
	        	 required: '请填写您网站的备案号'
	         }
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 });
	
	
	//ready();
	
	$('input[name="sys_open"]').click(function(){
		var value = $(this).val();
		if(value == 0)
		{
			$('.reason').removeClass('hide').show();
		}else{
			$('.reason').addClass('hide').hide();
		}
	});
});


