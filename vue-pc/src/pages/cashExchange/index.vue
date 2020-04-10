<template>
  <div id="cashExchange">
    <!-- 面包屑 -->
    <div class="nav">
      <span @click="go_back()">兑换中心</span>
      <img src="../../assets/images/icon/icon_right59@2x.png" alt />
      <span>金龙体验卡{{money}}元</span>
    </div>
    <!-- 点卡详情 -->
    <div class="card_info">
      <div class="list_info">
        <img class="bg_img" src="../../assets/images/bg/bg_kami@2x.png" alt />
        <p class="top_title">卡密</p>
        <p class="number">
          <span>￥</span>
          {{money}}
        </p>
        <p class="info">金龙体验卡{{money}}元</p>
        <p class="explain">(七天未回收，此卡作废)</p>
      </div>
      <div class="table_info">
        <div class="title">
          <p>金龙体验卡{{money}}元</p>
          <span>注：幸运系列游戏不算流水</span>
        </div>
        <div class="tip">{{is_suit == 1 ? '流水已达到 提现无限制' : '流水未达到 提现收取2%手续费'}}</div>
        <ul class="cash_number">
          <li class="list" v-for="(item, index) in list" :key="index">
            <p class="left_label">{{item.title}}：</p>
            <p class="right_label">{{item.number | changeBigNum}}</p>
          </li>
        </ul>
        <ul class="form_list">
          <li class="list_infp">
            <div class="left_label">兑换数量</div>
            <input type="text" :class="{blank: count!=''}" v-model="count" />
          </li>
          <li class="list_infp">
            <div class="left_label">安全密码</div>
            <input
              class="circle"
              type="password"
              autocomplete="new-password"
              :class="{blank: pwd!=''}"
              v-model="pwd"
            />
          </li>
        </ul>
        <div class="verify_code">
          <p class="left_label">短信验证码</p>
          <div class="right_info">
            <input type="text" :class="{blank: code!=''}" v-model="code" />
            <div class="btn" v-if="!sendAuthCode" @click="get_verify()">获取验证码</div>
            <div class="btn" v-if="sendAuthCode">{{auth_time}}s后重新获取</div>
          </div>
        </div>
        <div class="exchange" @click="exchange_now()">立即兑换</div>
      </div>
    </div>
    <!-- 等级价格 -->
    <div class="card_info card_level">
      <h2 class="title">等级价格</h2>
      <div class="tab">
        <div class="name">VIP等级</div>
        <div class="line"></div>
        <div class="name">兑换价格</div>
      </div>
      <div class="table">
        <div class="box" v-for="item of level_list" :key="item.level_name">
          <div class="item val">{{item.level_name}}</div>
          <div class="item">{{item.exchange_money | changeBigNum}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import { formCheck } from '@/config/checkout'
export default {
  name: "cashExchange",
  components: {

  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'userInfo'
    ])
  },
  data () {
    return {
      money: '',
      list: [{
        title: '七天内流水',
        number: ''
      }, {
        title: '七天内已提',
        number: ''
      }, {
        title: '七天内充值',
        number: ''
      }, {
        title: '兑换价格',
        number: ''
      },],
      level_list: [],
      count: '', // 兑换数量
      pwd: '', // 密码
      code: '', // 验证码
      sendAuthCode: false,
      auth_time: 0,
      is_suit: 0, // 1是达到要求 0是未达到
    }
  },
  created () {
    this.$Api({
      api_name: 'kkl.index.giftCardInfo',
      gift_card_id: this.$route.query.id
    }, (err, data) => {
      if (!err) {
        console.log(res)
        let res = data.data.gift_card_info
        
        this.money = res.cash
        this.list[0].number = res.flow
        this.list[1].number = res.deposit
        this.list[2].number = res.recharge
        this.list[3].number = res.money
        this.level_list = data.data.level_list;
        this.is_suit = res.is_suit;
      } else {
        this.$msg(err.error_msg, 'error', 1500)
      }
    })
  },
  methods: {
    // 发送验证码
    get_verify () {
      this.$Api({
        api_name: 'kkl.user.sendVerifyCode',
        mobile: this.userInfo.mobile
      }, (err, data) => {
        if (!err) {
          this.$msg(data.data, 'success', 1500)
          this.sendAuthCode = true
          this.auth_time = 60
          var auth_timetimer = setInterval(() => {
            this.auth_time--;
            if (this.auth_time <= 0) {
              this.sendAuthCode = false
              clearInterval(auth_timetimer);
            }
          }, 1000);
        } else {
          this.$msg(err.error_msg, 'error', 1500)
        }
      })
    },
    // 兑换卡券
    exchange_now () {
      let checkItem = [{
        reg: 'noData',
        val: this.count,
        errMsg: '兑换数量还不能为空'
      }, {
        reg: 'noData',
        val: this.pwd,
        errMsg: '安全密码不能为空'
      }, {
        reg: 'noData',
        val: this.code,
        errMsg: '验证码不能为空'
      }]
      if (formCheck(checkItem).result) {
        this.$Api({
          api_name: 'kkl.index.exChangeCard',
          safe_password: this.$MD5.hex_md5(this.pwd),
          number: this.count,
          verify_code: this.code,
          gift_card_id: this.$route.query.id
        }, (err, data) => {
          if (!err) {
            this.$msg(data.data, 'success', 1500)
            this.pwd = ''
            this.count = ''
            this.code = ''
            this.$Api({
              api_name: 'kkl.user.getUserInfo',
            }, (erra, res) => {
              if (!erra) {
                this.setUser(res.data)
              } else {
                this.$msg(err.error_msg, 'error', 1500)
              }
            })
          } else {
            this.$msg(err.error_msg, 'error', 1500)
          }
        })
      } else {
        this.$msg(formCheck(checkItem), 'error', 1500)
      }
    },
    // 返回上一页
    go_back () {
      this.$router.go(-1);
    },
    ...mapMutations({
      setUser: 'SET_USER',
      delUser: 'DEL_USER'
    })
  }
}
</script>

