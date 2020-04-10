<template>
  <div class="tableTwo">
    <!-- <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 109px;">游戏名称</th>
        <th style="width: 110px;">今天</th>
        <th style="width: 110px;">昨天</th>
        <th style="width: 110px;">前天</th>
        <th style="width: 110px;">{{date_four}}</th>
        <th style="width: 110px;">{{date_five}}</th>
        <th style="width: 110px;">{{date_six}}</th>
        <th style="width: 110px;">{{date_seven}}</th>
        <th style="width: 120px;">总数</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.game_type_name}}</td>
        <td :class="{active:i>0}" v-for="(i, idx) in item.daily_data" :key="idx">{{i | changeBigNum}}</td>
        <td :class="{active:item.win_loss_count > 0}">{{item.win_loss_count | changeBigNum}}</td>
      </tr>
    </table> -->
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">游戏名称</th>
      </tr>
      <tr v-for="(item, index) of nameList" :key="index">
        <td>{{item.game_type_name}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">今天</th>
      </tr>
      <tr v-for="(item, index) of list[0]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">昨天</th>
      </tr>
      <tr v-for="(item, index) of list[1]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">前天</th>
      </tr>
      <tr v-for="(item, index) of list[2]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">{{date_four}}</th>
      </tr>
      <tr v-for="(item, index) of list[3]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">{{date_five}}</th>
      </tr>
      <tr v-for="(item, index) of list[4]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">{{date_six}}</th>
      </tr>
      <tr v-for="(item, index) of list[5]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">{{date_seven}}</th>
      </tr>
      <tr v-for="(item, index) of list[6]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">{{date_eight}}</th>
      </tr>
      <tr v-for="(item, index) of list[7]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:11%">
      <tr>
        <th style="width: 109px;">总数</th>
      </tr>
      <tr v-for="(item, index) of list[8]" :key="index">
        <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
      </tr>
    </table>
    <!-- <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 109px;">游戏名称</th>
        <th style="width: 110px;">今天</th>
        <th style="width: 110px;">昨天</th>
        <th style="width: 110px;">前天</th>
        <th style="width: 110px;">{{date_four}}</th>
        <th style="width: 110px;">{{date_five}}</th>
        <th style="width: 110px;">{{date_six}}</th>
        <th style="width: 110px;">{{date_seven}}</th>
        <th style="width: 120px;">汇总</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.game_type_name}}</td>
        <td v-for="(i, idx) in item.issue_data" :key="idx">{{i | changeBigNum}}</td>
        <td>共{{item.issue_count}}期,{{item.issue_rate}}%</td>
      </tr>
    </table> -->
  </div>
</template>
<script>
  export default {
    name: "tableTwo",
    data() {
      return {
        list: [],
        nameList: [],
        date_four: '',
        date_five: '',
        date_six: '',
        date_seven: '',
        date_eight: ''
      };
    },
    created() {
      this.getDay(-3)
      this.getDay(-4)
      this.getDay(-5)
      this.getDay(-6)
      this.getDay(-7)
    },
    mounted() {
      this.get_list()
    },
    methods: {
      // 获取盈利列表
      get_list() {
        this.$Api({
          api_name: 'kkl.game.getNewWinList'
        }, (err, data) => {
          if (!err) {
              this.list = data.data.game_type_list.list
              this.nameList = data.data.game_type_list.type_name
          } else {
              this.$msg(err.error_msg, 'error', 1500)
          }
        })
      },
      // 获取日期
      getDay(day) {
        var today = new Date()
        var seconds = today.getTime() + 1000*60*60*24*day
        today.setTime(seconds)
        var tMonth = today.getMonth()
        var tDate = today.getDate()
        tMonth = this.doHandleMonth(tMonth + 1)
        tDate = this.doHandleMonth(tDate);
        if (day == -3) {
          this.date_four = tMonth +'-' + tDate
        } else if (day == -4) {
          this.date_five = tMonth +'-' + tDate
        } else if (day == -5) {
          this.date_six = tMonth +'-' + tDate
        } else if (day == -6) {
          this.date_seven = tMonth +'-' + tDate
        } else {
          this.date_eight = tMonth +'-' + tDate
        }
      },
      doHandleMonth(month) {
        var m = month
        if(month.toString().length == 1){
          m = "0" + month;
        }
        return m
      }
    },
  }
</script>

<style scoped lang='less'>
    .tableTwo {
        width: @main-width;
        margin: 0 auto 40px auto;
        display: flex;
    }
    .active{
      background-color: pink;
    }
</style>
