$(function(){
	$('.del_item_gift').click(function(){
		var pi = $(this).parent().parent().attr('data_mate');
		if(!pi){
			return false;
		}else{
			$.jPops.confirm({  
			     title:"提示",  
			     content:"您确定要删除本次送礼活动吗？",  
			     okBtnTxt:"确定",  
			     cancelBtnTxt:"取消",  
			     callback:function(r){  
			         if(r){  
			        	 $.post('/AcpPromo/del_item_gift',{'act':'del','pi':pi},function(data){
			 				var info = eval("("+data+")");
			 				popAlert(info.message);
			 				if(info.type === 1)
			 				{
			 					location.reload();
			 				}
			 			});  
			         }  
			         return true;  
			     }  
			 }); 
		}
		
	});
	
});


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