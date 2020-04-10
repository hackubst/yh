function deleteHomeCartItem(shopping_cart_id)
{
		$.ajax({
		    url : '/HomeAjax/del_cart',   //请求地址
		    type : 'post',                          //请求方式
		    dataType : 'json',                      //数据传输方式
		    data:{id:shopping_cart_id},
		    success : function(data) {
		       if (data.code == '200')
				{
					//alert('恭喜您，购物车商品删除成功！');
					//location.reload();
				}
				else(data.code == '400')
				{
					// alert(data.msg);
				}
		    }
		});
}



function deleteCartItem(cart_id, obj)
{
	$.jPops.confirm(
	{  
		 title:"提示",  
		 content:"您确定要删除这个商品吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r)
		 {  
			 if(r)
			 {
				$.post('/FrontCart/delete_cart', {"cart_id":cart_id}, function(data){
					if (data != 'failure')
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
						alert('恭喜您，购物车商品删除成功！');
					}
					else
					{
						alert('对不起，删除失败！');
					}
				})
			 }
			return true;
		 }
	 });
}

function batchDeleteCartItem()
{
	var shopping_cart_ids = '';
	$('input:checkbox:checked').each(function(){
		shopping_cart_ids += $(this).val() + ',';
	});
	shopping_cart_ids = shopping_cart_ids.substr(0, shopping_cart_ids.length - 1);
	if (shopping_cart_ids == '')
	{
		$.jPops.confirm({
			title:"提示",
			content:"对不起，您没有选择任何商品",
			okBtnTxt:"确定",
			callback:function(r)
			{
				return true;
			}
		});
	}
	else
	{
		$.jPops.confirm(
		{  
			 title:"提示",  
			 content:"您确定要删除这些商品吗？",  
			 okBtnTxt:"确定",  
			 cancelBtnTxt:"取消",  
			 callback:function(r)
			 {  
				 if(r)
				 {
					$.post('/FrontCart/batchDeleteCartItem', {"shopping_cart_ids":shopping_cart_ids}, function(data){
						if (data == 'success')
						{
							alert('恭喜您，批量购物车商品删除成功！');
							location.reload();
						}
						else
						{
							alert('对不起，删除失败！');
						}
					})
				 }
				return true;
			 }
		 });
	}
}