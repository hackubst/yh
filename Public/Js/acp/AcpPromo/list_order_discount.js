$(function(){
	$('.del_order_promo').click(function(){
		var id = $(this).parent().parent().attr('data_ma');
//		console.log(id);
		var promo_name = $(this).parent().parent().find('td:first-child').html();
//   	 console.log(promo_name);
		$.jPops.confirm({  
		     title:"提示",  
		     content:"您确定要删除该活动'"+promo_name+"'吗？",  
		     okBtnTxt:"确定",  
		     cancelBtnTxt:"取消",  
		     callback:function(r){  
		         if(r){  
		        	 $.jPops.showLoading();
		        	 $.post('/AcpPromo/del_order_discount',{'act':'del','pi':id},function(data){
		        		$.jPops.hideLoading();  
		        		var info = eval("("+data+")");
		        		if(info.type == 1)
		        		{
		        			$.jPops.message({  
		        			    title:"提示",  
		        			    content:info.message,  
		        			    timing:1000,  
		        			    callback:function(){  
		        			    	location.reload();  
		        			    }  
		        			});  
		        		}
		        	 });
		         } 
		         return true;  
		     }  
		 });
	});
	
});