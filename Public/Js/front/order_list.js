
var myScroll,
    pullUpEl, pullUpOffset,
    generatedCount = 0;
$(function(){
  if(total <= firstRow)
  {
    $('.pullUpIcon').hide();
    $('.pullUpLabel').html('没有更多了');
  }
})
var num = 1; 
function pullUpAction () { 
  // setTimeout(function () {
    var sourceNode = $("#new_list"); 
    if(total > num*firstRow){
      $.post('/FrontOrder/order_list', {"firstRow":num*firstRow, "item_name":item_name, "opt":opt}, function(data, textStatus) 
       {
        if (data != 'failure')
        {
          var len = data.length;
          var html = ''; 
          for (i = 0; i < len; i++)
          {
            html += '<div class="box"><a href="/FrontOrder/order_detail/order_id/' + data[i].order_id + '"><div class="title clearfix"><p class="fl">下单时间:' + data[i].addtime + '</p><span class="fr">' + data[i].order_status_name + '</span></div>';
            
            if(data[i].item_list && data[i].item_list.length > 0)
            {
              html += '<ul class="img_box clearfix">';
              for (j = 0; j < data[i].item_list.length; j++)
              {
                html += '<li class="fl"><img src="' + data[i].item_list[j].small_pic + '"/><p class="txt_limit">' + data[i].item_list[j].item_name + '</p></li>';
              }
              html += '</ul>';
            }
            if(data[i].send_way)
            {
              html += '<div class="address"><p>门店:' + data[i].send_way + '</p></div>';
            }

            html += '</a><div class="price clearfix"><p class="fr">&nbsp;合计:<i>&nbsp;&yen;&nbsp;' + data[i].pay_amount + '</i>';
            
            if(data[i].express_fee != 0.00)
            {
              html += '(含配送费&nbsp;&yen;&nbsp;' + data[i].express_fee + ')';
            }

            html += '</p>';

            if(data[i].order_status == 0)
            {
              html += '<a href="javascript:;" onclick="goPay("' + data[i].order_id + '");" class="btn fr"> 去支付 </a>';
            }
            else if(data[i].order_status == 2)
            {
              html += '<a href="javascript:;" class="btn fr" onclick="confirm_order("' + data[i].order_id + '", 3)"> 确认收货 </a>';
            }
            else if(data[i].order_status == 3)
            {
              html += '<a href="/FrontUser/assessment/order_id/"' + data[i].order_id + '"" class="btn fr">评价</a>';
            }

            html += '</div></div>';

          }
          sourceNode.append(html);
          num ++;
          myScroll.refresh();
        }
      }, 'json');
      
    }
    else
    {
      $('.pullUpIcon').hide();
      $('.pullUpLabel').html('没有更多了');
    }

}

function loaded() {
  pullUpEl = document.getElementById('pullUp');
  pullUpOffset = pullUpEl.offsetHeight;

  myScroll = new iScroll('load_wrapper', {
    // useTransition: true,
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