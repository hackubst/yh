<template>
  <div class="betModelNum">
    <div class="mode_title" v-if="judgeEdit(choosedGame.game_type_id)">
      <span>
        我的投注模式：
        <span style="color: #D1913C;" class="pointer"></span>
        <el-select v-model="currentMode" placeholder="选择我的投注模式" @change="changeMode">
          <el-option
            v-for="item in haveModesItem"
            :key="item.bet_mode_id"
            :label="item.mode_name"
            :value="item.bet_mode_id">
          </el-option>
        </el-select>
        <router-link
          tag="span"
          :to="{path: '/gameIndex/editMode'}"
          class="title_item"
          replace
        >模式编辑</router-link>
      </span>
    </div>
    <div class="mode_types" v-if="choosedGame.game_type_id != 58 || choosedGame.game_type_id != 59 || choosedGame.game_type_id != 60 || choosedGame.game_type_id != 61">
      <div
        class="type_item"
        v-for="(item, index) in chooseTypes"
        :key="index"
        @click="item.typeFnType == 1 ? filterDivisor(item.divisor, item.remainder, index): filterSize(item.size, item.sizeType, index)"
        :class="ModeTypeIndex == index ? 'active':''"
      >{{item.typeName}}</div>
    </div>
    <div class="mode_operate">
      <div class="mode_multiply">
        <div
          class="multiply_item"
          v-for="(item, index) in mode_multiply"
          :key="index"
          @click="allDouble(item)"
        >
          <span>{{item}}倍</span>
        </div>
      </div>
      <div class="operate_btn">
        <div class="btn_item" @click="betMyAll()">梭哈</div>
        <div class="btn_item" @click="allChoose()">全包</div>
        <div class="btn_item" @click="reverseChoose()">反选</div>
        <div class="btn_item" @click="clearChoose()">清除</div>
      </div>
    </div>
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
          <input type="number" v-model="rationBet" />
          <div class="nums_inp_btn" @click="rationBetFn()">定额梭哈</div>
        </div>
      </div>
      <div class="choose_right">
        <div class="right_inp">
          总量:
          <input type="number" v-model="allBet" disabled />
        </div>
        <div class="sure_btn" @click="sureBet()">确认投注</div>
        <div class="last_btn" @click="lastBet()">上次投注</div>
      </div>
    </div>
    <!--  -->
    <template v-if="choosedGame.game_type_id == 58 || choosedGame.game_type_id == 59 || choosedGame.game_type_id == 60 || choosedGame.game_type_id == 61">
      <edit-tableb
      :modeHalf="modeHalf"
      :modeTable="modeNumsMore[0]"
      :tableType="tableType"
      @countDefault="countDefault"
      v-loading="loading"
    ></edit-tableb>
    </template>
    <template v-else>
      <edit-tablea
      :modeHalf="modeHalf"
      :modeTable="modeNumsMore[0]"
      :tableType="tableType"
      @countDefault="countDefault"
      v-loading="loading"
    ></edit-tablea>
    </template>
  </div>
