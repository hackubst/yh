<template>
  <div class="trendChart">
    <div class="box">
      <div class="select_box" v-if="choosedGame.game_type_id != 6">
        <div class="down_menu">
          走势图
          <select name="" id="" @change="chooseModel()" v-model="chooseItem">
            <option v-for="(item, index) in testOption" :key="index" :value="index">{{item}}</option>
          </select>
        </div>
      </div>
      <!-- 走势图样式一  蛋蛋28 -->
      <trend-chart-one v-if="chartType == 1 && trendData && trendData.game_log_list"></trend-chart-one>
      <!-- 走势图样式二  蛋蛋定位 -->
      <trend-chart-two v-if="chartType == 2 && trendData && trendData.game_log_list"></trend-chart-two>
      <!-- 走势图样式三  蛋蛋星座 -->
      <trend-chart-three v-if="chartType == 3 && trendData && trendData.game_log_list"></trend-chart-three>
      <!-- 走势图样式四  分分彩 -->
      <trend-chart-four v-if="chartType == 4"></trend-chart-four>
      <!-- 走势图样式五  比特币赛车 -->
      <trend-chart-five v-if="chartType == 5"></trend-chart-five>
      <!-- 路子走势  表格循环 (两球)-->
      <div v-if="chartType == 6 && tableTrendArr.length > 0">
        <table-trend v-for="(item, index) in tableTrendArr" :key="index" :list='item'></table-trend>
      </div>
      <!-- 路子走势  300期 -->
      <div v-if="chartType == 7 && tableTrendArr.length > 0">
        <table-trend v-for="(item, index) in tableTrendArr" :key="index" :list='item' :cb='1' @maxNum='maxNum'>
        </table-trend>
      </div>
      <!-- 走势图样式六  重庆时时彩 -->
      <trend-chart-six v-if="chartType == 8 && trendData && trendData.game_log_list"></trend-chart-six>
      <!-- 走势图样式七  比特币赛车 -->
      <trend-chart-seven v-if="chartType == 9 && trendData && trendData.game_log_list"></trend-chart-seven>
    </div>
  </div>
</template>
<script>
  import trendChartOne from '@/components/games/trendChart/trendChartOne'
  import trendChartTwo from '@/components/games/trendChart/trendChartTwo'
  import trendChartThree from '@/components/games/trendChart/trendChartThree'
  import trendChartFour from '@/components/games/trendChart/trendChartFour'
  import trendChartFive from '@/components/games/trendChart/trendChartFive'
  import trendChartSix from '@/components/games/trendChart/trendChartSix'
  import trendChartSeven from '@/components/games/trendChart/trendChartSeven'
  import tableTrend from '@/components/games/tableTrend/tableTrend'
  import {
    getRunchartType,
    ALERT_TIME
  } from '@/config/config.js'
  import {
    mapGetters,
    mapMutations
  } from 'vuex'
  export default {
    name: "trendChart",
    data() {
      return {
        testOption: ['最近100期', '最近200期', '最近300期', '最近400期', '最近500期'],
        chooseItem: 0, // 0 最近一百条  .... 
        chartType: 1, // 走势图表格类型
        tableTrendArr: [], // 二维数组
        maxArr: [] // 最近300期数组
      };
    },
    components: {
      trendChartOne,
      trendChartTwo,
      trendChartThree,
      trendChartFour,
      trendChartFive,
      trendChartSix,
      trendChartSeven,
      tableTrend
    },
    computed: {
      ...mapGetters([
        'choosedGame',
        'trendData'
      ])
    },
    methods: {
      chooseModel: function () {
        this.getChartsList()
      },
      // 获取走势图列表
      getChartsList: function () {
        this.setTrendData(null)
        if (this.choosedGame.game_type_id == 7) {
          this.testOption = ['最近300期']
        }
        this.$Api({
          api_name: 'kkl.game.getRunChart',
          game_type_id: this.choosedGame.game_type_id,
          type: this.chooseItem + 1
        }, (err, data) => {
          if (!err) {
            this.setTrendData(data.data)
            if (this.chartType == 6) {
              this.tableTrendArr.push(data.data.one_result_list)
              this.tableTrendArr.push(data.data.two_result_list)
            } else if (this.chartType == 7) {
              this.maxArr = [...data.data.one_result_list]
              let arr = [...data.data.one_result_list]
              this.tableTrendArr.push(arr)
            }
          } else {
            this.$msg(err.error_msg, 'error', ALERT_TIME)
          }
        })
      },
      maxNum: function (num) {
        for (let i = 0; i < num; i++) {
          this.maxArr.pop()
        }
        if (this.maxArr.length == 0) return
        let arr = [...this.maxArr]
        this.tableTrendArr.push(arr)
      },
      ...mapMutations({
        setTrendData: 'SET_TREND_DATA'
      })
    },
    created() {
      this.chartType = getRunchartType(this.choosedGame.game_type_id)
      this.getChartsList()
    }
  }
</script>
<style scoped lang='less'>
  .trendChart {
    // display: flex;
    margin: 0 auto;
    // justify-content: center;

    .select_box {
      width: 100%;
      height: 50px;

      .down_menu {
        height: 100%;
        box-sizing: border-box;
        background: #fbfbfb;
        font-size: 14px;
        color: #4A4130;
        display: flex;
        align-items: center;
        justify-content: center;

        select {
          width: 138px;
          height: 25px;
          border: 1px solid #999999;
          border-radius: 4px;
          padding: 2px;
          box-sizing: border-box;
          background: url("~images/icon/Angle-down@2x.png") no-repeat scroll right center transparent;
          background-size: 16px 14px;
          background-position: 95% 50%;
          margin-left: 6px;

          option {
            height: 25px;
            padding: 2px;
            box-sizing: border-box;
          }
        }

      }
    }
  }
</style>