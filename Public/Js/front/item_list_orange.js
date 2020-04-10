/*
* @Author: zhangfengjie
* @Date:   2016-04-01 16:57:34
* @Last Modified by:   Marte
* @Last Modified time: 2016-11-25 16:38:21
*/

var myScroll,
  pullDownEl, pullDownOffset,
  pullUpEl, pullUpOffset,
  generatedCount = 0;
  
  $(function()
  {
  	if(total<=firstRow)
  	{
  		$(".pullUpIcon").hide();
  		$(".pullUpLabel").html("没有更多了");
  	}
  });

var num = 1; //次数
function pullUpAction () { //加载方法
  // setTimeout(function () {
    var sourceNode = $("#new_list"); // 获得被克隆的节点对象
    if(total > num*firstRow){
      $.post('/FrontMall/get_item_list', {"firstRow":num * parseInt(firstRow), "cur_sort_id":cur_sort_id, "merchant_id":merchant_id}, function(data, textStatus) 
       {//console.log(data);
        if (data != 'failure')
        {
          console.log("total")
          var len = data.length;
          var html = '';
         for (i = 0; i < len; i++)
          {
            if(data[i].unit==""){
              html += '<div class="foodlist_wrap"><li class="fooditem" onclick="show_detail(this);"><input type="hidden" id="item_id" value="' + data[i].item_id + '"><input type="hidden" id="item_unit" value="' + data[i].unit + '"><input type="hidden" id="base_img" value="' + data[i].base_pic + '"><input type="hidden" id="item_price" value="' + data[i].mall_price + '"><div class="food_content1"><div class="food_pic_wrap"><img onerror="no_pic(this);" class="food_pic" src="' + data[i].small_img + '"></div><div class="foodname" id="foodname">' + data[i].item_name + '</div><div class="food_price_region"><span class="food_price" id="mall_price">' + data[i].mall_price + '</span><span class="food_price_unit">元' + data[i].unit + '</span></div><div class="sale_num_wrap"><i class="sale_num">' + data[i].sales_num + '</i>销量<span id="desc" style="display:none;">' + data[i].item_desc + '</span></div></div>';
               html += '<span class="sales_num" style="position:absolute;right:8px;bottom:15px;color:#FFD057">';
               if(data[i].purchase_limit > 0){
                html += '限购'+data[i].purchase_limit+data[i].unit;
               }else{
                html += '剩余'+data[i].stock+data[i].unit;
               }
            
               html+= '</span></li><div class="foodop"><span class="foodop_tit">数量</span><a class="add addto_cart2" href="javascript:void(0);">＋</a><span class="add_num popone" id="popone">+1</span></div></div>';
           
            }else{ 
                html += '<div class="foodlist_wrap"><li class="fooditem" onclick="show_detail(this);"><input type="hidden" id="item_id" value="' + data[i].item_id + '"><input type="hidden" id="item_unit" value="' + data[i].unit + '"><input type="hidden" id="base_img" value="' + data[i].base_pic + '"><input type="hidden" id="item_price" value="' + data[i].mall_price + '"><div class="food_content1"><div class="food_pic_wrap"><img onerror="no_pic(this);" class="food_pic" src="' + data[i].small_img + '"></div><div class="foodname" id="foodname">' + data[i].item_name + '</div><div class="food_price_region"><span class="food_price" id="mall_price">' + data[i].mall_price + '</span><span class="food_price_unit">元/' + data[i].unit + '</span></div><div class="sale_num_wrap"><i class="sale_num">' + data[i].sales_num + '</i>销量<span id="desc" style="display:none;">' + data[i].item_desc + '</span></div></div>';
                  if(data[i].purchase_limit > 0){
                    html += '限购'+data[i].purchase_limit+data[i].unit;
                   }else{
                    html += '剩余'+data[i].stock+data[i].unit;
                   }
            
                  html+= '</span></li><div class="foodop"><span class="foodop_tit">数量</span><a class="add addto_cart2" href="javascript:void(0);">＋</a><span class="add_num popone" id="popone">+1</span></div></div>';
                
            }
             
            
          }
          sourceNode.append(html);
          // $(".lazyload").lazyload({
          //   effect : "fadeIn",
          //   threshold : 200
          // });
           num ++;
           console.log(num);
        }
      }, 'json');
      setTimeout(function(){
        myScroll.refresh();
      },500);//重置高度
      
    }
    else
    {
      $('.pullUpIcon').hide();
      $('.pullUpLabel').html('没有更多了');
    }
    
    //loadItemList(sourceNode); 
  // }, 500); // 加载完后界面更新方法
}

function loaded() {
  pullUpEl = document.getElementById('pullUp');
  pullUpOffset = pullUpEl.offsetHeight;

  myScroll = new iScroll('load_wrapper', {
    // useTransition: true,
    topOffset: pullDownOffset,
    onRefresh: function () {
      if (pullUpEl.className.match('loading')) {
        pullUpEl.className = '';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
      }
    },
    onScrollMove: function () {
       if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
        pullUpEl.className = 'flip';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始刷新...';
        this.maxScrollY = this.maxScrollY;
      } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
        pullUpEl.className = '';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载...';
        this.maxScrollY = pullUpOffset;
      }
    },
    onScrollEnd: function () {
      if (pullUpEl.className.match('flip')) {
        pullUpEl.className = 'loading';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';
        pullUpAction(); // ajax call?
      }
    }
  });

  setTimeout(function () { document.getElementById('load_wrapper').style.left = '0'; }, 300);
}
//初始化控件
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
document.addEventListener('DOMContentLoaded', function () { setTimeout(loaded, 200); }, false);
