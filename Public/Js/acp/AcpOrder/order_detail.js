//订单备注
$('#remark_btn').click(function(){
	var admin_remark = $('#admin_remark').val();
	var order_type = $('#order_type').val();
	var html='<form id="remark_form"><div class="formitems inline">'+
		'<label class="fi-name"><span class="colorRed">*</span>备注：</label>'+ 
		'<div class="form-controls">'+
			"<textarea name='remark' id='remark' cols='80' rows='5'>" + admin_remark + "</textarea>" + 
			'<span class="fi-help-text"> </span>'+
		'</div></div>'+
	'</form>';
	$.jPops.custom({
		title:"备注",  
		content:html,
		//content:"<textarea id='remark' cols='80' rows='5'>" + admin_remark + "</textarea>",  
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r){  
			if(r)
			{
				$.validator.setDefaults(
				{
					//表单验证通过后的处理，异步提交表单
					submitHandler: function()
					{
						var remark = $('#remark').val();
						var order_id = $('#order_id').val();
						$.post('/AcpOrderAjax/remark_order',{"order_id":order_id, "remark":remark,"order_type":order_type},function(data)
						{
							if (data == 'success')
							{
								alert('恭喜您，添加订单备注成功，代理商将看到您填写的备注内容！');
								$('#admin_remark').val(remark);
							}
							else
							{
								alert('对不起，添加订单备注失败！');
							}
						})
						acp.remarkFormStatus = true;
					}
				});

				//表单验证规则
				$("#remark_form").validate(
				{
					rules: 
					{
						remark: 
						{
							required: true
						}
					},
					messages: 
					{
						remark: 
						{
							required: "对不起，备注不能为空"
						}
					},
					errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
					success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
				});

				//模拟提交表单
				$("#remark_form").submit();
				return acp.remarkFormStatus;
			}
			else
			{
				return true;  
			}
		}  
	});
	
})

//修改订单价格
$('#change_price_btn').click(function(){
	var pay_amount = $('#pay_amount').val();
	var html='<form id="change_price_form"><div class="formitems inline">'+
	'<label class="fi-name"><span class="colorRed">*</span>订单实付款：</label>'+ 
	'<div class="form-controls">'+
		'<input type="text" id="change_pay_amount" name="change_pay_amount" value="' + pay_amount + '">'+
		'<span class="fi-help-text"> </span>'+
	'</div></div>'+
	'</form>';
	$.jPops.custom({
		title:"修改订单价格",  
		content:html,
		//content:"商品总价格：<input class='small' id='change_total_amount' value='" + total_amount + "'>元<br>物流费用：<input id='change_express_fee' value='" + express_fee + "'>元",
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r){  
			if(r){
				$.validator.setDefaults(
				{
					//表单验证通过后的处理，异步提交表单
					submitHandler: function()
					{
						//@#$加入pay_amount
						var change_pay_amount = $('#change_pay_amount').val();
						//var change_total_amount = $('#change_total_amount').val();
						//var change_express_fee = $('#change_express_fee').val();
						var order_id = $('#order_id').val();
						//$.post('/AcpOrderAjax/edit_price',{"order_id":order_id, "change_total_amount":change_total_amount, "change_express_fee":change_express_fee},function(data){
						$.post('/AcpOrderAjax/edit_price',{"order_id":order_id, "change_pay_amount":change_pay_amount},function(data){
							if (data == 'success')
							{
								alert('恭喜您，订单价格修改成功！');
								location.reload();
							}
							else
							{
								alert('对不起，订单价格修改失败！');
							}
						})
						acp.changePriceFormStatus = true;
					}
				});

				//表单验证规则
				$("#change_price_form").validate(
				{
					rules: 
					{
						//@#$加入pay_amount
						/*total_amount: 
						{
							required: true,
							num: true,
						},
						express_fee: 
						{
							required: true,
							num: true,
						}*/
						pay_amount: 
						{
							required: true,
							num: true,
						}
					},
					messages: 
					{
						/*total_amount: 
						{
							required: "对不起，商品总金额不能为空",
							num: "对不起，商品总金额必须是数字"
						},
						express_fee: 
						{
							required: "对不起，物流费用不能为空",
							num: "对不起，物流费用必须是数字"
						},*/
						pay_amount: 
						{
							required: "对不起，订单实付款不能为空",
							num: "对不起，订单实付款必须是数字"
						},
					},
					errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
					success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
				});

				//模拟提交表单
				$("#change_price_form").submit();
				return acp.changePriceFormStatus;
			}
			else
			{
				return true;  
			}
		}  
	});
})

//关闭订单
$('#cancel_order_btn').click(function(){
	var order_type = $('#order_type').val();
	$.jPops.custom({
		title:"提示",  
		content:"您确定要关闭该订单吗？",
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r){  
			if(r){  
				var order_id = $('#order_id').val();
				$.post('/AcpOrderAjax/cancel_order',{"order_id":order_id,"order_type":order_type},function(data){
					if (data == 'success')
					{
						alert('恭喜您，订单已关闭！');
						location.reload();
					}
					else
					{
						alert('对不起，订单关闭失败！');
					}
				})
			}  
			return true;  
		}  
	});
	
})
