<template>
  <div class="gameHome">
    <!-- 开奖结果组件 -->
    <win-result :gameType="gameType"></win-result>
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
        <!-- <div class="mid_bottom">
          最高下注
          <span>{{awardResult.game_type_info.max_bet_money | changeBigNum}}</span>
          万豆,最高中奖
          <span>{{awardResult.game_type_info.max_win_money | changeBigNum}}</span>
          万豆
        </div> -->
      </div>
      <div class="count_down_right">
        <div class="right_btn" @click="autoBet()" v-if="judgeEdit(choosedGame.game_type_id)">自动投注</div>
      </div>
      <img src="http://www.yunshenghuo88.com/images/S_Open.gif" alt="" class="audio-icon" @click="setAudioFn()" v-if="openAudio">
      <img src="http://www.yunshenghuo88.com/images/S_Close.gif" alt="" class="audio-icon" @click="setAudioFn()" v-else>
      <audio src="http://www.yunshenghuo88.com/image/security.mp3" id="myaudio" style="display: none;"></audio>
    </div>
    <div class="count_tips" v-if="awardResult && awardResult.bet_log_info">
      <p class="left">游戏时间：北京时间 {{awardResult.game_type_info.start_time}}～{{awardResult.game_type_info.end_time}},每{{awardResult.game_type_info.each_issue_time}}分钟一期，全天 {{awardResult.game_type_info.issue_num}} 期。</p>
      <p class="right">投注范围:{{awardResult.game_type_info.min_bet_money | changeBigNum}}～ {{awardResult.game_type_info.max_bet_money | changeBigNum}}/ 最高中奖:{{awardResult.game_type_info.max_win_money | changeBigNum}}</p>
    </div>
    <!-- 投注记录表格 -->
    <!-- 赛车系列投注记录类型 -->
    <!-- {{choosedGame.game_type_id}} -->
    <record-nine :list="list" @view_bet="view_bet" @view="view" v-if="choosedGame.game_type_id == 46 || choosedGame.game_type_id == 48 || choosedGame.game_type_id == 50 || choosedGame.game_type_id == 51 || choosedGame.game_type_id == 57"></record-nine>
    <!-- 蛋蛋定位投注记录类型 -->
    <record-two :list="list" @view_bet="view_bet" @view="view" v-else-if="choosedGame.game_type_id == 4 || choosedGame.game_type_id == 25 || choosedGame.game_type_id == 36"></record-two>
    <!-- 蛋蛋百家乐投注记录类型 -->
    <record-three :list="list" @view_bet="view_bet" @view="view" v-else-if="choosedGame.game_type_id == 6 || choosedGame.game_type_id == 28"></record-three>
    <!-- 蛋蛋星座投注记录类型 -->
    <record-four :list="list" @view_bet="view_bet" @view="view" v-else-if="choosedGame.game_type_id == 8"></record-four>
    <!-- 重庆时时彩投注记录类型 -->
    <record-five :list="list" @view_bet="view_bet" @view="view" v-else-if="choosedGame.game_type_id == 43"></record-five>
    <!-- pk系列投注记录类型 -->
    <record-six :list="list" @view_bet="view_bet" @view="view" v-else-if="choosedGame.game_type_id == 16 || choosedGame.game_type_id == 17 || choosedGame.game_type_id == 18 || choosedGame.game_type_id == 19 || choosedGame.game_type_id == 20 || choosedGame.game_type_id == 52 || choosedGame.game_type_id == 53 || choosedGame.game_type_id == 54 || choosedGame.game_type_id == 55 || choosedGame.game_type_id == 56"></record-six>
    <!-- 其他系列投注记录 -->
    <table class="game_table" style="margin-bottom: 10px;" border="1" cellpadding="0" cellspacing="0" v-else>
      <tr>
        <th style="width: 88px;">期号</th>
        <th style="width: 150px;">投注时间</th>
        <th style="width: 249px;">开奖结果</th>
        <th style="width: 101px;">投注数量</th>
        <th style="width: 101px;">获得数量</th>
        <th style="width: 89px;">赢取</th>
        <th style="width: 64px;">详情</th>
        <th style="width: 78px;">保存模式</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <!-- <td>{{getStrNum(item.result, 0)}}</td> -->
        <td>
          <!-- 开奖结果为数字 -->
          <div class="result_num_arr" v-if="numsResultGame(choosedGame.game_type_id) && item.is_open == 1">
            <span v-for="(item, index) in getStrArr(item.result)" :key="index">
              <img
                :src="getNumImage(item)"
                alt
                :class="ifHaveItem(item, item.game_log_info.result) ? 'active':''"
              >
            </span>
            <!-- 结果为数字 -->
            <div
              class="result_num_arr_result"
              v-if="choosedGame.game_type_id == 17 || choosedGame.game_type_id == 20 || choosedGame.game_type_id == 53 || choosedGame.game_type_id == 56"
            >{{getStrNum(item.game_log_info.result, 0)}}</div>
            <!-- 结果为龙虎和 -->
            <div class="result_num_arr_result" v-if="choosedGame.game_type_id == 19">
              <span v-if="getStrNum(item.game_log_info.result, 0) == 1">龙</span>
              <span v-if="getStrNum(item.game_log_info.result, 0) == 2">虎</span>
              <span v-if="getStrNum(item.game_log_info.result, 0) == 3">和</span>
            </div>
          </div>
          <!-- 新蛋蛋百家乐开奖结果 -->
          <div class="poker_result" v-if="item.game_log_info.result && (choosedGame.game_type_id == 7 || choosedGame.game_type_id == 29) && item.is_open == 1">
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 1)">庄</div>
            <div class="bg_blue" v-if="judgeHaveNum(item.game_log_info.result, 2)">闲</div>
            <div class="bg_green" v-if="judgeHaveNum(item.game_log_info.result, 3)">和</div>
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 4)">大</div>
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 5)">小</div>
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 6)">庄对</div>
            <div class="bg_blue" v-if="judgeHaveNum(item.game_log_info.result, 7)">闲对</div>
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 8)">任意对</div>
            <div class="bg_red" v-if="judgeHaveNum(item.game_log_info.result, 9)">完美对</div>
          </div>
          <div class="result_num_str" v-else-if="choosedGame.game_type_id == 34 && item.is_open == 1">
            <div>{{item.game_log_info.result}}</div>
          </div>
          <div class="result" v-else-if="item.is_open == 1">
            <!-- 图片 -->
            <img :src="getResultNum(item.game_log_info.part_one_result)">
            <span>+</span>
            <img :src="getResultNum(item.game_log_info.part_three_result)" v-if="onlyTwoGame(choosedGame.game_type_id)">
            <img :src="getResultNum(item.game_log_info.part_two_result)" v-else>
            <span v-if="!onlyTwoGame(choosedGame.game_type_id)">+</span>
            <img :src="getResultNum(item.game_log_info.part_three_result)" v-if="!onlyTwoGame(choosedGame.game_type_id)">
            =
            <div class="result_num" v-if="oneResultGame(choosedGame.game_type_id)">
              <span>{{item.game_log_info.result}}</span>
            </div>
            <!-- 结果为汉字的游戏表格 -->
            <div v-else-if="strResultGame(choosedGame.game_type_id)">
              <div
                class="result_str"
                style="background: #66ff33;"
                v-if="item.game_log_info.result == 1"
              >豹</div>
              <div
                class="result_str"
                style="background: #B822DD;"
                v-else-if="item.game_log_info.result == 2"
              >顺</div>
              <div
                class="result_str"
                style="background: #3C3CC4;"
                v-else-if="item.game_log_info.result == 3"
              >对</div>
              <div
                class="result_str"
                style="background: #EE1111;"
                v-else-if="item.game_log_info.result == 4"
              >半</div>
              <div class="result_str" style="background: #1AE6E6;" v-else>杂</div>
            </div>
            <span class="result_look" @click="view(item)">[查看]</span>
          </div>
        </td>
        <td>{{item.total_bet_money | changeBigNum}}</td>
        <td>{{item.total_after_money | changeBigNum}}</td>
        <td>{{item.win_loss}}</td>
        <td style="color: rgba(209,145,60,1); cursor: pointer;" @click="view_bet(item)">查看</td>
        <td
          style="color: rgba(209,145,60,1); cursor: pointer;"
          @click="save_model(item.bet_log_id)"
        >保存</td>
      </tr>
    </table>
    <!--分页按钮  -->
    <div class="paging_box" v-if="total!=0">
      <el-pagination
        @current-change="changePage"
        :page-size="10"
        :current-page="page"
        layout="prev, pager, next"
        :total="total"
        prev-text="上一页"
        next-text="下一页"
      ></el-pagination>
    </div>
    <!-- 查看详情弹窗 -->
    <record-dialog v-if="showDialog" v-model="showDialog" :recordItem="recordItem"></record-dialog>
    <!-- 查看弹出框 -->
    <div class="dialog" v-if="dialog_show">
      <div class="dialog_bg" @click="close_dialog()"></div>
      <div class="dialog_table" ref="dialogtable">
        <eggSeries
          :game_type_name="game_type_name"
          v-if="choosedGame.game_type_id != 2 && choosedGame.game_type_id != 7 && choosedGame.game_type_id != 10 && choosedGame.game_type_id != 12 && choosedGame.game_type_id != 21 && choosedGame.game_type_id != 29 && choosedGame.game_type_id != 39 && choosedGame.game_type_id != 42 && choosedGame.game_type_id != 45 && choosedGame.game_type_id != 46 && choosedGame.game_type_id != 47 && choosedGame.game_type_id != 48 && choosedGame.game_type_id != 49 && choosedGame.game_type_id != 50 && choosedGame.game_type_id != 51 && choosedGame.game_type_id != 57"
          @close="close_dialog()"
          :item="table_info"
        ></eggSeries>
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
    <el-dialog :visible.sync="dialogVisible" width="840px" :show-close="boolear" v-if="choosedGame.game_type_id == 2">
      <eggAnother :game_type_name="game_type_name" @close="close_dialog()" :item="table_info"></eggAnother>
    </el-dialog>
  </div>
