function toggleSelect(obj){
	var css = $(obj).parent().hasClass("selected");
	var merchant_obj = $(obj).parent().parent().parent();
	if(css){
		$(obj).parent().removeClass("selected");
	}else{
		$(obj).parent().addClass("selected");
	}
	checkMerchantSelected(merchant_obj);

	cal_total_price();
}

//商家选择框选中处理，若商家下所有商品均未选中，商家选择框选中，否则关闭；若商家下面没有商品，则删除整个商家
function checkMerchantSelected(merchant_obj)
{
	//商家选择框选中处理，若商家下所有商品均未选中，商家选择框选中，否则关闭
	var tag = false;
	var item_count = 0;
	$(merchant_obj).find('.item').each(function(){
		item_count ++;
		var css1 = $(this).hasClass("selected");
		if (css1)
		{
			tag = true;
		}
	});
	if (tag)
	{
		$(merchant_obj).find('.sel_shop_wrap').addClass('selected');
	}
	else
	{
		$(merchant_obj).find('.sel_shop_wrap').removeClass('selected');
	}

	console.log(item_count);
	if (!item_count)
	{
		console.log('aaa');
		$(merchant_obj).remove();
	}
}

function toggleSelectMerchant(obj){
	var css = $(obj).parent().hasClass("selected");
	var merchant_obj = $(obj).parent().parent().parent();
	if(css){
		//该商家全部不选中
		$(obj).parent().removeClass("selected");
		$(merchant_obj).find('.item.selected').each(function(){
			$(this).removeClass('selected');
		});
	}else{
		//该商家全部选中
		$(obj).parent().addClass("selected");
		$(merchant_obj).find('.item').each(function(){
			var css1 = $(this).hasClass("selected");
			if (!css1)
			{
				$(this).addClass('selected');
			}
		});
	}
	cal_total_price();
}

function toggleSelectAll(obj){
	var css = $(obj).parent().hasClass("selected");
	if(css){
		$(".icon_select").parent().removeClass("selected");
	}else{
		$(".icon_select").parent().addClass("selected");
	}
	cal_total_price();
}

$('.btn_pay').click(function()
{
	if (user_default_addr == "")
	{
		alert('对不起，请选择收货地址！');
		return false;
	}
	var number_list = '';
	var shopping_cart_id_list = '';
	$('.item.selected').each(function()
	{
		var obj = $(this).find('#item_num');
		var num = obj.val();
		var shopping_cart_id = obj.data('shopping_cart_id');
		number_list += num + ',';
		shopping_cart_id_list += shopping_cart_id + ',';
		//alert(number_list + ', ' + shopping_cart_id_list);
	});

	number_list = number_list.substr(0, number_list.length - 1);
	shopping_cart_id_list = shopping_cart_id_list.substr(0, shopping_cart_id_list.length - 1);
	//console.log(number_list);
	//console.log(shopping_cart_id_list);

	if (!number_list)
	{
		alert('对不起，请至少选择一个商品！');
		return false;
	}
	$('#number_list').val(number_list);
	$('#shopping_cart_id_list').val(shopping_cart_id_list);

	$('#cart_form').submit();
});

//直接改变输入框的值
$('#item_num').bind('blur', function()
{
	var $self = $(this),
		num = $self.val();

	var pattern = /^\d+$/;
	if(!pattern.test(num) || parseInt(num) <= 1)	//输入的是数字
	{
		num = 1;
	}
	$self.val(num);
	$self.data('pre_num', num);
	cal_total_price();

	//获取该商品价格
	/*$self.data('mall_price');
	var total_price = parseFloat($('#total_price').html());
	var totalNum = parseInt($('#totalNum').html());
	var totalPrice = parseFloat($('#totalPrice').html());
	var pre_num = parseInt($(this).next().val());
	var mall_price = parseFloat($(this).data('mall_price'));
	num = parseInt(num);
	total_price = mall_price * num;
	totalNum = totalNum + num - pre_num;
	totalPrice = totalPrice + (num - pre_num) * mall_price;

	$self.val(num);
	$self.data('pre_num', num);
	$('#total_price').html(total_price);
	$('#totalNum').html(totalNum);
	$('#totalPrice').html(totalPrice.toFixed(2));*/
});

