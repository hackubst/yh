<template>
  <div id="login">
    <bgImg></bgImg>
    <div class="login_info">
      <p class="title">账号登录</p>
      <div class="form">
        <div class="mobile">
          <input type="number" placeholder="请输入手机号" v-model="mobile" />
        </div>
        <div class="pwd">
          <input type="password" placeholder="请输入密码" v-model="pwd" />
        </div>
        <div class="verify_code">
          <input type="text" placeholder="请输入验证码" v-model="verify_code" />
          <p class="code_img" @click="change_code_img()">
            <img :src="img_code" alt />
          </p>
        </div>
      </div>
      <div class="res_forget">
        <p @click="register()">账号注册</p>
        <p @click="getPwd()">忘记密码</p>
      </div>
      <div class="login" @click="confirm()">确定</div>
    </div>
  </div>
</template>

<script>
import bgImg from '../../components/bg_img/bg_img'
import { formCheck } from '@/config/checkout'
import { ALERT_TIME } from '@/config/config'
import { mapGetters, mapMutations } from 'vuex'
export default {
  name: "login",
  components: {
    bgImg
  },
  data () {
    return {
      mobile: '', // 手机号
      pwd: '', // 密码
      verify_code: '', //验证码
      img_code: '', // 图形验证码
    }
  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'userInfo'
    ])
  },
  created () {
    this.delUser()
    this.get_img_code()
  },
  methods: {
    // 去注册
    register () {
      this.$router.push({
        path: '/register'
      })
    },
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
    // 忘记密码
    getPwd () {
      this.$router.push({
        path: '/stepOne'
      })
    },
    // 登录
    confirm () {
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
            this.$message({
              message: '登录成功',
              type: 'success',
              duration: ALERT_TIME,
              onClose: () => {
                this.$router.replace({
                  path: '/'
                })
              }
            });
            localStorage.setItem('loginStatu',1)
            this.$Api({ api_name: 'kkl.user.getUserInfo'}, (erra, res) => {
              if (!erra) {
                this.setUser(res.data)
              } else {
                this.$msg(erra.error_msg, 'error', 1500)
              }
            })
          } else {
            if (data.code === 40051) {
              this.img_code = ''
              this.get_img_code()
            }
            this.$msg(err.error_msg, 'error', 1500)
          }
        })
      } else {
        this.$msg(formCheck(checkItem), 'error', 1500)
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
#login {
  .wh(100%, 100%);
  flex: 1;
  position: relative;
  overflow: hidden;
  .login_info {
    .wh(360px, 355px);
    margin: 110px auto 303px auto;
    border-radius: 8px;
    padding: 30px 50px 50px 50px;
    background: -webkit-linear-gradient(
      #ffd194,
      #d1913c
    ); /* Safari 5.1 - 6.0 */
    background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
    background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
    background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-direction: column;
    .title {
      .sc(18px, #fff8ef);
      line-height: 25px;
      margin-bottom: 20px;
    }
    .form {
      .wh(360px, auto);
      margin-bottom: 10px;
      .mobile,
      .pwd {
        .wh(100%, 55px);
        margin-bottom: 20px;
        border-bottom: 1px solid #fff8ef;
        box-sizing: border-box;
        input {
          .wh(100%, 100%);
          .sc(18px, #fff8ef);
        }
        input::-webkit-input-placeholder {
          color: #ffd49a;
        }
      }
      .verify_code {
        .wh(100%, 55px);
        border-bottom: 1px solid #fff8ef;
        box-sizing: border-box;
        display: flex;
        justify-content: space-between;
        align-items: center;
        input {
          .wh(220px, 100%);
          .sc(18px, #fff8ef);
        }
        input::-webkit-input-placeholder {
          color: #ffd49a;
        }
        .code_img {
          .wh(124px, 44px);
          background-color: #fff;
          img {
            .wh(100%, 100%);
          }
        }
      }
    }
    .res_forget {
      .wh(100%, auto);
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      p {
        .sc(16px, #ffd49a);
        line-height: 22px;
      }
    }
    .login {
      .wh(100%, 56px);
      background-color: #fff8ef;
      .sc(18px, #d1913c);
      font-weight: bold;
      text-align: center;
      line-height: 56px;
      border-radius: 8px;
    }
  }
}
</style>