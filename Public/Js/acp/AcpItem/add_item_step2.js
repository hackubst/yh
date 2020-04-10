$(function(){
    // 判断规格是否重复
    $(document).on("change", "select[name='sku0[]'],select[name='sku1[]']", function(){
        //如果选择值为空的话直接中断
        if (parseInt($(this).val()) == 0){
            return;
        }

        //当前触发的select
        var $trig_dom=$(this);

        //$dom_sku0_cur 当前触发规格的颜色dom
        //sku1_val_cur 当前触发规格的尺寸value
        if($(this).attr("name")=="sku0[]"){
            var $dom_sku0_cur=$trig_dom,
                sku1_val_cur=$trig_dom.parent("td").siblings("td").find("select[name='sku1[]']").val();
        }
        else{
            var $dom_sku0_cur=$trig_dom.parent("td").siblings("td").find("select[name='sku0[]']"),
                sku1_val_cur=$trig_dom.val();
        }

        //当前触发规格的颜色value
        var sku0_val_cur=$dom_sku0_cur.val();
        //缓存当前颜色列表dom
        var $dom_sku0_list=$dom_sku0_cur.parent("td").parent("tr").siblings("tr").find("select[name='sku0[]']");

        //对比每个颜色值
        $dom_sku0_list.each(function(){
            var $self=$(this);
            //只对比颜色值和触发dom值一样的select
            if($self.val()==sku0_val_cur){
                //获取尺码dom及它的值
                var $sku1=$self.parent("td").siblings("td").find("select[name='sku1[]']"),
                    sku1_val=$sku1.val();

                //当尺码也匹配时，则判定为重复规格，中断程序并给出提示
                if(sku1_val==sku1_val_cur){
                    $trig_dom.find("option:first").attr("selected",true);
                    $.jPops.alert({
                        title:"提示",
                        content:"已存在该规格！",
                        okBtnTxt:"确定",
                        callback:function(){
                            return true;
                        }
                    });
                    return false;
                }
            }
        });

    });

    // 是否开启规格属性
	$(".j-skupanelCtrl").live("change", function(){
		var val=$(this).val();
		if(val==1){
            var sku_num = $(":hidden[name='sku_num']").val();
            if (sku_num == 0) {
                var type_id = $("[name='item_type_id']").val();
                $.jPops.alert({
                    title:"提示",
                    content:"请到<a href='/AcpItemType/edit_type/id/" + type_id +"' target='_blank'>“" + item_name + "类型”</a>中添加规格属性。",
                    okBtnTxt:"确定",
                    callback:function(){
                        $(":radio[name='has_sku'][value='0']").attr("checked", "checked");
                        return true;
                    }
                });
            } else {
                $("#j-skupanel").show();
            }
		}
		else{
			$("#j-skupanel").hide();
		}
	});

	//var flag = 1;
	
	//生成部分规格
	$("#j-createPartsku").live("click", function(){
        var html = $("#J_type_sku").html();
		$.jPops.custom({
			title:"生成部分规格",
			content:html,
			okBtnTxt:"生成",
			callback:function(r){
				if (r) {
                    var item_sn = $("[name='item_sn']").val();
                    var price   = $("[name='mall_price']").val();
                    var stock   = $("[name='stock']").val();

                    var tr_num = $(".J_sku_tbody").find("tr.J_sku_show").size();

                    var str = '',
                    skuValue0 = '',
                    sku_sn = '';
                    
                    $(':checked[name="skuValue0[]"]').each(function(){
                    	var tmp_sku0_obj = $(this);
                        if ($(':checked[name="skuValue1[]"]').size() > 0) {		//如果该" + item_name + "类型拥有第二个规格属性，且这里已经勾选需要生成
//                        	log($(':checked[name="skuValue1[]"]').size());
                            $(':checked[name="skuValue1[]"]').each(function(){
                            	skuValue0 = $("#J_sku0_select").html().replace('name="J_sku0[]"', 'name="new_J_sku0_'+flag+'"').replace('value="' + tmp_sku0_obj.val() + '"', 'value="' + tmp_sku0_obj.val() + '" selected');
                                tr_num += 1;
                                if (item_sn) {
                                    sku_sn = item_sn + '-' + tr_num;
                                } else {
                                    sku_sn = '';
                                }
                                str += '<tr class="J_sku_show"><input type="hidden" name="new_sku[]" value="'+flag+'">';
                                str += '<td>' + skuValue0 + '</td>';
                                str += '<td>' + $("#J_sku1_select").html().replace('name="J_sku1[]"', 'name="new_J_sku1_'+flag+'"').replace('value="' + $(this).val() + '"', 'value="' + $(this).val() + '" selected') + '</td>';
                                str += '<td><input type="text" name="new_sku_sn'+flag+'" class="small sku_sn" value="' + sku_sn + '"></td>';
                                str += '<td><input type="text" name="new_sku_stock'+flag+'" class="small sku_stock" value="' + stock + '"></td>';
                                str += '<td><input type="text" name="new_sku_price'+flag+'" class="sku_price" value="' + price + '"></td>';
	                        	str += '<td><a href="javascript:;" class="btn J_sku_del" title="删除">删除</a></td>';
                                str += '</tr>';
                                str += "<tr id='hide_new' style='display:none;'>"
                                	+  "<td colspan='4'></td>"
                                	+  "<td colspan='2' style='text-align:right;'>"
                                	+  "<table class='wxtables tables-form' style='width: 230px;margin:0'>"
                                    +  "<colgroup>"
                                    +  "<col width='10%'>"
                                    +  "<col width='20%'>"
                                    +  "</colgroup><tbody>";
                                
                                var rank_str = '';
                                    sku_rank_price_obj = $('#J_add_sku_rank_price');
                                    sku_rank_price_obj.find('tr').each(function(){
//                                    	log($(this).find('td:first').html());
                                    	rank_str += '<tr><td class="tables-form-title">'+$(this).children('td').eq(0).html()+'</td>';
                                    	rank_str += '<td><input type="hidden" name="new_sku_rank_id'+flag+'[] value="'+$(this).children('td').eq(1).find('input:first-child').val()+'">';
                                    	rank_str += '<input type="text" class="mini" name="new_sku_rank_price'+flag+'[] value="">元</td></tr>'
                                    });
                                str += rank_str;
                                str += "</tbody></table>"
                                	+  "(如果不填写，则该级别默认取本规格的定价)"
                                	+  "</td>"
                                	+  "</tr>";
                                flag++;
                                
                            });
                        } else {
                        	if (!$("#J_sku1_select").html()) {	//如果该" + item_name + "类型没有第二个规格属性
                                tr_num += 1;
                        		if (item_sn) {
	                                sku_sn = item_sn + '-' + tr_num;
	                            } else {
	                                sku_sn = '';
	                            }
                        		skuValue0 = $("#J_sku0_select").html().replace('name="J_sku0[]"', 'name="new_J_sku0_'+flag+'"').replace('value="' + tmp_sku0_obj.val() + '"', 'value="' + tmp_sku0_obj.val() + '" selected');
	                            str += '<tr class="J_sku_show"><input type="hidden" name="new_sku[]" value="'+flag+'">';
	                            str += '<td>' + skuValue0 + '</td>';
	                            str += '<td><input type="text" name="new_sku_sn'+flag+'" class="small sku_sn" value="' + sku_sn + '"></td>';
	                            str += '<td><input type="text" name="new_sku_stock'+flag+'" class="small sku_stock" value="' + stock + '"></td>';
                                str += '<td><input type="text" name="new_sku_price'+flag+'" class="sku_price" value="' + price + '"></td>';
	                        	str += '<td><a href="javascript:;" class="btn J_sku_del" title="删除">删除</a></td>';
	                            str += '</tr>';
	                            str += "<tr id='hide_new' style='display:none;'>"
	                            	+  "<td colspan='3'></td>"
	                            	+  "<td colspan='2' style='text-align:right;'>"
	                            	+  "<table class='wxtables tables-form' style='width: 230px;margin:0'>"
	                                +  "<colgroup>"
	                                +  "<col width='10%'>"
	                                +  "<col width='20%'>"
	                                +  "</colgroup><tbody>";
	                            
	                            var rank_str = '';
	                                sku_rank_price_obj = $('#J_add_sku_rank_price');
	                                sku_rank_price_obj.find('tr').each(function(){
//	                                	log($(this).find('td:first').html());
	                                	rank_str += '<tr><td class="tables-form-title">'+$(this).children('td').eq(0).html()+'</td>';
	                                	rank_str += '<td><input type="hidden" name="new_sku_rank_id'+flag+'[]" value="'+$(this).children('td').eq(1).find('input:first-child').val()+'">';
	                                	rank_str += '<input type="text" class="mini" name="new_sku_rank_price'+flag+'[] value="">元</td></tr>'
	                                });
	                            str += rank_str;
	                            
	                            str += "</tbody></table>"
	                            	+  "(如果不填写，则该级别默认取本规格的定价)"
	                            	+  "</td>"
	                            	+  "</tr>";
                        	}else{		//进入这里，说明" + item_name + "本身有2个规格属性，但本次批量生成时，没有勾选第二规格属性
                        		return false;
                        	}
                        	flag++;
                        }
                    });
                    $(".J_sku_tbody").append(str);

                }
				return true;
			}
		})
	});
	
	function log(msg)
	{
		log(msg);
	}
	
    // 增加一个规格
    $(".J_add_sku").live("click", function(){
        var item_sn = $("[name='item_sn']").val();
        var price   = $("[name='mall_price']").val();
        var stock   = $("[name='stock']").val();

        var sku_sn = parseInt($(".J_sku_tbody").find("tr.J_sku_show").size()) + 1;
        if (item_sn) {
            sku_sn = item_sn + '-' + sku_sn;
        } else {
            sku_sn = '';
        }

        var str  = '<tr class="J_sku_show"><input type="hidden" name="new_sku[]" value="'+flag+'">';
            str += '<td>' + $("#J_sku0_select").html().replace('name="J_sku0[]"' , 'name="new_J_sku0_'+flag+'"') + '</td>';

            if ($("#J_sku1_select").html()) {	//如果该" + item_name + "类型有第二个规格属性
                str += '<td>' + $("#J_sku1_select").html().replace('name="J_sku1[]"' , 'name="new_J_sku1_'+flag+'"') + '</td>';
                var colspan = 4;
            }else{
            	var colspan = 3;
            }
            
            str += '<td><input type="text" name="new_sku_sn'+flag+'" class="small sku_sn" value="' + sku_sn + '"></td>'
            	+  '<td><input type="text" name="new_sku_stock'+flag+'" class="small sku_stock" value="' + stock + '"></td>'
            	+  '<td><input type="text" name="new_sku_price'+flag+'" class="sku_price" value="' + price + '"></td>'
            	+  '<td><a href="javascript:;" class="btn J_sku_del" title="删除">删除</a></td>'
            	+  '</tr>';
            
            str += "<tr id='hide_new' style='display:none;'>"
            	+  "<td colspan='"+colspan+"'></td>"
            	+  "<td colspan='2' style='text-align:right;'>"
            	+  "<table class='wxtables tables-form' style='width: 230px;margin:0'>"
                +  "<colgroup>"
                +  "<col width='10%'>"
                +  "<col width='20%'>"
                +  "</colgroup><tbody>";
            
            var rank_str = '';
                sku_rank_price_obj = $('#J_add_sku_rank_price');
                sku_rank_price_obj.find('tr').each(function(){
//                	log($(this).find('td:first').html());
                	rank_str += '<tr><td class="tables-form-title">'+$(this).children('td').eq(0).html()+'</td>';
                	rank_str += '<td><input type="hidden" name="new_sku_rank_id'+flag+'[]" value="'+$(this).children('td').eq(1).find('input:first-child').val()+'">';
                	rank_str += '<input type="text" class="mini" name="new_sku_rank_price'+flag+'[] value="">元</td></tr>'
                });
            str += rank_str;
            
            str += "</tbody></table>"
            	+  "(如果不填写，则该级别默认取本规格的定价)"
            	+  "</td>"
            	+  "</tr>";
         flag++;
        $(".J_sku_tbody").append(str);
    });

    
    // 删除规格属性
    $(".J_sku_del").live("click", function(){
        var _this = this;
        var href = window.location.href;
        var content = '删除这个属性，可能导致某些未付款或者未发货订单出现问题。您确定要删除它吗？';
        if(href.indexOf('add_item') > -1){
        	content = '确定删除这个属性吗？';
        }
        $.jPops.confirm({
            title:"警告！",
            content:content,
            okBtnTxt:"确定",
            cancelBtnTxt:"取消",
            callback:function(r){
                if(r){
                	$(_this).parent().parent().next().remove();
                    $(_this).parent().parent().remove();
                    flag--;
                    flag = flag?flag:1;
                    
                }
                return true;
            }
        });
    });
    
    $('.J_vip_price').live('click',function(){
//    	var item_sku_id = $(this).parent().parent().data('value');
    	$(this).parent().parent().next().slideToggle();
//    	$('#hide_'+item_sku_id).slideToggle();
    });
    
    

	//选择扩展属性
	$(document).on("change","#j-item-extend input[type=checkbox]",function(){
		var $self=$(this);
		
		if($self.is(":checked")){
			$self.parent().siblings("select").attr("disabled",false);
		}
		else{
			$self.parent().siblings("select").attr("disabled",true);
		}
	});

    // 添加扩展属性
    $("#J_add_extend_prop").live("click", function() {
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
                            var p_type   = $("[name='item_type_id']").val();
                            var p_name   = $("[name='p_name']").val();
                            var p_serial = $("[name='p_serial']").val();
                            var p_value  = $("[name='p_value']").val();

                            $.post("/AcpItemAjax/add_extend_prop", {'p_type': p_type, 'p_name': p_name, 'p_serial': p_serial, 'p_value': p_value}, function(data){
                                if (data) {
                                    var str = '<li><label><input type="checkbox" data-id="' + p_name
                                        + '" class="checkbox" name="extend_prop_id[]" value="' + data.prop_id + '" checked>' + p_name + '</label> '
                                        + '<select name="extend_prop_value[]"><option value="">--请选择--</option>';

                                    if (data.prop_value) {
                                        for (var i in data.prop_value) {
                                            str += '<option value="' + data.prop_value[i].id + '" data-serial="' + data.prop_value[i].serial + '">';
                                            str += data.prop_value[i].name + '</option>';
                                        }
                                    }

                                    str += '</select> <span class="fi-help-text inline hide"></span>';
                                    str += '<a href="###" data-prop="' +  data.prop_id + '" class="btn btn-blue j-showExtValPanel">添加值</a></li>';

                                    $(str).insertBefore(".J_li_add_extend");
                                } else {
                                    $.jPops.alert({
                                        title:"提示",
                                        content:"对不起，扩展属性添加失败！",
                                        okBtnTxt:"确定",
                                        callback:function(){
                                            return true;
                                        }
                                    });
                                }
                            });

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


	//添加扩展属性值
	$(".j-showExtValPanel").live("click", function(){
		var $btnShowPn=$(this);//添加值按钮
		if($btnShowPn.siblings(".j-addExtValPanel").length==0){
			var html='<span class="j-addExtValPanel" style="margin-left:10px;">'+
                        '<input type="text" class="extval">'+
                        '<a href="###" class="btn btn-blue j-addExtVal">添加</a> '+
                        '<a href="###" class="btn j-CancelExtVal">取消</a>'+
                    '</span>';
            $(html).insertAfter($btnShowPn);
		}
		var $panel=$btnShowPn.siblings(".j-addExtValPanel");//添加值容器
		$btnShowPn.hide();
		$panel.show();

		//取消添加
		$panel.find(".j-CancelExtVal").click(function(){
			$btnShowPn.show();
			$panel.hide();
		});

		//添加属性值
		$panel.find(".j-addExtVal").click(function(){
			var $btnadd=$(this),
				$input=$btnadd.siblings(".extval"),
				$select=$btnShowPn.siblings("select"),
                $label=$btnShowPn.siblings("label"),
				val=$btnadd.siblings(".extval").val();

			if(val!="" && val!=undefined){
                var serial  = parseInt($select.find("option").last().data("serial")) + 1;
                var prop_id = $label.find("input").val();
                $.post("/AcpItemAjax/add_prop_value", {prop_id: prop_id, prop_value: val, serial: serial}, function(data){
                    if (data) {
                        $select.append('<option value="'+data+'" data-serial="'+ serial + '" selected>'+val+'</option>');//向select中添加option
                    }
                });
			}
			
			$btnShowPn.show();
			$panel.hide();
			$input.val("");
		});
	});

    // 提交表单时验证
    $("#form_prop, #form_addItem").submit(function(){
        var status  = true;
        var content = '';
        var _this   = '';

        // 判断是否开启规格属性
        var has_sku = $(":checked[name='has_sku']").val();
        if (has_sku == 1) {
            
            $('.sku_stock').each(function(){
            	if ( isNaN( $(this).val() ) || $(this).val() == '') {
                    status  = false;
                    content = "请输入正确的" + item_name + "库存！";
                    _this   = this;
                    return false;
                }
            });
            

            $(".sku_price").each(function(){
                if ( isNaN( $(this).val() ) || $(this).val() == '') {
                    status    = false;
                    content   = "请输入正确的" + item_name + "价格！";
                        _this = this;
                    return false;
                }
            });

            $(".sku_sn").each(function(){
                if ($(this).val() == '') {
                    status    = false;
                    content   = "请输入" + item_name + "货号！";
                        _this = this;
                    return false;
                }
            });
            
            // 判读sku是否重复
            var arr_sku = [];
            $(".J_sku_tbody").find("tr.J_sku_show").each(function(){
                var str = '';
                	sku_obj0 = $(this).children('td').eq(0).find('select:first');
                	sku_obj1 = $(this).children('td').eq(1).find('select:first');
                	sku0 = sku_obj0.val();					//每一个sku的第一个规格属性值
                	sku1 = sku_obj1?sku_obj1.val():1;		//每一个sku的第二个规格属性值(如果存在第二个规格，则取该规格的值，否则赋予一个假参数1表示真)
                	sn	 = $(this).children('td').eq()
                
                if(sku0 == 0 || sku1 == 0)
                {
                	status    = false;
                    content   = "您有未选择的规格属性！";
                    _this = this;
                    return false;
                }else{
                	str = sku0 + ',' + sku1+',';
                }
                if ($.inArray(str, arr_sku) != -1) {
                    status    = false;
                    content   = "规格属性不能重复！";
                    _this = $(this).find("select").eq(0);
                    return false;
                }

                arr_sku.push(str);
            });
//            log(sku1);
        }

        // 判断选择的扩展属性的值是否为空
        $("#j-item-extend").find("label :checked").each(function(){
            var sel = $(this).parent().next();
            if (sel.val() == "") {
                status  = false;
                content = "请选择扩展属性的值！";
                _this   = sel;
                return false;
            }
        });

        if (status == false) {
            $.jPops.alert({
                title:"提示",
                content:content,
                okBtnTxt:"确定",
                callback:function(){
                    $(_this).focus();
                    return true;
                }
            });

            if ($(this).attr("id") == 'form_addItem') {
                $(".tabs_a").eq(1).click();
            }
        }

        return status;
    });
});
