<template>
  <div class="login-wrap">
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_phonenumber@3x.png" alt />
      </div>
      <input type="number" placeholder="请输入您的手机号" v-model="mobile" />
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_verify@3x.png" alt />
      </div>
      <input type="text" placeholder="请输入图形验证码" v-model="code" />
      <p class="code_img" @click="change_code_img()">
        <img :src="img_code" alt />
      </p>
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_note@3x.png" alt />
      </div>
      <input type="text" placeholder="请输入短信验证码" v-model="verify_code" />
      <p class="send_code" v-if="!sendAuthCode" @click="get_verify()">获取验证码</p>
      <p class="send_code" v-if="sendAuthCode">{{auth_time}}s</p>
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_password@3x.png" alt />
      </div>
      <input type="password" placeholder="请输入您的密码" v-model="pwd" />
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_nickname@2x.png" alt />
      </div>
      <input type="text" placeholder="请输入推荐人ID(选填)" v-model="referee" />
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_nickname@2x.png" alt />
      </div>
      <input type="text" placeholder="起一个响亮的昵称" v-model="realname" />
    </div>
    <div class="login-btn" @click="register()">注册</div>
    <div class="foot">
      <!-- <p @click="register()">免费注册</p> -->
      <!-- <p @click="getPwd()">忘记密码？</p> -->
    </div>
  </div>
</template>
<script>
import {
  ALERT_TIME
} from '@/config/config'
import {
  formCheck
} from '@/config/checkout'
export default {
  name: "login",
  data () {
    return {
      mobile: '', // 手机号
      pwd: '', // 密码
      realname: '', // 昵称
      referee: '', // 推荐人
      verify_code: '', // 短信验证码
      code: '', // 图形验证码
      sendAuthCode: false,
      auth_time: 0,
      img_code: '', // 图形验证码
    };
  },
  created () {
    this.get_img_code()
  },
  methods: {
    // 获取图形验证码
    get_img_code () {
      this.$Api({
        api_name: 'kkl.user.getImgCode',
      }, (err, data) => {
        if (!err) {
          this.img_code = data.data.url
        } else {
          this.$msg(err.error_msg, 'error', 1500)
        }
      })
    },
    // 更换图形验证码
    change_code_img () {
      this.img_code = ''
      this.get_img_code()
    },
    // 发送验证码
    get_verify () {
      let checkMobile = [{
        reg: 'mobile',
        val: this.mobile,
        errMsg: '请输入正确的手机号'
      }, {
        reg: 'noData',
        val: this.code,
        errMsg: '校验码不能为空'
      }]
      if (formCheck(checkMobile).result) {
        this.$Api({
          api_name: 'kkl.user.sendVerifyCode',
          mobile: this.mobile
        }, (err, data) => {
          if (!err) {
            console.log(data)
            this.$msg(data.data, 'success', 'middle', 1500)
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
            this.img_code = ''
            this.get_img_code()
            this.$msg(err.error_msg, 'cancel', 'middle', 1500)
          }
        })
      } else {
        this.$msg(formCheck(checkMobile), 'cancel', 'middle', 1500)
      }
    },
    // 注册
    register () {
      let checkItem = [{
        reg: 'mobile',
        val: this.mobile,
        errMsg: '请输入正确的手机号'
      }, {
        reg: 'pwd',
        val: this.pwd,
        errMsg: '密码请输入6-20位字母和数字'
      }, {
        reg: 'noData',
        val: this.realname,
        errMsg: '昵称不能为空'
      }, {
        reg: 'noData',
        val: this.verify_code,
        errMsg: '短信验证码不能为空'
      }, {
        reg: 'noData',
        val: this.code,
        errMsg: '图形验证码不能为空'
      }]
      if (formCheck(checkItem).result) {
        this.$Api({
          api_name: 'kkl.user.signup',
          mobile: this.mobile,
          verify_code: this.verify_code,
          code: this.code,
          password: this.$MD5.hex_md5(this.pwd),
          again_password: this.$MD5.hex_md5(this.pwd),
          parent_id: this.referee,
          nickname: this.realname
        }, (err, data) => {
          console.log(data, err)
          if (!err) {
            this.$msg('注册成功', 'success', 'middle', 1500)
            setTimeout(() => {
              this.$router.replace({
                path: '/login'
              })
            }, 1500);
            // this.$message({
            //   message: '注册成功',
            //   type: 'success',
            //   duration: ALERT_TIME,
            //   onClose: () => {
            //     this.$router.replace({
            //       path: '/login'
            //     })
            //   }
            // });
          } else {
            if (data.code === 40051) {
              this.img_code = ''
              this.get_img_code()
            }
            this.$msg(err.error_msg, 'cancel', 'middle', 1500)
          }
        })
      } else {
        this.$msg(formCheck(checkItem), 'cancel', 'middle', 1500)
      }
    },
    getPwd () {
      this.$router.push({
        path: '/login'
      })
    },
  }
}
</script>
<style scoped lang='less'>
.login-wrap {
  padding: 0 12px;
}
.login-wrap * {
  box-sizing: border-box;
}
.item {
  width: 100%;
  height: 48px;
  background: #f2f2f2;
  border-radius: 4px;
  margin-top: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 12px;
  position: relative;
  .icon {
    width: 24px;
    margin-right: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    img {
      max-width: 24px;
    }
  }
  input {
    width: 100%;
    flex: 1;
    height: 100%;
    background: none;
    border: 0;
    font-size: 16px;
    font-family: PingFangSC-Regular, PingFangSC;
    font-weight: 400;
    color: rgba(153, 153, 153, 1);
  }
  .code_img {
    width: 96px;
    height: 44px;
    position: absolute;
    right: 2px;
    top: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3fbfe;
    img {
      width: 100%;
      height: auto;
    }
  }
  .send_code {
    font-size: 16px;
    font-family: PingFangSC-Medium, PingFangSC;
    font-weight: 500;
    color: rgba(74, 65, 48, 1);
  }
}
.login-btn {
  width: 100%;
  height: 48px;
  background: linear-gradient(
    180deg,
    rgba(255, 209, 148, 1) 0%,
    rgba(209, 145, 60, 1) 100%
  );
  border-radius: 4px;
  font-size: 18px;
  font-family: PingFangSC-Regular, PingFangSC;
  font-weight: 400;
  color: rgba(255, 255, 255, 1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 24px;
}
.foot {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 12px;
  p {
    font-size: 16px;
    font-family: PingFangSC-Medium, PingFangSC;
    font-weight: 500;
    color: rgba(74, 65, 48, 1);
  }
}
</style>