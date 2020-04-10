/*
* @Author: zhangfengjie
* @Date:   2016-04-01 16:57:34
* @Last Modified by:   Administrator
* @Last Modified time: 2016-04-26 16:38:34
*/

  var myScroll,
    pullDownEl, pullDownOffset,
    pullUpEl, pullUpOffset,
     generatedCount = 0;

  var num = 1; //次数
  function pullUpAction () { //加载方法
      var sourceNode = $("#new_list"); // 获得被克隆的节点对象
      if(total > num*firstRow){
        $.post('/FrontUser/integral_exchange_list', {"firstRow":num*firstRow}, function(data, textStatus) 
         {
          if (data != 'failure')
          {
            var len = data.length;
            var html = '';
            for (i = 0; i < len; i++)
            {
              
              html += '<li><a href="'+link+''+data[i].order_id+'" class="od_item_link"><div class="order_d_info"><div class="order_sn">订单号：<em>'+data[i].order_sn+'</em></div><div class="order_status">'+data[i].order_status+'</div></div><div class="order_d_detail"><div class="case_item_photo"><img class="photo" src="'+data[i].small_pic+'" alt="'+data[i].item_name+'"  onerror="no_pic(this);"/></div><div class="case_item_cont"><h3 class="case_item_name">'+data[i].item_name+'</h3><label class="case_item_count"><h6 class="case_i_count">价格：</h6><span class="case_i_count_txt"> <em>'+data[i].integral_amount+'</em>&nbsp;积分</span></label></div></div></a></li>';
              
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
