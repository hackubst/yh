var b = false;
//添加批发价区间
function add_qj(){
    r = check_qj();
    if(r){
    	b = false;
    	return;
    }
    var html = '<div class="formitems inline">';  
    html +='<label class="fi-name"><span class="colorRed">*</span>购买数量：</label>';
    html += '<div class="form-controls"><input type="text" placeholder="" class="mini " name="min_num[]"  value=""><span class="btn" onclick="del_qj($(this))">删除</span><span class="fi-help-text hide"></span></div>';
    html += '<label class="fi-name"><span class="colorRed">*</span>批发优惠：</label><div class="form-controls"><input type="text" placeholder="" class="mini " name="price[]"  value="">元<span class="fi-help-text hide"></span></div></div>';

    $('#set_btn').before(html);
}

//删除批发价区间
function del_qj(obj){
	obj.parent().parent().remove();
}

function ts(msg){
	$.jPops.message({title:"操作提示",content:msg ,timing:2000});
}


function check_qj(){
	$('input[name="min_num[]"]').each(function(){
        if($(this).val() == '' || !(/^(\+|-)?\d+$/.test( $(this).val() )) || $(this).val() < 0 ){
        	b = true;
        	$(this).focus().select();
        	ts('请填写数量并且数量必须为大于0的整数');
        	return;
        }
    });
    if(b){
    	
    	return b; 
    }
    /*$('input[name="max_num[]"]').each(function(){
        if($(this).val() == '' || !(/^(\+|-)?\d+$/.test( $(this).val() )) || $(this).val() < 0 ){
        	b = true;
        	$(this).focus().select();
        	ts('请填写完整的数量区间并且数量必须为大于0的整数');
        	return;
        }
    });
    if(b){
    	
    	return b;
    }*/
    $('input[name="price[]"]').each(function(){
        if($(this).val() == '' || isNaN($(this).val()) || $(this).val() < 0 ){
        	b = true;
        	$(this).focus().select();
        	ts('请填写批发价并且批发价必须为大于0的数字');
        	return;
        }
    });
    if(b){
    	
    	return b;
    }
}


$(function(){
	$('.submit').on('click',function(){
		r = check_qj();
	    if(r){
	    	b = false;
	    	return;
	    }
	    $('#wholesale').submit();
	});
})