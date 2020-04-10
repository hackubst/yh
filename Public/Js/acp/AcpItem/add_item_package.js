/**
 * js文件 
 * @todo 添加、编辑商品促销活动(打折优惠) js
 * @author zhoutao0928@sina.com zhoutao@360shop.cc
 */

acp.tabsFromOrigin="discount";
$(function(){
// 	//表单验证
// 	$("#add_item_discount").validate({
// //		debug:true, 
// 		 ignore: "",
// 	     rules: {  
// 	    	 dis_type: {  
// 	        	 tabform_required: true,   
// 	         },  
// 	         discount:{  
// 	        	 tabform_required: true,
// 	        	 tabform_number:true 
// 	         },  
// 	         title:{  
// 	        	 tabform_required: true  
// 	         }
// 	     },  
// 	     messages: {  
// 	    	 dis_type: {  
// 	        	 tabform_required: "请选择一种促销类型"
// 	         },  
// 	         discount: {  
// 	        	 tabform_required: "请输入活动的促销幅度",
// 	        	 tabform_number:"请输入正确的数字" 
// 	         },  
// 	         title:{  
// 	        	 tabform_required: "请给活动指定一个名称"  
// 	         } 
// 	     },  
// 	     errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)  
// 	     success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)  
// 	 }); 
	
	main_function();
	
	//为分页添加事件
	onloadFunction();
	
	//用来删除数组中某个特定的元素
	Array.prototype.indexOf = function(val) {
         for (var i = 0; i < this.length; i++) {
             if (this[i] == val) return i;
         }
         return -1;
     };
     Array.prototype.remove = function(val) {
         var index = this.indexOf(val);
         if (index > -1) {
             this.splice(index, 1);
         }
     };
	
//	//处理折扣数据(商品页面)
//	calculate_newprice();
//	
//	//处理页面分页的显示格式(商品页面)
//	reply_page();
});


/******* 以下为自定义函数 ******/
function main_function(){
	//类型提示
	$('input[name=dis_type]').click(function(){
		var type = $(this).val();
		var discount = $('input[name="discount"]').val();	//折扣
		if(!discount){
			if(type == 1)
			{
				$('.type').find('.fi-help-text').html('举例：如果打9折，请输入0.9。85折请输入0.85');
			}else if(type == 2){
				$('.type').find('.fi-help-text').html('举例：如果减价4.5元,请输入4.5');
			}
		}else{
			calculate_newprice();
		}
		
	});


	//弹窗中的全选
	$('.all').click(function(){
		$('#jpops-container').find('.useful').attr("checked",true);
		var istr = checkSelected();
		if(!istr)
		{
			return false;
		}
		var temp_arr = istr.split(',');		//商品ID组成的数组
		for(var i=0;i<temp_arr.length;i++)
		{
//			console.log(temp_arr[i]);
			if(contains(item_arr,temp_arr[i]))	//全局数组中已经存在
			{
//				console.log('找到');
			}else{					//全局数组中不存在
				add_item_element(temp_arr[i]);
			}
		}
//		console.log(item_arr);
	});

	//弹窗中的全部取消
	$('.cancel').click(function(){
		$('input[name="checkIds[]"]').attr('checked',false);
		$('input[name="checkIds[]"]').each(function(){
			var this_id = $(this).val();
			if(contains(item_arr,this_id)){
				del_item_element(this_id);
			}
		});
//		console.log(item_arr);
	});
	

	//搜索展示商品
	$('.search').click(function(){
		var item_name 	= $('#jpops-container').find('#s_name').val();
		var category_id = $('#jpops-container').find('#s_category').val();
		var brand_id 	= $('#jpops-container').find('#s_brand').val();
		var mall_price  = $('#jpops-container').find('#s_mall_price').val();
		
//		console.log({'title':item_name,'category':category_id,'brand':brand_id,'mall_price':mall_price});
//		$.jPops.showLoading();
		$.post('/AcpItemPackage/get_items_ajax',{'title':item_name,'category':category_id,'brand':brand_id,'mall_price':mall_price},function(data){
//			$.jPops.hideLoading();
			insert_new_item_inf(data);
		});
	});
	
	//填写活动规则后自动计算折扣价（参考）
	$('input[name="discount"]').blur(function(){
		var type = $('input[name="dis_type"]:checked').val();
		var discount = $(this).val();
		$(this).change(function(){		//更改幅度值时
			del_insert();
		});
	});
	
	$('#jump2').click(function(){
		acp.switchTabByDataIndex(2,"discount"); 
	});
	
	
	$('.no_one').show();
}

