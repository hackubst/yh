<template>
  <div class="gameHome">
    <!-- 开奖结果组件 -->
    <win-result :gameType="gameType" v-loading="resultLoading" :id="choosedGame.game_type_id"></win-result>
    <!-- 投注倒计时 -->
    <div class="count_down" v-if="newestItem">
      <div class="count_down_left">
        <div class="expect">
          第
          <span>{{newestItem.issue}}</span>
          期
        </div>
        <div class="second" v-if="newestState == 0">
          <span class="normal" v-if="seconds <= 0">停止下注，</span>
          还有
          <span v-if="seconds > 0">{{seconds}}</span>
          <span v-else-if="otherSeconds > 0">{{otherSeconds}}</span>
          <span v-if="seconds > 0" class="normal">秒停止下注!</span>
          <span v-else-if="otherSeconds > 0" class="normal">秒开奖!</span>
        </div>
        <div class="wait" v-else-if="newestState == 1">
          <span>正在开奖，请稍后！</span>
        </div>
      </div>
      <div class="count_down_mid" v-if="awardResult && awardResult.bet_log_info">
        <div class="mid_top">
          <div class="top_item">
            今日亏盈:
            <span>{{awardResult.bet_log_info.win_loss | changeBigNum}}</span>
          </div>
          <div class="top_item">
            参与:
            <span>{{awardResult.bet_log_info.total_issue}}</span>
            期
          </div>
          <div class="top_item">
            胜率:
            <span>{{awardResult.bet_log_info.rate}}</span>
            %
          </div>
        </div>
      </div>
      <div class="count_down_right">
        <div class="right_btn" @click="autoBet()" v-if="judgeEdit(choosedGame.game_type_id)">自动投注</div>
      </div>
      <img src="http://www.yunshenghuo88.com/images/S_Open.gif" alt="" class="audio-icon" @click="setAudioFn()" v-if="openAudio">
      <img src="http://www.yunshenghuo88.com/images/S_Close.gif" alt="" class="audio-icon" @click="setAudioFn()" v-else>
      <audio src="http://www.yunshenghuo88.com/image/security.mp3" id="myaudio" style="display: none;"></audio>
    </div>
    <!--  -->
    <div class="count_tips" v-if="awardResult && awardResult.bet_log_info">
      <p class="left">游戏时间：北京时间 {{awardResult.game_type_info.start_time}}～{{awardResult.game_type_info.end_time}},每{{awardResult.game_type_info.each_issue_time}}分钟一期，全天 {{awardResult.game_type_info.issue_num}} 期。</p>
      <p class="right">投注范围:{{awardResult.game_type_info.min_bet_money | changeBigNum}}～ {{awardResult.game_type_info.max_bet_money | changeBigNum}}/ 最高中奖:{{awardResult.game_type_info.max_win_money | changeBigNum}}</p>
    </div>
    <!-- 游戏表格 样式一 -->
    <table-one
      v-if="gameTableType == 1"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-one>
    <!-- 游戏表格 样式二 -->
    <table-two
      v-if="gameTableType == 2"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-two>
    <!-- 游戏表格 样式三 -->
    <table-three
      v-if="gameTableType == 3"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-three>
    <!-- 游戏表格 样式四 -->
    <table-four
      v-if="gameTableType == 4"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-four>
    <!-- 游戏表格 样式五 -->
    <table-five
      v-if="gameTableType == 5"
      :gameResultList="gameResultList"
      v-loading="loading"
      @guessing="guessing"
    ></table-five>
    <!-- 游戏表格 样式六 -->
    <table-six
      v-if="gameTableType == 6"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-six>
    <!-- 游戏表格 样式七 -->
    <table-seven
      v-if="gameTableType == 13"
      :gameResultList="gameResultList"
      v-loading="loading"
      @guessing="guessing"
    ></table-seven>
    <!-- 游戏表格 样式八 -->
    <table-eight
      v-if="gameTableType == 14"
      :gameResultList="gameResultList"
      v-loading="loading"
      @guessing="guessing"
    ></table-eight>
    <!-- 游戏表格 样式九 -->
    <table-nine
      v-if="gameTableType == 15"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing"
    ></table-nine>

    <!-- 游戏表格 群玩法 -->
    <table-ten
      v-if="gameTableType == 17"
      :gameResultList="gameResultList"
      v-loading="loading"
      @view="view"
      @guessing="guessing">
    </table-ten>
    <!-- 号码投注 -->
    <bet-model-num v-if="gameTableType == 7" @betSucceed="betSucceed()"></bet-model-num>
    <!-- 扑克投注 -->
    <bet-model-poker v-if="gameTableType == 8" @betSucceed="betSucceed()"></bet-model-poker>
    <!-- 蛋蛋外围、蛋蛋百家乐 投注列表 -->
    <guess-betting v-if="gameTableType == 9" @betSucceed="betSucceed()"></guess-betting>
    <!-- 蛋蛋定位 投注列表 -->
    <guess-bet v-if="gameTableType == 10" @betSucceed="betSucceed()"></guess-bet>
    <!--  -->
    <guess-bitcoin v-if="gameTableType == 16" @betSucceed="betSucceed()"></guess-bitcoin>
    <!-- 蛋蛋星座 投注列表 -->
    <guess-constellation v-if="gameTableType == 11" @betSucceed="betSucceed()"></guess-constellation>
    <!-- 有五个球的投注列表 -->
    <color-separation v-if="gameTableType == 12" @betSucceed="betSucceed()"></color-separation>
    <!-- 输赢类型走势图组件 -->
    <!-- <table-trend v-if="(choosedGame.game_type_id == 6 && gameTableType !== 9) || choosedGame.game_type_id == 28 || choosedGame.game_type_id == 29"></table-trend> -->
    <!--分页按钮  -->
    <div
      class="paging_box"
      v-if="gameTableType !== 7 && gameTableType !== 8 && gameTableType !== 9 && gameTableType !== 10 && gameTableType !== 11 && gameTableType !== 12 && gameTableType !== 16"
    >
      <el-pagination
        layout="prev, pager, next"
        @current-change="currentChange"
        :current-page.sync="currentPage"
        :total="totalList"
        prev-text="上一页"
        next-text="下一页"
        :pager-count="5"
        :page-size="pageSize"
      ></el-pagination>
    </div>
    <!-- 查看弹出框 -->
    <div class="dialog" v-if="dialog_show">
      <div class="dialog_bg" @click="close_dialog()"></div>
      <div class="dialog_table" ref="dialogtable">
        <eggSeries
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id != 2 && choosedGame.game_type_id != 7 && choosedGame.game_type_id != 10 && choosedGame.game_type_id != 12 && choosedGame.game_type_id != 21 && choosedGame.game_type_id != 34 && choosedGame.game_type_id != 29 && choosedGame.game_type_id != 39 && choosedGame.game_type_id != 42 && choosedGame.game_type_id != 45 && choosedGame.game_type_id != 46 && choosedGame.game_type_id != 47 && choosedGame.game_type_id != 48 && choosedGame.game_type_id != 49 && choosedGame.game_type_id != 50 && choosedGame.game_type_id != 51 && choosedGame.game_type_id != 57"
          @close="close_dialog()"
          :item="table_info"
        ></eggSeries>
        <eggTen
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id == 34"
          @close="close_dialog()"
          :item="table_info"
        ></eggTen>
        <eggBaccarat
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id == 7 || choosedGame.game_type_id == 29 || choosedGame.game_type_id == 39"
          @close="close_dialog()"
          :item="table_info"
        ></eggBaccarat>
        <eggEleven
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id == 10 || choosedGame.game_type_id == 12 || choosedGame.game_type_id == 21 || choosedGame.game_type_id == 42"
          @close="close_dialog()"
          :item="table_info"
        ></eggEleven>
        <eggBitcoin
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id == 45 || choosedGame.game_type_id == 47 || choosedGame.game_type_id == 49"
          @close="close_dialog()"
          :item="table_info"
        ></eggBitcoin>
        <eggBitcoincar
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id == 46 || choosedGame.game_type_id == 48 || choosedGame.game_type_id == 50 || choosedGame.game_type_id == 51 || choosedGame.game_type_id == 57"
          @close="close_dialog()"
          :item="table_info"
        ></eggBitcoincar>
      </div>
    </div>
    <el-dialog
      :visible.sync="dialogVisible"
      width="840px"
      center
      :show-close="boolear"
      v-if="choosedGame.game_type_id == 2"
    >
      <eggAnother :game_type_name="game_type_name" @close="close_dialog()" :item="table_info"></eggAnother>
    </el-dialog>
  </div>
