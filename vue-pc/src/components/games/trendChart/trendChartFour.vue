<template>
  <div class="trendChartFour">
    <table border="1" cellpadding="0" cellspacing="0" class="tableOne">
      <tr>
        <th rowspan="2" width="70"></th>
        <th colspan="5" width="305">前三</th>
        <th colspan="5" width="305">中三</th>
        <th colspan="5" width="305">后三</th>
      </tr>
      <tr>
        <th v-for="(item, index) in gameType" :key="'0'+index">{{item}}</th>
        <th v-for="(item, index) in gameType" :key="'1'+index">{{item}}</th>
        <th v-for="(item, index) in gameType" :key="'2'+index">{{item}}</th>
      </tr>
      <tr>
        <td>标准次数</td>
        <td v-for="item in 5" :key="'5'+item">0</td>
        <td v-for="item in 5" :key="'6'+item">0</td>
        <td v-for="item in 5" :key="'7'+item">0</td>
      </tr>
      <tr>
        <td>实际次数</td>
        <td v-for="item in 5" :key="'5'+item">0</td>
        <td v-for="item in 5" :key="'6'+item">0</td>
        <td v-for="item in 5" :key="'7'+item">0</td>
      </tr>
    </table>
    <table border="1" cellpadding="0" cellspacing="0">
      <tr class="table-title">
        <th width="120">期号</th>
        <th width="120">投注时间</th>
        <th width="270">开奖结果</th>
        <th width="160">前三</th>
        <th width="160">中三</th>
        <th width="160">后三</th>
      </tr>
      <tr>
          <td>2184630</td>
          <td>03-27 11:07:00</td>
          <td>
              
          </td>
          <td>杂</td>
          <td>半</td>
          <td>对</td>
      </tr>
    </table>
  </div>
</template>
<script>
  export default {
    name: "trendChartFour",
    data() {
      return {
        gameType: ['豹', '对', '顺', '半', '杂']
      };
    }
  }
</script>
<style scoped lang='less'>
  .trendChartFour {
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
  }
</style>