//删除已经添加到页面的已选商品
function del_insert(){
	$('#slected_item').find('.has_item').find('input[type="hidden"]').each(function(){
		var i = $(this).val();
//		console.log(i);
		del_item_element(i);
	});
	
}


function onloadFunction(){
	//点击，异步请求分页数据
	$('.paginate').find('a').click(function(){
		var nhref = $(this).attr('nhref');
//		$.jPops.showLoading();
		$.post(nhref,{},function(data){
//			$.jPops.hideLoading();
			insert_new_item_inf(data);
		});
	});
	
	//单独勾选商品时
	$('input[name="checkIds[]"]').click(function(){
		var this_id = $(this).val();
		if($(this).attr('checked'))		//选中时
		{
			if(!contains(item_arr,this_id))	//不存在于全局数组中
			{
				add_item_element(this_id);
			}
		}else{			//取消选中时
			if(contains(item_arr,$(this).val()))	//已经存在全局数组中
			{
				del_item_element(this_id);
			}
		}
//		console.log(item_arr);
	});

}


/**
 * 弹窗添加商品
 */
function clik(){
	var html=$(".tj-all").html();
	$.jPops.custom({  
	    title:"选择商品",  
	    content:html,  
	    callback:function(r){  
	        acp.popFormStatus=false;//弹窗表单验证状态  
	  
	        if(r){//点击确定按钮执行的事件  
	        	$.jPops.hideAlerts(); 
	        }  
	        else{//点击取消按钮执行的事件  all
//	        	del_insert();
	        	$('input[name="checkIds[]"]').attr('checked',false);
	    		$('input[name="checkIds[]"]').each(function(){
	    			var this_id = $(this).val();
	    			if(contains(item_arr,this_id)){
	    				del_item_element(this_id);
	    			}
	    		});
	        	$.jPops.hideAlerts();
	            return true;  
	        }  
	    }  
	});  
		
	$.post('/AcpItemPackage/get_items_ajax',{},function(data){
		insert_new_item_inf(data);
	});
	main_function();
	onloadFunction();
	calculate_newprice();
	reply_page();
}

/**
 *	截取字符串 
 */
function SetString(str,len)
{
 var strlen = 0; 
 var s = "";
 for(var i = 0;i < str.length;i++)
 {
  if(str.charCodeAt(i) > 128){
   strlen += 2;
  }else{ 
   strlen++;
  }
  s += str.charAt(i);
  if(strlen >= len){ 
   return s ;
  }
 }
return s;
}



/**
 * 处理分页数据的展示格式,替换href属性，添加新的nhref属性。
 * 该操作旨在将分页链接改为ajax异步请求
 */
function reply_page(){
	$('#jpops-container').find('.paginate').find('a').each(function(){		//
		var href = $(this).attr('href');
		if(href){
			href = href.replace('/index.php','');
			href = href.replace('add_item_discount','get_items_ajax');
		}
		$(this).attr('href','javascript:void(0);').addClass('ajaxhref');
		$(this).attr('nhref',href);
	});
}

/**
 *	将ajax异步请求的分页数据替换旧的内容(弹窗中的数据)
 */
function insert_new_item_inf(data){
	$('#jpops-container').find('.item_list').html(data);		//新的数据
	$('#jpops-container').find('.paginate').html($('#page_flag').html());	//新的分页信息
	reply_page();		//替换分页内容
	onloadFunction();	//绑定事件
	calculate_newprice(); //计算新的价格
	$('#jpops-container').find('input[name="checkIds[]"]').each(function(){
		var item_id = $(this).val();
		if(contains(item_arr,item_id))
		{
			$(this).attr('checked',true);
		}else{
			$(this).attr('checked',false);
		}
	});
}

/*
 * 自定义函数，用来判断数组中是否存在某元素
 * arr为待查询的数组
 * obj为要查找的原始值
 */
