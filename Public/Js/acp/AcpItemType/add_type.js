$(function(){
	//添加扩展属性
	$("#j-addsx").click(function() {
		var html='<form id="pop_form"><div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>属性名：</label>'+ 
				    '<div class="form-controls">'+
				        '<input type="text" name="p_name">'+
				        '<span class="fi-help-text"></span>'+
				    '</div></div>'+
				'<div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>排序：</label>'+ 
				    '<div class="form-controls">'+
				        '<input type="text" name="p_serial" value="0">'+
				        '<span class="fi-help-text"></span>'+
				    '</div></div>'+
				'<div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>属性值：</label>'+
				    '<div class="form-controls">'+
				        '<input type="text" class="xxlarge" name="p_value">'+
				        '<span class="fi-help-text">扩展属性的值，多个属性值可用“,”号隔开</span>'+
				    '</div></div></form>';
		$.jPops.custom({
			title:"添加扩展属性",
			content:html,
			callback:function(r){
				if (r) {
                    $.validator.setDefaults({
                        submitHandler: function() {
                            var p_name   = $("[name='p_name']").val();
                            var p_serial = $("[name='p_serial']").val();
                            var p_value  = $("[name='p_value']").val();

                            var str = '<tr><td><input type="text" name="prop_name[]" value="' + p_name + '"></td><td>'
                                + '<input type="text" class="xxlarge" name="prop_value[]" value="' + p_value + '"></td><td>'
                                + '<input type="text" class="mini" name="prop_serial[]" value="' + p_serial + '"></td><td>'
                                + '<input type="hidden" name="prop_id[]" value="0">'
                                + '<a href="javascript:;" class="btn J_prop_del" title="删除">删除</a></td></tr>';

                            $(".J_prop").append(str);
                            acp.popFormStatus=true;//设置弹窗表单验证状态为通过
                        }
                    });

                    //表单验证规则
                    $("#pop_form").validate({
                        rules: {
                            p_name: {
                                required: true
                            },
                            p_serial: {
                                required: true,
                                number: true
                            },
                            p_value: {
                                required: true
                            }
                        },
                        messages: {
                            p_name: {
                                required: "属性名不能为空"
                            },
                            p_serial: {
                                required: "属性排序不能为空",
                                number: "请输入正确格式的序号"
                            },
                            p_value: {
                                required: "属性值不能为空"
                            }
                        },
                        errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
                        success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
                    });

                    //模拟提交表单
                    $("#pop_form").submit();

                    return acp.popFormStatus;//通过表单验证状态确定是否关闭窗口
                } else {
                    return true;
                }
			}
		});

		return false;
	});

    // 删除扩展属性
    $(".J_prop_del").live("click", function(){
        var _this = this;
        $.jPops.confirm({
            title:"提示",
            content:"您确定要删除这个属性吗？",
            okBtnTxt:"确定",
            cancelBtnTxt:"取消",
            callback:function(r){
                if(r){
                    $(_this).parent().parent().remove();
                }
                return true;
            }
        });
    });

	//添加新规格
	$("#j-addgg").click(function() {
        // 判断规格数量是否超过两个
        var sku_num = $(".J_sku").find("tr").size();
        console.log(sku_num);
        if (sku_num >= 2) {
            $.jPops.alert({
                title:"提示",
                content:"每个商品类型最多包含两种规格属性！",
                okBtnTxt:"确定",
                callback:function(){
                    return true;
                }
            });
            return false;
        }

		var html='<form id="pop_form"><div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>规格名：</label>'+ 
				    '<div class="form-controls">'+
				        '<input type="text" name="s_name">'+
				        '<span class="fi-help-text"></span>'+
				    '</div></div>'+
				'<div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>排序：</label>'+ 
				    '<div class="form-controls">'+
				        '<input type="text" value="0" name="s_serial">'+
				        '<span class="fi-help-text"></span>'+
				    '</div></div>'+
				'<div class="formitems inline">'+
				    '<label class="fi-name"><span class="colorRed">*</span>规格属性值：</label>'+
				    '<div class="form-controls">'+
				        '<input type="text" class="xxlarge" name="s_value">'+
				        '<span class="fi-help-text">规格属性的值，多个属性值可用“,”号隔开</span>'+
				    '</div></div></form>';

		$.jPops.custom({
			title:"添加新规格",
			content:html,
			callback:function(r){
                if (r) {
                    $.validator.setDefaults({
                        submitHandler: function() {
                            var s_name   = $("[name='s_name']").val();
                            var s_serial = $("[name='s_serial']").val();
                            var s_value  = $("[name='s_value']").val();

                            var str = '<tr><td><input type="text" name="sku_name[]" value="' + s_name + '"></td><td>'
                                + '<input type="text" class="xxlarge" name="sku_value[]" value="' + s_value + '"></td><td>'
                                + '<input type="text" class="mini" name="sku_serial[]" value="' + s_serial + '"></td><td>'
                                + '<input type="hidden" name="sku_id[]" value="0">'
                                + '<a href="javascript:;" class="btn J_prop_del" title="删除">删除</a></td></tr>';

                            $(".J_sku").append(str);
                            acp.popFormStatus=true;//设置弹窗表单验证状态为通过
                        }
                    });

                    //表单验证规则
                    $("#pop_form").validate({
                        rules: {
                            s_name: {
                                required: true
                            },
                            s_serial: {
                                required: true,
                                number: true
                            },
                            s_value: {
                                required: true
                            }
                        },
                        messages: {
                            s_name: {
                                required: "规格名不能为空"
                            },
                            s_serial: {
                                required: "排序不能为空",
                                number: "请输入正确格式的序号"
                            },
                            s_value: {
                                required: "规格属性值不能为空"
                            }
                        },
                        errorPlacement: acp.form_ShowError,//显示出错信息(这段代码必须加)
                        success:acp.form_HideError//验证成功隐藏错误信息(这段代码必须加)
                    });

                    //模拟提交表单
                    $("#pop_form").submit();

                    return acp.popFormStatus;//通过表单验证状态确定是否关闭窗口
                } else {
                    return true;
                }
			}
		});

		return false;
	});

    // 点击上一步、下一步
    $(".j-wizardstep").click(function(){
        var type = $(this).data("type");
        var origin = $(this).data("origin");
        var step = $(this).data("step");
        var status = true;

        if (type == "prev") {
            acp.wizardstep(parseInt(step) - 1, origin);
        } else if (type == "next") {
            if (step == 1 && $("input[name='item_type_name']").val() == "") {
                status = false;
                $.jPops.alert({
                    title:"提示",
                    content:"商品类型名称不能为空！",
                    okBtnTxt:"确定",
                    callback:function(){
                        $("input[name='item_type_name']").focus();
                        return true;
                    }
                });
            } else if (step == 2) {
                $("[name='sku_name[]']").each(function(){
                    if ($(this).val() == "") {
                        status = false;
                        var _this = this;
                        $.jPops.alert({
                            title:"提示",
                            content:"规格名称不能为空！",
                            okBtnTxt:"确定",
                            callback:function(){
                                $(_this).focus();
                                return true;
                            }
                        });
                    }
                });

                $("[name='sku_value[]']").each(function(){
                    if ($(this).val() == "") {
                        status = false;
                        var _this = this;
                        $.jPops.alert({
                            title:"提示",
                            content:"规格属性值不能为空！",
                            okBtnTxt:"确定",
                            callback:function(){
                                $(_this).focus();
                                return true;
                            }
                        });
                    }
                });

                $("[name='sku_serial[]']").each(function(){
                    if ($(this).val() == "" || $(this).val().match("/\D/")) {
                        status = false;
                        var _this = this;
                        $.jPops.alert({
                            title:"提示",
                            content:"请填写正确的规格排序！",
                            okBtnTxt:"确定",
                            callback:function(){
                                $(_this).focus();
                                return true;
                            }
                        });
                    }
                });
            }

            if (status == true) {
                acp.wizardstep(parseInt(step) + 1, origin);
            }
        }
    });

    $("#form2").submit(function(){
        var status = true;

        $("[name='prop_name[]']").each(function(){
            if ($(this).val() == "") {
                status = false;
                var _this = this;
                $.jPops.alert({
                    title:"提示",
                    content:"属性名称不能为空！",
                    okBtnTxt:"确定",
                    callback:function(){
                        $(_this).focus();
                        return true;
                    }
                });
            }
        });

        $("[name='prop_value[]']").each(function(){
            if ($(this).val() == "") {
                status = false;
                var _this = this;
                $.jPops.alert({
                    title:"提示",
                    content:"属性值不能为空！",
                    okBtnTxt:"确定",
                    callback:function(){
                        $(_this).focus();
                        return true;
                    }
                });
            }
        });

        $("[name='prop_serial[]']").each(function(){
            if ($(this).val() == "" || $(this).val().match("/\D/")) {
                status = false;
                var _this = this;
                $.jPops.alert({
                    title:"提示",
                    content:"请填写正确的属性排序！",
                    okBtnTxt:"确定",
                    callback:function(){
                        $(_this).focus();
                        return true;
                    }
                });
            }
        });

        return status;
    });
});