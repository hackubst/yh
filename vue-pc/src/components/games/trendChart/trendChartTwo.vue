<template>
  <div class="trendChartTwo">
    <table border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th width="210">开奖结果</th>
        <th width="120">投注时间</th>
        <th>结果</th>
        <th colspan="3">前二</th>
        <th colspan="3">后二</th>
        <th colspan="1" width="60">龙虎和</th>
      </tr>
      <tr v-for="(item, index) in trendList" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateMonth}}</td>
        <td>
          <div class="result_ball">
            <img :src="getBallIcon('blue', item.part_one_result)" alt>
            <img :src="getBallIcon('yellow', item.part_two_result)" alt>
            <img :src="getBallIcon('pink', item.part_three_result)" alt>
            <!-- <span>[查看]</span> -->
          </div>
        </td>
        <td width="30">
          <span style="color: #14D4D0; font-weight: bold;">{{getStrNum(item.result, 0)}}</span>
        </td>
        <td width="30">
          <span style="color: #FD00FF; font-weight: bold;" v-if="getStrNum(item.result, 1) == 1">小</span>
          <span style="color: #14D4D0; font-weight: bold;" v-else>大</span>
        </td>
        <td>
          <span style="color: #1909F7; font-weight: bold;" v-if="getStrNum(item.result, 2) == 1">单</span>
          <span style="color: #C00; font-weight: bold;" v-else>双</span>
        </td>
        <td>
          <span style="color: #14D4D0; font-weight: bold;">{{getStrNum(item.result, 3)}}</span>
        </td>
        <td>
          <span style="color: #FD00FF; font-weight: bold;" v-if="getStrNum(item.result, 4) == 1">小</span>
          <span style="color: #14D4D0; font-weight: bold;" v-else>大</span>
        </td>
        <td>
          <span style="color: #1909F7; font-weight: bold;" v-if="getStrNum(item.result, 5) == 1">单</span>
          <span style="color: #C00; font-weight: bold;" v-else>双</span>
        </td>
        <td>
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 6) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 6) == 2"
          >虎</span>
          <span style="color: #018796; font-weight: bold;" v-else>和</span>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { trendChart } from "@/config/trendChart.js";
import { defalutImg } from "@/config/mixin.js";
export default {
  name: "trendChartTwo",
  data() {
    return {
      trendList: []
    };
  },
  mixins: [trendChart, defalutImg],
  created() {
    this.trendList = this.trendData.game_log_list;
  }
};
</script>
<style scoped lang='less'>
.trendChartTwo {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;

  table {
    width: @main-width;
    margin: 0 auto;

    th {
      font-size: 14px;
      background: @chart-color;
      color: #4a4130;
      border: 1px solid #e8e8e8;
      text-align: center;
      box-sizing: border-box;
      vertical-align: bottom;
      height: 42px;
      line-height: 42px;
    }
    td {
      font-size: 14px;
      color: #4a4130;
      border: 1px solid #e8e8e8;
      text-align: center;
      box-sizing: border-box;
      vertical-align: bottom;
      height: 44px;
      line-height: 44px;
      .result_ball {
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        img {
          width: 30px;
          height: 30px;
          margin-right: 10px;
        }
        span {
          font-size: 12px;
          color: #d1913c;
          &:hover {
            cursor: pointer;
          }
        }
      }
    }
  }
}
</style>