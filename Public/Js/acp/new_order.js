var t = setInterval("get_new_order()", 2000);
function get_new_order()
{
	$.post('/AcpOrder/get_new_order', {}, function(data, textStatus) 
		 {
			 console.log(data);
			if (parseInt(data) > 0)
			{
				play_click(music, parseInt(data));
			}
		});
}

function play_click(url, order_num){
	var div = document.getElementById('div1');
	div.innerHTML = '<audio controls="controls" autoplay="autoplay"> <source src="' + url + '" type="audio/ogg"> <source src="' + url + '" type="audio/mpeg"> Your browser does not support the audio tag. </audio>';
	var emb = document.getElementsByTagName('audio')[0];
	if (emb) {
		/* 这里可以写成一个判断 wav 文件是否已加载完毕，以下采用setTimeout模拟一下 */
		div = document.getElementById('div2');
		div.innerHTML = '<b style="color:white;font-size:20px;">有 <label style="color:#FFFFFF;font-size:25px;"><a href="/AcpOrder/get_pre_deliver_order_list">' + order_num + '</a></label> 个新订单</b>';
		//unhandled_order_num += order_num;
		//$('#unhandled_order_num').html('(' + unhandled_order_num + ')');
		setTimeout(function(){div.innerHTML='';},3000);
	}
}
