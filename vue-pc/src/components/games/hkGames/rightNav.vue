<template>
  <div>
    <div  v-for="(l,i) in list" :key="i">
      <div class="nav-right" @click="toggle(i)">hahhah</div>
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" v-show="l.show">
        <tr>
          <th style="width:82px;">
            项目
          </th>
          <th style="width: 47px;">
            号码
          </th>
          <th style="width: 47px;">
            遗漏
          </th>
        </tr>
        <tr v-for="(firstItem,firstIndex) in l.items" :key="firstIndex">
          <td v-for="(secondedItem,secondedIndex) in firstItem">{{secondedItem}}</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
  export default {
    name: "rightNav",
    data() {
      return {
        isShow: false,
        list: [
          { title: '我的同学', items: ["张三", "李四", "王五"], show: false },
          { title: '我的同事', items: ["张三1", "李四1", "王五1"], show: true },
          { title: '我的家人', items: ["张三2", "李四2", "王五2"], show: false }
        ]
      }
    },
    methods: {
      toggle: function (index) {
        console.log(index)
        // ES6的新语法，称为箭头函数， 它是语法糖。
        // 当箭头函数函数体中只有一条语句时，return语句可省略。
        this.list.forEach((item, i) => item.show = i == index)
        // 回调函数， 回调函数有三个形参，list当前循环中对象,i表示当前循环中的索引
        // arr 表示循环的数组本身
        // this.lists.forEach(function(list, i, arr){
        //     // console.log(arr[i]==list);
        //     // return list.show = i == index;
        //     if(i == index){
        //         list.show = true;
        //     } else {
        //         list.show = false;
        //     }
        // });
      }
    }
  }
</script>

<style scoped lang="less">
  .nav-right {
    text-align: center;
    width: 180px;
    height: 34px;
    font-size: 14px;
    font-weight: 600;
    color: rgba(255, 255, 255, 1);
    line-height: 34px;
    background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
    border-radius: 4px;
    position: relative;
  }

  .game_table {
    width: 180px;
  }

</style>