<style scoped lang='less'>
#cashExchange {
  .wh(920px, auto);
  margin: 0 auto;
  .nav {
    padding: 10px 20px;
    display: inline-block;
    box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
    margin: 30px 0;
    border-radius: 8px;
    span {
      .sc(16px, #d1913c);
    }
    img {
      .wh(5px, 8px);
      margin: 0 5px;
    }
  }
  .card_info {
    .wh(100%, 731px);
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    .list_info {
      .wh(214px, 205px);
      position: relative;
      margin: 30px;
      overflow: hidden;
      .bg_img {
        .wh(100%, 100%);
        position: absolute;
        top: 0;
        z-index: 1;
      }
      .top_title {
        width: 100%;
        .sc(16px, #fff8ef);
        text-align: center;
        line-height: 22px;
        margin-top: 8px;
        margin-bottom: 10px;
        position: relative;
        z-index: 100;
      }
      .number {
        .sc(36px, #fff8ef);
        margin-left: 10px;
        line-height: 50px;
        position: relative;
        z-index: 100;
        span {
          .sc(20px, #fff8ef);
        }
      }
      .info {
        margin-left: 10px;
        .sc(16px, #fff8ef);
        margin-bottom: 43px;
        position: relative;
        z-index: 100;
      }
      .explain {
        margin-left: 10px;
        .sc(12px, #fff8ef);
        position: relative;
        z-index: 100;
      }
    }
  }
  .table_info {
    .wh(615px, auto);
    margin-top: 30px;
    .title {
      display: flex;
      width: 100%;
      justify-content: space-between;
      align-items: flex-end;
      p {
        .sc(24px, #4a4130);
        line-height: 33px;
      }
      span {
        .sc(18px, #ccc);
        line-height: 25px;
      }
    }
    .tip {
      .sc(18px, #FF0000);
      line-height: 25px;
    }
    .cash_number {
      .wh(100%, auto);
      .list {
        .wh(100%, 65px);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: relative;
        border-bottom: 1px solid #e8e8e8;
        .left_label {
          .sc(18px, #4a4130);
        }
        .right_label {
          .sc(18px, #d1913c);
          position: absolute;
          left: 202px;
        }
      }
    }
    .form_list {
      .wh(100%, auto);
      .list_infp {
        .wh(100%, 65px);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        position: relative;
        border-bottom: 1px solid #e8e8e8;
        .left_label {
          .sc(18px, #4a4130);
        }
        input {
          .sc(18px, #4a4130);
          position: absolute;
          left: 202px;
          .wh(322px, 54px);
          border: 1px solid #cccccc;
          border-radius: 4px;
          text-indent: 14px;
          &:focus {
            border: 1px solid rgba(209, 145, 60, 1);
          }
        }
        .circle {
          font-size: 18px;
        }
        .blank {
          border: 1px solid rgba(209, 145, 60, 1);
        }
      }
    }
    .verify_code {
      .wh(100%, 65px);
      display: flex;
      align-items: center;
      justify-content: flex-start;
      position: relative;
      .left_label {
        .sc(18px, #4a4130);
      }
      .right_info {
        position: absolute;
        left: 202px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        input {
          .sc(18px, #4a4130);
          .wh(190px, 54px);
          border: 1px solid #cccccc;
          border-radius: 4px;
          text-indent: 14px;
          margin-right: 10px;
          &:focus {
            border: 1px solid rgba(209, 145, 60, 1);
          }
        }
        .blank {
          border: 1px solid rgba(209, 145, 60, 1);
        }
        div {
          .wh(122px, 54px);
          background: -webkit-linear-gradient(
            #ffd194,
            #d1913c
          ); /* Safari 5.1 - 6.0 */
          background: -o-linear-gradient(
            #ffd194,
            #d1913c
          ); /* Opera 11.1 - 12.0 */
          background: -moz-linear-gradient(
            #ffd194,
            #d1913c
          ); /* Firefox 3.6 - 15 */
          background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
          text-align: center;
          line-height: 54px;
          .sc(16px, #fff8ef);
          border-radius: 4px;
          cursor: pointer;
        }
      }
    }
    .exchange {
      .wh(187px, 56px);
      background: -webkit-linear-gradient(
        #ffd194,
        #d1913c
      ); /* Safari 5.1 - 6.0 */
      background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
      background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
      background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
      text-align: center;
      line-height: 54px;
      .sc(18px, #fff8ef);
      border-radius: 4px;
      margin: 58px auto 0 auto;
      cursor: pointer;
    }
  }
  .card_level {
    box-sizing: border-box;
    margin-bottom: 50px;
    padding: 15px 30px 30px;
    flex-direction: column;
    height: auto;
    .title {
      width: 100%;
      font-size: 24px;
      font-family: PingFangSC;
      font-weight: 600;
      color: rgba(74, 65, 48, 1);
      line-height: 63px;
      text-align: center;
    }
    .tab {
      width: 100%;
      height: 54px;
      border-radius: 8px;
      background: #d59744;
      display: flex;
      align-items: center;
      justify-content: center;
      .line {
        width: 1px;
        height: 14px;
        background: #ffffff;
      }
      .name {
        width: 50%;
        font-size: 18px;
        font-family: PingFangSC;
        font-weight: 600;
        color: rgba(255, 255, 255, 1);
        line-height: 54px;
        text-align: center;
      }
    }
    .table {
      width: 100%;
      background: #e7e7e7;
      border-radius: 8px;
      margin-top: 10px;
      .box {
        width: 100%;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #FFFFFF;
        &:last-child {
          border-bottom: 0;
        }
        .val {
          border-right: 1px solid #FFFFFF;
        }
        .item {
          width: 50%;
          text-align: center;
          font-size: 16px;
          font-family: PingFangSC;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          line-height: 54px;
        }
      }
    }
  }
}
</style>