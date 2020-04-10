$(function() {

    $("#form1").validate({
        rules: {
            title: {
                required: true
            },
            menu_type: {
                equal_select: true
            },
            link_id_items:{
            	select_link_id_items:true
            },
            limit_total:{
            	input_limit_total:true,
            	number:true
            },
            link_id_article_class:{
            	select_link_id_article_class:true
            },
            serial:{
            	number:true
            },
            out_url:{
            	input_out_url:true,
            	url:true
            }
        },
        messages: {
            title: {
                required: "请输入菜单显示文字"
            },
            menu_type: {
                equal_select: "请选择菜单类型"
            },
            limit_total:{
            	number:"请输入数字"
            },
            serial:{
            	number:"请输入数字"
            },
            out_url:{
            	url:"请输入正确的url类型地址,例如http://www.baidu.com"
            }
        },
        errorPlacement: acp.form_ShowError, //显示出错信息(这段代码必须加)  
        success: acp.form_HideError //验证成功隐藏错误信息(这段代码必须加)  
    });

    //商品类型下拉框
    $.validator.addMethod("select_link_id_items", function(value) {
    	var menu_type=$("#menu_type").val();
	    if(menu_type==1 && value!=""){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}, "请选择商品类型");

	//条数输入框
	$.validator.addMethod("input_limit_total", function(value) {
    	var menu_type=$("#menu_type").val();
	    if((menu_type==1 || menu_type==2) && value!=""){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}, "请输入在二级菜单中显示多少条数据");

	//文章分类下拉框
	$.validator.addMethod("select_link_id_article_class", function(value) {
    	var menu_type=$("#menu_type").val();
	    if(menu_type==2 && value!=""){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}, "请选择文章分类");

	//外链地址输入框
	$.validator.addMethod("input_out_url", function(value) {
    	var menu_type=$("#menu_type").val();
	    if(menu_type==3 && value!=""){
	    	return true;
	    }
	    else{
	    	return false;
	    }
	}, "请输入外链地址");
	
});
