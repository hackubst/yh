<template>
  <!-- 表格输赢走势 -->
  <div class="tableTrend">
    <canvas ref="trendCanvas" width="920px" height="240px"></canvas>
  </div>
</template>
<script>
  export default {
    name: "tableTrend",
    data() {
      return {
        tableWidth: 920,
        tableHeight: 240,
        row: 6,
        col: 23
      };
    },
    props: {
      list: {
        type: Array,
        default: () => []
      },
      cb: {
        type: Number,
        default: 0
      }
    },
    methods: {
      //   绘制格子
      drawTable: function () {
        let context = this.context;
        // 23列， 6行
        // 绘制行
        for (let i = 0; i <= this.row; i++) {
          context.moveTo(0, i * 40);
          context.lineTo(this.tableWidth, i * 40);
          context.stroke();
        }
        // 绘制列
        for (let i = 0; i <= this.col; i++) {
          context.moveTo(i * 40, 0);
          context.lineTo(i * 40, this.tableHeight);
          context.stroke();
        }
      },
      /**
       * 绘制格子中的图片
       * x:  绘制的 x坐标
       * y: 绘制的  y坐标
       * imgType： imgType图片类型   1庄 2闲 3和
       */
      drawImg: function (x, y, imgType) {
        var img = new Image();
        switch (imgType) {
          case 1:
            img.src = require('images/icon/icon_red.png');
            break;
          case 2:
            img.src = require('images/icon/icon_blue.png');
            break;
          default:
            img.src = require('images/icon/icon_green.png');
        }
        img.onload = () => {
          let context = this.context
          context.drawImage(img, x * 40 + 5, y * 40 + 5, 30, 30);
        }
      },



      //  获取能可以排下的最新数据
      getNewData: function (arr) {
        return this.drawGameCount(arr, 0)
      },

      //  游戏的排序算法,   fn_type 0: 获取数组  1： 绘制画布
      drawGameCount: function (arr, fn_type) {
        let restArr = [];
        arr.reverse()
        let x = 0,
          y = 0,
          row = this.row,
          col = this.col;
        let lastResult = arr[0] // 上一次是哪家赢
        let lastX = 0 // 记录当到达最底下时，x平移了多少
        let maxY = this.row - 1 // 记录对多可以沿y轴重复多少
        let ifmoreThen = false // 判断连续的状态是否已经超出
        let maxThenX // 下一次到达最底部时 是否会与上一次的内容重合
        for (let i = 0; i < arr.length; i++) { 
          if (i > 0) {
            if (arr[i] == lastResult || arr[i] == 3) {
              if (y == maxY) {
                lastX += 1
                ifmoreThen = true
                if (x == col && fn_type == 0) {
                  return restArr
                }
              } else {
                y += 1;
              }
            } else {
              x += 1;
              y = 0;
              if (ifmoreThen) {
                maxThenX = x + lastX
                if (x > maxThenX) {
                  maxY -= 1
                }
                ifmoreThen = false
              }
              lastX = 0
              lastResult = arr[i]
              if (x == col && fn_type == 0) {
                return restArr
              }
            }
          }
          restArr.push(arr[i])
          if (fn_type == 1) {
            this.drawImg(x + lastX, y, arr[i])
          }
        }
        if (fn_type == 0) {
          return restArr
        }
      },
      // 获取 23 * 6 个 随机的 数字  （1-3）
      getTestNum: function () {
        let arr = [];
        for (var i = 0; i < 138; i++) {
          arr.push(Math.floor(Math.random() * 3) + 1)
        }
        return arr
      }
    },
    mounted() {
      this.canvas = this.$refs.trendCanvas
      this.context = this.canvas.getContext('2d');
      this.context.strokeStyle = '#e8e8e8';
      this.drawTable();
      let newArr
      if(this.list.length > 0){
        let list = this.list.map(item => parseInt(item))
        newArr = this.getNewData(list)
        if(this.cb == 1){
          this.$emit('maxNum', newArr.length)
        }
      }else{
        newArr = this.getNewData(this.getTestNum())
      }
      this.drawGameCount(newArr, 1)
    }
  }
</script>
<style scoped lang='less'>
  .tableTrend {
    margin-bottom: 40px;
  }

  .trend_canvas {
    border: 1px solid red;
  }
</style>