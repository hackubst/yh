/*
 * @author zhoutao@360shop.cc zhoutao0928@sina.com
 * 短信设置页面的JS 
 */
function popNotice(message)
    {
        $.jPops.alert({  
            title:"提示",  
            content:message,  
            okBtnTxt:"确定",  
            callback:function(){  
              //  console.log("我是alert的回调");
                return true;  
            }  
        });
    }

function popAlert(message)
{
    $.jPops.alert({  
    title:"提示",  
    content:message,  
    okBtnTxt:"确定",  
    callback:function(){  
        // console.log("我是alert的回调"); 
        return true;  
    }  
});
}


$(function(){
	//恢复默认模板
	$('.rest').click(function(){
		var input_obj = $(this).prev();
		var def_val = input_obj.val();
		var input_name = input_obj.attr('name');
		$('#rest_'+input_name).val(def_val);
	})
	
	//保存当前模板
	$('.sms_save').click(function(){
		var input_obj = $(this).parent().prev().prev().find('input[type="hidden"]');
		var type = input_obj.attr('name');
		
		var state    = $(this).parent().prev().prev().find('input[name='+type+'_state]:checked').val();
		var to_admin = $(this).parent().prev().prev().find('input[name='+type+'_to_admin]:checked').val();
		var template = $('#rest_'+type).val();
		
		state = state?state:0;
		to_admin = to_admin?to_admin:0;
		if(!template)
		{
			popNotice('不能为空');return false;
		}
		$.jPops.showLoading();
		$.post('/AcpConfig/sms_config',{'type':type,'state':state,'to_admin':to_admin,'sms_text':template,'act':'ajaxset'},function(data){
			$.jPops.hideLoading(); 
			var info = eval("("+data+")");
			popNotice(info.message);
		})
		
//		console.log(state);
//		console.log(to_admin);
//		console.log(template);
		
	})
	
	//重置当前模板
	$('.rest_now').click(function(){
		var textarea_obj = $(this).parent().prev().find('textarea');
		textarea_obj.val(textarea_obj.get(0).defaultValue);
	})
	
	//测试发送短信
	$('.test_send').click(function(){
		var mobile = $(this).prev().val();
		if(!mobile)
		{
			popAlert('没有接受者');
			return false;
		}else{
			$.jPops.showLoading();
			$.post('/AcpConfig/sms_config',{'mobile':mobile,'act':'test_send'},function(data){
				$.jPops.hideLoading(); 
				var info = eval("("+data+")");
				if(info.type == 1)
				{
					$('.flag_total').html($('.flag_total').html()-1);
				}
				popAlert(info.message);
			})
		}
	})
	
})