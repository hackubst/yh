$(function() {
    acp.tabsFromOrigin="additem";//设置表单跳转的tabs origin

    $("#form_addItem").validate({
        // debug: true,
        ignore: "",
        rules: {
            //tabsindex1
            category_id: {
                tabform_required: true
            },
            item_name: {
                tabform_required: true
            },
            item_sn: {
                tabform_required: true
            },
            serial: {
                tabform_required: true
            },
            market_price: {
                tabform_required: true,
                tabform_number: true
            },
            stock: {
                tabform_required: true,
                tabform_number: true
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
            },
            least_purchase_number: {
                digits: true
            }
            //tabsindex3
            /*contents: {
                tabform_required: true
            }*/
        },
        messages: {
            //tabsindex1
            category_id: {
                tabform_required: "请选择" + item_name + "分类"
            },
            item_name: {
                tabform_required: "" + item_name + "名称不能为空！"
            },
            item_sn: {
                tabform_required: "" + item_name + "货号不能为空！"
            },
            serial: {
                tabform_required: "排序号不能为空！",
                tabform_number: "请填写正确的排序号!"
            },
            market_price: {
                tabform_required: "市场价格不能为空！",
                tabform_number: "请填写正确的市场价！"
            },
            stock: {
                tabform_required: "库存数量不能为空！",
                tabform_number: "请填写正确的库存数量!"
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
            },
            least_purchase_number: {
                digits: "请填写正确的最少购买数量!"
            }
            //tabsindex3
            /*contents: {
                tabform_required: "" + item_name + "详情不能为空！"
            }*/
        },
        errorPlacement: acp.form_ShowError,
        success: acp.form_HideError
    });
});
