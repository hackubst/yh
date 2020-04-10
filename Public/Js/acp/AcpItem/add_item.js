$(function(){
    // 根据商品类型获取商品属性
    $("select[name='item_type_id']").change(function(){
        var type_id = $(this).val();

        if (type_id == 0) {
            var html = '<p class="edit_item_tip">设置商品属性前，请先选择商品类型。</p>';
            $("#J_item_prop").html(html);
        } else {
			get_properys(type_id);
        }
    });
    
    $('.jump').click(function(){
    	var index = $(this).data('value');
    	acp.switchTabByDataIndex(index,"additem");
    })
});

function get_properys(type_id)
{
	$.post("/AcpItemAjax/get_type_prop", {'type_id': type_id},function(data){
		if (data) {
			var html = '<div class="formitems inline">';
			html += '<label class="fi-name">商品扩展属性：</label>';
			html += '<div class="form-controls">';
			html += '<ul id="j-item-extend">';
				
			for (var i in data.extend) {
				html += '<li><label><input type="checkbox" data-id="' + data.extend[i].property_name + '" class="checkbox"'
					+ 'name="extend_prop_id[]" value="' + data.extend[i].property_id + '">' + data.extend[i].property_name + '</label> ';
				html += '<select name="extend_prop_value[]" disabled><option value="">--请选择--</option>';

				for (var j in data.extend[i].prop_value) {
					html += '<option value="' + data.extend[i].prop_value[j].property_value_id + '" data-serial="'
						+ data.extend[i].prop_value[j].serial +  '">' + data.extend[i].prop_value[j].property_value + '</option>';
				}
				html += '</select> <span class="fi-help-text inline hide"></span>';
				html += '<a href="###" data-prop="{$row1.property_id}" class="btn btn-blue j-showExtValPanel">添加值</a></li>';
			}

			html += '<li class="J_li_add_extend"><label></label><button class="btn btn-orange" id="J_add_extend_prop">'
				+ '添加扩展属性</button></li></ul></div></div>';
			html += '<div class="formitems inline"><label class="fi-name">商品规格属性：</label><div class="form-controls">'
				+ '<div class="radio-group"><label><input type="radio" name="has_sku" class="j-skupanelCtrl" value="0" checked>关闭</label> '
				+ '<label><input type="radio" name="has_sku" class="j-skupanelCtrl" value="1">开启</label></div>'
				+ '<span class="fi-help-text">开启规格前先填写基本信息，可自动复制信息到每个规格。</span></div></div>';
			html += '<div class="tablesWrap" id="j-skupanel" style="display:none;"><div class="tables-searchbox">'
				+ '<a href="javascript:;" class="btn btn-blue" id="j-createPartsku">生成部分规格</a> '
				+ '<a href="javascript:;" class="btn btn-blue J_add_sku">增加一个规格</a></div>';

			if (data.sku != null) {
				var sku_num = data.sku.length;
			} else {
				var sku_num = 0;
			}
			html += '<input type="hidden" name="sku_num" value="' + sku_num + '" />';

			if (sku_num != 0) {
				html += '<table class="wxtables"><colgroup>';
				for (var k = 0; k < sku_num; k++) {
					html += '<col width="12%">';
				}

				html += '<col width="12%"><col width="12%"><col width="12%"><col width="14%"></colgroup><thead><tr>';
				for (var k = 0; k < sku_num; k++) {
					html += '<td>' + data.sku[k].property_name+'</td>';
				}
				html += '<td>货号</td><td>库存</td><td>商品价格</td><td>操作</td></tr></thead><tbody class="J_sku_tbody"></tbody></div>';

				html += '<div hidden="hidden" id="J_type_sku">';
				for (i in data.sku) {
					html += '<div class="formitems inline"><label class="fi-name">' + data.sku[i].property_name + '：</label> ';
					html += '<div class="form-controls"><div class="checkbox-group">';
					for (j in data.sku[i].prop_value) {
						html += '<label><input type="checkbox" name="skuValue' + i + '[]" value="' + data.sku[i].prop_value[j].property_value_id
							+ '">' + data.sku[i].prop_value[j].property_value + '</label> ';
					}
					html += '</div><span class="fi-help-text error"></span></div></div>';
				}
				html += '</div>';

				for (i in data.sku) {
					html += '<div hidden="hidden" id="J_sku' + i + '_select"><select class="small" name="J_sku' + i + '[]">'
						+ '<option value="0">选择' + data.sku[i].property_name + '</option>';
					for (j in data.sku[i].prop_value) {
						html += '<option value="' + data.sku[i].prop_value[j].property_value_id + '">'
							+ data.sku[i].prop_value[j].property_value + '</option>';
					}
					html += '</select></div>';
				}
			}

			$("#J_item_prop").html(html);
		}
	});
}
