// 对商品添加购物车
$('.addto_cart').on('click',function(e)
     {
               e.stopPropagation();
               var mall_price = $(this).siblings('#mall_price').val();
               var item_id = $(this).siblings('#item_id').val();               
               console.log("item_id="+item_id+"mall_price="+mall_price);
              /* if (!item_id)
               {
                    layer.open({
                        content: '抱歉，请选择商品！',
                        style:'background-color: #000;opacity: 0.7;border: none;text-align: center;color: #fff;',
                        time: 2,
                        shade: false
                    });
                    return false;
               }*/
               
               $.post('/FrontCart/add_cart', {'item_id': item_id}, function(data){
                    if (data != 'failure')
                    {
                         //加购物车成功的代理写这里，主要是在页面上的反馈，具体有哪些元素，查看打印出来的data
                         layer.open({
                             content: '添加成功',
                             style:'background-color: #000;opacity: 0.7;border: none;text-align: center;color: #fff;',
                             time: 2,
                             shade: false
                         });
                    }
                    else
                    {
                         //添加失败的代码写这里
                    }
               }, 'json');
     });
// alert重写,有url时点确定跳转，有opt直接跳转
window.alert = function(str,url,opt){
     var shield = document.createElement("DIV");
     var clientWidth = document.body.clientWidth;
     shield.id = "shield";
     shield.style.position = "absolute";
     shield.style.left = "0px";
     shield.style.top = "0px";
     shield.style.width = "100%";
     shield.style.height = document.body.scrollHeight+"px";
     shield.style.background = "rgba(0, 0, 0, 0.3)";
     shield.style.textAlign = "center";
     shield.style.zIndex = "25";
     //背景透明 IE有效
     //shield.style.filter = "alpha(opacity=0)";
     var alertFram = document.createElement("DIV");
     alertFram.id="alertFram";
     alertFram.style.position = "fixed";
     alertFram.style.left = "50%";
     alertFram.style.top = "50%";
     alertFram.style.background = "#fff";
     alertFram.style.textAlign = "center";
     alertFram.style.zIndex = "300";  
   	 alertFram.style.marginLeft = "-125px";
     alertFram.style.marginTop = "-58px";
     alertFram.style.width = "250px";    
     alertFram.style.lineHeight = "116px";
     alertFram.style.minHeight = "116px";     
     alertFram.style.lineHeight = "116px";
     alertFram.style.borderRadius = "2px";
     strHtml = "<ul id=\"cfm_ul\">\n";
     strHtml += " <li id=\"alert_li\"><div id=\"cfm_txt\">"+str+"</div></li>\n";
     strHtml += " <a herf=\"javascript:void(0);\" onclick=\"doOk();\"><li id=\"alert_ok\" class=\"cfm_btn\">确定</li></a>\n";
     strHtml += "</ul>\n";
     alertFram.innerHTML = strHtml;
     document.body.appendChild(alertFram);
     document.body.appendChild(shield);
     this.doOk = function(){
     		if(url && !opt)
     		{
     			location.href = url;
     		}
     		else
     		{
     			alertFram.style.display = "none";
       		shield.style.display = "none";	
     		}	 
     }
     if(url && opt)
	 	 {
	 			setTimeout(function(){location.href = url;},300);
	 	 }
     alertFram.focus();
     document.body.onselectstart = function(){return false;};
}
// confirm重写
window.confirm = function(str, ok_callback, oktxt, quittxt){
	//	_str=str;
     var shield = document.createElement("div");
     var clientWidth = document.body.clientWidth;
     shield.id = "shield";
     shield.style.position = "absolute";
     shield.style.left = "0px";
     shield.style.top = "0px";
     shield.style.width = "100%";
     shield.style.height = document.body.scrollHeight+"px";
     shield.style.background = "rgba(0, 0, 0, 0.3)";
     shield.style.textAlign = "center";
     shield.style.zIndex = "25";
     var alertFram = document.createElement("div");
     alertFram.id="alertFram";
     alertFram.style.position = "fixed";
     alertFram.style.left = "50%";
     alertFram.style.top = "50%";
     alertFram.style.background = "#fff";
     alertFram.style.textAlign = "center";
     alertFram.style.zIndex = "300";
   	 alertFram.style.marginLeft = "-120px";
     alertFram.style.marginTop = "-85px";
     alertFram.style.width = "240px";
     alertFram.style.minHeight = "171px";     
     alertFram.style.lineHeight = "171px";
     alertFram.style.borderRadius = "2px";		   	
     strHtml = "<ul id=\"cfm_ul\">\n";
     strHtml += " <li id=\"cfm_li\"><div id=\"cfm_icon\" ><svg id=\"warn_icon\"><use xlink:href=\"#warn\"></use></svg></div><div id=\"cfm_txt\">"+str+"</div></li>\n";
     if(oktxt && quittxt)
     {
     		strHtml += " <a herf=\"javascript:void(0);\" onclick=\"doOk();\"><li id=\"cfm_ok\" class=\"cfm_btn\">"+oktxt+"</li></a>\n";
     		strHtml += " <a herf=\"javascript:void(0);\" onclick=\"doQuit();\"><li id=\"cfm_quit\" class=\"cfm_btn\">"+quittxt+"</li></a>\n";	
     }
     else
     {
     		strHtml += " <a herf=\"javascript:void(0);\" onclick=\"doOk();\"><li id=\"cfm_ok\" class=\"cfm_btn\">确定</li></a>\n";
     		strHtml += " <a herf=\"javascript:void(0);\" onclick=\"doQuit();\"><li id=\"cfm_quit\" class=\"cfm_btn\">取消</li></a>\n";
     }     		
     strHtml += "</ul>\n";
     alertFram.innerHTML = strHtml;
     document.body.appendChild(alertFram);
     document.body.appendChild(shield);
     this.doQuit = function(){
         alertFram.style.display = "none";
         shield.style.display = "none";
     }
     this.doOk = function(){ 
				eval(ok_callback);
				alertFram.style.display = "none";
				shield.style.display = "none";
				return true;         
     }
     alertFram.focus();
     document.body.onselectstart = function(){return false;};
}

//提示框
function tishi(){
	$('#tan_wrap').show();
	setTimeout("$('#tan_wrap').hide()",3000);
}

//当用户第一次进入时，让用户确认默认收货地址
$(function()
{
		if(has_deflt_addr == 'false' || first == 'yes')
		{
               $('#addr_cont,#screenOptIfm').show();
		}
		
});