</template>
<script>
import { mapMutations, mapActions } from "vuex";
import { getGameType, getTableType, getLotteryTime } from "@/config/config.js";
import { gameCuntDown, judgeMixin } from "@/config/mixin.js";
import winResult from "@/components/games/winResult";
import tableOne from "@/components/games/gameTable/tableOne";
import tableTwo from "@/components/games/gameTable/tableTwo";
import tableThree from "@/components/games/gameTable/tableThree";
import tableFour from "@/components/games/gameTable/tableFour";
import tableFive from "@/components/games/gameTable/tableFive";
import tableSix from "@/components/games/gameTable/tableSix";
import tableSeven from "@/components/games/gameTable/tableSeven";
import tableEight from "@/components/games/gameTable/tableEight";
import tableNine from "@/components/games/gameTable/tableNine";
import tableTen from "@/components/games/gameTable/tableTen";
import betModelNum from "@/components/games/betModel/betModelNum";
import betModelPoker from "@/components/games/betModel/betModelPoker";
import guessBetting from "@/components/games/betModel/guessBetting";
import guessBet from "@/components/games/betModel/guessBet";
import guessBitcoin from "@/components/games/betModel/guessBitcoin";
import guessConstellation from "@/components/games/betModel/guessConstellation";
import colorSeparation from "@/components/games/betModel/colorSeparation";
import tableTrend from "@/components/games/tableTrend/tableTrend";
import eggSeries from "@/components/games/lottery_results/egg_series";
import eggTen from "@/components/games/lottery_results/egg_ten";
import eggBaccarat from "@/components/games/lottery_results/egg_baccarat";
import eggEleven from "@/components/games/lottery_results/egg_eleven";
import eggAnother from "@/components/games/lottery_results/egg_another";
import eggBitcoin from "@/components/games/lottery_results/egg_bitcoin";
import eggBitcoincar from "@/components/games/lottery_results/egg_bitcoincar"

