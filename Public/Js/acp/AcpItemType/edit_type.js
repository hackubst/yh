$(function(){
    $("#form1").validate({
        rules: {
            item_type_name: {
                required: true
            }
        },
        messages: {
            item_type_name: {
                required: "商品类型名称不能为空！"
            }
        },
        errorPlacement: acp.form_ShowError,
        success:acp.form_HideError
    });

    $("#form3").submit(function(){
        var status = true;
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

        return status;
    });
});