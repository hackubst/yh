<template>
    <div class="game-history">
      <table class="game_table" border="1" cellpadding="0" cellspacing="0" >
        <tr>
          <th style="width:145px;">
            交易日期
          </th>
          <th style="width: 84px;">
            注单笔数
          </th>
          <th style="width: 189px;">
            下注金额
          </th>
          <th style="width: 183px;">
            输赢结果
          </th>
          <th style="width: 131px;">
            退水
          </th>
          <th style="width: 183px;">
            退水后结果
          </th>

        </tr>
        <tr v-for="(firstItem,firstIndex) in list" :key="firstIndex">
          <td>{{firstItem.time}}</td>
          <td>{{firstItem.order_num}}</td>
          <td>{{firstItem.total_bet_money}}</td>
          <td>{{firstItem.win_sum}}</td>
          <td>{{firstItem.tuishui_money}}</td>
          <td>{{firstItem.tuishui_result}}</td>
        </tr>
      </table>
    </div>
</template>

<script>
    export default {
        name: "gameHistory",
        props: {
            list: {
                type:Array,
                default: () => []
            },
        },
      data(){
        return{
            list:[]
        }
      },
        methods:{
            //获取六合彩最近7天下注
            // getLastSevenList() {
            //     this.$Api(
            //         {
            //             api_name: "kkl.user.getLastSevenList",
            //             game_type_id: this.id
            //         },
            //         (err, data) => {
            //             if (!err) {
            //                 this.list = data.data
            //             }
            //         }
            //     );
            // },
        },
        mounted() {
            this.getLastSevenList()
        }
    }
</script>

<style scoped lang="less">
    .game-history{
      .game_table{
        .wh(920px,auto);
        margin-top: 10px;
        th{
          height: 24px;
          line-height: 24px;
        }
        td{
          height: 32px;
          text-align: right;
          padding-right: 10px;
          line-height: 32px;
          font-size:14px;
          font-weight:600;
          color:rgba(51,51,51,1);
        }
        tr>td:first-child{
          text-align: center;
        }
        tr td:nth-child(2) {
          text-align: center;
        }
      }
    }
</style>
