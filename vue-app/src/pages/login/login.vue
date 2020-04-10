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
        <img src="~images/icon/icon_password@3x.png" alt />
      </div>
      <input type="password" placeholder="请输入您的密码" v-model="pwd" />
    </div>
    <div class="item">
      <div class="icon">
        <img src="~images/icon/icon_verify@3x.png" alt />
      </div>
      <input type="text" placeholder="请输入图形验证码" v-model="verify_code" />
      <p class="code_img" @click="change_code_img()">
        <img :src="img_code" alt />
      </p>
    </div>
    <div class="login-btn" @click="login()">登录</div>
    <div class="foot">
      <p @click="register()">免费注册</p>
      <p @click="getPwd()">忘记密码？</p>
    </div>
  </div>
</template>
<script>
import { formCheck } from '@/config/checkout'
import { ALERT_TIME } from '@/config/config'
import { mapGetters, mapMutations } from 'vuex'
export default {
  name: "login",
  data () {
    return {
      mobile: '', // 手机号
      pwd: '', // 密码
      verify_code: '', //验证码
      img_code: '', // 图形验证码
    };
  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'userInfo'
    ])
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
    // 注册跳转
    register () {
      this.$router.push({
        path: '/register'
      })
    },
    // 忘记密码跳转
    getPwd () {
      this.$router.push({
        path: '/forgetPwd'
      })
    },
    // 登录
    login () {
      let checkItem = [{
        reg: 'mobile',
        val: this.mobile,
        errMsg: '请输入正确的手机号'
      }, {
        reg: 'noData',
        val: this.pwd,
        errMsg: '密码不能为空'
      }, {
        reg: 'noData',
        val: this.verify_code,
        errMsg: '校验码不能为空'
      }]
      if (formCheck(checkItem).result) {
        this.$Api({
          api_name: 'kkl.user.login',
          mobile: this.mobile,
          password: this.$MD5.hex_md5(this.pwd),
          code: this.verify_code
        }, (err, data) => {
          if (!err) {
            this.$toast({ text: '登录成功' });
            this.$router.replace({
              path: '/'
            })
            localStorage.setItem('loginStatu',1)
            this.$Api({ api_name: 'kkl.user.getUserInfo' }, (erra, res) => {
              if (!erra) {
                this.setUser(res.data)
              } else {
                this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
              }
            })
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
    ...mapMutations({
      setUser: 'SET_USER',
      delUser: 'DEL_USER'
    })
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