<template>
  <div class="gameHome">
    <div class="top">
      <div class="top-user">
        <div class="avatar">
          <img
            v-if="userInfo.headimgurl"
            :src="userInfo.headimgurl"
            alt
          />
          <img v-else src="~images/icon/default-avatar.jpeg" alt="">
        </div>
        <div class="text">
          <h2 class="name">{{userInfo.nickname}}</h2>
          <div class="info">
            <div class="num">
              <img src="~images/icon/icon_lightbean@2x.png" alt />
              <span class="tit">乐豆</span>
              <span class="val">{{userInfo.left_money | changeBigNum}}</span>
            </div>
            <div class="num num2">
              <img src="~images/icon/icon_bank_white@2x.png" alt />
              <span class="tit">银行</span>
              <span class="val">{{userInfo.frozen_money | changeBigNum}}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="profile">
        <b>今日</b> 盈亏
        <span>{{bet_log_info.win_loss | changeBigNum}}</span> 参与
        <span>{{bet_log_info.total_issue}}</span> 胜率
        <span>{{bet_log_info.rate}}%</span>
      </div>
    </div>
    <div class="count_down flex" v-if="newestItem">
      <span class="left">
        第
        <span style="color: #FF1E1E;">{{newestItem.issue}}</span>期
      </span>
      <span v-if="newestState == 0">
        <span class="normal" v-if="seconds <= 0">停止下注，</span>
        还有
        <span v-if="seconds > 0">{{seconds}}</span>
        <span v-else-if="otherSeconds > 0">{{otherSeconds}}</span>
        <span v-if="seconds > 0" class="normal">秒停止下注!</span>
        <span v-else-if="otherSeconds > 0" class="normal">秒开奖!</span>
        <!-- 还有
        <span v-if="seconds > 0" style="color: #FF1E1E;">{{seconds}}</span>
        <span v-else style="color: #FF1E1E;">0</span>
        <span v-if="seconds > 50" class="normal">秒停止下注!</span>
        <span v-else class="normal">秒开奖!</span>-->
      </span>
      <span v-else>
        <span style="color: #FF1E1E;">开奖中</span>
      </span>
    </div>
    <div class="count_down flex-center" v-else>已开奖</div>
    <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
      <award-table-one v-if="tableType == 6 && gameResultList.length > 0"></award-table-one>
      <award-table-two v-if="tableType == 3 && gameResultList.length > 0"></award-table-two>
    </mescroll-vue>
  </div>
