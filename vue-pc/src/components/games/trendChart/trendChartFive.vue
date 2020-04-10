<template>
  <div class="trendChartFive">
    <table border="1" cellpadding="0" cellspacing="0">
      <tr height="20" class="title_line">
        <th rowspan="2" class="row_two">期号</th>
        <th rowspan="2" width="120" class="row_two">投注时间</th>
        <th rowspan="2" width="270" class="row_two">开奖结果</th>
        <th rowspan="2" colspan="3" class="row_two">冠亚</th>
        <th colspan="5">龙虎</th>
      </tr>
      <tr height="20" class="title_line">
        <th>1</th>
        <th>2</th>
        <th>3</th>
        <th>4</th>
        <th>5</th>
      </tr>
      <tr>
        <td>903271307</td>
        <td>03-27 13:07:00</td>
        <td></td>
        <td>
          <span style="color: #018796;">11</span>
        </td>
        <td>
          <span style="color: #FD00FF;">小</span>
          <!-- <span style="color: #14D4D0;">大</span> -->
        </td>
        <td>
          <!-- <span style="color: #1909F7;">单</span> -->
          <span style="color: #C00;">双</span>
        </td>
        <td>
          <!-- <span style="color: #1200FF;">龙</span> -->
          <span style="color: #F00;">虎</span>
        </td>
        <td>
          <span style="color: #1200FF;">龙</span>
          <!-- <span style="color: #F00;">虎</span> -->
        </td>
        <td>
          <span style="color: #1200FF;">龙</span>
          <!-- <span style="color: #F00;">虎</span> -->
        </td>
        <td>
          <span style="color: #1200FF;">龙</span>
          <!-- <span style="color: #F00;">虎</span> -->
        </td>
        <td>
          <span style="color: #1200FF;">龙</span>
          <!-- <span style="color: #F00;">虎</span> -->
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
  export default {
    name: "trendChartFive",
    data() {
      return {

      };
    },
    methods: {

    }
  }
</script>
<style scoped lang='less'>
  .trendChartFive {
    width: @main-width;
    margin: 0 auto;
    margin-bottom: 40px;

    table {
      width: @main-width;
      margin: 0 auto;

      .title_line {
        th {
          border: 1px solid #e8e8e8;
          line-height: 20px;
          text-align: center;
          vertical-align: bottom;
          background: #FFEFD4;
        }

        .row_two {
          line-height: 40px;
        }
      }

      td {
        border: 1px solid #e8e8e8;
        height: 60px;
        text-align: center;
        line-height: 60px;
        vertical-align: bottom;

        span {
          font-size: 14px;
          font-weight: bold;
        }
      }
    }
  }
</style>