<template>
  <div class="gameStatistic">
    <!-- <div class="top">
      <p>游戏名称</p>
      <p>今天</p>
      <p>昨天</p>
      <p>前天</p>
      <p>总数</p>
    </div>-->
    <div class="lists">
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:20%">
        <tr>
          <th style="width: 20%;">游戏名称</th>
        </tr>
        <tr v-for="(item, index) of nameList" :key="index">
          <td>{{item.game_type_name}}</td>
        </tr>
      </table>
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:20%">
        <tr>
          <th style="width: 20%;">今天</th>
        </tr>
        <tr v-for="(item, index) of list[0]" :key="index">
          <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
        </tr>
      </table>
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:20%">
        <tr>
          <th style="width: 20%;">昨天</th>
        </tr>
        <tr v-for="(item, index) of list[1]" :key="index">
          <td :class="{active:item.win_loss > 0}" class="red">{{item.win_loss | changeBigNum}}</td>
        </tr>
      </table>
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:20%">
        <tr>
          <th style="width: 20%;">前天</th>
        </tr>
        <tr v-for="(item, index) of list[2]" :key="index">
          <td :class="{active:item.win_loss > 0}" class="blue">{{item.win_loss | changeBigNum}}</td>
        </tr>
      </table>
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" style="width:20%">
        <tr>
          <th style="width: 20%;">总数</th>
        </tr>
        <tr v-for="(item, index) of list[7]" :key="index">
          <td :class="{active:item.win_loss > 0}">{{item.win_loss | changeBigNum}}</td>
        </tr>
      </table>
    </div>
    <!-- <div class="box">
      <div class="box_item">
        <div class="box_left">
            参与期数
        </div>
        <div class="box_right">
            {{issue_num}}
        </div>
      </div>
      <div class="box_item">
        <div class="box_left">
           合计输赢
        </div>
        <div class="box_right">
           {{win_loss | changeBigNum}}
        </div>
      </div>
    </div>-->
  </div>
</template>
<script>
export default {
  name: "gameStatistic",
  data () {
    return {
      totalItem: {},
      issue_num: 0,
      win_loss: 0,
      list: [],
      nameList: []
    };
  },
  created () {
    this.$Api({
      api_name: 'kkl.game.getNewWinList'
    }, (err, data) => {
      if (!err) {
        this.list = data.data.game_type_list.list
        this.nameList = data.data.game_type_list.type_name
        // let list = data.data.game_type_list.list[0]
        // for (let i = 0; i < list.length; i++) {
        //   if (this.$route.query.game_type_id == list[i].game_type_id) {
        //     this.issue_num = list[i].issue_num
        //     this.win_loss = list[i].win_loss
        //   }
        // }
      }
    })
  }
}
</script>
<style scoped lang='less'>
.top {
  width: 100%;
  height: 38px;
  background: #fed093;
  display: flex;
  align-items: center;
  p {
    width: 20%;
    text-align: center;
    font-size: 14px;
    font-family: PingFangSC-Regular, PingFangSC;
    font-weight: 400;
    color: rgba(74, 65, 48, 1);
  }
}
.lists {
  display: flex;
  table {
    td {
      font-size: 12px;
      font-family: PingFangSC-Regular, PingFangSC;
      font-weight: 400;
      color: rgba(74, 65, 48, 1);
      white-space: nowrap;
      text-overflow: ellipsis;
      -o-text-overflow: ellipsis;
      overflow: hidden;
      &.red {
        color: #FF1E1E;
      }
      &.blue {
        color: #1979FF;
      }
    }
  }
}
// .box {
//   .box_item {
//     width: 100%;
//     display: flex;
//     height: 72px;
//     margin-bottom: 1px;
//     .box_left {
//       width: 96px;
//       height: 72px;
//       line-height: 72px;
//       background: #fed093;
//       .sc(17px, #4a4130);
//       text-align: center;
//     }
//     .box_right {
//       flex: 1;
//       text-align: center;
//       height: 72px;
//       line-height: 72px;
//     }
//   }
//   .box_item:nth-child(2n) {
//     .box_right {
//       background: #fff7ec;
//     }
//   }
// }
</style>