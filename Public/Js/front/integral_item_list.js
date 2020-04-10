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
      $.post('/FrontMall/integral_item_list', {"firstRow":num*firstRow, 'class_id' : class_id}, function(data, textStatus) 
       {
        if (data != 'failure')
        {
          var len = data.length;
          var html = '';
          for (i = 0; i < len; i++)
          {
            
            html += '<li class="item_list_li"><a href="{$integral_item_detail_link}'+data[i].item_id+'" class="content_left"><img src="'+data[i].base_pic+'" data-original="'+data[i].small_img+'" class="lazyload" /><p>'+data[i].item_name+'</p><h4>'+data[i].integral_exchange+'积分</h4></a></li>';
            
            
          }
          sourceNode.append(html);
          $(".lazyload").lazyload({
            effect : "fadeIn",
            threshold : 200
          });
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
