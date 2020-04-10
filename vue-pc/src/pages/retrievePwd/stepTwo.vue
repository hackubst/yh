<template>
    <div id="stepTwo">
        <bgImg></bgImg>
        <div class="login_info">
            <p class="title">找回{{type==1?'登录':'安全'}}密码</p>
            <div class="operation">
                <p class="account_num">已有账号，<span @click="login()">马上登录</span></p>
            </div>
            <div class="form">
                <div class="mobile">
                    <input type="number" placeholder="请输入手机号" v-model="mobile">
                </div>
                <div class="verify_code">
                    <input type="text" placeholder="请输入图形验证码" v-model="graph_code">
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
            <div class="next" @click="next_step()">下一步</div>
        </div>
    </div>
</template>

<script>
import bgImg from '../../components/bg_img/bg_img'
import {formCheck} from '@/config/checkout'
export default {
  name: "stepTwo",
  components: {
      bgImg
  },
  data () {
    return {
        mobile: '', // 手机号
        graph_code: '', // 图形验证码
        verify_code: '', // 短信验证码
        type: '',
        sendAuthCode: false,
        auth_time: 0,
        img_code: '', // 图形验证码
    }
  },
  created() {
      this.mobile = this.$route.query.mobile
      this.type = this.$route.query.type
      this.get_img_code()
  },
  methods: {
      // 去登录
      login() {
          this.$router.push({
              path: '/login'
          })
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
      },
      // 下一步
      next_step() {
        let checkItem = [{
            reg: 'mobile',
            val: this.mobile,
            errMsg: '请输入正确的手机号'
        }, {
            reg: 'noData',
            val: this.graph_code,
            errMsg: '图形验证码不能为空'
        }, {
            reg: 'noData',
            val: this.verify_code,
            errMsg: '短信验证码不能为空'
        }]
        if (formCheck(checkItem).result) {
            this.$Api({
              api_name: 'kkl.user.findPassword',
              mobile: this.mobile,
              verify_code: this.verify_code,
              code: this.graph_code
            }, (err, data) => {
              if (!err) {
                this.$router.push({
                    path: '/stepThree',
                    query: {
                        mobile: this.mobile,
                        type: this.type
                    }
                })
              } else {
                this.$msg(err.error_msg, 'error', 1500)
              }
            })
        } else {
          this.$msg(formCheck(checkItem), 'error', 1500)
        }
      }
  }
}
</script>

<style scoped lang='less'>
    #stepTwo{
        .wh(100%,100%);
        flex: 1;
        position: relative;
        overflow: hidden;
        .login_info{
            .wh(360px, 373px);
            margin: 110px auto 285px auto;
            border-radius: 8px;
            padding: 30px 50px 50px 50px;
            background: -webkit-linear-gradient( #FFD194,#D1913C); /* Safari 5.1 - 6.0 */
            background: -o-linear-gradient( #FFD194,#D1913C); /* Opera 11.1 - 12.0 */
            background: -moz-linear-gradient( #FFD194,#D1913C); /* Firefox 3.6 - 15 */
            background: linear-gradient( #FFD194,#D1913C); /* 标准的语法 */
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-direction: column;
            .title{
                .sc(18px, #fff8ef);
                line-height: 25px;
                margin-bottom: 20px;
            }
            .operation{
                .wh(100%, auto);
                display: flex;
                justify-content: flex-end;
                align-items: center;
                margin-bottom: 30px;
                .account_num{
                    .sc(14px, #FFD49A);
                    line-height: 20px;
                    span{
                        .sc(14px, #FFF8EF);
                    }
                }
            }
            .form{
                .wh(360px, auto);
                margin-bottom: 20px;
                .mobile{
                    .wh(100%, 55px);
                    margin-bottom: 20px;
                    border-bottom: 1px solid #FFF8EF;
                    box-sizing: border-box;
                    input{
                        .wh(100%, 100%);
                        .sc(18px, #FFF8EF);
                    }
                    input::-webkit-input-placeholder{
                        color: #FFD49A;
                    }
                }
                .verify_code{
                    .wh(100%, 55px);
                    border-bottom: 1px solid #FFF8EF;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    input{
                        .wh(220px, 100%);
                        .sc(18px, #FFF8EF);
                    }
                    input::-webkit-input-placeholder{
                        color: #FFD49A;
                    }
                    .code_img{
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
            .next{
                .wh(100%, 56px);
                background-color: #FFF8EF;
                .sc(18px, #D1913C);
                font-weight: bold;
                text-align: center;
                line-height: 56px;
                border-radius: 8px;
            }
        }
    }
</style>