</template>
<script>
import { mapMutations, mapActions } from "vuex";
import { getGameType, getTableType } from "@/config/config.js";
import { gameCuntDown, defalutImg, filterNum, judgeMixin } from "@/config/mixin.js";
import winResult from "@/components/games/winResult";
import betModelNum from "@/components/games/betModel/betModelNum";
import betModelPoker from "@/components/games/betModel/betModelPoker";
import tableTrend from "@/components/games/tableTrend/tableTrend";
import { setInterval } from "timers";
import recordDialog from "@/components/games/recordDialog";
import eggSeries from "@/components/games/lottery_results/egg_series";
import eggBaccarat from "@/components/games/lottery_results/egg_baccarat";
import eggEleven from "@/components/games/lottery_results/egg_eleven";
import eggAnother from "@/components/games/lottery_results/egg_another";
import eggBitcoin from "@/components/games/lottery_results/egg_bitcoin";
import eggBitcoincar from "@/components/games/lottery_results/egg_bitcoincar"
import recordTwo from "@/components/games/gameRecord/recordTwo";
import recordThree from "@/components/games/gameRecord/recordThree";
import recordFour from "@/components/games/gameRecord/recordFour";
import recordFive from "@/components/games/gameRecord/recordFive";
import recordSix from "@/components/games/gameRecord/recordSix";
import recordNine from "@/components/games/gameRecord/recordNine";
export default {
  name: "gameHome",
  mixins: [gameCuntDown, defalutImg, filterNum, judgeMixin],
  data() {
    return {
      gameType: -1, // 游戏类型  0： 号码类型    1： 扑克类型
      gameTableType: -1, // 游戏表格类型  (1-6)    7: 号码投注   8：扑克投注
      currentGame: {},
      loading: true,
      table_info: {},
      game_type_name: "",
      list: [],
      firstRow: 0,
      fetchNum: 10,
      total: 0,
      page: 1,
      showDialog: false,
      recordItem: {},
      dialogVisible: false, // 判断是否弹框
      dialog_show: false,
      game_type_id: this.$route.query.game_type_id
    };
  },
  components: {
    winResult,
    betModelNum,
    betModelPoker,
    tableTrend,
    recordDialog,
    eggSeries,
    eggBaccarat,
    eggEleven,
    eggAnother,
    eggBitcoin,
    eggBitcoincar,
    recordTwo,
    recordThree,
    recordFour,
    recordFive,
    recordSix,
    recordNine
  },
  watch: {
    $route(){
      this.initTable();
    }
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
    // 查看投注详情
    view_bet(item) {
      this.recordItem = item;
      this.showDialog = true;
    },
    close_dialog() {
      if (this.choosedGame.game_type_id == 2) {
        this.dialogVisible = false;
        return;
      }
      this.dialog_show = false;
    },
    // 保存模式
    save_model(id) {
      this.$Api(
        {
          api_name: "kkl.game.saveBetMode",
          mode_name: "",
          bet_log_id: id
        },
        (err, data) => {
          if (!err) {
            this.$msg("保存成功", "success", 1500);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 分页功能
    changePage(page) {
      this.page = page;
      this.firstRow = this.fetchNum * (page - 1);
      this.get_Bet_Log();
    },
    // 获取投注记录
    get_Bet_Log() {
      this.$Api(
        {
          api_name: "kkl.game.getBetLogList",
          game_type_id: this.choosedGame.game_type_id || this.game_type_id,
          firstRow: this.firstRow,
          pageSize: this.fetchNum
        },
        (err, data) => {
          if (!err) {
            this.list = data.data.bet_log_list;
            this.total = Number(data.data.total);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 跳转自动投注
    autoBet() {
      this.$router.push({
        path: "/gameIndex/autoBet"
      });
    },
    // 获取游戏开奖列表
    getAwaedList: function() {
      this.loading = true;
      return new Promise((resolve, reject) => {
        this.$Api(
          {
            api_name: "kkl.game.getResultLogList",
            firstRow: this.firstRow,
            pageSize: this.fetchNum,
            game_type_id: this.choosedGame.game_type_id || this.game_type_id
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
    // 初始化数据
    initTable: function() {
      let currentGame = this.choosedGame;
      this.firstRow = 0;
      this.gameTableType = getTableType(currentGame.table_type);
      this.gameType = getGameType(currentGame.game_type_id || this.game_type_id);
      this.setResultList([]);
      this.loading = true;
      this.getAwaedList().then(res => {
        let list = res.game_result_list || [];
        this.loading = false;
        this.setResultList(list);
        this.totalList = parseInt(res.total);
        this.filterNewstItem(list);
      });
      this.get_Bet_Log();
    },
    ...mapMutations({
      chooseGame: "CHOOSE_GAME",
      setSeconds: "SET_SECONDS",
      setResultList: "SET_RESULT_LIST"
    }),
    ...mapActions(["startCountDown"])
  },
  created() {
    this.initTable();
  }
};
</script>

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
      // width: 407px;
      padding-right: 20px;
      padding-left: 20px;
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
        .normal{
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
          .sc(16px, #4a4130);
          margin-right: 14px;

          span {
            color: #f5a623;
          }
        }
      }

      .mid_bottom {
        .sc(16px, #4a4130);

        span {
          color: #f5a623;
        }
      }
    }

    .count_down_right {
      min-width: 118px;

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
    margin: 20px auto 55px auto;
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
      top: 50%;
      left: 50%;
      margin-left: -420px;
      z-index: 1000;
    }
  }
}
.result_num_str {
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.result_num_str div {
  width: 24px;
  height: 24px;
  border-radius: 24px;
  font-size: 14px;
  color: #fff;
  background: linear-gradient(180deg, #f9d423 0%, #ff4e50 100%);
  line-height: 24px;
  text-align: center;
}
</style>