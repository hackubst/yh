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
      $.post('/FrontUser/get_account_list', {"firstRow":num*firstRow,"change_type":change_type}, function(data, textStatus) 
       {
        if (data != 'failure')
        {
          var len = data.length;
          var html = '';
          for (i = 0; i < len; i++)
          {
              html='<li><div class="header clearfix"><p class="fl">'+data[i].addtime+'</p><span class="fr">'+data[i].change_type+'</span></div><div class="cont"><h1>&yen;'+data[i].amount_in+'</h1><div class="cont_top clearfix"><p class="fl">变动前金额:</p><span class="fl">'+data[i].amount_before_pay+'</span></div><div class="cont_top clearfix"><p class="fl">变动后金额:</p><span class="fl">'+data[i].amount_after_pay+'</span></div></div><div class="footer"><div class="footer_cont"><p>订单号:</p><span>'+data[i].order_sn+'</span></div><div class="footer_cont"><p>操作人:</p><span>'+data[i].operater+'</span></div><div class="footer_cont"><p>备注:</p><span>'+data[i].remark+'</span></div></div></li>'
          }
          sourceNode.append(html);
            // $(".lazyload").lazyload({
            //   effect : "fadeIn",
            //   threshold : 200
            // });
          num ++;
        }
      }, 'json');
      setTimeout(function(){
        myScroll.refresh();
      },500);
      
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
