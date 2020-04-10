function del(order_id, order_item_id)
{
	order_type = $('#order_type').val();
	$.jPops.confirm(
	{  
		 title:"提示",  
		 content:"您确定要删除这条数据吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r)
		 {  
			 if(r)
			 {
				 $.post('/AcpOrderAjax/delete_item', {"order_id":order_id,"order_item_id":order_item_id,"order_type":order_type}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，订单商品删除成功，请在页面下方修改订单价格！');
						location.reload();
					}
					else
					{
						alert('对不起，订单或订单商品不存在！');
					}
				});
			 }  
			 else{  
				 // console.log("我是confirm的回调,false");  
			 }  
			 return true;  
		 }  
	 });  	
}

function offline_pay(order_id, is_virtual_stock_order)
{
	var html='<form id="offline_pay_form"><div class="formitems inline">'+
	'<label class="fi-name"><span class="colorRed">*</span>交易单号：</label>'+ 
	'<div class="form-controls">'+
		'<input type="text" id="proof" name="proof" value = >'+
		'<span class="fi-help-text"> </span>'+
	'</div></div>'+
'<div class="formitems inline">'+
	'<label class="fi-name"><span class="colorRed">*</span>备注：</label>'+ 
	'<div class="form-controls">'+
		'<textarea cols="30" rows="5" id="change_admin_remark" name="admin_remark"></textarea>'+
		'<span class="fi-help-text"> </span>'+
	'</div></div>'+
	'</form>';

	$.jPops.custom({
			title:"已线下收款",
			content:html,
			callback:function(r){
				if (r) {
                    $.validator.setDefaults({
						//表单验证通过后的处理，异步提交表单
                        submitHandler: function() {
							var proof = $('#proof').val();
							var admin_remark = $('#change_admin_remark').val();
							$.post('/AcpOrderAjax/offline_pay', {"order_id":order_id,"is_virtual_stock_order":is_virtual_stock_order,"proof":proof,"admin_remark":admin_remark}, function(data, textStatus) 
							{
								if (data == 'success')
								{
									alert('恭喜您，订单已线下收款成功！');
									location.reload();
								}
								else
								{
									alert('对不起，订单线下收款失败！');
								}
							});
                        }
                    });

                    //表单验证规则
                    $("#offline_pay_form").validate({
                        rules: {
                            proof: {
                                required: true
                            },
                            admin_remark: {
                                required: true
                            }
                        },
                        messages: {
                            proof: {
                                required: "对不起，交易号不能为空"
                            },
                            admin_remark: {
                                required: "对不起，备注不能为空"
                            }
                        },
                        errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
                        success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
                    });

                    //模拟提交表单
                    $("#offline_pay_form").submit();
                } else {
                    return true;
                }
			}
		});
}

//打印某个div的内容
function printdiv(printpage)
{
	var headstr = "<html><head><title></title><style>.PageNext{page-break-after: always; height:1px;}</style></head><body>";
	var footstr = "</body></html>";
	var newstr = document.getElementById(printpage).innerHTML;
	var oldstr = document.body.innerHTML;
	document.body.innerHTML = headstr+newstr+footstr;
	window.print();
	document.body.innerHTML = oldstr;
	return false;
}

function delete_order(order_id){
	var order_type = $('#order_type').val();
		$.jPops.confirm({  
		 title:"提示",  
		 content:"您确定要删除这条数据吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r){  
			 if(r){  
				$.post('/AcpOrderAjax/delete_order', {"order_id":order_id,"order_type":order_type}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，订单删除成功！');
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

function batch_delete()
{
	var order_ids = '';
	var count = 0;
	$('input[name="a[]"]:checked').each(function()
	{
		count ++;
		order_ids += $(this).val() + ',';
	});
	if (!count)
	{
		alert('对不起，请选择至少一个订单进行删除！');
		return;
	}

	order_ids = order_ids.substr(0, order_ids.length - 1);
	$.jPops.confirm(
	{  
		title:"提示",  
		content:"您确定要删除这些数据吗？",  
		okBtnTxt:"确定",  
		cancelBtnTxt:"取消",  
		callback:function(r)
		{
			if(r)
			{  
				$.post('/AcpOrderAjax/batch_delete', {"order_ids":order_ids}, function(data, textStatus) 
				{
					if (data == 'success')
					{
						alert('恭喜您，批量删除订单成功！');
						location.reload();
					}
					else
					{
						alert('对不起，批量删除订单失败！');
					}
				});
			}  
			return true;  
		}
	});
}

//通过退换货
function passItemRefundChangeApply(item_refund_change_id)
{
	$.jPops.confirm(
	{
		 title:"提示",
		 content:"您确定要同意这条申请吗？",  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r)
		 {  
			 if(r)
			 {
				 $.post('/AcpOrderAjax/passItemRefundChangeApply', {"item_refund_change_id":item_refund_change_id}, function(data, textStatus) 
				 {
					if (data == 'success')
					{
						alert('恭喜您，已通过该条订单退款申请！');
						location.reload();
					}
					else
					{
						alert('对不起，订单退款申请未通过！');
					}
				});
			 }  
			 else{  
				 // console.log("我是confirm的回调,false");  
			 }  
			 return true;  
		 }
	});
}

//拒绝退换货
function refuseItemRefundChangeApply(item_refund_change_id)
{
	var html='<form id="refuse_refund_change_form"><div class="formitems inline">'+
				'<label class="fi-name"><span class="colorRed">*</span>备注：</label>'+ 
				'<div class="form-controls">'+
					'<textarea cols="30" rows="5" id="change_admin_remark" name="change_admin_remark"></textarea>'+
					'<span class="fi-help-text"> </span>'+
				'</div></div>'+
			'</form>';

	$.jPops.custom(
	{
		 title:"提示",
		 content:html,  
		 okBtnTxt:"确定",  
		 cancelBtnTxt:"取消",  
		 callback:function(r)
		 {  
			 if(r)
			 {
				$.validator.setDefaults({
					//表单验证通过后的处理，异步提交表单
					submitHandler: function() {
						 var admin_remark = $('#change_admin_remark').val();
						 $.post('/AcpOrderAjax/refuseItemRefundChangeApply', {"item_refund_change_id":item_refund_change_id,"admin_remark":admin_remark}, function(data, textStatus) 
						 {
							if (data == 'success')
							{
								alert('恭喜您，已拒绝该条订单退款申请！');
								location.reload();
							}
							else
							{
								alert('对不起，订单退款申请拒绝失败！');
							}
						});
				   }
				});

				//表单验证规则
				$("#refuse_refund_change_form").validate({
					rules: {
						change_admin_remark: {
							required: true
						}
					},
					messages: {
						change_admin_remark: {
							required: "对不起，备注不能为空"
						}
					},
					errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
					success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
				});

				//模拟提交表单
				$("#refuse_refund_change_form").submit();
			} else {
				return true;
			}
		 }
	});
}
