<template>
  <div class="loginBox">
    <div class="mainLogin">
      <div class="loginTitle">
        <div class="title_left">
          欢迎来到金龙28
        </div>
        <div class="title_right pointer" v-if="haveLogin" @click="outLogin()">
          退出登录
        </div>
        <div class="title_right" v-else @click="register()">
          免费注册
        </div>

      </div>
      <!-- 已经登录状态 -->
      <div class="havelogin" v-if="haveLogin">
        <div class="login_line">
          <div class="line_title">账号：</div>
          <div class="line_info">{{userInfo.mobile}}</div>
        </div>
        <div class="login_line">
          <div class="line_title">昵称：</div>
          <div class="line_info">{{userInfo.nickname}}</div>
        </div>
        <div class="login_line">
          <div class="line_title">乐豆：</div>
          <div class="line_info">
            {{userInfo.left_money | changeBigNum}}
            <img src="~images/icon/icon_douzi@2x.png" alt="" srcset="">
          </div>
        </div>
        <div class="login_line">
          <div class="line_title">银行：</div>
          <div class="line_info">
            {{userInfo.frozen_money | changeBigNum}}
            <img src="~images/icon/icon_douzi@2x.png" alt="" srcset="">
          </div>
        </div>
        <div class="login_line">
          <div class="line_title">经验：</div>
          <div class="line_info">
            {{userInfo.exp}}
          </div>
        </div>
        <div class="login_line">
          <div class="line_title">等级：</div>
          <div class="line_grade">
            <img :src="getUserGrad" alt="">
          </div>
        </div>
      </div>
      <!-- 未登录状态 -->
      <div class="nologin" v-else>
        <div class="input_box">
          <img src="~images/icon/icon_phone@2x.png" alt="">
          <input type="number" placeholder="请输入您的手机号" v-model="mobile">
        </div>
        <div class="input_box">
          <img src="~images/icon/icon_lock@2x.png" alt="">
          <input type="password" placeholder="请输入密码" v-model="pwd">
        </div>
        <div class="check_code">
          <div class="check_inp">
            <img src="~images/icon/icon_check@2x.png" alt="">
            <input type="" placeholder="请输入验证码" v-model="verify_code">
          </div>
          <div class="check_img" @click="change_code_img()">
            <img :src="img_code" alt="">
          </div>
        </div>
        <div class="forget" @click="forget_pwd()">
          忘记密码？
        </div>
        <div class="longinBtn" @click="login()">登录</div>
      </div>
    </div>
  </div>
</template>
<script>
  import {
    mapGetters,
    mapMutations
  } from 'vuex'
  import {
    formCheck
  } from '@/config/checkout'
  import {
    defalutImg
  } from '@/config/mixin'
  export default {
    name: "loginBox",
    mixins: [defalutImg],
    props: {

    },
    computed: {
      ...mapGetters([
        'haveLogin',
        'userInfo'
      ])
    },
    data() {
      return {
        mobile: '',
        pwd: '',
        verify_code: '',
        img_code: ''
      };
    },
    created() {
      this.get_img_code()
    },
    methods: {
      // 获取图形验证码
      get_img_code() {
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
      change_code_img() {
        this.img_code = ''
        this.get_img_code()
      },
      // 去登录
      register() {
        // this.$router.push({
        //   path: '/register'
        // })
        this.$emit('register')
      },
      forget_pwd() {
        this.$router.push({
          path: '/stepOne'
        })
      },
      login() {
        let checkItem = [{
          reg: 'mobile',
          val: this.mobile,
          errMsg: '请输入正确的手机号'
        }, {
          reg: 'noData',
          val: this.pwd,
          errMsg: '请输入密码'
        }, {
          reg: 'noData',
          val: this.verify_code,
          errMsg: '请输入验证码'
        }]
        if (formCheck(checkItem).result) {
          this.$Api({
            api_name: 'kkl.user.login',
            mobile: this.mobile,
            password: this.$MD5.hex_md5(this.pwd),
            code: this.verify_code
          }, (err, data) => {
            if (!err) {
              this.$Api({
                api_name: 'kkl.user.getUserInfo',
              }, (erra, res) => {
                if (!erra) {
                  this.mobile = ''
                  this.pwd = ''
                  this.verify_code = ''
                  this.setUser(res.data)
                  localStorage.setItem('loginStatu',1)
                  this.$emit('changeLoginStatu')
                } else {
                  this.$Alert(erra.error_msg, '提示')
                }
              })
            } else {
              this.$msg(err.error_msg, 'error', 1500)
              if (data.code === 40051) {
                this.img_code = ''
                this.get_img_code()
              }
              // this.$Alert(err.error_msg, '提示')
            }
          })


        } else {
          this.$Alert(formCheck(checkItem), '提示')
        }
      },
      //   退出登录
      outLogin() {
        this.$Api({
          api_name: 'kkl.user.logout'
        }, (err, data) => {
          if (!err) {
            this.delUser()
            localStorage.setItem('loginStatu',0)
          } else {
            this.$Alert(err.error_msg, '提示')
          }
        })
      },
      ...mapMutations({
        setUser: 'SET_USER',
        delUser: 'DEL_USER'
      })
    }
  }
</script>
<style scoped lang='less'>
  .loginBox {
    width: @main-width;
    height: 374px;
    position: absolute;
    top: 0px;
    left: 50%;
    margin-left: -@main-width / 2;
    z-index: 999;

    .mainLogin {
      background: rgba(0, 0, 0, 0.7);
      position: absolute;
      right: 0;
      top: 0;
      width: 350px;
      height: 374px;
      z-index: 999;
      border-radius: 8px;
      padding: 24px 30px 30px;
      box-sizing: border-box;
    }
  }

  .loginTitle {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;

    .title_left {
      font-size: 24px;
      color: #fff;
    }

    .title_right {
      font-size: 16px;
      color: @main-color;
    }
  }

  // 首页登录输入框公用样式
  .index_input {
    display: flex;
    align-items: center;
    padding: 0 10px;
    box-sizing: border-box;
    background: #fff;

    img {
      width: 24px;
      height: 24px;
      margin-right: 10px;
    }

    input {
      flex: 1;
      font-size: 16px;
    }
  }

  // 未登录样式
  .nologin {
    .input_box {
      width: 290px;
      height: 40px;
      margin-top: 20px;
      border-radius: 4px;
      .index_input();
    }

    .check_code {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;

      .check_inp {
        border-radius: 4px;
        width: 159px;
        height: 40px;
        display: flex;
        align-items: center;
        overflow: hidden;
        .index_input();
      }

      .check_img {
        border-radius: 4px;
        width: 121px;
        height: 40px;
        background: #fff;

        img {
          width: 121px;
          height: 40px;
        }
      }
    }

    .forget {
      font-size: 16px;
      color: @main-color;
      margin-top: 17px;
      margin-bottom: 17px;
      text-align: right;
    }

    .longinBtn {
      .commonBtn(45px, 290px);
    }
  }

  // 已登录样式
  .havelogin {
    .login_line:nth-child(1) {
      margin-top: 14px;
    }

    .login_line {
      padding: 10px 0;
      box-sizing: border-box;
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.3);
      font-size: 16px;
      color: #Fff;

      .line_title {
        width: 48px;
        margin-right: 24px;
      }

      .line_info {
        display: flex;
        align-items: center;

        img {
          width: 16px;
          height: 16px;
          margin-left: 6px;
        }
      }

      .line_grade img {
        width: 58.8px;
        height: 30px;
      }
    }
  }
</style>