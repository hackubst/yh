<template>
  <div class="trendChartSeven">
    <table border="1" cellpadding="0" cellspacing="0" class="game_table">
      <tr>
				<th style="width: 10%;">期号</th>
				<th style="width: 12%;">投注时间</th>
				<th style="width: 38%;">开奖结果</th>
				<th style="width: 20%;" colspan="3">冠亚</th>
				<th style="width: 20%;" colspan="5">
          <table class="longhu" cellspacing="0" cellpadding="0" style="width: 100%;">
            <tbody>
              <tr>
                <td colspan="5">龙虎</td>
              </tr>
              <tr class="longhunum">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td>
              </tr>
					  </tbody>
          </table>
				</th>
			</tr>
      <tr v-for="(item, index) in trendList" :key="index">
        <td class="black777">{{item.issue}}</td>
        <td class="black777">{{item.addtime | formatDateMonth}}</td>
        <td class="regular" valign="middle">
          <div class="result_num_arr">
            <span v-for="(dot, index) in getStrArr(item.game_result_info.result)" :key="index">
              <img
                :src="getNumImage(dot)"
                class="active"
              >
            </span>
          </div>
        </td>
        <td class="guanya">
          <span style="color: #14D4D0; font-weight: bold;">{{getStrNum(item.result, 0)}}</span>
        </td>
        <td class="guanya">
          <span style="color: #FD00FF; font-weight: bold;" v-if="getStrNum(item.result, 1) == 1">小</span>
          <span style="color: #14D4D0; font-weight: bold;" v-else>大</span>
        </td>
        <td class="guanya">
          <span style="color: #1909F7; font-weight: bold;" v-if="getStrNum(item.result, 2) == 1">单</span>
          <span style="color: #C00; font-weight: bold;" v-else>双</span>
        </td>
        <td class="longhu">
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 3) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 3) == 2"
          >虎</span>
        </td>
        <td class="longhu">
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 4) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 4) == 2"
          >虎</span>
        </td>
        <td class="longhu">
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 5) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 5) == 2"
          >虎</span>
        </td>
        <td class="longhu">
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 6) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 6) == 2"
          >虎</span>
        </td>
        <td class="longhu">
          <span style="color: #F00; font-weight: bold;" v-if="getStrNum(item.result, 7) == 1">龙</span>
          <span
            style="color: #1200FF; font-weight: bold;"
            v-else-if="getStrNum(item.result, 7) == 2"
          >虎</span>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { trendChart } from "@/config/trendChart.js";
import { filterNum, defalutImg } from "@/config/mixin.js";
import { mapGetters } from "vuex";
export default {
  name: "trendChartSeven",
  data() {
    return {
      trendList: []
    };
  },
  mixins: [trendChart,filterNum, defalutImg],
  created() {
    console.log(this.trendData.game_log_list)
    this.trendList = this.trendData.game_log_list;
  },
  computed: {
    ...mapGetters(["choosedGame"])
  }
};
</script>
<style scoped lang='less'>
.trendChartSeven {
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