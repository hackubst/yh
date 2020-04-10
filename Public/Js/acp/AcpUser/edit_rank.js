$(function(){
    // 展示上传图片
 /*   $(".J_logo_up").change(function(){
        ViewImage(this, "J_logo");
    });
*/
    // 表单验证
    $("#add_rank").validate({
        rules: {
            rankname: {
                required: true
            },
            money: {
                required: true,
                number: true
            },
            discount: {
                required: true,
                number: true
            }
        },
        messages: {
            rankname: {
                required: "等级名称不能为空！"
            },
            money: {
                required: "预消费额不能为空！",
                number: "请输入正确格式的数字！"
            },
            discount: {
                required: "请输入1-100的整数！",
                number: "请输入正确格式的数字！"
            }
        },
        errorPlacement: acp.form_ShowError,
        success:acp.form_HideError
    });
})

/*$(function(){
    $(".t-rank-tit a").click(function(){
		//$(".t-alert").show();
       // location.href = '/AcpUser/add_rank/mod_id/2/reidrect/{url_jiami($cur_url)}';
	})
	
	$(".ri,.t-but").click(function(){
		$(".t-alert").hide();
	})
})*/

function popMessage(message){
	$.jPops.message({  
	    title:"提示",  
	    content:message,  
	    timing:1000,  
	    callback:function(){  
	        // console.log("我是message的回调");  
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
	        return true;  
	    }  
	});  
}

	$('.del_rank').click(function(){
		var agent_rank_id = $(this).parent().find('input[name="rank_id"]').val();
		$.jPops.confirm({  
		     title:"提示",  
		     content:"您确定要删除该条级别信息吗？",  
		     okBtnTxt:"确定",  
		     cancelBtnTxt:"取消",  
		     callback:function(r){  
		         if(r){  
		             if(agent_rank_id)
		             {
		            	 $.jPops.showLoading(); 
		            	 $.post('/AcpUser/del_rank',{ rank:agent_rank_id},function(data){
		            		 $.jPops.hideLoading();
		            		 var info = eval("("+data+")");
		            		 if(info.type === 1)
		            		 {
		            			 popMessage(info.message);
		            			 window.setTimeout(function(){ location.reload();},3000)
		            		 }else{
		            			 popAlert(info.message);
		            		 }
		            		 
		            	 })
		             }else{
		            	 popAlert('错误的操作请求！');
		             }
		             
		         }  
		         else{  
		             // console.log("我是confirm的回调,false");  
		         }  
		         return true;  
		     }  
		 });
	})


