<template>
  <div class="trendChartThree">
    <!-- <table border="1" cellpadding="0" cellspacing="0" class="tableOne">
      <tr>
        <th rowspan="2" width="70"></th>
        <th colspan="3" width="75">1:3龙虎和</th>
        <th colspan="3" width="75">2:3龙虎和</th>
        <th colspan="5" width="125">结果</th>
        <th colspan="4" width="100">前二</th>
        <th colspan="4" width="100">后二</th>
      </tr>
      <tr>
        <th v-for="(item, index) in gameType" :key="'0'+index">{{item}}</th>
        <th v-for="(item, index) in gameType" :key="'1'+index">{{item}}</th>
        <th v-for="(item, index) in resultType" :key="'2'+index">{{item}}</th>
        <th v-for="(item, index) in sizeType" :key="'3'+index">{{item}}</th>
        <th v-for="(item, index) in sizeType" :key="'4'+index">{{item}}</th>
      </tr>
      <tr>
        <td>标准次数</td>
        <td v-for="item in 3" :key="'5'+item">0</td>
        <td v-for="item in 3" :key="'6'+item">0</td>
        <td v-for="item in 5" :key="'7'+item">0</td>
        <td v-for="item in 4" :key="'8'+item">0</td>
        <td v-for="item in 4" :key="'9'+item">0</td>
      </tr>
      <tr>
        <td>实际次数</td>
        <td v-for="item in 3" :key="'10'+item">0</td>
        <td v-for="item in 3" :key="'11'+item">0</td>
        <td v-for="item in 5" :key="'12'+item">0</td>
        <td v-for="item in 4" :key="'13'+item">0</td>
        <td v-for="item in 4" :key="'14'+item">0</td>
      </tr>
    </table> -->
    <table border="1" cellpadding="0" cellspacing="0">
      <tr class="table-title">
        <th width="80">期号</th>
        <th width="100">投注时间</th>
        <th width="200">开奖结果</th>
        <th width="60">1:3龙虎和</th>
        <th width="60">2:3龙虎和</th>
        <th width="40">豹对</th>
        <th width="40">五行</th>
        <th width="40">四季</th>
        <th width="40">星座</th>
        <th width="40">生肖</th>
        <th width="40" colspan="2">前二</th>
        <th width="40" colspan="2">后二</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateMonth}}</td>
        <td>
          <div class="result">
            <img :src="getResultNum(item.part_one_result)" alt="">
            +
            <img :src="getResultNum(item.part_two_result)" alt="">
            +
            <img :src="getResultNum(item.part_three_result)" alt="">
            =
            <div class="result_num">
              <span>{{getStrNum(item.result, 0)}}</span>
            </div>
            <!-- <div class="result_look">
              [查看]
            </div> -->
          </div>
        </td>
        <td>
          {{filterTiger(getStrNum(item.result, 1))}}
        </td>
        <td>
          {{filterTiger(getStrNum(item.result, 2))}}
        </td>
        <td>
          {{filterPard(getStrNum(item.result, 3))}}
        </td>
        <td>
          {{filterWood(getStrNum(item.result, 4))}}
        </td>
        <td>
          {{filterSeasons(getStrNum(item.result, 5))}}
        </td>
        <td>
          {{filterConstellation(getStrNum(item.result, 6))}}
        </td>
        <td>
          {{filterZodiac(getStrNum(item.result, 7))}}
        </td>
        <td>
          {{filterSize(getStrNum(item.result, 8))}}
        </td>
        <td>
          {{filterOddEven(getStrNum(item.result, 9))}}
        </td>
        <td>
          {{filterSize(getStrNum(item.result, 10))}}
        </td>
        <td>
          {{filterOddEven(getStrNum(item.result, 11))}}
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
  import { trendChart } from '@/config/trendChart.js'
  import { filterNum, defalutImg } from '@/config/mixin.js'
  export default {
    name: "trendChartThree",
    data() {
      return {
        gameType: ['龙', '虎', '和'],
        resultType: ['豹', '对', '顺', '半', '杂'],
        sizeType: ['大', '小', '单', '双'],
        list: []
      };
    },
    mixins: [trendChart, filterNum, defalutImg],
    created(){
       this.list = this.trendData.game_log_list
    }
  }
</script>
<style scoped lang='less'>
  .trendChartThree {
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

        .result {
          font-size: 14px;
          display: flex;
          align-items: center;
          justify-content: center;

          img {
            height: 14px;
            margin: 0 4px;
          }

          .result_num {
            margin-left: 6px;
            .result_ball(24px, 14px);
          }
          
          .result_look {
            .sc(12px, #D1913C);
            margin-left: 10px;

            &:hover {
              cursor: pointer;
            }
          }
        }
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
  }
</style>