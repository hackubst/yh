function toggleSelect(obj){
	var css = $(obj).parent().hasClass("selected");
	if(css){
		$(obj).parent().removeClass("selected");
	}else{
		$(obj).parent().addClass("selected");
		}
	}
	
function toggleSelectAll(obj){
	var css = $(obj).parent().hasClass("selected");
	if(css){
		$(".icon_select").parent().removeClass("selected");
	}else{
		$(".icon_select").parent().addClass("selected");
		}
	}	

$('#cart_form').submit(function()
{
	var number_list = '';
	var shopping_cart_id_list = '';
	$('#list').find('.item.selected').each(function()
	{
		var obj = $(this).find('#item_num');
		var num = obj.val();
		var shopping_cart_id = obj.data('shopping_cart_id');
		number_list += num + ',';
		shopping_cart_id_list += shopping_cart_id + ',';
	});

	number_list = number_list.substr(0, number_list.length - 1);
	shopping_cart_id_list = shopping_cart_id_list.substr(0, shopping_cart_id_list.length - 1);

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
$('.item_num').bind('blur', function()
{
	var $self = $(this),
		num = $self.val();

	var pattern = /^\d+$/;
	if(!pattern.test(num) || parseInt(num) <= 1)	//输入的是数字
	{
		num = 1;
	}

	//获取该商品价格
	$self.data('mall_price');
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
	$('#totalPrice').html(totalPrice);
});

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
					var $self = $(obj).prev().find('#item_num');
					//获取该商品价格
					var total_price = parseFloat($('#total_price').html());
					var totalNum = parseInt($('#totalNum').html());
					var totalPrice = parseFloat($('#totalPrice').html());
					var pre_num = parseInt($self.val());
					var mall_price = parseFloat($self.data('mall_price'));
					num = parseInt(num);
					total_price = mall_price * num;
					totalNum = totalNum + num - pre_num;
					totalPrice = totalPrice + (num - pre_num) * mall_price;

					$('#total_price').html(total_price);
					$('#totalNum').html(totalNum);
					$('#totalPrice').html(totalPrice);
					$(obj).parents(".cart_goods").remove();
				}
				else
				{
				}
			}
		}
	});
}

function add_cart()
{
}
