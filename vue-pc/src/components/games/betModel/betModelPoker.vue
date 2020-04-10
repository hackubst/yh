<template>
  <div class="betModelPoker">
    <div class="choose_money">
      <div class="choose_left">
        <div class="nums_ball">
          <img src="~images/nums/icon_chouma10@2x.png" alt @click="chooseChip(10)" />
          <img src="~images/nums/icon_chouma100@2x.png" alt @click="chooseChip(100)" />
          <img src="~images/nums/icon_chouma500@2x.png" alt @click="chooseChip(500)" />
          <img src="~images/nums/icon_chouma5k@2x.png" alt @click="chooseChip(5000)" />
          <img src="~images/nums/icon_chouma10k@2x.png" alt @click="chooseChip(10000)" />
        </div>
        <div class="nums_inp">
          预设金额:
          <input type="number" v-model="rationBet" />
        </div>
      </div>
      <div class="choose_right">
        <div class="right_inp">
          总量:
          <input type="number" v-model="allBet" disabled />
        </div>
        <div class="sure_btn" @click="sureBet()">确认投注</div>
        <div class="last_btn" @click="lastBet()">上次投注</div>
        <div class="clear_btn" @click="clearChoose()">清除</div>
      </div>
    </div>
    <div class="betItemBox" v-loading="loading">
      <div class="box_top">
        <div
          class="top_item"
          v-for="(item, index) in topTable"
          :key="index"
          @click="chooseBet(item)"
        >
          <span class="item_bet">{{item.bet}}</span>
          <p class="item_title">{{item.name}}</p>
          <p class="item_info">赔率:{{item.rate}}</p>
        </div>
      </div>
      <div class="box_bottom">
        <div
          class="bottom_item"
          v-for="(item, index) in bottomTable"
          :key="index"
          @click="chooseBet(item)"
        >
          <span class="item_bet">{{item.bet}}</span>
          <p class="item_title">{{item.name}}</p>
          <p class="item_info">赔率:{{item.rate}}</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { betMixin } from "@/config/mixin.js";
import { DEFAULT_GAME_BET, ALERT_TIME } from "@/config/config.js";
import { mapGetters, mapActions } from "vuex";
export default {
  name: "betModelPoker",
  mixins: [betMixin],
  data() {
    return {
      rationBet: DEFAULT_GAME_BET * 10,
      loading: false
    };
  },
  computed: {
    topTable: function() {
      return this.modeNumsMore[0].filter((item, index) => index < 6);
    },
    bottomTable: function() {
      return this.modeNumsMore[0].filter((item, index) => index >= 6);
    },
    ...mapGetters(["guessingGame", "choosedGame"])
  },
  methods: {
    chooseChip: function(times) {
      this.rationBet = DEFAULT_GAME_BET * times;
    },
    chooseBet: function(item) {
      if (!this.rationBet) return;
      if (parseInt(item.bet)) {
        item.bet += parseInt(this.rationBet);
      } else {
        item.bet = parseInt(this.rationBet);
      }
      item.chooseed = true;
    },
    // 确认投注
    sureBet: function() {
      let bet_json = this.getBetJSON(this.choosedGame.game_type_id);
      if (!bet_json) return;
      if (!parseInt(this.allBet)) return;
      this.$confirm(`确认投注${this.allBet}金豆`, "", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        center: true
      })
        .then(() => {
          this.loading = true;
          this.$Api(
            {
              api_name: "kkl.game.gameBet",
              bet_json: bet_json,
              game_result_id: this.guessingGame.game_result_id,
              total_bet_money: parseInt(this.allBet),
              game_type_id: this.choosedGame.game_type_id
            },
            (err, data) => {
              if (!err) {
                this.loading = false;
                this.refreshUserInfo();
                this.$msg("投注成功", "success", ALERT_TIME);
                this.$emit("betSucceed");
              } else {
                this.$msg(err.error_msg, "error", ALERT_TIME);
              }
            }
          );
        })
        .catch(() => {
          //  取消后逻辑
        });
    },
    ...mapActions(["refreshUserInfo"])
  }
};
</script>
<style scoped lang='less'>
.betModelPoker {
  margin-bottom: 50px;

  .choose_money {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;

    .choose_left {
      display: flex;
      align-items: center;

      .nums_ball {
        display: flex;
        align-items: center;

        img {
          width: 40px;
          height: 40px;
          margin-right: 5px;
        }
      }

      .nums_inp {
        width: 171px;
        height: 45px;
        border-radius: 4px;
        border: 1px solid #d1913c;
        padding-left: 10px;
        box-sizing: border-box;
        background: #ffefd4;
        display: flex;
        align-items: center;
        margin-right: 5px;
        .sc(16px, #d1913c);

        input {
          width: 80px;
          .sc(18px, #fb3a3a);
        }
      }
    }

    .choose_right {
      display: flex;
      align-items: center;

      .right_inp {
        width: 171px;
        height: 45px;
        border-radius: 4px;
        border: 1px solid #d1913c;
        padding-left: 10px;
        box-sizing: border-box;
        background: #ffefd4;
        display: flex;
        align-items: center;
        margin-right: 5px;
        .sc(16px, #d1913c);

        input {
          width: 100px;
          .sc(18px, #fb3a3a);
        }
      }

      .sure_btn {
        .commonBtn(45px, 84px);
        font-size: 16px;
        margin-right: 5px;
      }

      .last_btn {
        .rightBtn(84px, 45px);
        margin-right: 5px;
        &:hover {
          cursor: pointer;
        }
      }

      .clear_btn {
        .rightBtn(48px, 45px);
        &:hover {
          cursor: pointer;
        }
      }
    }
  }

  .betItemBox {
    .box_top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 11px;

      .top_item {
        width: 144px;
        height: 80px;
        background: #ffefd4;
        border: 1px solid #d1913c;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        position: relative;

        .item_title {
          font-size: 18px;
          color: #d1913c;
          margin-bottom: 3px;
        }

        .item_info {
          font-size: 14px;
          color: #d1913c;
        }
      }
    }

    .box_bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .bottom_item {
        width: 299px;
        height: 110px;
        border-radius: 4px;
        background: #7eadff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        position: relative;

        .item_title {
          font-size: 30px;
          margin-right: 39px;
        }

        .item_info {
          font-size: 18px;
        }
      }

      .bottom_item:nth-child(3) {
        background: #ffbf56;
      }
    }

    .item_bet {
      position: absolute;
      text-align: center;
      width: 100%;
      top: 10px;
      font-size: 25px;
      color: #f00;
    }
  }
}

.rightBtn(@width, @height) {
  width: @width;
  height: @height;
  border: 1px solid #d1913c;
  text-align: center;
  line-height: @height;
  border-radius: 4px;
  background: #ffefd4;
  border: 1px solid #d1913c;
  .sc(16px, #d1913c);
}
</style>