<template>
  <div class="gameBet">
    <div class="choose_box flex-center" v-if="modeNums.length == 1 && (game_type_id != 58 || game_type_id != 59 || game_type_id != 60 || game_type_id != 61)">
      <div
        :class="['choose_item', { actived:  ModeTypeIndex == index }]"
        v-for="(item, index) in chooseTypes"
        @click="item.typeFnType == 1 ? filterDivisor(item.divisor, item.remainder, index): filterSize(item.size, item.sizeType, index)"
        :key="index"
      >{{item.typeName}}</div>
    </div>
    <div class="choose_box flex-center" v-if="modeNums.length == 1" style="background: #FFF7EC">
      <div
        class="choose_item"
        v-for="(item, index) in multiple"
        :key="index"
        @click="allDouble(item)"
      >{{item}}倍</div>
    </div>
    <div class="bet_box flex-center" v-if="modeNums.length == 1">
      <img src="~images/icon/icon_chip1@2x.png" @click="chooseChip(1)" />
      <img src="~images/icon/icon_chip10@2x.png" @click="chooseChip(10)" />
      <img src="~images/icon/icon_chip100@2x.png" @click="chooseChip(100)" />
      <img src="~images/icon/icon_chip500@2x.png" @click="chooseChip(500)" />
      <img src="~images/icon/icon_chip1k@2x.png" @click="chooseChip(1000)" />
      <img src="~images/icon/icon_chip5k@2x.png" @click="chooseChip(5000)" />
    </div>
    <bet-table-one :list="modeNums[0]" v-if="modeNums.length == 1" @countDefault="countDefault"></bet-table-one>
    <bet-table-two :list="modeNums" :modeNames="modeNames" v-else @countDefault="countDefault"></bet-table-two>
    <div class="bot_box">
      <div class="choose_btn">
        <div class="btn_item" v-if="modeNums.length == 1" @click="betMyAll(myBet)">梭哈</div>
        <div class="btn_item" v-if="modeNums.length == 1" @click="allChoose()">全包</div>
        <div class="btn_item" v-if="modeNums.length == 1" @click="reverseChoose()">反选</div>
        <div class="btn_item">刷新赔率</div>
        <div class="btn_item" @click="clearChoose()">清空</div>
        <div class="btn_item" @click="lastBet()">上次投注</div>
      </div>
      <div class="bet_info">
        <div class="info_left">
          <p>
            <span style="margin-right: 10px;">
              余额
              <span style="color: #FF1E1E;">{{myBet | changeBigNum}}</span>
            </span>
            <span>
              投注额
              <span style="color: #FF1E1E;">{{allBet | changeBigNum}}</span>
            </span>
          </p>
          <p>
            <span style="margin-right: 4px;">
              第
              <span style="color: #FF1E1E;">{{newestItem.issue}}</span>
              期
            </span>
            <span v-if="newestState == 0">
              还有
              <span style="color: #FF1E1E;">{{seconds}}</span>
              秒停止下注！
            </span>
            <span v-else>
              <span style="color: #FF1E1E;">开奖中</span>
            </span>
          </p>
        </div> 
        <div class="info_right" @click="sureBet()">
          <div>投注</div>
        </div>
      </div>
    </div>
    <Confirm v-model="show" :title="title" @on-confirm="confirm()"></Confirm>
  </div>
