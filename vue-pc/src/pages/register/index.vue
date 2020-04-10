<template>
  <div id="register">
    <bgImg></bgImg>
    <div class="register_info">
      <p class="title">账号注册</p>
      <div class="form">
        <div class="mobile">
          <input type="number" placeholder="请输入手机号" v-model="mobile">
        </div>
        <div class="pwd">
          <input type="password" placeholder="设置密码，密码长度6-20位, 包含字母数字" v-model="pwd">
        </div>
        <div class="pwd">
          <input type="password" placeholder="请确认密码" v-model="pwd_two">
        </div>
        <div class="realname">
          <input type="text" placeholder="请输入昵称" v-model="realname">
        </div>
        <div class="recommender">
          <input type="text" placeholder="请输入推荐人ID(选填)" v-model="referee">
        </div>
        <div class="verify_code">
          <input type="text" placeholder="请输入图形验证码" v-model="code">
          <p class="code_img" @click="change_code_img()">
            <img :src="img_code" alt="">
          </p>
        </div>
        <div class="message_code">
            <input type="text" placeholder="请输入短信验证码" v-model="verify_code">
            <p class="send_code" v-if="!sendAuthCode" @click="get_verify()">发送验证码</p>  
            <p class="send_code" v-if="sendAuthCode">{{auth_time}}s</p>
        </div>
      </div>
      <div class="clause">
        <div class="choose_img" @click="change_img()">
          <img v-if="!img_boolear" src="../../assets/images/icon/icon_16_kongbai@2x.png" alt="空白图片">
          <img v-if="img_boolear" src="../../assets/images/icon/icon_16@2x.png" alt="打勾图片">
        </div>
        <p>我已阅读并接受 <span @click="richText()">《版权声明》</span> 和 <span @click="rich_text()">《隐私保护》</span></p>
      </div>
      <div class="register" @click="register()">确定</div>
      <p class="to_login">已有账号，<span @click="login()">马上登录</span></p>
    </div>
  </div>
</template>

<script>
  import bgImg from '../../components/bg_img/bg_img'
  import {
    ALERT_TIME
  } from '@/config/config'
  import {
    formCheck
  } from '@/config/checkout'
  export default {
    name: "login",
    components: {
      bgImg
    },
    data() {
      return {
        img_boolear: false, // 是否勾选
        mobile: '', // 手机号
        pwd: '', // 密码
        pwd_two: '',   // 确认密码
        realname: '', // 昵称
        referee: '', // 推荐人
        verify_code: '', // 短信验证码
        code: '', // 图形验证码
        sendAuthCode: false,
        auth_time: 0,
        img_code: '', // 图形验证码
      }
    },
    created() {
      this.get_img_code()
      if (this.$route.query.id) {
        this.referee = this.$route.query.id
      }
    },
    methods: {
      // 是否已阅读
      change_img() {
        this.img_boolear = !this.img_boolear
      },
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
      // 发送验证码
      get_verify() {
        let checkMobile = [{
          reg: 'mobile',
          val: this.mobile,
          errMsg: '请输入正确的手机号'
        }]
        if (formCheck(checkMobile).result) {
          this.$Api({
            api_name: 'kkl.user.sendVerifyCode',
            mobile: this.mobile
          }, (err, data) => {
              if (!err) {
                this.$msg(data.data, 'success', 1500)
                this.sendAuthCode = true
                this.auth_time = 60
                var auth_timetimer =  setInterval(()=>{
                    this.auth_time--;
                    if(this.auth_time<=0){
                        this.sendAuthCode = false
                        clearInterval(auth_timetimer);
                    }
                }, 1000);
              } else {
                  this.$msg(err.error_msg, 'error', 1500)
              }
          })
        } else {
          this.$msg(formCheck(checkMobile), 'error', 1500)
        }
      },
      // 注册
      register() {
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
        },{
          reg: 'noData',
          val: this.code,
          errMsg: '图形验证码不能为空'
        }]
        if (formCheck(checkItem).result) {
          if(this.pwd !== this.pwd_two){
             this.$msg('两次输入密码不一致', 'error', 1500)
             return
          }
          if (this.img_boolear) {
            this.$Api({
              api_name: 'kkl.user.signup',
              mobile: this.mobile,
              verify_code: this.verify_code,
              code: this.code,
              password: this.$MD5.hex_md5(this.pwd),
              again_password: this.$MD5.hex_md5(this.pwd_two),
              parent_id: this.referee,
              nickname: this.realname
            }, (err, data) => {
              if (!err) {
                this.$message({
                  message: '注册成功',
                  type: 'success',
                  duration: ALERT_TIME,
                  onClose: () => {
                      this.$router.replace({
                          path: '/'
                      })
                  }
                });
              } else {
                this.$msg(err.error_msg, 'error', 1500)
              }
            })
          } else {
            this.$msg('请阅读并同意《版权声明》和《隐私保护》', 'error', 1500)
          }
        } else {
          this.$msg(formCheck(checkItem), 'error', 1500)
        }
      },
      // 去登录
      login() {
        this.$router.push({
          path: '/login'
        })
      },
      // 跳转富文本
      richText() {
        this.$router.push({
          path: '/richText',
          query: {
            type: 'banquan'
          }
        })
      },
      rich_text() {
        this.$router.push({
          path: '/richText',
          query: {
            type: 'yinsi'
          }
        })
      }
    }
  }
