
var myScroll,
  pullUpEl, pullUpOffset,
   generatedCount = 0;

function pullUpAction () {
  setTimeout(function () {
    //var sourceNode = document.getElementById("new_list"); // 获得被克隆的节点对象
    //for (var i = 1; i <3; i++) {
    //var clonedNode = sourceNode.cloneNode(true); // 克隆节点
    //sourceNode.parentNode.appendChild(clonedNode);
    //}// 在父节点插入克隆的节点/
    get_content();
    myScroll.refresh();
  }, 1000); // 加载完后界面更新方法
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



function get_content()
{
    $.ajax({
      url: "/FrontGroupBuy/get_group_buy_list",
      type: "post",
      data: {'firstRow': page*page_num},
      dataType: 'json',
      success:function(data){
          if(data != 'failure'){
            var len = data.length;
            var html = '';
            for (var i = 0; i < len; i++)
            {
                if(data[i].item_name == ''){
                    data[i].item_name = '--';
                }
                if(data[i].group_name== ''){
                    data[i].group_name= '--';
                }

               html += '<a href="/FrontGroupBuy/group_buy_item_detail/group_buy_id/'+data[i].group_buy_id+'" class="box clearfix">'+
						'<img src="'+data[i].pic+'"  />'+
						'<div class="box_div clearfix">'+
							'<p>'+data[i].group_name+'</p>'+
							'<p>'+data[i].people_limit+'人团</p>'+
							'<p>拼团价</p>'+
							'<p>￥'+data[i].group_price+'</p>'+
                        '</div>'+
                    '</a>';
            }
            $("#content").append(html);
            page += 1;
          }
          else
          {
            pullUpEl.querySelector('.pullUpIcon').style.display = 'none';
            pullUpEl.querySelector('.pullUpLabel').innerHTML = '没数据咯...';
            scrollFlag = false;

          }
      },
      error:function(){  
        pullUpEl.querySelector('.pullUpIcon').style.display = 'none';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '没数据咯...'; 
        scrollFlag = false;

        },
    });
}
