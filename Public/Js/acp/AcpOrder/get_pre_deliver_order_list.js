function print_invoice()
{
	var html='<div id="print_invoice"></div>';

	var order_ids = '';
	var count = 0;
	$('input[name="a[]"]:checked').each(function()
	{
		count ++;
		order_ids += $(this).val() + ',';
	});
	if (!count)
	{
		alert('对不起，请选择至少一个订单进行打印！');
		return;
	}

	var order_ids = order_ids.substr(0, order_ids.length - 1);
	var deliver_info_list = '';

	//异步获取订单商品信息
	$.post('/AcpOrderAjax/get_deliver_info_list', {"order_ids":order_ids,"order_type":2}, function(data)
	{
		if (data == 'failure')
		{
			alert('对不起，获取订单信息失败，请重试！');
			return;
		}

		var len = data.length;
		var deliver_info_list = data;

		var item_list_tbody = '';
		var i = 0;
		for (i = 0; i < len; i ++)
		{
			var len_item = data[i]['item_list'].length;
			item_list_tbody += '<div style="page-break-after: always;" id="print_invoice' + i + '"';
			item_list_tbody += ' style="display:block">';
			//订单信息
			item_list_tbody += '订单号：' + data[i]['order_info']['order_id'];
			item_list_tbody += '<br>卖家：' + data[i]['order_info']['agent_name'];
			item_list_tbody += '<br>下单时间：' + data[i]['order_info']['addtime'];

			item_list_tbody += '<table class="wxtables">' + 
									'<colgroup>' + 
									'<col width="30%">' + 
									'<col width="20%">' + 
									'<col width="20%">' + 
									'<col width="26%">' + 
									'</colgroup>' + 
									'<thead>' + 
										'<tr>' + 
											'<td>商品名称</td>' + 
											'<td>商品货号</td>' + 
											'<td>规格属性</td>' + 
											'<td>数量</td>' + 
										'</tr>' + 
									'</thead>' + 
									'<tbody id="item_list_tbody">';
			var j = 0;
			for (j = 0; j < len_item; j ++)
			{
				item_list_tbody += '<tr>';
				item_list_tbody += '<td>' + data[i]['item_list'][j].item_name + '</td>';
				item_list_tbody += '<td>' + data[i]['item_list'][j].item_sn + '</td>';
				item_list_tbody += '<td>' + data[i]['item_list'][j].property + '</td>';
				item_list_tbody += '<td>' + data[i]['item_list'][j].number + '</td>';
				item_list_tbody += '</tr>';
			}
			item_list_tbody += '</tbody></table>';
			item_list_tbody += '<br>赠品：' + data[i]['order_info']['order_id'];
			item_list_tbody += '<br>管理员留言：' + data[i]['order_info']['admin_remark'];
			item_list_tbody += '<br>会员留言：' + data[i]['order_info']['user_remark'];
			item_list_tbody += '<br>物流公司：' + data[i]['order_info']['express_company_name'];
			item_list_tbody += '<br>收货人姓名：' + data[i]['order_info']['consignee'];
			item_list_tbody += '<br>收货人手机：' + data[i]['order_info']['mobile'];
			item_list_tbody += '<br>收货人地址：' + data[i]['order_info']['address'];
			//订单信息
			item_list_tbody += '</div>';
		}
		$('#print_invoice').css('display', 'none');
		$('#print_invoice').html(item_list_tbody);
	}, 'json')
	
	$.jPops.custom({
		title:"您确定要批量打印发货单吗？",  
		content:html,
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r)
		{  
			if(r)
			{
				$('#print_invoice').css('display', 'block');
				printdiv('print_invoice');
			}

			return true;
		}  
	});

	return false;
}

function batch_deliver()
{
	var html='';

	var order_ids = '';
	var count = 0;
	$('input[name="a[]"]:checked').each(function()
	{
		count ++;
		order_ids += $(this).val() + ',';
	});
	if (!count)
	{
		alert('对不起，请选择至少一个订单进行发货！');
		return;
	}

	var order_ids = order_ids.substr(0, order_ids.length - 1);
	var html='<form id="batch_deliver_form"><div class="formitems inline">'+
		'<label class="fi-name"><span class="colorRed">*</span>起始物流单号：</label>'+ 
		'<div class="form-controls">'+
			'<input name="start_express_number" id="start_express_number"><span class="fi-help-text"> </span>'+
		'</div></div>'+
	'</form>';
	
	$.jPops.custom({
		title:"批量发货",  
		content:html,
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r)
		{
			if(r)
			{
				var start_express_number = $('#start_express_number').val();
				$.validator.setDefaults(
				{
					//表单验证通过后的处理，异步提交表单
					submitHandler: function()
					{
						$.post('/AcpOrderAjax/batch_deliver',{"order_ids":order_ids,"start_express_number":start_express_number},function(data)
						{
							if (data == 'success')
							{
								alert('恭喜您，订单批量发货成功！');
								$('#express_number').val(start_express_number);
							}
							else
							{
								alert('对不起，订单批量发货失败！');
							}
						})
						acp.batchDeliverFormStatus = true;
					}
				});
			
				//表单验证规则
				$("#batch_deliver_form").validate(
				{
					rules: 
					{
						start_express_number: 
						{
							required: true,
						}
					},
					messages: 
					{
						start_express_number: 
						{
							required: "对不起，请填写起始物流单号",
						}
					},
					errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
					success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
				});

				//模拟提交表单
				$("#batch_deliver_form").submit();
				return acp.batchDeliverFormStatus;
			}
			else
			{
				return true;  
			}
		}
	});
}

function deliver_order(order_id){
		$.jPops.confirm({  
		 title:"提示",  
		 content:"您确定要发货吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r){  
			 if(r){  
				$.post('/AcpOrderAjax/deliver_order', {"order_id":order_id}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，订单已发货！');
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

function deliver_gift_order(order_id){
		$.jPops.confirm({  
		 title:"提示",  
		 content:"您确定要发货吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r){  
			 if(r){  
				$.post('/AcpIntegral/deliver_order', {"order_id":order_id}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，订单已发货！');
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