export default {
  name: "gameHome",
  mixins: [gameCuntDown, judgeMixin],
  data() {
    return {
      gameType: -1, // 游戏类型  0： 号码类型    1： 扑克类型
      gameTableType: -1, // 游戏表格类型  (1-6)    7: 号码投注   8：扑克投注
      currentGame: {},
      firstRow: 0,
      pageSize: 20,
      totalList: 0,
      loading: true,
      resultLoading: false,
      dialog_show: false,
      table_info: {},
      game_type_name: "",
      dialogVisible: false, // 判断是否弹框
      boolear: false, // 查看卡密弹出框
      currentPage: 1 // 当前页数
    };
  },
  components: {
    winResult,
    tableOne,
    tableTwo,
    tableThree,
    tableFour,
    tableFive,
    tableSix,
    tableSeven,
    tableEight,
    tableNine,
    tableTen,
    betModelNum,
    betModelPoker,
    guessBetting,
    guessBet,
    guessBitcoin,
    guessConstellation,
    colorSeparation,
    tableTrend,
    eggSeries,
    eggTen,
    eggBaccarat,
    eggEleven,
    eggAnother,
    eggBitcoin,
    eggBitcoincar
  },
  methods: {
    view(item) {
      setTimeout(() => {
        let dialogtableHeight = this.$refs.dialogtable.offsetHeight
        this.$refs.dialogtable.style.height = dialogtableHeight+ 'px'
        this.$refs.dialogtable.style.marginTop = -(dialogtableHeight/2)+ 'px'
      }, 0);
      this.table_info = item;
      if (this.choosedGame.game_type_id == 2) {
        this.dialogVisible = true;
        return;
      }
      this.dialog_show = true;
    },
    autoBet() {
      this.$router.push({
        path: "/gameIndex/autoBet"
      });
    },
    close_dialog() {
      if (this.choosedGame.game_type_id == 2) {
        this.dialogVisible = false;
        return;
      }
      this.dialog_show = false;
    },
    // 竞猜
    guessing(gameItem) {
      if(gameItem.is_auto == 1){
        this.$msg("请先取消自动投注", 'error', 1500)
        return;
      }
      this.setGussGame(gameItem);
      if (this.gameType == 0) {
        if (
          this.choosedGame.game_type_id == 3 ||
          this.choosedGame.game_type_id == 6 ||
          this.choosedGame.game_type_id == 26 ||
          this.choosedGame.game_type_id == 28 ||
          this.choosedGame.game_type_id == 35
        ) {
          this.gameTableType = 9;
        } else if (
          this.choosedGame.game_type_id == 4 ||
          this.choosedGame.game_type_id == 25 ||
          this.choosedGame.game_type_id == 36
        ) {
          this.gameTableType = 10;
        } else if (
          this.choosedGame.game_type_id == 8 ||
          this.choosedGame.game_type_id == 30 ||
          this.choosedGame.game_type_id == 37 ||
          this.choosedGame.game_type_id == 40
        ) {
          this.gameTableType = 11;
        } else if (this.choosedGame.game_type_id == 43) {
          this.gameTableType = 12;
        } else {
          this.gameTableType = 7;
        }
      } else if (this.gameType == 1) {
        this.gameTableType = 8;
      } else {
        this.gameTableType = 16;
      }
    },
    // 获取游戏开奖列表
    getAwaedList: function() {
      this.loading = true;
      return new Promise((resolve, reject) => {
        this.$Api(
          {
            api_name: "kkl.game.getResultLogList",
            firstRow: this.firstRow,
            pageSize: this.pageSize,
            game_type_id: this.choosedGame.game_type_id
          },
          (err, data) => {
            if (!err) {
              this.loading = false;
              resolve(data.data);
            }
          }
        );
      });
    },
    // 投注成功
    betSucceed: function() {
      let currentGame = this.choosedGame;
      this.gameTableType = getTableType(currentGame.table_type);
      this.gameType = getGameType(currentGame.game_type_id);
      this.initTable();
    },
    // 初始化数据
    initTable: function() {
      let currentGame = this.choosedGame;
      this.game_type_name = currentGame.game_type_name;
      this.firstRow = 0;
      this.currentPage = 1;
      this.gameTableType = getTableType(currentGame.table_type);
      this.gameType = getGameType(currentGame.game_type_id);
      let lotteryTime = getLotteryTime(currentGame.game_type_id);
      this.setLotteryTime(lotteryTime);
      this.setResultList([]);
      this.loading = true;
      this.getAwaedList().then(res => {
        let list = res.game_result_list || [];
        this.loading = false;
        this.setResultList(list);
        this.totalList = parseInt(res.total);
        this.filterNewstItem(this.gameResultList);
      });
    },
    currentChange: function(e) {
      // if (this.newestState == 1) return;
      // 根据页面请求对应的数据
      this.firstRow = (e - 1) * this.pageSize;
      this.getAwaedList().then(res => {
        let list = res.game_result_list || [];
        this.setResultList(list);
        this.totalList = parseInt(res.total);
        this.filterNewstItem(this.gameResultList);
      });
    },
    ...mapMutations({
      chooseGame: "CHOOSE_GAME",
      setSeconds: "SET_SECONDS",
      setResultList: "SET_RESULT_LIST",
      setGussGame: "SET_GUSS_GAME",
      setLotteryTime: "SET_LOTTERY_TIME",
      setIsExperience: "SET_IS_EXPERIENCE"
    }),
    ...mapActions(["startCountDown", "startCountDownOther"])
  },
  watch: {
    choosedGame(currentGame) {
      this.chooseGame(currentGame);
      clearInterval(this.waitTimer);
      this.initTable();
    }
  },
  beforeRouteLeave(to, from, next) {
    next();
    // if(this.newestState == 0 || to.fullPath.indexOf("/gameIndex") == -1){
    //   next()
    // }
  },
  created() {
    this.initTable();
  }
};
</script>
<style>
.paging_box .el-pagination button {
  width: 66px;
  height: 36px;
  border-radius: 4px;
  border: 1px solid #d1913c;
  font-size: 14px;
  color: #d1913c;
  margin-right: 10px;
}

