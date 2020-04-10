<template>
  <!--  还差正码特,规则(富文本)-->
  <div class="hk-game-header">
    <!--  开奖结果-->
    <div class="hk-games-tab">
      <!--      六合彩种类-->
      <div class="hk-game flex-center">香港六合彩</div>
      <div class="gameType">
        <div class="game_items">
          <div
            class="game_name"
            :class="secindex == secondIndex ? 'game_active':''"
            v-for="(game, secindex) in list"
            :key="secindex"
            @click="chooseItem(secindex)"
          >{{game}}
          </div>
        </div>
      </div>
    </div>
    <div class="winResult">
      <div class="homeTop">
        <div class="tip">
          <p>第9999999期</p>
          <p>开奖结果</p>
        </div>
        <div class="result">
          <ul>
            <li v-for="(item,index) in resultNumber">
              <div class='result-number'>{{item.number}}</div>
              <p>{{item.name}}</p>
            </li>
          </ul>
          <div class="add">+</div>
          <div class="last-number">
            <div class='result-number'>49</div>
            <p>和</p>
          </div>
        </div>
      </div>
    </div>
    <!--    选择操作-->
    <div class="more-options">
      <div
        class="game_name"
        :class="index == firstIndex ? 'game_active':''"
        v-for="(item, index) in optionsList"
        :key="index"
        @click="chooseOption(index)"
      >{{item}}
      </div>
    </div>
    <!--    顶部六合彩信息-->
    <div class="game-title">
      <div class="game-title-left">
        <span>香港六合彩 - 特码 &nbsp;&nbsp;当前彩种输赢：</span>
        <div>0.0000</div>
      </div>
      <div class="game-title-right">
        <div class="game-date">
          <span>2020009 期 距离封盘：</span>
          <div class="plate red-plate">未开盘</div>
          <span>距离封盘：</span>
          <div class="plate green-plate">未开盘</div>
        </div>
        <div >
          <el-dropdown>
            <div class="area-plate">
              A盘<i class="el-icon-arrow-down el-icon--right"></i>
            </div>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>A盘</el-dropdown-item>
              <el-dropdown-item>B盘</el-dropdown-item>
              <el-dropdown-item>C盘</el-dropdown-item>
              <el-dropdown-item>D盘</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </div>
    </div>
    <!--    七色波-->
    <sevenWave v-if="false"/>
    <!--    特双面，两面-->
    <doubleSided v-if="false"></doubleSided>
    <!--    五行-->
    <fiveElement v-if="false"></fiveElement>
    <!--    连肖连尾-->
    <evenTail v-if="false"></evenTail>
    <!--    连码-->
    <evenCode></evenCode>
    <!--  合肖  -->
    <addShaw v-if="false"></addShaw>
    <!--    总肖-->
    <chiefShaw v-if="false"></chiefShaw>
    <!--    平特肖-->
    <PinterShaw v-if="false"></PinterShaw>
    <!--    正码-->
    <positiveCode v-if="false"></positiveCode>
    <!--  正码特  -->
    <positiveCodeSpecial v-if="false"></positiveCodeSpecial>
    <!--    自选不中-->
    <selfChoose v-if="false"></selfChoose>
    <!--    特码-->
    <specialCode v-if="false"></specialCode>
    <!--    特肖-->
    <specialShaw v-if="false"></specialShaw>
    <!--    色波-->
    <colorWave v-if="false"></colorWave>
    <!--    正肖-->
    <positiveShaw v-if="false"></positiveShaw>
    <!--    正码1-6-->
    <oneToSix v-if="false"></oneToSix>
    <!--  特码头尾数  -->
    <headAndTail v-if="false"></headAndTail>
    <!--    会员-->
    <gameVip v-if="false"></gameVip>
    <!--    历史-->
    <gameHistory v-if="false"></gameHistory>
    <!--    结果-->
    <hkGameResult v-if="false"></hkGameResult>
  </div>
</template>

