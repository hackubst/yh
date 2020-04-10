<template>
  <div class="autoBet">
    <!-- 开奖结果组件 -->
    <win-result :gameType="gameType"></win-result>
    <!-- 投注倒计时 -->
    <div class="count_down">
      <div class="count_down_left">
        <div class="expect">
          第
          <span>{{newestItem.issue}}</span>
          期
        </div>
        <div class="second" v-if="newestState == 0">
          <span class="normal" v-if="seconds <= 10">停止下注，</span>
          还有
          <span v-if="seconds > 0">{{seconds}}</span>
          <span v-else>0</span>
          <span v-if="seconds > 10" class="normal">秒停止下注!</span>
          <span v-else class="normal">秒开奖!</span>
        </div>
        <div class="wait" v-else-if="newestState == 1">
          <span>正在开奖，请稍后！</span>
        </div>
      </div>
      <div class="count_down_mid" v-if="awardResult.bet_log_info">
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
        <div class="mid_bottom">
          最高下注
          <span>{{awardResult.game_type_info.max_bet_money | changeBigNum}}</span>
          万豆,最高中奖
          <span>{{awardResult.game_type_info.max_win_money | changeBigNum}}</span>
          万豆
        </div>
      </div>
      <div class="count_down_right">
        <div class="right_btn">自动投注</div>
      </div>
      <img src="http://www.yunshenghuo88.com/images/S_Open.gif" alt="" class="audio-icon" @click="setAudioFn()" v-if="openAudio">
      <img src="http://www.yunshenghuo88.com/images/S_Close.gif" alt="" class="audio-icon" @click="setAudioFn()" v-else>
      <audio src="http://www.yunshenghuo88.com/image/security.mp3" id="myaudio" style="display: none;"></audio>
    </div>
    <!-- 自动投注组件 -->
    <bet-model-one
      :bet_list="bet_list"
      @begin_bet="begin_bet"
      :bet_boolean="bet_boolean"
      :bet_info="bet_info"
    ></bet-model-one>
  </div>
</template>
<script>
import { mapMutations, mapActions } from "vuex";
import { getGameType } from '@/config/config.js'
import winResult from "@/components/games/winResult";
import betModelOne from "@/components/games/betModel/betModelOne";
import { gameCuntDown } from "@/config/mixin.js";
export default {
  name: "autoBet",
  mixins: [gameCuntDown],
  components: {
    winResult,
    betModelOne
  },
  data() {
    return {
      gameType: 0, // 游戏类型  0： 号码类型    1： 扑克类型
      bet_list: [],
      click: true,
      bet_boolean: false,
      bet_info: ""
    };
  },
  created() {
    this.get_bet_detail();
    this.gameType = getGameType(this.choosedGame.game_type_id)
  },
  methods: {
    // 获取投注模式列表
    get_bet_list() {
      this.$Api(
        {
          api_name: "kkl.game.BetModeList",
          game_type_id: this.choosedGame.game_type_id
        },
        (err, data) => {
          if (!err) {
            if (this.bet_boolean == true) {
              data.data.bet_mode_list.map(item => {
                this.$set(item, "win_mode", item.win_change);
                this.$set(item, "loss_mode", item.loss_change);
              });
            } else {
              data.data.bet_mode_list.map(item => {
                this.$set(item, "win_mode", item.bet_mode_id);
                this.$set(item, "loss_mode", item.bet_mode_id);
              });
            }
            this.bet_list = data.data.bet_mode_list;
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 获取自动投注详情
    get_bet_detail() {
      if(!this.choosedGame.game_type_id) return
      this.$Api(
        {
          api_name: "kkl.game.getAutoBetInfo",
          type: "",
          game_type_id: this.choosedGame.game_type_id
        },
        (err, data) => {
          if (!err) {
            this.bet_info = data.data.bet_auto_info;
            if (data.data.bet_auto_info.is_open == 1) {
              this.bet_boolean = true;
            } else {
              this.bet_boolean = false;
            }
            this.get_bet_list();
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    //自动投注
    begin_bet(bet_mode_id, start_num, number, max_bean, min_bean, change_json) {
      // 取消自动投注
      if (this.bet_boolean == true) {
        this.$Api(
          {
            api_name: "kkl.game.stopAutoBet",
            game_type_id: this.choosedGame.game_type_id
          },
          (err, data) => {
            if (!err) {
              this.$msg(data.data, "success", 1500);
              this.get_bet_detail();
            } else {
              this.$msg(err.error_msg, "error", 1500);
            }
          }
        );
        return;
      }
      change_json.map(item => {
        if (item.win_change == "" || item.loss_change == "") {
          this.click = false;
        } else {
          this.click = true;
        }
      });
      if (this.click == false) {
        this.$msg("请选择模式或期号期数", "error", 1500);
      } else {
        this.$Api(
          {
            api_name: "kkl.game.setAutoBet",
            game_type_id: this.choosedGame.game_type_id,
            start_issue: start_num,
            start_mode_id: bet_mode_id,
            issue_number: number,
            max_money: max_bean,
            min_money: min_bean,
            change_json: JSON.stringify(change_json)
          },
          (err, data) => {
            if (!err) {
              this.$msg(data.data, "success", 1500);
              this.get_bet_detail();
            } else {
              this.$msg(err.error_msg, "error", 1500);
            }
          }
        );
      }
    },
    ...mapMutations({
      chooseGame: "CHOOSE_GAME"
    })
  }
};
</script>
<style scoped lang='less'>
.autoBet {
  width: @main-width;
  height: auto;
  margin: 0 auto;
  padding-bottom: 50px;
  box-sizing: border-box;
  .count_down {
    margin: 20px auto;
    margin-bottom: 30px;
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
      }
    }
  }
}
</style>