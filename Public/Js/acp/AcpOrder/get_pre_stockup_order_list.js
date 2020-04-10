function print_packing_list()
{
	var html='<div id="print_packing_list_div"></div>';

	var order_ids = '';
	$('input[name="a[]"]:checked').each(function()
	{
		order_ids += $(this).val() + ',';
	});

	var order_ids = order_ids.substr(0, order_ids.length - 1);

	//异步获取订单商品信息
	$.post('/AcpOrderAjax/get_order_item_list', {"order_ids":order_ids,"order_type":1}, function(data)
	{
		if (data == 'failure')
		{
			alert('对不起，获取商品列表失败，请重试！');
			return;
		}

		var len = data.length;
		var item_list_tbody = '';

		var i = 0;
		for (i = 0; i < len; i ++)
		{
			item_list_tbody += '<table class="wxtables">' + 
									'<colgroup>' + 
									'<col width="50%">' + 
									'<col width="20%">' + 
									'<col width="10%">' + 
									'<col width="20%">' + 
									'</colgroup>' + 
									'<thead>' + 
										'<tr>' + 
											'<td>商品名称</td>' + 
											'<td>商品货号</td>' + 
											'<td>数量</td>' + 
											'<td>规格属性</td>' + 
										'</tr>' + 
									'</thead>' + 
									'<tbody id="item_list_tbody">';
			item_list_tbody += '<tr>';
			item_list_tbody += '<td>' + data[i].item_name + '</td>';
			item_list_tbody += '<td>' + data[i].item_sn + '</td>';
			item_list_tbody += '<td>' + data[i].number + '</td>';
			item_list_tbody += '<td>' + data[i].property + '</td>';
			item_list_tbody += '</tr>';
			item_list_tbody += '</tbody></table>';
			//加入剪刀样式
			item_list_tbody += '';
		}

		$('#print_packing_list_div').html(item_list_tbody);
	}, 'json')
	
	$.jPops.custom({
		title:"打印配货单",  
		content:html,
		okBtnTxt:"打印",  
		cancelBtnTxt:"取消",  
		callback:function(r)
		{  
			if(r)
			{
				printdiv('print_packing_list_div');
			}

			return true;
		}  
	});

	return false;
}

function stockup_order(order_id){
	var order_type = $('#order_type').val();
		$.jPops.confirm({  
		 title:"提示",  
		 content:"您确定要设置成备货完成吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r){  
			 if(r){  
				$.post('/AcpOrderAjax/stockup_order', {"order_id":order_id}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，订单已设置备货完成！');
						location.reload();
					}
					else
					{
						alert('对不起，订单不存在！');
					}
				});
			 }  
			 else{  
			 }  
			 return true;  
		 }  
	 });  	
}
