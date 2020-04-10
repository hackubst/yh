<template>
  <div class="guessConstellation">
    <!-- 选择投注金额 -->
    <div class="choose_money">
      <div class="choose_left">
        <div class="nums_ball">
          <img src="~images/nums/icon_chouma10@2x.png" alt="" @click="chooseChip(10)">
          <img src="~images/nums/icon_chouma100@2x.png" alt="" @click="chooseChip(100)">
          <img src="~images/nums/icon_chouma500@2x.png" alt="" @click="chooseChip(500)">
          <img src="~images/nums/icon_chouma5k@2x.png" alt="" @click="chooseChip(5000)">
          <img src="~images/nums/icon_chouma10k@2x.png" alt="" @click="chooseChip(10000)">
        </div>
        <div class="nums_inp">
          <div class="default_amount">
            <p>预设金额</p>
            <input type="number" v-model="rationBet">
          </div>
        </div>
      </div>
      <div class="choose_right">
        <div class="right_inp">
          总量:
          <input type="number" v-model="allBet" disabled>
        </div>
        <div class="sure_btn" @click="sureBet()">
          确认投注
        </div>
        <div class="last_btn" @click="lastBet()">
          上次投注
        </div>
        <div class="last_btn" @click="clearChoose()">
          清除
        </div>
      </div>
    </div>
    <ul class="bet_list" v-loading='loading'>
      <li class="list_info" v-for="(item, index) in modeNumsMore" :key="index">
        <div class="title">{{getTitle(index)}}</div>
        <ul class="bet_detail">
          <li class="detail_info" v-for="i in item" :key="i.key">
            <p>{{i.name}}</p>
            <span>{{i.rate}}</span>
            <input type="number" v-model="i.bet" @blur="inputFocus(i)" @change="inputChange(i)">
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
<script>
  import {
    DEFAULT_GAME_BET, ALERT_TIME
  } from '@/config/config.js'
  import {
    mapGetters,
    mapMutations,
    mapActions
  } from 'vuex'
  import {
    betMixin
  } from '@/config/mixin.js'
  export default {
    name: "guessConstellation",
    computed: {
      ...mapGetters([
        'guessingGame',
        'choosedGame'
      ])
    },
    data() {
      return {
        rationBet: '', // 定额梭哈的豆子总数
        betArr: ['大小', '大小极值', '豹对', '五行', '四季', '星座', '生肖', '球1:球3', '球2:球3', '前二合并', '后二合并'],
        loading: false
      };
    },
    methods: {
      getTitle: function (index) {
        return this.betArr[index]
      },
      // 选择定额梭哈的倍数
      chooseChip: function (times) {
        this.rationBet = DEFAULT_GAME_BET * times
      },
      // 确认投注
      sureBet: function () {
        if (!this.allBet) return
        let bet_json = this.getBetJSON(this.choosedGame.game_type_id)
        this.loading = true
        this.$Api({
          api_name: 'kkl.game.gameBet',
          bet_json: bet_json,
          game_result_id: this.guessingGame.game_result_id,
          total_bet_money: parseInt(this.allBet),
          game_type_id: this.choosedGame.game_type_id
        }, (err, data) => {
          if (!err) {
            this.loading = false
            this.refreshUserInfo()
            this.$msg('投注成功', 'success', ALERT_TIME)
            this.$emit('betSucceed')
          } else {
            this.$msg(err.error_msg, 'error', ALERT_TIME)
          }
        })
      },
      ...mapActions([
        'refreshUserInfo'
      ])
    },
    mixins: [betMixin]
  }
</script>
<style scoped lang='less'>
  .guessConstellation {
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
          border: 1px solid #D1913C;

          .default_amount {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            width: 170px;

            p {
              width: 70px;
              height: 45px;
              .sc(16px, #D1913C);
              background-color: #FFF5C7;
              text-align: center;
              line-height: 45px;
            }

            input {
              width: 100px;
              height: 45px;
              background: #fff;
              color: #FB3A3A;
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
          border: 1px solid #D1913C;
          padding-left: 10px;
          box-sizing: border-box;
          background: #FFEFD4;
          display: flex;
          align-items: center;
          margin-right: 5px;
          .sc(16px, #D1913C);

          input {
            width: 100px;
            .sc(18px, #FB3A3A);
          }
        }

        .sure_btn {
          .commonBtn(45px, 84px);
          font-size: 16px;
          margin-right: 5px;
        }

        .last_btn {
          .sc(16px, #D1913C);
          width: 84px;
          height: 45px;
          border: 1px solid #D1913C;
          text-align: center;
          line-height: 45px;
          border-radius: 4px;
          background: #FFEFD4;
          margin-right: 5px;
          &:hover{
            cursor: pointer;
          }
        }
      }
    }

    .bet_list {
      .wh(100%, auto);

      .list_info {
        width: 100%;
        border-bottom: 1px solid #D1913C;

        .title {
          width: 100%;
          text-align: center;
          .sc(18px, #4A4130);
          margin: 10px 0;
        }

        .bet_detail {
          display: flex;
          justify-content: flex-start;
          align-items: center;
          flex-wrap: wrap;

          .detail_info {
            display: flex;
            align-items: center;
            justify-content: space-around;
            border: 1px solid #ccc;
            border-radius: 8px;
            height: 42px;
            padding: 0 10px;
            background-color: #ffc1070a;
            margin-bottom: 10px;
            margin-left: 10px;

            p {
              .sc(20px, #FB3A3A);
              margin-right: 10px;
            }

            span {
              .sc(16px, #4A4130);
              margin-right: 10px;
            }

            input {
              height: 28px;
              background-color: #fff;
              .wh(80px, 30px);
              border-radius: 20px;
              border: 1px solid #ccc;
              padding-right: 15px;
              text-align: right;
            }
          }
        }
      }
    }
  }
</style>