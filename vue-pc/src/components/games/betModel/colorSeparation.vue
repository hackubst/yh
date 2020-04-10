<template>
  <div class="colorSeparation">
    <!-- 选择投注金额 -->
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
          <div class="default_amount">
            <p>预设金额</p>
            <input type="number" v-model="rationBet" />
          </div>
        </div>
      </div>
      <div class="choose_right">
        <div class="right_inp">
          总量:
          <input type="number" v-model="allBet" disabled />
        </div>
        <div class="sure_btn" @click="sureBet()">确认投注</div>
        <div class="last_btn" @click="lastBet()">上次投注</div>
        <div class="last_btn" @click="clearChoose()">清除</div>
      </div>
    </div>
    <!-- 总和龙虎和 -->
    <table
      class="betting_table"
      cellspacing="0px"
      style="border-collapse:collapse; table-layout: fixed;"
      v-loading="loading"
    >
      <tbody>
        <tr>
          <th colspan="5">总和-龙虎和</th>
        </tr>
        <tr>
          <td v-for="(item, index) in topTable" :key="index">
            <div class="td_box">
              <div class="box_name">
                <div>{{item.name}}</div>
              </div>
              <div class="box_rate">{{item.rate}}</div>
              <div class="box_inp">
                <input
                  type="number"
                  v-model="item.bet"
                  :placeholder="item.have_bet"
                  @blur="inputFocus(item)"
                  @change="inputChange(item)"
                />
              </div>
            </div>
          </td>
        </tr>
        <tr>
          <td v-for="(item, index) in botTable" :key="index">
            <div class="td_box">
              <div class="box_name">
                <div>{{item.name}}</div>
              </div>
              <div class="box_rate">{{item.rate}}</div>
              <div class="box_inp">
                <input
                  type="number"
                  v-model="item.bet"
                  :placeholder="item.have_bet"
                  @blur="inputFocus(item)"
                  @change="inputChange(item)"
                />
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- 球一  到  球五 -->
    <div class="ball_box" v-loading="loading">
      <div class="ball_item" v-for="(ballItem, index) in ballArr" :key="index">
        <table class="betting_table_five" style="border-collapse:collapse; table-layout: fixed;">
          <tr>
            <th>{{ballItem}}</th>
          </tr>
          <tr v-for="(item, secIndex) in modeNumsMore[index + 1]" :key="secIndex">
            <td>
              <div class="td_box">
                <div class="box_name">
                  <div v-if="isStr(item.name)">{{item.name}}</div>
                  <div class="ball_img" v-else>
                    <img
                      :src="getBallIcon('pink', item.name)"
                      class="ball_img"
                      alt
                      v-if="index == 0"
                    />
                    <img
                      :src="getBallIcon('blue', item.name)"
                      class="ball_img"
                      alt
                      v-if="index == 1"
                    />
                    <img
                      :src="getBallIcon('yellow', item.name)"
                      class="ball_img"
                      alt
                      v-if="index == 2"
                    />
                    <img
                      :src="getBallIcon('blueblue', item.name)"
                      class="ball_img"
                      alt
                      v-if="index == 3"
                    />
                    <img
                      :src="getBallIcon('purple', item.name)"
                      class="ball_img"
                      alt
                      v-if="index == 4"
                    />
                  </div>
                </div>
                <div class="box_rate">{{item.rate}}</div>
                <div class="box_inp">
                  <input
                    type="number"
                    v-model="item.bet"
                    :placeholder="item.have_bet"
                    @blur="inputFocus(item)"
                    @change="inputChange(item)"
                  />
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>

    <!-- 前三 中三  后三 -->
    <div class="box_three" v-loading="loading">
      <div class="three_item" v-for="(threeItem, index) in threeArr" :key="index">
        <table
          class="betting_table"
          cellspacing="0px"
          style="border-collapse:collapse; table-layout: fixed;"
        >
          <tbody>
            <tr>
              <th colspan="5">{{threeItem}}</th>
            </tr>
            <tr>
              <td v-for="(item, index) in modeNumsMore[index + 6]" :key="index">
                <div class="td_box">
                  <div class="box_name">
                    <div>{{item.name}}</div>
                  </div>
                  <div class="box_rate">{{item.rate}}</div>
                  <div class="box_inp">
                    <input
                      type="number"
                      :placeholder="item.have_bet"
                      v-model="item.bet"
                      @blur="inputFocus(item)"
                      @change="inputChange(item)"
                    />
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import { DEFAULT_GAME_BET, ALERT_TIME } from "@/config/config.js";
import { mapGetters, mapMutations, mapActions } from "vuex";
import { betMixin, defalutImg } from "@/config/mixin.js";
import editTablea from "@/components/games/editTable/editTablea.vue";
export default {
  name: "colorSeparation",
  components: {
    editTablea
  },
  computed: {
    topTable: function() {
      return this.modeNumsMore[0].filter((item, index) => index < 5);
    },
    botTable: function() {
      return this.modeNumsMore[0].filter((item, index) => index >= 5);
    },
    ...mapGetters(["guessingGame", "choosedGame"])
  },
  data() {
    return {
      rationBet: "", // 定额梭哈的豆子总数
      ballArr: ["球一", "球二", "球三", "球四", "球五"],
      threeArr: ["前三", "中三", "后三"],
      loading: false,
      tableType: 1
    };
  },
  methods: {
    isStr: function(name) {
      return typeof name == "string";
    },
    // 选择定额梭哈的倍数
    chooseChip: function(times) {
      this.rationBet = DEFAULT_GAME_BET * times;
    },
    // 确认投注
    sureBet: function() {
      if (!this.allBet) return;
      this.$confirm(`确认投注${this.allBet}金豆`, "", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        center: true
      })
        .then(() => {
          let bet_json = this.getBetJSON();
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
                this.loading = false;
                this.$msg(err.error_msg, "error", ALERT_TIME);
              }
            }
          );
        })
        .catch(() => {
          //  取消后逻辑
        });
    },
    // 获取当前投注和上期赔率
    getLastBet: function() {
      this.$Api(
        {
          api_name: "kkl.game.getLastBetInfo",
          game_result_id: this.guessingGame.game_result_id,
          game_type_id: this.choosedGame.game_type_id
        },
        (err, data) => {
          if (!err) {
            let last_bet_rate = data.data.last_bet_rate
              ? JSON.parse(data.data.last_bet_rate)
              : "";
            let have_bet = data.data.have_bet
              ? JSON.parse(data.data.have_bet)
              : [];
            let modeNums = this.modeNumsMore;
            if (have_bet.length > 0) {
              for (let i = 0; i < modeNums.length; i++) {
                this.getLastBetInfo(modeNums[i], have_bet[i]);
              }
            }
          }
        }
      );
    },
    getLastBetInfo: function(arr, have_bet_arr) {
      let have_bet = have_bet_arr.bet_json;
      for (let i = 0; i < have_bet.length; i++) {
        let arrIndex = arr.findIndex(item => item.key == have_bet[i].key);
        if (arrIndex > -1) {
          arr[arrIndex].chooseed = true;
          arr[arrIndex].have_bet = have_bet[i].money;
        }
      }
    },
    ...mapActions(["refreshUserInfo"])
  },
  mixins: [betMixin, defalutImg]
};
</script>
<style scoped lang='less'>
.colorSeparation {
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
        display: flex;
        align-items: center;
        border: 1px solid #d1913c;

        .default_amount {
          display: flex;
          align-items: center;
          justify-content: flex-start;
          width: 170px;

          p {
            width: 70px;
            height: 45px;
            .sc(16px, #d1913c);
            background-color: #fff5c7;
            text-align: center;
            line-height: 45px;
          }

          input {
            width: 100px;
            height: 45px;
            background: #fff;
            color: #fb3a3a;
            box-sizing: border-box;
            font-size: 20px;
          }
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
        .sc(16px, #d1913c);
        width: 84px;
        height: 45px;
        border: 1px solid #d1913c;
        text-align: center;
        line-height: 45px;
        border-radius: 4px;
        background: #ffefd4;
        margin-right: 5px;
      }
    }
  }

  .betting_table {
    width: @main-width;
  }

  .betting_table_five {
    width: @main-width / 5;
  }

  table {
    text-align: center;

    th {
      height: 42px;
      line-height: 42px;
      font-size: 16px;
      color: #4a4130;
      border: 1px solid #d1913c;
    }

    td {
      height: 44px;
      border: 1px solid #d1913c;
      text-align: center;
      font-size: 14px;
      color: #4a4130;
      line-height: 44px;
      box-sizing: border-box;

      .td_box {
        display: flex;
        height: 44px;
        align-items: center;

        .box_name {
          width: 40px;
          display: flex;
          align-items: center;
          justify-content: center;

          div {
            .result_ball(25px, 12px);
          }
        }

        .ball_img {
          height: 44px;
          display: flex;
          align-items: center;
          justify-content: center;

          img {
            width: 30px;
            height: 30px;
          }
        }

        .box_rate {
          width: 32px;
          text-align: center;
        }

        .box_inp {
          flex: 1;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }

      input {
        width: 60px;
        border: 1px solid #ccc;
        background: #fff;
        height: 28px;
        text-align: left;
        padding: 0 4px;
        color: #f00;
        font-weight: 600;
      }
    }
  }

  .ball_box {
    width: @main-width;
    display: flex;

    .ball_item {
      width: @main-width / 5;
    }
  }
}
</style>