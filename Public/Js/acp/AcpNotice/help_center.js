//by zhengzhen

$('.serial input').mouseenter(function(){
	$(this).css('cursor', 'pointer');
})
.click(function(){
	$(this).css('cursor', 'text');
	$(this).css('border-color', '#72b7cd');
})
.blur(function(){
	$(this).css('border-color', '#fff');
});

function setSerial(_id, _this){
	var initVal = _this.defaultValue;
	var curVal = $(_this).val();
	if(curVal == ''){
		$(_this).val(initVal);
		return false;
	}
	if(curVal == initVal){
		return false;
	}
	$.ajax({
		url: '/AcpHelpAjax/edit_serial',
		data: {id: _id, serial: curVal},
		dataType: 'json',
		beforeSend: function(){
			$(_this).prev('img').removeClass('hide');
			$(_this).addClass('hide');
		},
		success: function(data){
			$(_this).prev('img').addClass('hide');
			$(_this).removeClass('hide');
			if(data.status === 1){
				_this.defaultValue = curVal;
			} else {
				$(_this).val(initVal);
			}
			$.jPops.alert({
				title:"提示",
				content:data.msg,
				okBtnTxt:"确定",
				callback:function(){
					return true;
				}
			});
		}
	});
}

function delArticle(_id, _this){
	$.jPops.confirm({
		title:"提示",
		content:'您确定要删除这条数据吗？',
		okBtnTxt:"确定",
		cancelBtnTxt:"取消",
		callback:function(r){
			if(r){
				$.ajax({
					url: '/AcpHelpAjax/del_help',
					data: {id: _id},
					dataType: 'json',
					beforeSend: function(){
						$.jPops.showLoading();
					},
					success: function(data){
						$.jPops.hideLoading();
						if(data.status === 1){
							$(_this).parents('tr').fadeOut(400, function(){
								$(this).remove();
							});
						}
						$.jPops.alert({
							title:"提示",
							content:data.msg,
							okBtnTxt:"确定",
							callback:function(){
								return true;
							}
						});
					}
				});
			}
			return true;
		}
	});
}

function delArticleBatch(){
	var _ids = [];
	$('._checkbox :checked').each(function(){
		_ids.push($(this).val());
	});
	if(_ids.length === 0){
		$.jPops.alert({
			title:"提示",
			content:'对不起，请选择要删除项！',
			okBtnTxt:"确定",
			callback:function(){
				return true;
			}
		});
		return false;
	}
	$.jPops.confirm({
		title:"提示",
		content:'您确定要删除这些数据吗？',
		okBtnTxt:"确定",
		cancelBtnTxt:"取消",
		callback:function(r){
			if(r){
				$.ajax({
					url: '/AcpHelpAjax/del_help',
					data: {id: _ids},
					dataType: 'json',
					beforeSend: function(){
						$.jPops.showLoading();
					},
					success: function(data){
					//	console.log(data);
						$.jPops.hideLoading();
						if(data.status === 1){
							for(var i in data){
								if(data[i].error === 0){
									$('._checkbox input:checked[value="' + data[i].id + '"]').parents('tr').fadeOut(400, function(){
										$(this).remove();
									});
								}
							}
						}
						$.jPops.alert({
							title:"提示",
							content:data.msg,
							okBtnTxt:"确定",
							callback:function(){
								return true;
							}
						});
					}
				});
			}
			return true;
		}
	});
}