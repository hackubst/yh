$(function(){
	//表单验证
	$("#add_order_discount").validate({
//		debug:true, 
		 ignore: "",
	     rules: { 
	         title:{  
	        	 required: true  
	         },
	         gift:{
	        	 equal_select: true
	         },
	         total:{
	        	 required: true,
	        	 number:true
	         }
	     },  
	     messages: {  
	         title:{  
	        	 required: "请给活动指定一个名称"  
	         },
	         gift:{
	        	 equal_select: '请选择一件礼品'
	         },
	         total:{
	        	 required: '您还未指定订单额度',
	        	 number:'请输入正确的数字额度信息'
	         }
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 }); 

//	onlyNumberInput('g_total');

	
});	


/******* 以下为自定义函数 ******/

//表单只能输入数字
function onlyNumberInput(name){
	$('input[name='+name+']').keyup(function(){
		this.value=this.value.replace(/[^\d]/g,''); 
	});
} 
