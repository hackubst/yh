<template>
    <div id="safePwd">
        <input type="password" placeholder="请输入原安全密码" v-model="old_pwd">
        <input type="password" placeholder="请输入新的安全密码" v-model="new_pwd">
        <input type="password" placeholder="再次输入新的安全密码" v-model="confirm_pwd">
        <div class="prompt">提示：初始密码与登录密码相同</div>
        <div class="btn" @click="editor_now">马上修改</div>
    </div>
</template>

<script>
import { formCheck } from '@/config/checkout'
import { setTimeout } from 'timers';
export default {
    components:{
        
    },
    name: 'safePwd',
    data () {
        return {
           old_pwd: '',
           new_pwd: '',
           confirm_pwd: ''
        }
    },
    created() {
        
    },
    methods: {
        editor_now() {
            let checkItem = [{
                reg: 'noData',
                val: this.old_pwd,
                errMsg: '请输入原银行密码'
            }, {
                reg: 'noData',
                val: this.new_pwd,
                errMsg: '请输入新的银行密码'
            }, {
                reg: 'noData',
                val: this.confirm_pwd,
                errMsg: '请再次输入密码'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.editPassword',
                    type: 2,
                    password: this.$MD5.hex_md5(this.old_pwd),
                    new_password: this.$MD5.hex_md5(this.new_pwd),
                    re_password: this.$MD5.hex_md5(this.confirm_pwd)
                }, (err, data) => {
                    if (!err) {
                        this.old_pwd = ''
                        this.new_pwd = ''
                        this.confirm_pwd = ''
                        this.$msg(data.data, 'success', 'middle', 1500)
                        setTimeout(function() {
                            this.$router.go(-1)
                        }, 1500)
                    } else {
                        this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                    }
                })  
            } else {
                this.$msg(formCheck(checkItem), 'cancel','middle', 1500)
            }
        }
    }
}
</script>

<style scoped>
    #safePwd{
        width: 100%;
        /* height: 100%; */
        background-color: #fff;
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
    }
    input{
        width: 351px;
        height: 48px;
        background-color: #f2f2f2;
        font-size: 16px;
        color: #333;
        text-indent: 12px;
        margin: 12px 0 0 12px;
        border: none;
        outline: none;
        border-radius: 4px;
        padding: 0;
    }
    input::-webkit-input-placeholder{
        color: #999;
    }
    .prompt{
        font-size:16px;
        font-weight:400;
        color:rgba(51,51,51,1);
        line-height:22px;
        margin-bottom: 12px;
        margin-top: 12px;
        margin-left: 12px;
    }
    .btn{
        width: 351px;
        height: 48px;
        background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
        border-radius: 4px;
        font-size: 18px;
        font-weight:500;
        color:rgba(255,255,255,1);
        line-height:48px;
        margin: 24px auto 0 auto;
        text-align: center;
    }
</style>