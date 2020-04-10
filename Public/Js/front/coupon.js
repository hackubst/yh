//显示优惠提示内容
function show_coupon()
{
	//异步取优惠数据
	var cont = '';
	$.ajax(
	{
		url:"/FrontOrder/getCouponInfo",
		type:"POST",
		dataType:"json",
		data:{
			"cart_ids":typeof(cart_ids) != "undefined" ? cart_ids : '',
			"number_str":typeof(number_str) != "undefined" ? number_str : '',
		},
		timeout:10000,
		success:function(d){
		console.log(d);
			if(d)
			{
				console.log(d);
				if (d.discount_amount == 0)
				{
					cont = '当前无优惠~~~';
					layer.open({
						style:'font-size: 15px;line-height: 25px;max-width: 80%;',
						content:cont
					});
				}
				else
				{
					layer.open({
						style:'font-size: 15px;line-height: 25px;max-width: 80%;',
						content:d.coupon_desc
					});
				}
			}
		}
	});
}