//计算已选商品总价
function cal_total_price()
{
	var total_price = 0.00;
	var total_num = 0;
	$('.item.selected').each(function(){
		var item_num_obj = $(this).find('#item_num');
		var number = $(item_num_obj).val();
		var real_price = $(item_num_obj).data('real_price');
		total_price += parseFloat(real_price) * parseInt(number);
		total_num += parseInt(number);
		console.log('total_price='+total_price);
	});

	var number_list = '';
	var shopping_cart_id_list = '';
	$('.item.selected').each(function()
	{
		var obj = $(this).find('#item_num');
		var num = obj.val();
		var shopping_cart_id = obj.data('shopping_cart_id');
		number_list += num + ',';
		shopping_cart_id_list += shopping_cart_id + ',';
		//alert(number_list + ', ' + shopping_cart_id_list);
	});

	number_list = number_list.substr(0, number_list.length - 1);
	shopping_cart_id_list = shopping_cart_id_list.substr(0, shopping_cart_id_list.length - 1);
	cart_ids = shopping_cart_id_list;
	number_str = number_list;

	$.ajax(
	{
		url:"/FrontOrder/getCouponInfo",
		type:"POST",
		dataType:"json",
		data:{
			"cart_ids":cart_ids,
			"number_str":number_str,
		},
		timeout:10000,
		success:function(d){
		console.log(d);
			if(d)
			{
				console.log(d);
				if (d.total_amount != undefined)
				{
					var _item_amount = total_num > 0 ? d.item_amount.toFixed(2) : 0.00;
					$('#totalPrice').html(_item_amount);
					$('#totalNum').html(total_num);
				}
				else
				{
					$('#totalPrice').html(parseFloat(total_price).toFixed(2));
					$('#totalNum').html(total_num);
				}
			}
		}
	});

}

function delete_cart(cart_id, obj)
{
	$.ajax(
	{
		url:"/FrontCart/delete_cart",
		type:"POST",
		data:{
			cart_id: cart_id
		},
		timeout:10000,
		success:function(d){
			if(d) {
				if(d != 'failure')
				{
					var num = 0;
					var $self = obj.parent().find('#item_num');
					//获取该商品价格
					var total_price = parseFloat($('#total_price').html());
					var totalNum = $('#totalNum').html();
					totalNum = parseInt(totalNum);
					var totalPrice = $('#totalPrice').html();
					totalPrice = parseFloat(totalPrice);
					var pre_num = $self.val();
					pre_num = parseInt(pre_num);
					var mall_price = $self.data('real_price');
					mall_price = parseFloat(mall_price);
					num = parseInt(num);
					total_price = mall_price * num;
					totalNum = totalNum + num - pre_num;
					totalPrice = totalPrice + (num - pre_num) * mall_price;
					/*alert(totalNum + ', ' + totalPrice + ', ' + pre_num + ', ' + mall_price);
					console.log($self);
					console.log('num = ' + num);
					console.log('mall_price = ' + mall_price);
					console.log('pre_num = ' + pre_num);
					console.log('totalNum = ' + totalNum);
					console.log('totalPrice = ' + totalPrice);
					*/

					$('#total_price').html(total_price);
					$('#totalNum').html(totalNum);
					$('#totalPrice').html(totalPrice.toFixed(2));
					var merchant_obj = obj.parent().parent();
					$(obj).parents(".cart_goods").remove();

					console.log($(merchant_obj).html());
					checkMerchantSelected(merchant_obj);
				}
				else
				{
				}
			}
		}
	});
}

function sub(obj)
{
	var tmp=parseInt($(obj).next().val());
	tmp--;
	if(tmp<1)
	{
		tmp=1;
	}
	$(obj).next().val(tmp);
	cal_total_price();
	// coupon_updown();
}

function add(obj)
{
	var tmp=parseInt($(obj).prev().val());
	var stock = parseInt($(obj).parent().find('#stock').val());
	if (tmp > stock)
	{
		tmp = stock;
	}
	tmp++;
	$(obj).prev().val(tmp);
	cal_total_price();
	// coupon_updown();
	
}

function delet()
{
	//删除商品
	$('.item.selected').each(function(){
		var shopping_cart_id = $(this).find('#item_num').data('shopping_cart_id');
		delete_cart(shopping_cart_id, $(this));
	});
}
