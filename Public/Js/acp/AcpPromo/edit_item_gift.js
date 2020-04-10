$(function(){
	//表单验证
	$("#add_item_gift").validate({
//		debug:true, 
		 ignore: "",
	     rules: { 
	    	 gift: {
	    		 equal_select:true 
	    	 },
	    	 i_total:{
	    		 tabform_number:true
	    	 }
	     },  
	     messages: {
	    	 gift: {
	    		 equal_select:'请选择一件礼品' 
	    	 },
	    	 i_total:{
	    		 tabform_number:'所需商品购买量不能为空'
	    	 }
	     },  
	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
	 }); 

	onlyNumberInput('i_total');
	onlyNumberInput('g_total');
	
});


/******* 以下为自定义函数 ******/

//表单只能输入数字
function onlyNumberInput(name){
	$('input[name='+name+']').keyup(function(){
		this.value=this.value.replace(/[^\d]/g,''); 
	});
} 
