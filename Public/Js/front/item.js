function addClickdot(item_id)
{
	$.post('/FrontMall/addClickdot', {'item_id': item_id}, function(data){
		if (data != 'failure')
		{
		}
		else
		{
		}
	}, 'json');
}