function contains(arr, obj) {
    for (var i = 0; i < arr.length; i++) {
        if (arr[i] === obj) {
            return true;
        }
    }
    return false;
}


/*
 * 自定义函数，用来向全局数组item_arr中添加某个特定值的原始，同时添加页面相应的展示元素
 * obj 要删除的元素的元素值
 * 
 */
function add_item_element(i){
	item_arr.push(i);
//	console.log(i);
	var obj = $('input[name="checkIds[]"][value="'+i+'"]')
	var item_name = obj.parent().next().next().html();
	var item_img  = obj.parent().next().find('img').attr('src');
	var mall_price = obj.parent().next().next().next().find('.cost').html();

//	alert(obj[0].tagName);
	var element_str = "<tr class='has_item flag_"+i+"'><input type='hidden' name='choose[]' value='"+i+"'>"
					  +"<td><img src="+item_img+" width='50' height='50' ></td>"
					  +"<td>"+item_name+"</td>"
					  +"<td><span>¥</span><span>"+mall_price+"</span></td>"
					  +"<td><a href='javascript:;' class='btn' onclick='del_item_element("+i+")' title='删除'>删除</a></td>"
					  +"</tr>";
	
	$('#slected_item').find('.wxtables').find('.item_list').append(element_str);

	if(item_arr.length >= 1)
	{
		$('.no_one').hide();
	}else{
		$('.no_one').show();
	}
	return true;
}


/*
 * 自定义函数，用来删除全局数组item_arr中某个特定值的原始，同时删除页面相应展示的元素
 * obj 要删除的元素的元素值
 * 
 */
function del_item_element(i){
	item_arr.remove(i);
//	console.log($.inArray(i,item_arr)+' || '+i+' || '+item_arr);
	$('#slected_item').find('.flag_'+i).remove();
	if(item_arr.length >= 1)
	{
		$('.no_one').hide();
	}else{
		$('.no_one').show();
	}
	return true;
}


//获取所有选中的checkbox
function checkSelected()
{
    var iObj = new Array;
    $('#jpops-container').find('input[name="checkIds[]"]:checked').each(function(){
    	iObj.push($(this).val());
    });
    if(iObj.length<1)
    {
         return false;
    }else{
        var istr = iObj.join(',');
        return istr;
    }  
}


//计算商品列表页的表格中，每一件商品的折扣价，此价格紧紧用作参考
function calculate_newprice()
{
	var item_length = $('.cost').length;	//当前页面展示出来的商品的数量
	var type = $('input[name="dis_type"]:checked').val();	//表单中设置的促销类型
	var discount = $('input[name="discount"]').val();	//促销幅度
//	
//	console.log(type);
//	console.log(discount);
	
	if(item_length && type && discount){
		$('input[name="checkIds[]"]').attr('disabled',false).addClass('useful');
		$('.cost').each(function(){
			var mall_price = $(this).html();	//市场价格
			if(mall_price > 0.00)			//市场价大于0的可以参加促销活动
			{
				var new_price = (type == 1)?mall_price * discount:mall_price-discount;
				new_price = Math.round(new_price*100)/100;		//取2位小数
				if(new_price <= 0.00)		//新价格需要大于0元
				{
					var obj = $(this).parent().parent().find('input[name="checkIds[]"]');
					var i = obj.val();
					del_item_element(i);
					obj.attr('disabled',true).removeClass('useful').attr('checked',false);
					var str_dis = '不能参加本次活动';
					$(this).parent().next().next().find('.now_price').html('未知');
				}else{
					$(this).parent().next().next().find('.now_price').html(new_price);  //显示新价格
					$(this).parent().parent().find('input[name="checkIds[]"]').attr('disabled',false).addClass('useful');
					var str_dis = (type == 1)?'打'+discount+'折':'减价'+discount+'元';
				}
			}else{
				var obj = $(this).parent().parent().find('input[name="checkIds[]"]');
				var i = obj.val();
				del_item_element(i);
				obj.attr('disabled',true).removeClass('useful').attr('checked',false);
				var str_dis = '不能参加本次活动';
			}
			$(this).parent().next().html(str_dis);
		})
	}else{
		$('input[name="checkIds[]"]').attr('disabled',false).addClass('useful');
	}
}

