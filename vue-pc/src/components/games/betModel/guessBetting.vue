<template>
  <div class="guessBetting">
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

    <div class="table_box" v-loading="loading">
      <div class="box_item">
        <table class="betting_table" cellspacing="0px" style="border-collapse:collapse;">
          <tbody>
            <tr>
              <td>号码</td>
              <td>赔率</td>
              <td>投注</td>
            </tr>
            <tr v-for="(item, index) in leftTable" :key="index">
              <td style="width: 65px;">
                <span>{{item.name}}</span>
              </td>
              <td style="width: 100px;">{{item.rate}}</td>
              <td>
                <input
                  type="number"
                  v-model="item.bet"
                  :placeholder="item.have_bet"
                  @blur="inputFocus(item)"
                  @change="inputChange(item)"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="box_item">
        <table class="betting_table" cellspacing="0px" style="border-collapse:collapse;">
          <tbody>
            <tr>
              <td>号码</td>
              <td>赔率</td>
              <td>投注</td>
            </tr>
            <tr v-for="(item, index) in rightTable" :key="index">
              <td style="width: 65px;">
                <span>{{item.name}}</span>
              </td>
              <td style="width: 100px;">{{item.rate}}</td>
              <td>
                <input
                  type="number"
                  v-model="item.bet"
                  :placeholder="item.have_bet"
                  @blur="inputFocus(item)"
                  @change="inputChange(item)"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <table
      class="betting_table"
      cellspacing="0px"
      style="border-collapse:collapse;"
      v-if="modeNumsMore[1]"
      v-loading="loading"
    >
      <tbody>
        <tr>
          <th colspan="3">龙虎豹</th>
        </tr>
        <tr>
          <td v-for="(item, index) in modeNumsMore[1]" :key="index">
            <div class="td_box">
              <div class="title">{{item.name}}</div>
              <div class="rate">{{item.rate}}</div>
              <div class="inp_box">
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
  </div>
</template>
<script>
import { DEFAULT_GAME_BET, ALERT_TIME } from "@/config/config.js";
import { mapGetters, mapMutations, mapActions } from "vuex";
import { betMixin } from "@/config/mixin.js";
export default {
  name: "guessBetting",
  computed: {
    leftTable: function() {
      return this.modeNumsMore[0].filter(
        (item, index) => index < this.modeHalf
      );
    },
    rightTable: function() {
      return this.modeNumsMore[0].filter(
        (item, index) => index >= this.modeHalf
      );
    },
    ...mapGetters(["guessingGame", "choosedGame"])
  },
  data() {
    return {
      rationBet: "", // 定额梭哈的豆子总数
      loading: false,
      tableType: 1
    };
  },
  methods: {
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
          let bet_json = this.getBetJSON(this.choosedGame.game_type_id);
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
              this.loading = false;
              if (!err) {
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
  mixins: [betMixin]
};
</script>
<style scoped lang='less'>
.guessBetting {
  margin-bottom: 50px;

  .table_box {
    display: flex;
    width: @main-width;

    .box_item {
      width: 50%;
    }
  }

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
        &:hover {
          cursor: pointer;
        }
      }
    }
  }

  .betting_table {
    width: 100%;
    text-align: center;

    th {
      height: 42px;
      line-height: 42px;
      font-size: 16px;
      color: #4a4130;
      border: 1px solid #d1913c;
      border-top: none;
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

        div {
          height: 44px;
          box-sizing: border-box;
          border-right: 1px solid #d1913c;
        }

        .title {
          width: 32px;
          text-align: center;
        }

        .rate {
          width: 53px;
          text-align: center;
        }

        .inp_box {
          width: 220px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }

      input {
        width: 120px;
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
}
</style>