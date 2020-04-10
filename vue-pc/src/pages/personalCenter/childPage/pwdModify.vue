<template>
    <div id="pwdModify">
        <headBar head_title="密码修改" head_pro="可修改登录密码和安全密码"></headBar>
        <div class="form_list">
            <div class="top_part">
                <ul class="tab_nav">
                    <li :class="{active: currentIndex == index}" v-for="(item, index) in list" :key="index" @click="change_index(index)">{{item.title}}</li>
                </ul>
                <ul class="info_detail">
                    <li>
                        <p>{{type==1 ? '原登录密码':(type==2?'原安全密码：':'原银行密码')}}<span v-if="type == 2">初始密码与登录密码相同</span></p>
                        <input type="password" :class="{blank: old_pwd!=''}" v-model="old_pwd">
                    </li>
                    <li>
                        <p>{{type==1?'新登录密码':(type==2?'新安全密码':'新银行密码')}}</p>
                        <input type="password" :class="{blank: new_pwd!=''}" v-model="new_pwd">
                    </li>
                    <li>
                        <p>确认新密码</p>
                        <input type="password" :class="{blank: confirm_pwd!=''}" v-model="confirm_pwd">
                    </li>
                </ul>
            </div>  
            <div class="confirm" @click="confirm()">确定</div>
        </div>
    </div>
</template>

<script>
import headBar from '../../../components/headBar/index'
import {formCheck} from '@/config/checkout'
export default {
  name: "pwdModify",
  components: {
      headBar
  },
  data () {
    return {
        list: [{
            title: '登录密码'
        },{
            title: '安全密码'
        },{
            title: '银行密码'
        }],
        currentIndex: 0,
        type: 1,
        old_pwd: '', // 原始密码
        new_pwd: '', // 新密码
        confirm_pwd: '', // 确认密码
    }
  },
  methods: {
    // 切换修改类型
    change_index(index) {
        this.currentIndex = index
        this.type = index + 1
    },
    // 确认新密码
    confirm() {
        let checkItem = [{
            reg: 'pwd',
            val: this.new_pwd,
            errMsg: '请输入6-20位字母和数字'
        }, {
            reg: 'pwd',
            val: this.confirm_pwd,
            errMsg: '请输入6-20位字母和数字'
        }]
        if (formCheck(checkItem).result) {
            console.log(formCheck(checkItem).result)
            this.$Api({
              api_name: 'kkl.user.editPassword',
              type: this.type,
              password: this.$MD5.hex_md5(this.old_pwd),
              new_password: this.$MD5.hex_md5(this.new_pwd),
              re_password: this.$MD5.hex_md5(this.confirm_pwd)
            }, (err, data) => {
              if (!err) {
                this.old_pwd = ''
                this.new_pwd = ''
                this.confirm_pwd = ''
                this.$msg(data.data, 'success', 1500)
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
    #pwdModify{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background-color: #F5F5F5;
            border-radius: 8px;
            overflow: hidden;
            .top_part{
                margin-left: 20px;
                margin-top: 17px;
                .tab_nav{
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    margin-bottom: 20px;
                    li{
                        .wh(112px, 41px);
                        margin-right: 20px;
                        background-color: #E8E8E8;
                        text-align: center;
                        line-height: 41px;
                        .sc(18px, #4A4130);
                        cursor: pointer;
                    }
                    .active{
                        background-color: #D1913C;
                        .sc(18px, #FFF8EF);
                    }
                }
                .info_detail{
                    .wh(405px, auto);
                    margin-bottom: 40px;
                    li{
                        .wh(100%, auto);
                        margin-bottom: 20px;
                        p{
                            .sc(18px, #4A4130);
                            line-height: 25px;
                            margin-bottom: 10px;
                            span{
                                .sc(18px, #CCCCCC);
                                line-height: 25px;
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
                        .blank{
                            border:1px solid rgba(209,145,60,1);
                        }
                    }
                }
            }
            .confirm{
                margin-left: 20px;
                margin-bottom: 40px;
                width:187px;
                height:56px;
                background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                border-radius:8px;
                line-height: 56px;
                text-align: center;
                .sc(18px, #fff);
                font-weight: 500;
                cursor: pointer;
            }
        }
    }
</style>