</template>
<script>
import { CHOOSE_TYPES, DEFAULT_GAME_BET, ALERT_TIME } from "@/config/config.js";
import { mapGetters, mapMutations, mapActions } from "vuex";
import { betMixin, judgeMixin } from "@/config/mixin.js";
import editTablea from "@/components/games/editTable/editTablea.vue";
import editTableb from "@/components/games/editTable/editTableb.vue";
export default {
  name: "betModelNum",
  components: {
    editTablea,
    editTableb
  },
  computed: {
    ...mapGetters(["guessingGame", "choosedGame"])
  },
  data() {
    return {
      chooseTypes: CHOOSE_TYPES,
      tableType: 1,
      rationBet: "", // 定额梭哈的豆子总数
      loading: false,
      haveModesItem: [],
      currentMode: ''
    };
  },
  async created () {
    await this.get_model_list();
  },
  methods: {
    // 定额梭哈
    rationBetFn: function() {
      if (!this.rationBet) return;
      this.betMyAll(parseInt(this.rationBet));
    },
    // 选择定额梭哈的倍数
    chooseChip: function(times) {
      console.log(times)
      this.rationBet = DEFAULT_GAME_BET * times;
    },
    // 确认投注
    sureBet: function() {
      if (!parseInt(this.allBet)) return;
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
    // 获取上期赔率，当前投注
    getLastBet: function() {
      this.$Api(
        {
          api_name: "kkl.game.getLastBetInfo",
          game_result_id: this.guessingGame.game_result_id,
          game_type_id: this.choosedGame.game_type_id
        },
        (err, data) => {
          if (!err) {
            let last_bet_rate = JSON.parse(data.data.last_bet_rate);
            let have_bet = data.data.have_bet
              ? JSON.parse(data.data.have_bet)
              : [];
            let have_bet_arr = have_bet.length == 0 ? [] : have_bet[0].bet_json;
            this.getLastBetInfo(last_bet_rate[0].bet_json, have_bet_arr);
          }
        }
      );
    },
    getLastBetInfo: function(arr, have_bet_arr) {
      let modeNums = this.modeNumsMore[0];
      for (let i = 0; i < modeNums.length; i++) {
        let arrIndex = arr.findIndex(item => item.key == modeNums[i].key);
        if (arrIndex > -1) {
          modeNums[i].last_bet_rate = parseFloat(arr[arrIndex].rate).toFixed(2);
        }
        let have_bet_index = have_bet_arr.findIndex(
          item => item.key == modeNums[i].key
        );
        if (have_bet_index > -1) {
          modeNums[i].have_bet = parseFloat(have_bet_arr[have_bet_index].money);
        } else {
          modeNums[i].have_bet = 0;
        }
      }
    },
    // 获取模式列表
    get_model_list() {
      this.$Api({
          api_name: 'kkl.game.BetModeList',
          game_type_id: this.choosedGame.game_type_id
      }, (err, data) => {
          if (!err) {
              this.haveModesItem = data.data.bet_mode_list
              // if (this.haveModesItem.length != 0) {
              //   this.modeName = this.haveModesItem[this.chooseModeIndex].mode_name
              //   this.bet_mode_id = this.haveModesItem[this.chooseModeIndex].bet_mode_id
              //   this.allBet = this.haveModesItem[this.chooseModeIndex].total_money
              //   let lastBetObj = JSON.parse(this.haveModesItem[this.chooseModeIndex].bet_json)
              //   this.renderLastBet(lastBetObj)
              // }
          } else {
              this.$msg(err.error_msg, 'error', 1500)
          }
      })
    },
    // change mode 
    changeMode(value) {
      console.log({value});
      const item = this.haveModesItem.find(v => v.bet_mode_id == value);
      this.choose_model(item.mode_name, item.bet_mode_id, item.total_money, item.bet_json);
    },
    // choosee mode
    choose_model(mode_name, id, num, bet_json) {
      console.log(1111111);
      this.clearChoose()
      this.modeName = mode_name
      this.bet_mode_id = id
      this.allBet = num
      let lastBetObj = JSON.parse(bet_json)
      this.renderLastBet(lastBetObj)
    },
    ...mapActions(["refreshUserInfo"])
  },
  mixins: [betMixin, judgeMixin]
};
</script>
<style scoped lang='less'>
.betModelNum {
  margin-bottom: 50px;

  .mode_title {
    display: flex;
    align-items: center;
    height: 22px;
    padding-left: 10px;
    box-sizing: border-box;
    position: relative;
    line-height: 22px;
    color: #4a4130;
    font-size: 16px;
    margin-bottom: 20px;

    &:before {
      content: "";
      width: 4px;
      height: 22px;
      background: #d1913c;
      left: 0;
      top: 0;
      position: absolute;
    }
  }

  .mode_types {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    .type_item {
      width: 48px;
      height: 30px;
      line-height: 30px;
      text-align: center;
      background: #ffefd4;
      color: #d1913c;
      margin-right: 2px;
      margin-bottom: 2px;
      font-size: 16px;
    }

    .active {
      background: #d1913c;
      color: #fff;
    }
  }

  .mode_operate {
    display: flex;
    justify-content: space-between;
    height: 42px;
    margin-bottom: 20px;

    .mode_multiply {
      width: 498px;
      height: 42px;
      display: flex;
      background: #d1913c;
      border-radius: 4px;
      padding: 10px;
      box-sizing: border-box;
      align-items: center;

      .multiply_item {
        flex-grow: 1;
        font-size: 16px;
        color: #fff8ef;
        position: relative;
        text-align: center;

        &:after {
          content: "";
          width: 1px;
          height: 12px;
          background: #fff8ef;
          position: absolute;
          right: 0;
          top: 50%;
          margin-top: -6px;
        }
      }

      .multiply_item:last-child {
        &:after {
          content: "";
          width: 0px;
          height: 0px;
        }
      }
    }

    .operate_btn {
      display: flex;
      justify-content: space-between;

      .btn_item {
        width: 42px;
        height: 42px;
        border-radius: 4px;
        border: 1px solid rgba(209, 145, 60, 1);
        margin-left: 5px;
        background: rgba(255, 239, 212, 1);
        text-align: center;
        line-height: 42px;
        color: #d1913c;
        font-size: 16px;
        box-sizing: border-box;
      }
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

        input {
          width: 118px;
          height: 45px;
          background: #ffefd4;
          color: #fb3a3a;
          padding-left: 10px;
          box-sizing: border-box;
          border: 1px solid #d1913c;
          margin-right: 5px;
          font-size: 20px;
        }

        .nums_inp_btn {
          .commonBtn(45px, 84px);
          font-size: 16px;
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
        &:hover {
          cursor: pointer;
        }
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
        &:hover {
          cursor: pointer;
        }
      }
    }
  }
}
</style>