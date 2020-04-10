<template>
  <div class="trendChartOne">
    <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout: fixed;">
      <tr>
        <th colspan="2" style="height: 25px; line-height: 25px;">标准次数</th>
        <th v-for="(item) in chartJson" :key="item.key">
          {{item.num}}
        </th>
        <!-- <th v-for="(item, oindex) in lastArr" :key="'100'+oindex"></th> -->
        <th colspan="11" v-if="lastArr.length > 0"></th>
      </tr>
      <tr>
        <th colspan="2" style="height: 50px;">
          <div class="line_two">
            实际次数
          </div>
        </th>
        <th v-for="(item, index) in chartJson" :key="index">
          <div class="line_two">
            <p>{{item.real_num}}</p>
            <!-- <p>{{item.realTimeBot}}</p> -->
          </div>
        </th>
        <th v-for="(item, index) in lastArr" :key="'103'+index">
          <div class="line_two">
            <p>{{item}}</p>
            <!-- <p>1</p> -->
          </div>
        </th>
        <th colspan="2" v-if="lastArr.length > 0">
          <div class="line_two">
            尾数#
          </div>
        </th>
        <th colspan="3" v-if="lastArr.length > 0">
          <div class="line_two">
            余数@
          </div>
        </th>
      </tr>
      <tr class="table_title">
        <td width="50">期号</td>
        <td width="70">时间</td>
        <td v-for="(item, index) in chartJson" :key="index">
          {{item.key}}
        </td>
        <td v-for="(item, index) in lastResult" :key="'101'+index">{{item}}</td>
      </tr>
      <tr class="table_item" v-for="(item, index) in tableArr" :key="'001'+index">
        <td>
          {{item.issue}}
        </td>
        <td>
          {{item.addtime | formatDateMonth}}
        </td>
        <td v-for="(betItem, betIndex) in chartJson" :key="betIndex">
          <div class="bet_box">
            <div class="bet_choosed" v-if="getStrNum(item.result, 0) == betItem.key">
              <span v-if="strResultGame(choosedGame.game_type_id)">
                {{filterPard(getStrNum(item.result, 0))}}
              </span>
              <span v-else-if="choosedGame.game_type_id == 19 || choosedGame.game_type_id == 55">
                 {{filterTiger(getStrNum(item.result, 0))}}
              </span>
              <span v-else>
                {{getStrNum(item.result, 0)}}
              </span>
            </div>
          </div>
        </td>
        <td v-for="(lastItem, index) in lastResult" :key="'102'+index">
          <div class="size_box" v-if="getStrNum(item.last_result, index) == 1 && index < 8"
            :style="'background:'+ lastResultBg[index]">
            {{lastItem}}
          </div>
          <div v-else-if="index < 8"></div>
          <span v-else>
            {{getStrNum(item.last_result, index)}}
          </span>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
  import {
    trendChart
  } from '@/config/trendChart.js'
  import {
    defalutImg, filterNum
  } from '@/config/mixin.js'
  export default {
    name: "trendChartOne",
    data() {
      return {
        chartJson: [],
        tableArr: [],
        lastArr: [],
        lastResult: ['单', '双', '中', '边', '大', '小', '大', '小', '3/', '4/', '5/'],
        lastResultBg: ['#0033CC', '#FF3333', '#660099', '#FF9900', '#FF0099', '#00CC00', '#0033CC', '#FF3333'],
        lasrNum: 6
      };
    },
    mixins: [trendChart, defalutImg, filterNum],
    created() {
      this.chartJson = this.trendData.chart_json || []
      this.tableArr = this.trendData.game_log_list
      if (!this.trendData.last_arr) {
        this.lastResult = []
        this.lasrNum = 0
      }
      this.lastArr = this.trendData.last_arr ? this.trendData.last_arr.split(',') || [] : []
    }
  }
</script>
<style scoped lang='less'>
  .trendChartOne {
    margin-bottom: 40px;

    table {
      min-width: @main-width;
      margin: 0 auto;

      th {
        font-size: 14px;
        background: @chart-color;
        color: #4A4130;
        border: 1px solid #e8e8e8;
        text-align: center;
        box-sizing: border-box;
        padding: 0 6px;
        line-height: 25px;
        vertical-align: bottom;

        .line_two {
          height: 50px;
          display: flex;
          flex-direction: column;
          justify-content: space-around;
          align-items: center;
        }
      }

      .table_title {
        background: #e3f0ff;
        box-sizing: border-box;
      }

      td {
        height: 25px;
        line-height: 25px;
        font-size: 12px;
        color: #4A4130;
        box-sizing: border-box;
        border: 1px solid #e8e8e8;
        text-align: center;
        vertical-align: bottom;
      }

      .table_item {
        height: 37px;

        td {
          height: 37px;
          .bet_box{
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
          }
          .bet_choosed {
            .result_ball(37px, 18px);
          }

          .size_box {
            width: 100%;
            height: 100%;
            text-align: center;
            line-height: 37px;
            color: #fff;
          }
        }
      }

    }
  }
</style>