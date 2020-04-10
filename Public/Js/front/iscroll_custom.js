/*
* @Author: zhangfengjie
* @Date:   2016-04-01 16:57:34
* @Last Modified by:   Administrator
* @Last Modified time: 2016-07-09 15:08:50
*/

var myScroll,
  pullDownEl, pullDownOffset,
  pullUpEl, pullUpOffset,
   generatedCount = 0;

// function pullDownAction () {
//   setTimeout(function () {
//     var sourceNode = document.getElementById("new_list"); // 获得被克隆的节点对象
//     for (var i = 1; i < 2; i++) {
//     var clonedNode = sourceNode.cloneNode(true); // 克隆节点
//     sourceNode.parentNode.appendChild(clonedNode);
//     }// 在父节点插入克隆的节点/
//     myScroll.refresh();
//   }, 1000); //加载完后界面更新方法
// }

function pullUpAction () {
  setTimeout(function () {
    var sourceNode = document.getElementById("new_list"); // 获得被克隆的节点对象
    var pullUp = document.getElementById("#pullUp");
    for (var i = 1; i < 2; i++) {
    var clonedNode = sourceNode.cloneNode(true); // 克隆节点
    sourceNode.parentNode.insertBefore(clonedNode,sourceNode);
    }// 在父节点插入克隆的节点/
    myScroll.refresh();
  }, 1000); // 加载完后界面更新方法
}

function loaded() {
  // pullDownEl = document.getElementById('pullDown');
  // pullDownOffset = pullDownEl.offsetHeight;
  pullUpEl = document.getElementById('pullUp');
  pullUpOffset = pullUpEl.offsetHeight;

  myScroll = new iScroll('load_wrapper', {
    // useTransition: true,
    topOffset: pullDownOffset,
    onRefresh: function () {
      // if (pullDownEl.className.match('loading')) {
      //   pullDownEl.className = '';
      //   pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
      // } else
      if (pullUpEl.className.match('loading')) {
        pullUpEl.className = '';
        // pullUpEl.querySelector('.icon1').classList.remove("jiazaizhong");
        // pullUpEl.querySelector('.icon1').classList.add("shangla");
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '努力加载中';
      }
    },
    onScrollMove: function () {
      // if (this.y > 5 && !pullDownEl.className.match('flip')) {
      //   pullDownEl.className = 'flip';
      //   pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始刷新...';
      //   this.minScrollY = 0;
      // } else if (this.y < 5 && pullDownEl.className.match('flip')) {
      //   pullDownEl.className = '';
      //   pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
      //   this.minScrollY = -pullDownOffset;
      // } else
       if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
        pullUpEl.className = 'flip';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始刷新...';
        this.maxScrollY = this.maxScrollY;
      } else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
        pullUpEl.className = '';
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多';
        this.maxScrollY = pullUpOffset;
      }
    },
    onScrollEnd: function () {
      // if (pullDownEl.className.match('flip')) {
      //   pullDownEl.className = 'loading';
      //   pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
      //   pullDownAction(); // ajax call？
      // } else
      if (pullUpEl.className.match('flip')) {
        pullUpEl.className = 'loading';
        // pullUpEl.querySelector('.icon1').classList.remove("shangla");
        // pullUpEl.querySelector('.icon1').classList.add("jiazaizhong");
        pullUpEl.querySelector('.pullUpLabel').innerHTML = '努力加载中...';
        pullUpAction(); // ajax call?
      }
    }
  });

  setTimeout(function () { document.getElementById('load_wrapper').style.left = '0'; }, 300);
}
//初始化控件
document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);
document.addEventListener('DOMContentLoaded', function () { setTimeout(loaded, 200); }, false);