</template>
<script>
import { CHOOSE_TYPES, DEFAULT_GAME_BET } from "@/config/config.js";
import { mapGetters } from "vuex";
import { gameMixns } from "@/config/gameMixin.js";
import betTableOne from "@/components/game/betTable/betTableOne";
import betTableTwo from "@/components/game/betTable/betTableTwo";
import { Confirm } from 'vux';
export default {
  name: "gameBet",
  data() {
    return {
      game_type_id: this.$route.query.game_type_id,
      chooseTypes: CHOOSE_TYPES,
      multiple: [0.1, 0.5, 0.8, 1.2, 1.5, 2, 5, 10, 50, 100],
      myBet: 0,
      modeNames: [],
      userInfo: {},
      show: false,
      title: ''
    };
  },
  mixins: [gameMixns],
  components: {
    betTableOne,
    betTableTwo,
    Confirm
  },
  computed: {
    ...mapGetters(["newestItem", "seconds", "newestState"])
  },
  methods: {
    // 选择定额梭哈的倍数
    chooseChip: function(times) {
      let rationBet = DEFAULT_GAME_BET * times;
      this.betMyAll(parseInt(rationBet));
    },
    // 确认投注
    sureBet: function() {
      if (!parseInt(this.allBet)) return;
      this.show = true,
      this.title = '您确定投注' + this.allBet + '吗？' 
      
    },
    confirm () {
      let bet_json = this.getBetJSON();
      this.$Api(
        {
          api_name: "kkl.game.gameBet",
          bet_json: bet_json,
          game_result_id: this.$route.query.game_result_id,
          total_bet_money: parseInt(this.allBet),
          game_type_id: this.$route.query.game_type_id
        },
        (err, data) => {
          if (!err) {
            this.$toast({
              text: "投注成功"
            });
            setTimeout(() => {
              this.$router.go(-1);
            }, 1500);
          } else {
            this.$toast({
              text: err.error_msg
            });
          }
        }
      );
    },
    // 获取当前投注和上期赔率
    getLastBet: function() {
      this.$Api(
        {
          api_name: "kkl.game.getLastBetInfo",
          game_result_id: this.$route.query.game_result_id,
          game_type_id: this.$route.query.game_type_id
        },
        (err, data) => {
          if (!err) {
            let last_bet_rate = data.data.last_bet_rate
              ? JSON.parse(data.data.last_bet_rate)
              : "";
            let have_bet = data.data.have_bet
              ? JSON.parse(data.data.have_bet)
              : [];
            let modeNums = this.modeNums;
            if (have_bet.length > 0) {
              for (let i = 0; i < modeNums.length; i++) {
                this.getLastBetInfo(modeNums[i], have_bet[i]);
              }
              // console.log('modeNums', modeNums);
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
          arr[arrIndex].chooseed = false;
          arr[arrIndex].have_bet = have_bet[i].money;
        }
      }
    }
  },
  created() {
    // 获取用户信息
    this.$Api(
        {
          api_name: "kkl.user.getUserInfo"
        },
        (err, data) => {
          this.userInfo = data.data;
          this.myBet = parseInt(data.data.left_money);
        }
      );
    // 获取开奖结果
    this.$Api(
      {
        api_name: "kkl.game.nowResult",
        game_type_id: this.$route.query.game_type_id
      },
      (err, data) => {
        if (!err) {
          let bet_json = data.data.game_type_info.bet_json;
          this.getModeNums(bet_json);
          if (this.modeNums.length > 1) {
            this.modeNames = this.getModeNames(bet_json);
          }
          this.getLastBet();
        }
      }
    );
  }
};
</script>
<style scoped lang='less'>
.gameBet {
  flex: 1;
  padding-bottom: 96px;
  box-sizing: border-box;
  .choose_box {
    padding: 12px 10px;
    padding-bottom: 4px;
    flex-wrap: wrap;
    .choose_item {
      .sc(14px, #4a4130);
      padding: 4px 7px;
      border: 1px solid #979797;
      border-radius: 4px;
      margin-right: 8px;
      margin-bottom: 8px;
      background: #fff;
    }
    .actived {
      .sc(14px, #fff);
      background: #ff851e;
    }
  }

  .bet_box {
    height: 62px;
    img {
      width: 30px;
      height: 30px;
      margin-right: 10px;
    }
  }
  .bot_box {
    height: 96px;
    position: fixed;
    bottom: 0;
    background: #fed093;
    width: 100%;
    .choose_btn {
      display: flex;
      justify-content: space-around;
      padding: 15px 0 9px;
      .btn_item {
        padding: 4px 8px;
        .sc(14px, #fff);
        background: #4a4130;
        border-radius: 4px;
      }
    }

    .bet_info {
      display: flex;
      justify-content: space-between;
      padding: 0 8px;
      box-sizing: border-box;
      .info_left {
        p:nth-child(1) {
          .sc(14px, #4a4130);
        }
        p:nth-child(2) {
          .sc(12px, #4a4130);
        }
      }
      .info_right {
        div {
          width: 88px;
          height: 36px;
          background: #ff851e;
          border-radius: 4px;
          text-align: center;
          line-height: 36px;
          .sc(18px, #fff);
        }
      }
    }
  }
}
</style>