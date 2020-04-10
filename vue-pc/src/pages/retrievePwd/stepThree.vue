<template>
    <div id="stepThree">
        <bgImg></bgImg>
        <div class="login_info">
            <p class="title">重新设置{{type==1?'登录':'安全'}}密码</p>
            <div class="operation">
                <p class="account_num">已有账号，<span @click="login()">马上登录</span></p>
            </div>
            <div class="form">
                <div class="pwd">
                    <input type="password" placeholder="设置密码，密码长度6-20位, 包含字母数字" v-model="pwd">
                </div>
                <div class="confirm_pwd">
                    <input type="password" placeholder="重置密码，密码长度6-20位, 包含字母数字" v-model="confirm_pwd">
                </div>
            </div>
            <div class="confirm" @click="confirm()">确定</div>
        </div>
    </div>
</template>

<script>
import bgImg from '../../components/bg_img/bg_img'
import {formCheck} from '@/config/checkout'
import {ALERT_TIME} from '@/config/config'
export default {
  name: "stepThree",
  components: {
      bgImg
  },
  data () {
    return {
        pwd: '', // 新密码
        confirm_pwd: '', // 确认密码
        type: ''
    }
  },
  created() {
      this.type = this.$route.query.type
  },
  methods: {
      // 去登录
      login() {
          this.$router.push({
              path: '/login'
          })
      },
      // 确认修改
      confirm() {
        let checkItem = [{
            reg: 'pwd',
            val: this.pwd,
            errMsg: '请输入6-20位字母和数字'
        }, {
            reg: 'pwd',
            val: this.confirm_pwd,
            errMsg: '请输入6-20位字母和数字'
        }]
        if (formCheck(checkItem).result) {
            this.$Api({
              api_name: 'kkl.user.resetPassword',
              mobile: this.$route.query.mobile,
              new_password: this.$MD5.hex_md5(this.pwd),
              re_password: this.$MD5.hex_md5(this.confirm_pwd),
              type: this.type
            }, (err, data) => {
              if (!err) {
                this.$message({
                  message: data.data,
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
          this.$msg(formCheck(checkItem), 'error', 1500)
        }
      }
  }
}
</script>

<style scoped lang='less'>
    #stepThree{
        .wh(100%,100%);
        flex: 1;
        position: relative;
        overflow: hidden;
        .login_info{
            .wh(360px, 299px);
            margin: 110px auto 359px auto;
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
                margin-bottom: 10px;
                .pwd, .confirm_pwd{
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
            .confirm{
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