<script>
  import sevenWave from "@/components/games/hkGames/tableWave/sevenColorWave";
  import doubleSided from "@/components/games/hkGames/tableWave/doubleSided";
  import fiveElement from "@/components/games/hkGames/tableWave/fiveElements";
  import evenTail from "@/components/games/hkGames/tableWave/evenTail";
  import evenCode from "@/components/games/hkGames/tableWave/evenCode";
  import addShaw from "@/components/games/hkGames/tableWave/addShaw";
  import chiefShaw from "@/components/games/hkGames/tableWave/chiefShaw";
  import PinterShaw from "@/components/games/hkGames/tableWave/PinterShaw";
  import positiveCode from "@/components/games/hkGames/tableWave/positiveCode";
  import selfChoose from "@/components/games/hkGames/tableWave/selfChoose";
  import specialCode from "@/components/games/hkGames/tableWave/specialCode";
  import specialShaw from "@/components/games/hkGames/tableWave/specialShaw";
  import colorWave from "@/components/games/hkGames/tableWave/colorWave";
  import positiveShaw from "@/components/games/hkGames/tableWave/positiveShaw";
  import oneToSix from "@/components/games/hkGames/tableWave/oneToSix";
  import headAndTail from "@/components/games/hkGames/tableWave/headAndTail";
  import gameVip from "@/components/games/hkGames/gameOperation/gameVip";
  import gameHistory from "@/components/games/hkGames/gameOperation/gameHistory";
  import hkGameResult from "@/components/games/hkGames/gameOperation/hkGameResult";
  import positiveCodeSpecial from "@/components/games/hkGames/tableWave/positiveCodeSpecial";

  export default {
    name: "hkHeader",
    components: {
      sevenWave,
      doubleSided,
      fiveElement,
      evenTail,
      evenCode,
      addShaw,
      chiefShaw,
      PinterShaw,
      positiveCode,
      selfChoose,
      specialCode,
      specialShaw,
      colorWave,
      positiveShaw,
      oneToSix,
      headAndTail,
      gameVip,
      gameHistory,
      hkGameResult,
      positiveCodeSpecial
    },
    data() {
      return {
        optionsList: ['香港六合彩', '游戏规则', '历史', '会员', '开奖'],
        list: [
          '总肖', '自选不中', '连肖连尾', '连码', '总肖', '自选不中', '连肖连尾', '连码', '总肖', '自选不中', '连肖连尾', '连码', '总肖', '自选不中', '连肖连尾', '连码'
        ],
        firstIndex: 0,
        secondIndex: 0,
        activeGame: {}, // 选中的游戏
        gamesType: [],
        resultNumber: [{number: '01', name: '鼠'}, {number: '11', name: '龙'}, {number: '12', name: '猪'}, {
          number: '18',
          name: '猪'
        }, {number: '28', name: '猪'}, {number: '39', name: '猪'}]
      }
    },
    methods: {
      chooseItem: function (secondIndex) {
        this.secondIndex = secondIndex;
      },
      chooseOption: function (firstIndex) {
        this.firstIndex = firstIndex;
      }
    }
  }
</script>

