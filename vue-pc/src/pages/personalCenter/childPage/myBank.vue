<template>
    <div id="myBank">
        <headBar head_title="我的银行" head_pro="银行只能存乐豆，转账乐豆"></headBar>
        <div class="form_list">
            <div class="top_part">
                <ul class="info_detail">
                    <li>
                        <p>我要存乐豆：<span>请输入乐豆数量</span></p>
                        <div class="save_btn">
                            <input type="text" :class="{blank: count!=''}" v-model="count">
                            <div @click="store()">存入</div>
                        </div>
                    </li>
                    <li>
                        <p>我要取乐豆：<span>请输入乐豆数量</span></p>
                        <input type="text" :class="{blank: bean_count!=''}" v-model="bean_count">
                    </li>
                    <li>
                        <div class="forget_pwd">
                            <p>银行密码：<span>请输入银行密码</span></p>
                            <div @click="forget_pwd()">忘记密码？</div>
                        </div>
                        <input type="password" autocomplete="new-password" class="pwd" :class="{blank: pwd!=''}" v-model="pwd">
                    </li>
                </ul>
            </div>  
            <div class="confirm" @click="take_out()">取出</div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import { formCheck } from '@/config/checkout'
import headBar from '../../../components/headBar/index'
export default {
  name: "myBank",
  components: {
      headBar
  },
  data () {
    return {
        count: '',
        bean_count: '',
        pwd: ''
    }
  },
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  methods: {
      get_userInfo() {
        this.$Api({
            api_name: 'kkl.user.getUserInfo',
        }, (erra, res) => {
            if (!erra) {
                this.setUser(res.data)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
      },
      // 存入
      store() {
        if (this.count != '') {
            this.$Api({
                api_name: 'kkl.user.accessBean',
                type: 1,
                number: this.count
            }, (err, data) => {
                if (!err) {
                    this.count = ''
                    this.$msg(data.data, 'success', 1500)
                    this.get_userInfo()
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        } else {
            this.$msg('请填写存入数量', 'error', 1500)
        }
      },
      // 取出
      take_out() {
        let checkItem = [{
          reg: 'noData',
          val: this.bean_count,
          errMsg: '请输入取出乐豆数量'
        }, {
          reg: 'noData',
          val: this.pwd,
          errMsg: '请输入密码'
        }]
        if (formCheck(checkItem).result) {
            this.$Api({
                api_name: 'kkl.user.accessBean',
                type: 0,
                number: this.bean_count,
                bank_password: this.$MD5.hex_md5(this.pwd),
            }, (err, data) => {
                if (!err) {
                    this.bean_count = ''
                    this.pwd = ''
                    this.$msg(data.data, 'success', 1500)
                    this.get_userInfo()
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        } else {
            this.$msg(formCheck(checkItem), 'error', 1500)
        }
      },
      // 忘记密码
      forget_pwd() {
          this.$router.push({
              path: '/stepOne'
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
    #myBank{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background-color: #F5F5F5;
            border-radius: 8px;
            overflow: hidden;
            .top_part{
                margin-left: 20px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                margin-top: 17px;
                .info_detail{
                    .wh(525px, auto);
                    li{
                        .wh(100%, auto);
                        margin-bottom: 20px;
                        &:first-child{
                            margin-bottom: 40px;
                        }
                        p{
                            .sc(18px, #4A4130);
                            line-height: 25px;
                            margin-bottom: 10px;
                            span{
                                .sc(18px, #CCCCCC);
                                line-height: 25px;
                            }
                        }
                        .save_btn{
                            .wh(100%, auto);
                            display: flex;
                            align-items: center;
                            justify-content: flex-start;
                            input{
                                .wh(405px, 54px);
                                background:rgba(255,255,255,1);
                                border-radius:4px;
                                border:1px solid #ccc;
                                text-indent: 14px;
                                box-sizing: border-box;
                                .sc(18px, #4A4130);
                                margin-right: 20px;
                                &:focus{
                                    border:1px solid rgba(209,145,60,1);
                                }
                            }
                            .blank{
                                border:1px solid rgba(209,145,60,1);
                            }
                            div{
                                width:100px;
                                height:54px;
                                background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                                border-radius:4px;
                                line-height: 54px;
                                text-align: center;
                                .sc(18px, #fff);
                                cursor: pointer;
                            }
                        }
                        input{
                            .wh(405px, 54px);
                            background:rgba(255,255,255,1);
                            border-radius:4px;
                            border:1px solid #ccc;
                            text-indent: 14px;
                            box-sizing: border-box;
                            .sc(18px, #4A4130);
                            &:focus{
                                border:1px solid rgba(209,145,60,1);
                            }
                        }
                        .pwd{
                            .sc(18px, #4A4130);
                        }
                        .blank{
                            border:1px solid rgba(209,145,60,1);
                        }
                        .forget_pwd{
                            .wh(405px, auto);
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            div{
                                font-size:18px;
                                color:rgba(209,145,60,1);
                                line-height:25px;
                            }
                        }
                    }
                }
            }
            .confirm{
                margin-left: 20px;
                width:100px;
                height:54px;
                background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                border-radius:8px;
                line-height: 54px;
                text-align: center;
                .sc(18px, #fff);
                font-weight: 500;
                cursor: pointer;
            }
        }
    }
</style>