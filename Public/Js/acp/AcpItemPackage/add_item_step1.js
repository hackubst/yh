$(function(){
    // ajax上传图片
    new AjaxUpload("#add_link", {
        action: "/AcpItemAjax/upload_img",
        name: "userfile",
        autoSubmit: true,
        responseType: "json",
        onComplete: function(file, response){
            var html = '<li class="fi-imgslist-item">'
                + '<img src="' + response.pic_url + '" alt="">'
                + '<input type="hidden" name="pic[]" value="' + response.pic_url + '">'
                + '<a href="javascript:;" class="del J_del_pic" title="删除这张图"><i class="gicon-remove"></i></a>'
                + '</li>';

            $(html).insertBefore("#J_add_pic");
        }
    });

    // 删除上传的图片
    $(".J_del_pic").live("click", function(){
        var img = $(this).prev().prev().attr("src");
        $(this).parent().remove();
        $.post("/AcpItemAjax/del_img", {img: img});
    });

    // 验证是否选择分类
    $.validator.addMethod("not_equal", function(value) {
        return value != '0.0';
    }, "");

    // 提交" + item_name + "基本信息时的表单验证
    $("#form_base").validate({
        rules: {
            category_id: {
                not_equal: true
            },
            item_name: {
                required: true
            },
            item_sn: {
                required: true
            },
            stock: {
                required: true,
                digits: true
            },
            market_price: {
                number: true
            },
            cost_price: {
                number: true
            },
            stock_alarm: {
                digits: true
            },
            weight: {
                digits: true
            }
        },
        messages: {
            category_id: {
                not_equal: "请选择" + item_name + "分类！"
            },
            item_name: {
                required: "" + item_name + "名称不能为空！"
            },
            item_sn: {
                required: "" + item_name + "货号不能为空！"
            },
            stock: {
                required: "库存数量不能为空！",
                digits: "请填写正确的库存数量!"
            },
            market_price: {
                number: "请填写正确的市场价！"
            },
            cost_price: {
                number: "请填写正确的成本价！"
            },
            stock_alarm: {
                digits: "请填写正确的警戒库存!"
            },
            weight: {
                digits: "请填写正确的" + item_name + "重量！"
            }
        },
        errorPlacement: acp.form_ShowError,
        success:acp.form_HideError
    });
});
