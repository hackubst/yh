<template>
    <div id="stepOne">
        <bgImg></bgImg>
        <div class="login_info">
            <p class="title">找回{{type==1?'登录':'安全'}}密码</p>
            <div class="operation">
                <p class="forgot_pwd" @click="forget_safe_pwd()">忘记{{type==2?'登录':'安全'}}密码</p>
                <p class="account_num">已有账号，<span @click="login()">马上登录</span></p>
            </div>
            <div class="form">
                <div class="mobile">
                    <input type="number" placeholder="请输入手机号" v-model="mobile">
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
  name: "stepOne",
  components: {
      bgImg
  },
  data () {
    return {
        mobile: '',
        type: 1
    }
  },
  methods: {
      // 忘记安全密码
      forget_safe_pwd() {
        if (this.type == 1) {
            this.type = 2
        } else if (this.type == 2) {
            this.type = 1
        }
      },
      // 去登录
      login() {
          this.$router.push({
              path: '/login'
          })
      },
      // 下一步
      next_step() {
        let checkItem = [{
            reg: 'mobile',
            val: this.mobile,
            errMsg: '请输入正确的手机号'
        }]
        if (formCheck(checkItem).result) {
            this.$router.push({
                path: '/stepTwo',
                query: {
                    mobile: this.mobile,
                    type: this.type
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
    #stepOne{
        .wh(100%,100%);
        flex: 1;
        position: relative;
        overflow: hidden;
        .login_info{
            .wh(360px, 225px);
            margin: 110px auto 433px auto;
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
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
                .forgot_pwd{
                    .sc(14px, #FFF8EF);
                    line-height: 20px;
                }
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
                margin-bottom: 10px;
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