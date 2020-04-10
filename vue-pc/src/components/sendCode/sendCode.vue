<template>
  <div class="sendCode">
    <div class="code_box">
      <p class="title">短信验证码</p>
      <div class="code_inp">
        <input type="text" placeholder="请输入验证码" v-model="code">
        <div class="send_btn pointer" @click="sendCode()" v-if="count == 60">发送验证码</div>
        <div class="send_btn" v-else>{{count}}s</div>
      </div>
      <div class="login_btn pointer" @click="checkLogin()">登录</div>
    </div>
  </div>
</template>
<script>
import { setInterval, clearInterval } from "timers";
import { mapMutations } from "vuex";
export default {
  name: "sendCode",
  data() {
    return {
      count: 60,
      code: ""
    };
  },
  watch: {
    count(e) {
      if (e == 0) {
        clearInterval(this.timer);
        this.count = 60;
      }
    }
  },
  methods: {
    sendCode() {
      this.$Api(
        {
          api_name: "kkl.user.sendLoginCode"
        },
        (err, data) => {
          if (!err) {
            this.$msg("发送成功", "success", 1500);
            this.timer = setInterval(() => {
              this.count--;
            }, 1000);
          }
        }
      );
    },
    checkLogin() {
      if (!this.code) {
        this.$msg("请输入验证码", "error", 1500);
        return;
      }
      this.$Api(
        {
          api_name: "kkl.user.checkVerifyCode",
          verify_code: this.code
        },
        (err, data) => {
          if (!err) {
            this.setLoginCheck(false);
            this.setCheckState(true);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    ...mapMutations({
      setLoginCheck: "SET_LOGIN_CHECK",
      setCheckState: 'SET_CHECK_STATE'
    })
  }
};
</script>
<style scoped lang='less'>
.sendCode {
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  position: fixed;
  top: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  .code_box {
    width: 460px;
    height: 265px;
    background: linear-gradient(
      360deg,
      rgba(209, 145, 60, 1) 0%,
      rgba(255, 209, 148, 1) 100%
    );
    border-radius: 8px;
    padding: 30px 50px;
    box-sizing: border-box;
    .title {
      text-align: center;
      margin-bottom: 30px;
      .sc(18px, #fff);
    }
    .code_inp {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #fff;
      justify-content: space-between;
      height: 54px;
      margin-bottom: 20px;
      input {
        border: none;
        height: 54px;
      }
      .send_btn {
        width: 70px;
        height: 54px;
        .sc(14px, #fff8ef);
        line-height: 54px;
        text-align: center;
      }
    }
    .login_btn {
      width: 360px;
      height: 56px;
      line-height: 56px;
      text-align: center;
      justify-content: center;
      .sc(18px, #d1913c);
      background: #fff8ef;
      border-radius: 8px;
    }
  }
}
</style>