</script>

<style scoped lang='less'>
  #register {
    .wh(100%, 950px);
    flex: 1;
    position: relative;
    overflow: hidden;
    .register_info {
      .wh(360px, 600px);
      margin: 110px auto 110px auto;
      border-radius: 8px;
      padding: 30px 50px 80px 50px;
      background: -webkit-linear-gradient(#FFD194, #D1913C);
      /* Safari 5.1 - 6.0 */
      background: -o-linear-gradient(#FFD194, #D1913C);
      /* Opera 11.1 - 12.0 */
      background: -moz-linear-gradient(#FFD194, #D1913C);
      /* Firefox 3.6 - 15 */
      background: linear-gradient(#FFD194, #D1913C);
      /* 标准的语法 */
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
        .pwd,
        .realname,
        .recommender {
          .wh(100%, 55px);
          margin-bottom: 12px;
          border-bottom: 1px solid #FFF8EF;
          box-sizing: border-box;
          input {
            .wh(100%, 100%);
            .sc(18px, #FFF8EF);
          }

          input::-webkit-input-placeholder {
            color: #FFD49A;
          }
        }

        .verify_code {
          .wh(100%, 55px);
          border-bottom: 1px solid #FFF8EF;
          box-sizing: border-box;
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 12px;
          input {
            .wh(220px, 100%);
            .sc(18px, #FFF8EF);
          }

          input::-webkit-input-placeholder {
            color: #FFD49A;
          }

          .code_img {
            .wh(124px, 44px);
            background-color: #fff;
            img{
              .wh(100%, 100%)
            }
          }
        }
        .message_code{
          .wh(100%, 55px);
          border-bottom: 1px solid #FFF8EF;
          box-sizing: border-box;
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 10px;
          input{
              .wh(220px, 55px);
              .sc(18px, #FFF8EF);
          }
          input::-webkit-input-placeholder{
              color: #FFD49A;
          }
          .send_code{
              .wh(70px, auto);
              .sc(14px, #FFF8EF);
              text-align: center;
          }
        }
      }

      .clause {
        .wh(100%, 20px);
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-start;
        align-items: center;

        .choose_img {
          .wh(16px, 16px);
          margin-right: 4px;

          img {
            .wh(100%, 100%);
          }
        }

        p {
          .sc(14px, #FFD49A);
          line-height: 20px;

          span {
            .sc(14px, #FFF8EF);
            line-height: 20px;
            cursor: pointer;
          }
        }
      }

      .register {
        .wh(100%, 56px);
        background-color: #FFF8EF;
        .sc(18px, #D1913C);
        font-weight: bold;
        text-align: center;
        line-height: 56px;
        border-radius: 8px;
        margin-bottom: 20px;
      }

      .to_login {
        .sc(18px, #FFD49A);
        line-height: 25px;

        span {
          .sc(18px, #FFF8EF);
          line-height: 25px;
        }
      }
    }
  }
</style>