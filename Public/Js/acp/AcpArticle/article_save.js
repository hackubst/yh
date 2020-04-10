$(function(){

	$("#J_FormArticle").validate({
		rules: {
			title: {
				required: true
			},
			sort_id: {
				equal_select: true
			},
			serial: {
				digits: true
			}
		},
		messages: {
			title: {
				required: "请输入标题"
			},
			sort_id: {
				equal_select: "请选择所属栏目"
			},
			serial: {
				digits: "请输入纯数字的排序号"
			}
		},
		errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
		success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
	});
});