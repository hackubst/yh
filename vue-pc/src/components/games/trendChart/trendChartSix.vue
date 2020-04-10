<template>
  <div class="trendChartSix">
    <table border="1" cellpadding="0" cellspacing="0">
      <tr class="table-title">
        <th width="120">期号</th>
        <th width="120">投注时间</th>
        <th width="270">开奖结果</th>
        <th colspan="3">总和</th>
        <th>龙虎</th>
        <th>前三</th>
        <th>中三</th>
        <th>后三</th>
      </tr>
      <tr v-for="(item, index) in tableArr" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateMonth}}</td>
        <td>
          <div class="result_box">
            <div>{{item.part_one_result}}</div>
            <div>{{item.part_two_result}}</div>
            <div>{{item.part_three_result}}</div>
            <div>{{item.part_four_result}}</div>
            <div>{{item.part_five_result}}</div>
          </div>
        </td>
        <td>{{getStrNum(item.result, 0)}}</td>
        <td>{{filterSize(getStrNum(item.result, 1))}}</td>
        <td>{{filterOddEven(getStrNum(item.result, 2))}}</td>
        <td>{{filterTiger(getStrNum(item.result, 3))}}</td>
        <td>{{filterPard(getStrNum(item.result, 4))}}</td>
        <td>{{filterPard(getStrNum(item.result, 5))}}</td>
        <td>{{filterPard(getStrNum(item.result, 6))}}</td>
      </tr>
    </table>
  </div>
</template>
<script>
  import {
    trendChart
  } from '@/config/trendChart'
  import {
    defalutImg
  } from '@/config/mixin'

  export default {
    name: "trendChartSix",
    data() {
      return {
        gameType: ['豹', '对', '顺', '半', '杂'],
        tableArr: []
      };
    },
    mixins: [trendChart, defalutImg],
    created() {
      this.tableArr = this.trendData.game_log_list
    }
  }
</script>
<style scoped lang='less'>
  .trendChartSix {
    width: @main-width;
    margin: 0 auto;
    margin-bottom: 40px;

    table {
      width: @main-width;
      margin: 0 auto;

      th {
        border: 1px solid #e8e8e8;
        height: 40px;
        line-height: 40px;
        text-align: center;
        vertical-align: bottom;
      }

      td {
        border: 1px solid #e8e8e8;
        height: 40px;
        line-height: 40px;
        text-align: center;
        vertical-align: bottom;
      }

      .table-title {
        height: 25px;
        line-height: 25px;
        background: @chart-color;
      }
    }

    .tableOne {
      margin-bottom: 20px;
    }

    .result_box {
      display: flex;
      align-items: center;
      justify-content: space-around;
      height: 45px;

      div {
        .result_ball(30px, 15px);
      }
    }
  }
</style>