.paging_box .el-pagination .el-pager .number {
  height: 36px;
  width: 32px;
  border: 1px solid #d1913c;
  font-size: 14px;
  line-height: 32px;
  color: #d1913c;
  margin-right: 10px;
  border-radius: 4px;
}

.paging_box .el-pagination .el-pager .active {
  background: linear-gradient(
    360deg,
    rgba(209, 145, 60, 1) 0%,
    rgba(255, 209, 148, 1) 100%
  );
  color: #fff;
}
</style>

<style scoped lang='less'>
.gameHome {
  width: @main-width;
  margin: 0 auto;
  .count_down {
    margin: 20px auto 0;
    display: flex;
    align-items: center;
    border-bottom: 1px solid #ffefd4;
    height: 60px;
    justify-content: space-between;
    padding-right: 20px;
    box-sizing: border-box;
    position: relative;
    .audio-icon{
        position: absolute;
        top: 0;
        right: 0;
        width: 15px;
      }

    .count_down_left {
      display: flex;
      align-items: center;
      background: #ffefd4;
      border-radius: 8px 8px 0px 0px;
      height: 60px;
      padding-left: 20px;
      padding-right: 20px;
      margin-right: 20px;
      box-sizing: border-box;

      .expect {
        margin-right: 20px;
        .sc(20px, #4a4130);

        span {
          .sc(28px, #f5a623);
        }
      }

      .second {
        .sc(20px, #4a4130);

        span {
          .sc(28px, #f5a623);
        }
        .normal {
          .sc(20px, #4a4130);
        }
      }
      .wait {
        span {
          .sc(24px, #f5a623);
          font-weight: bold;
        }
      }
    }

    .count_down_mid {
      flex: 1;

      .mid_top {
        display: flex;
        align-items: center;
        height: 30px;

        .top_item {
          .sc(18px, #4a4130);
          margin-right: 14px;

          span {
            color: #f5a623;
          }
        }
      }

      .mid_bottom {
        .sc(18px, #4a4130);

        span {
          color: #f5a623;
        }
      }
    }

    .count_down_right {
      min-width: 118px;
      position: relative;

      .right_btn {
        width: 118px;
        height: 32px;
        border-radius: 8px;
        background: #ffefd4;
        line-height: 32px;
        text-align: center;
        .sc(14px, #d1913c);
        &:hover {
          cursor: pointer;
        }
      }
    }
  }
  .count_tips {
    display: flex;
    justify-content: space-between;
    box-sizing: border-box;
    align-items: center;
    height: 39px;
    border: 1px solid #ffefd4;
    padding: 0 20px;
    margin-bottom: 20px;
    p {
      font-size: 16px;
      color: #333333;
    }
  }
  .paging_box {
    width: @main-width;
    display: flex;
    justify-content: center;
    margin-bottom: 50px;
  }
  .dialog {
    width: 100%;
    height: auto;
    position: relative;
    .dialog_bg {
      background: rgb(0, 0, 0);
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      overflow: auto;
      margin: 0;
      opacity: 0.5;
      z-index: 999;
    }
    .dialog_table {
      .wh(840px, auto);
      background-color: #fff;
      position: fixed;
      // top: 100px;
      top: 50%;
      left: 50%;
      margin-left: -420px;
      z-index: 1000;
    }
  }
}
</style>
