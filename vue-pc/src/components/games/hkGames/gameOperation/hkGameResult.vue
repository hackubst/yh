<template>
  <div class="game-result">
    <!--    表头-->
    <div class="table-title">
      <div class="period flex-center br1">期数</div>
      <div class="week flex-center br1">星期</div>
      <div class="date flex-center br1">时间</div>
      <div class="number  br1">
        <div class="number-top flex-center">彩球号码</div>
        <div class="number-bottom">
          <ul>
            <li v-for="(item,index) in numberList" class="flex-center">{{item}}</li>
          </ul>
        </div>
      </div>
      <div class="period flex-center br1">特码</div>
      <div class="grade flex-center br1">总分</div>
      <div class="sign flex-center br1">生肖</div>
      <div class="period flex-center br1">合数</div>
      <div class="one-sign  br1">
        <div class="number-top flex-center">一肖</div>
        <div class="number-bottom">
          <ul>
            <li v-for="(item,index) in numberList" class="flex-center" v-if="index + 1 !== numberList.length">{{item}}</li>
          </ul>
        </div>
      </div>
      <div class="wave flex-center">波段</div>
    </div>
    <!--    表个内容-->
    <!--    -->
    <div class="table-content">
      <ul>
        <li v-for="(item,index) in list">
          <div class="period flex-center br1">{{item.issue}}</div>
          <div class="week flex-center br1">{{item.week}}</div>
          <div class="date flex-center br1">{{item.time}}</div>
          <div class="more-number br1">
            <div class="box flex-center" v-for="firstItem in item.result">
              <div class="result-number" :class="[firstItem.bg === 1 ? 'bgr':firstItem.bg === 2 ? 'bgb':'bgg']">{{firstItem.title}}</div>
            </div>
          </div>
          <div class="flex-center br1">
            <div class="w38 flex-center br1">{{item.te_daxiao}}</div>
            <div class="w38 flex-center br1">{{item.danshuang}}</div>
          </div>
          <div class="flex-center br1">
            <div class="w39 flex-center br1">{{item.sum}}</div>
            <div class="w39 flex-center br1">{{item.zong_daxiao}}</div>
            <div class="w39 flex-center br1">{{item.zong_danshuang}}</div>
          </div>
          <div class="flex-center br1">
            <div class="w38 flex-center br1">{{item.te_shengxiao}}</div>
          </div>
          <div class="flex-center br1">
            <div class="w38 flex-center br1">{{item.heshu_danshuang}}</div>
            <div class="w38 flex-center br1">{{item.heshu_daxiao}}</div>
          </div>
          <div class="zodiac-sign br1">
            <div class="w19 flex-center">{{item.xiao_1}}</div>
            <div class="w19 flex-center">{{item.xiao_2}}</div>
            <div class="w19 flex-center">{{item.xiao_3}}</div>
            <div class="w19 flex-center">{{item.xiao_4}}</div>
            <div class="w19 flex-center">{{item.xiao_5}}</div>
            <div class="w19 flex-center">{{item.xiao_6}}</div>
          </div>
          <div class="wave flex-center">
            <button :class="[item.heshu_sebo === '红波' ? 'btnr':item.heshu_sebo  === '蓝波' ? 'btnb':'btng']">{{item.heshu_sebo}}</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
    export default {
        name: "hkGameResult",
        props: {
            list: {
                type:Array,
                default: () => []
            },
        },
        data() {
            return {
                numberList:['一','二','三','四','五','六','特码']
            }

        },
        methods: {
        },
        mounted() {
        }

    }
</script>

<style scoped lang="less">
  .game-result {
    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .br1 {
      border-right: 1px solid #e8e8e8;
    }

    .table-title, .table-content {
      display: flex;
      border-left: 1px solid #e8e8e8;
      border-right: 1px solid #e8e8e8;
      .wh(980px, 46px);
      background: rgba(255, 239, 212, 1);
      border-top: 2px solid #F5A623;
      font-size: 12px;
      font-weight: 600;
      color: rgba(74, 65, 48, 1);

      .period {
        .wh(76px, auto);

      }

      .week {
        .wh(43px, auto);
      }

      .date {
        .wh(102px, auto);
      }

      .number, .one-sign {
        .wh(272px, auto);

        .number-top {
          .wh(100%, 22px);
          border-bottom: 1px solid #e8e8e8;
        }

        .number-bottom {
          .wh(100%, 23px);

          ul {
            display: flex;

            li {
              flex: 1;
              font-size: 12px;
              font-weight: 600;
              color: rgba(74, 65, 48, 1);
              border-right: 1px solid #e8e8e8;
              height: 23px;
            }
          }

          li:last-child {
            border-right: none;
          }
        }
      }

      .grade {
        .wh(120px, auto)
      }

      .sign {
        .wh(38px, 100%)
      }

      .one-sign {
        .wh(106px, 100%);
      }

      .wave {
        .wh(62px, 100%);
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }

    .table-content {
      .wh(980px, auto);
      border: 1px solid #e8e8e8;

      li {
        display: flex;
        border-bottom: 1px solid #e8e8e8;
        background: #FFffff;

        .more-number {
          .wh(272px, auto);
          display: flex;

          .box {
            width: 38px;
            height: 100%;
            border-right: 1px solid #e8e8e8;

            .result-number {
              width: 30px;
              height: 30px;
              /*border:1px solid #1F70FF;*/
              text-align: center;
              line-height: 30px;
              font-size: 16px;
            }
            .bgr{
              background: url('~images/bg/red_circle.png') no-repeat;

            }
            .bgb{
              background: url('~images/bg/blue_circle.png') no-repeat;
            }
            .bgg{
              background: url('~images/bg/green_circle.png') no-repeat;
            }
          }

          .box:last-child {
            border-right: none;
          }
        }

        .zodiac-sign {
          display: flex;
          .wh(106px, 30px);

          .w19 {
            flex: 1;
            border-right: 1px solid #e8e8e8;
          }

          .w19:last-child {
            border-right: none;
          }
        }

        button {
          width: 45px;
          height: 24px;
          border-radius: 2px;
        }
        .btnr{
          background: linear-gradient(180deg, rgba(245, 81, 95, 1) 0%, rgba(159, 4, 27, 1) 100%);
        }
        .btnb{
          background:linear-gradient(360deg,rgba(16,38,123,1) 0%,rgba(89,141,244,1) 100%);
        }
        .btng{
          background:linear-gradient(360deg,rgba(16,123,36,1) 0%,rgba(71,219,120,1) 100%);
        }

        .w38 {
          .wh(37px, 100%)
        }

        .w38:last-child {
          width: 38px;
          border-right: none;
        }

        .w39 {
          .wh(39px, 100%)
        }

        .w39:last-child {
          width: 40px;
          border-right: none;
        }
      }

      li:last-child {
        border-bottom: none;
      }
    }
  }
</style>