<style scoped lang='less'>
  .flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hk-game-header {
    .wh(920px, auto);
    margin: 0 auto;

    .hk-games-tab {
      .wh(920px, auto);
      margin: 0 auto;
      position: relative;
      left: 50%;
      margin-left: -460px;
      display: flex;
      justify-content: space-between;

      .hk-game {
        width: 92px;
        height: 38px;
        background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
        border-radius: 19px;
        font-size: 16px;
        margin-right: 16px;
        font-family: PingFangSC-Regular, PingFang SC;
        font-weight: 400;
        color: rgba(255, 248, 239, 1);


      }

      .gameType {
        margin: 0px auto;
        box-sizing: border-box;
        width: 811px;
        height: 91px;
        margin-bottom: 20px;
        width: 811px;
        height: 91px;
        background: #fff8ef;
        padding: 10px 0 16px 0;
        box-sizing: border-box;
        box-shadow: 0px 2px 16px 0px rgba(209, 145, 60, 1);
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(209, 145, 60, 1);

        .game_items {
          display: flex;
          flex-wrap: wrap;
          font-size: 16px;
          color: #4a4130;
          line-height: 35px;
          flex: 1;

          .game_name {
            padding: 0 8px;
            position: relative;

            &:after {
              content: "";
              width: 1px;
              height: 12px;
              position: absolute;
              right: 0;
              top: 50%;
              transform: translateY(-50%);
              background: #4a4130;
            }
          }

          .game_active {
            color: rgba(209, 145, 60, 1);
          }

          .game_name:last-child {
            &:after {
              content: "";
              width: 0px;
              height: 0px;
            }
          }
        }

        .game_items:nth-child(2n + 1) {
          background: #fff8ef;
        }
      }

    }

    .winResult {
      height: 92px;
      border: 1px solid rgba(209, 145, 60, 1);
      border-radius: 8px;
    }

    .homeTop {
      height: 92px;
      border-radius: 8px;
      display: flex;
      overflow: hidden;
      background: linear-gradient(360deg, #d1913c 0%, #ffd194 100%);

      .tip {
        background: url('~images/bg/bg_game@2x.png') no-repeat;
        background-size: 62%, 100%;
        background-position: 100%, 100%;
        width: 217px;
        padding: 20px;
        box-sizing: border-box;

        p {
          color: #fff;
        }

        p:nth-child(1) {
          font-size: 24px;
          margin-bottom: 3px;
        }

        p:nth-child(2) {
          font-size: 20px;
        }
      }

      .result {
        flex: 1;
        background: #ffffff;
        justify-content: center;
        display: flex;
        align-items: center;

        ul {
          display: flex;

          li {
            width: 30px;
            text-align: center;
            margin-right: 15px;

            .result-number {
              width: 100%;
              height: 30px;
              /*border:1px solid #1F70FF;*/
              text-align: center;
              line-height: 30px;
              font-size: 16px;
              background: url('~images/bg/red_circle.png') no-repeat;
            }

            p {
              font-size: 14px;
              font-family: PingFangSC-Medium, PingFang SC;
              font-weight: 500;
              color: rgba(74, 65, 48, 1);
              margin-top: 12px;
            }
          }
        }

        .add {
          margin-right: 15px;
          width: 9px;
          height: 20px;
          font-size: 14px;
          font-family: PingFangSC-Medium, PingFang SC;
          font-weight: 500;
          color: rgba(74, 65, 48, 1);
          line-height: 0px;
        }

        .last-number {
          width: 30px;
          text-align: center;
          margin-right: 15px;

          .result-number {
            background: url('~images/bg/red_circle.png') no-repeat;
            width: 100%;
            height: 30px;
            text-align: center;
            line-height: 30px;
          }

          p {
            font-size: 14px;
            font-family: PingFangSC-Medium, PingFang SC;
            font-weight: 500;
            color: rgba(74, 65, 48, 1);
            margin-top: 12px;
          }
        }

      }
    }

    .more-options {
      margin: 20px auto;
      width: 326px;
      height: 42px;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      display: flex;
      font-size: 16px;
      font-weight: 400;
      line-height: 22px;
      align-items: center;
      color: #4a4130;

      .game_name {
        padding: 0 8px;
        position: relative;

        &:after {
          content: "";
          width: 1px;
          height: 12px;
          position: absolute;
          right: 0;
          top: 50%;
          transform: translateY(-50%);
          background: #4a4130;
        }
      }

      .game_active {
        color: rgba(209, 145, 60, 1);
      }

      .game_name:last-child {
        &:after {
          content: "";
          width: 0px;
          height: 0px;
        }
      }
    }

    .game-title {
      width: 100%;
      padding: 0 5px;
      box-sizing: border-box;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 40px;
      background: rgba(228, 170, 92, 1);
      border-radius: 4px;
      font-size: 16px;
      font-weight: 500;
      color: rgba(255, 255, 255, 1);
      line-height: 22px;

      .game-title-left {
        display: flex;
        align-items: center;

        div {
          text-align: center;
          width: 53px;
          height: 28px;
          background: rgba(255, 255, 255, 1);
          border-radius: 4px;
          font-size: 16px;
          font-weight: 500;
          color: rgba(224, 32, 32, 1);
          line-height: 28px;
        }
      }

      .game-title-right {
        display: flex;
        align-items: center;

        .game-date {
          display: flex;
          align-items: center;

          .plate {
            width: 53px;
            height: 28px;
            background: rgba(255, 255, 255, 1);
            border-radius: 4px;
            text-align: center;
            line-height: 28px;
            font-size: 16px;
            margin: 0 10px;
          }

          .red-plate {
            color: #E02020;
          }

          .green-plate {
            color: #57E020
          }
        }

        .area-plate {
          width: 62px;
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 0 8px;
          box-sizing: border-box;
          height: 28px;
          background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(226, 226, 226, 1) 100%);
          box-shadow: 0px -1px 0px 0px rgba(255, 255, 255, 1);
          border-radius: 4px;
          margin-left: 14px;
        }
      }
    }
  }

</style>
