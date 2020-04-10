function collect(merchant_id)
{
	$.post('/FrontMall/collect', {"merchant_id":merchant_id}, function(data, textStatus) 
	{
		if (data != 'failure')
		{
		}
		else
		{
		}
	});
}

function cancel_collect(merchant_id)
{
	$.post('/FrontMall/cancel_collect', {"merchant_id":merchant_id}, function(data, textStatus) 
	{
		if (data != 'failure')
		{
			$('#fav_total_num').text(parseInt(data));
		}
		else
		{
		}
	});
}
