<template>
    <div id="modifyPwd">
        <input type="password" placeholder="请输入原登录密码" v-model="old_pwd">
        <input type="password" placeholder="请输入新的登录密码" v-model="new_pwd">
        <input type="password" placeholder="再次输入新的登录密码" v-model="confirm_pwd">
        <div class="btn" @click="editor_now">马上修改</div>
    </div>
</template>

<script>
import { formCheck } from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'modifyPwd',
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
                    type: 1,
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
                this.$msg(formCheck(checkItem), 'cancel', 'middle', 1500)
            }
        }
    }
}
</script>

<style scoped>
    #modifyPwd{
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