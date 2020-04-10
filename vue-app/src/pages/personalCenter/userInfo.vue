<template>
  <div class="user-info">
    <div class="item">
      <span class="key">VIP等级：</span>
      <span class="val">{{info.level_name}}</span>
    </div>
    <div class="item">
      <span class="key">当前乐豆：</span>
      <span class="val">{{info.left_money | changeBigNum}}</span>
    </div>
    <div class="item">
      <span class="key">银行乐豆：</span>
      <span class="val">{{info.frozen_money | changeBigNum}}</span>
    </div>
    <div class="item">
      <span class="key">今天充值：</span>
      <span class="val">{{info.account_info.recharge | changeBigNum}}</span>
    </div>
    <div class="item">
      <span class="key">今日流水：</span>
      <span class="val">{{info.account_info.daily_flow | changeBigNum}}</span>
    </div>
    <div class="item">
      <span class="key">今日盈亏：</span>
      <span class="val">{{info.account_info.daily_win | changeBigNum}}</span>
    </div>
    <div class="item">
      <span class="key">最后登录：</span>
      <span class="val">{{info.login_log_info.login_time_str}}</span>
    </div>
    <div class="item">
      <span class="key">最后登录IP：</span>
      <span class="val">{{info.login_log_info.ip}}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: "userInfo",
  data () {
    return {
      info: {}
    }
  },
  methods: {
    getInfo () {
      this.$Api(
        {
          api_name: "kkl.user.getUserHomeInfo"
        },
        (err, data) => {
          if (!err) {
            this.info = data.data
          }
        }
      );
    }
  },
  mounted () {
    this.getInfo()
  }
}
</script>> 

<style lang="less" scoped>
.user-info {
  padding: 12px 0;
  .item {
    display: flex;
    align-items: center;
    justify-content: center;
    border-bottom: 1px solid #fed093;
    height: 50px;
    padding: 0 12px;
    box-sizing: border-box;
    span {
      font-size: 16px;
      font-family: PingFangSC-Medium, PingFangSC;
      font-weight: 500;
      color: rgba(74, 65, 48, 1);
    }
    .key {
      color: #444444;
    }
    .val {
      flex: 1;
    }
  }
}
</style>