</template>
<script>
import { countDown } from "@/config/mixin.js";
import { gameMixns } from "@/config/gameMixin.js";
import awardTableOne from "@/components/game/awardTable/awardTableOne";
import awardTableTwo from "@/components/game/awardTable/awardTableTwo";
import MescrollVue from "mescroll.js/mescroll.vue";
import {mapMutations} from "vuex";
export default {
  name: "gameHome",
  data () {
    return {
      mescroll: null,
      bet_log_info: {},
      mescrollDown: {}, //下拉刷新的配置.
      mescrollUp: {
        callback: this.upCallback,
        page: {
          num: 0,
          size: 20
        },
        htmlNodata: '<p class="upwarp-nodata">-- END --</p>'
      },
      userInfo: {}
    };
  },
  mixins: [countDown, gameMixns],
  props: {
    tableType: {
      type: Number,
      default: 0
    }
  },
  methods: {
    ...mapMutations({
      setAwardResult: 'SET_AWAED_RESULT',
    }),
    // 获取游戏开奖结果
    getAwardResult: function () {
      this.$Api({
        api_name: 'kkl.game.nowResult',
        game_type_id: this.$route.query.game_type_id
      }, (err, data) => {
        if (!err) {
          this.bet_log_info = data.data.bet_log_info;
          this.setAwardResult(data.data);
        }
      })
    },
    // 获取用户信息
    get_user_info () {
      this.$Api({
        api_name: 'kkl.user.getUserHomeInfo'
      }, (err, data) => {
        console.log(data)
        let res = data.data
        this.userInfo = res
        // this.user_list[0].number = res.left_money
        // this.user_list[1].number = res.frozen_money
        // this.user_list[2].number = res.exp
        // this.user_list[3].number = res.account_info.recharge
        // this.user_list[4].number = res.account_info.daily_win
        // this.user_list[5].number = res.account_info.daily_flow
        // this.ip_address = res.login_log_info.ip
        // this.login_time = res.login_log_info.login_time
        // this.username = '你好, ' + res.nickname
        // this.email = res.email
        // this.mobile = res.mobile
      })
    },
    // 获取开奖列表 并存入vuex中
    getAwardList (firstRow, pageSize) {
      return new Promise((resolve, reject) => {
        this.$Api(
          {
            api_name: "kkl.game.getResultLogList",
            firstRow: firstRow,
            pageSize: pageSize,
            game_type_id: this.$route.query.game_type_id
          },
          (err, data) => {
            if (!err) {
              this.listTotal = parseInt(data.data.total);
              resolve(data.data.game_result_list);
            }
          }
        );
      });
    },
    // 上拉加载
    upCallback (e) {
      console.log({e});
      this.getAwardList((e.num - 1) * e.size, e.size).then(res => {
        let list = [];
        if (e.num == 1) {
          list = res || [];
          this.filterNewstItem(list);
        } else {
          list = [...this.gameResultList, ...res];
        }
        this.setResultList(list);
        this.$nextTick(() => {
          this.mescroll.endSuccess(list.length);
          this.mescroll.endBySize(list.length, this.listTotal);
        });
      });
    },
    mescrollInit (mescroll) {
      this.mescroll = mescroll;
    }
  },
  created () {
    this.setResultList([]);
    this.getAwardResult()
    this.get_user_info()
  },
  components: {
    awardTableOne,
    awardTableTwo,
    MescrollVue
  }
};
</script>
<style scoped lang='less'>
.gameHome {
  flex: 1;
  background: #fff;
  .top {
    width: 100%;
    height: 112px;
    padding: 9px 12px;
    box-sizing: border-box;
    .top-user {
      width: 100%;
      box-sizing: border-box;
      height: 112px;
      padding: 24px 12px;
      background: #fff7ec;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 9px;
      .avatar {
        width: 64px;
        height: 64px;
        border: 2px solid rgba(74, 65, 48, 1);
        border-radius: 50%;
        overflow: hidden;
        margin-right: 12px;
        flex-shrink: 0;
        img {
          width: 64px;
          height: 64px;
          border-radius: 50%;
        }
      }
      .text {
        .name {
          font-size: 16px;
          font-family: PingFangSC-Medium, PingFangSC;
          font-weight: 500;
          color: rgba(74, 65, 48, 1);
          margin-bottom: 11px;
          margin-left: 3px;
        }
        .info {
          display: flex;
          align-items: center;
          justify-content: center;
          .num {
            display: flex;
            align-items: center;
            justify-content: center;
            img {
              width: 24px;
              height: 24px;
              margin-right: 4px;
            }
            .tit {
              font-size: 14px;
              font-family: PingFangSC-Regular, PingFangSC;
              font-weight: 400;
              color: rgba(74, 65, 48, 1);
              margin-right: 8px;
            }
            .val {
              font-size: 14px;
              font-family: PingFangSC-Medium, PingFangSC;
              font-weight: 500;
              color: rgba(255, 30, 30, 1);
            }
          }
          .num2 {
            margin-left: 44px;
          }
        }
      }
    }
    .profile {
      font-size: 14px;
      font-family: PingFangSC-Regular, PingFangSC;
      font-weight: 400;
      color: rgba(102, 102, 102, 1);
      b {
        font-size: 14px;
        font-family: PingFangSC-Semibold, PingFangSC;
        font-weight: 600;
        color: rgba(51, 51, 51, 1);
        margin-right: 8px;
      }
      span {
        color: #ff1e1e;
        margin-right: 8px;
      }
    }
  }
  .mescroll {
    position: fixed;
    top: 275px;
    bottom: 0;
    height: auto;
  }

  .count_down {
    height: 36px;
    // justify-content: center;
    position: fixed;
    padding: 0 0 0 12px;
    top: 240px;
    width: 100%;
    z-index: 999;

    span {
      .sc(14px, #333333);
    }

    .left {
      margin-right: 7px;
    }
  }
}
</style>