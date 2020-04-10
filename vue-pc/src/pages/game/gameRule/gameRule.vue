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
        <div class="right_btn" @click="autoBet()" v-if="judgeEdit(choosedGame.game_type_id)">
          自动投注
        </div>
      </div>
      <img src="http://www.yunshenghuo88.com/images/S_Open.gif" alt="" class="audio-icon" @click="setAudioFn()" v-if="openAudio">
      <img src="http://www.yunshenghuo88.com/images/S_Close.gif" alt="" class="audio-icon" @click="setAudioFn()" v-else>
      <audio src="http://www.yunshenghuo88.com/image/security.mp3" id="myaudio" style="display: none;"></audio>
    </div>
    <div class="count_tips" v-if="awardResult && awardResult.bet_log_info">
      <p class="left">游戏时间：北京时间 {{awardResult.game_type_info.start_time}}～{{awardResult.game_type_info.end_time}},每{{awardResult.game_type_info.each_issue_time}}分钟一期，全天 {{awardResult.game_type_info.issue_num}} 期。</p>
      <p class="right">投注范围:{{awardResult.game_type_info.min_bet_money | changeBigNum}}～ {{awardResult.game_type_info.max_bet_money | changeBigNum}}/ 最高中奖:{{awardResult.game_type_info.max_win_money | changeBigNum}}</p>
    </div>
    <!-- 游戏规则 -->
    <div class="rich_text" v-html="rules"></div>
  </div>
</template>
<script>
  import {
    mapMutations, mapActions
  } from 'vuex'
  import {
    getGameType,
    getTableType
  } from '@/config/config.js'
  import { gameCuntDown, judgeMixin } from '@/config/mixin.js'
  import winResult from '@/components/games/winResult'
  import betModelNum from '@/components/games/betModel/betModelNum'
  import betModelPoker from '@/components/games/betModel/betModelPoker'
  import tableTrend from '@/components/games/tableTrend/tableTrend'
  import { setInterval } from 'timers';

  export default {
    name: "gameHome",
    mixins: [gameCuntDown, judgeMixin],
    data() {
      return {
        gameType: -1, // 游戏类型  0： 号码类型    1： 扑克类型
        gameTableType: -1, // 游戏表格类型  (1-6)    7: 号码投注   8：扑克投注
        currentGame: {},
        loading: true,
        dialog_show: false,
        table_info: {},
        rules: '',
        pageSize: 10
      };
    },
    components: {
      winResult,
      betModelNum,
      betModelPoker,
      tableTrend,
    },
    methods: {
      autoBet(){
        this.$router.push({
          path: '/gameIndex/autoBet'
        })
      },
      // 获取游戏开奖列表
      getAwaedList: function () {
        this.loading = true
        return new Promise((resolve, reject) => {
          this.$Api({
            api_name: 'kkl.game.getResultLogList',
            firstRow: this.firstRow,
            pageSize: this.pageSize,
            game_type_id: this.choosedGame.game_type_id
          }, (err, data) => {
            if (!err) {
              this.loading = false
              resolve(data.data)
            }
          })
        })
      },
      // 初始化数据
      initTable: function () {
        let currentGame = this.choosedGame
        this.firstRow = 0
        this.gameTableType = getTableType(currentGame.table_type)
        this.gameType = getGameType(currentGame.game_type_id)
        this.setResultList([])
        this.loading = true
        this.getAwaedList().then(res => {
          let list = res.game_result_list || []
          this.loading = false
          this.setResultList(list)
          this.totalList = parseInt(res.total)
          this.filterNewstItem(this.gameResultList)
        })
      },
      ...mapMutations({
        chooseGame: 'CHOOSE_GAME',
        setSeconds: 'SET_SECONDS',
        setResultList: 'SET_RESULT_LIST'
      }),
      ...mapActions([
        'startCountDown'
      ])
    },
    watch: {
      choosedGame(currentGame) {
        this.chooseGame(currentGame);
        clearInterval(this.waitTimer)
        this.initTable()
      }
    },
    created(){
        this.initTable()
        this.$Api({
            api_name: 'kkl.game.getGameRule',
            game_type_id: this.choosedGame.game_type_id
        }, (err, data) => {
            if (!err) {
                this.rules = data.data.game_rule
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    }
  }
</script>

<style scoped lang='less'>
  .gameHome {
    width: @main-width;
    margin: 0 auto;
    .count_down {
      margin: 20px auto 0;
      display: flex;
      align-items: center;
      border-bottom: 1px solid #FFEFD4;
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
        background: #FFEFD4;
        border-radius: 8px 8px 0px 0px;
        height: 60px;
        // width: 407px;
        padding-left: 20px;
        padding-right: 20px;
        margin-right: 20px;
        box-sizing: border-box;

        .expect {
          margin-right: 20px;
          .sc(20px, #4A4130);

          span {
            .sc(28px, #F5A623);
          }
        }

        .second {
          .sc(20px, #4A4130);

          span {
            .sc(28px, #F5A623);
          }
          .normal{
            .sc(20px, #4A4130);
          }
        }
        .wait{
          span {
            .sc(24px, #F5A623);
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
            .sc(16px, #4A4130);
            margin-right: 14px;

            span {
              color: #F5A623;
            }
          }
        }

        .mid_bottom {
          .sc(16px, #4A4130);

          span {
            color: #F5A623;
          }
        }
      }

      .count_down_right {
        min-width: 118px;

        .right_btn {
          width: 118px;
          height: 32px;
          border-radius: 8px;
          background: #FFEFD4;
          line-height: 32px;
          text-align: center;
          .sc(14px, #D1913C);
          &:hover{
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
        top: 100px;
        left: 50%;
        margin-left: -420px;
        z-index: 1000;
      }
    }
    .rich_text{
      margin-top: 30px;
      margin-bottom: 10px;
    }
  }
</style>