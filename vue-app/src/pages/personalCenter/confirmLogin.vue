<template>
    <div id="confirmLogin">
        <input type="password" placeholder="请输入安全密码" v-model="pwd">
        <div class="prompt">提示：初始密码与登录密码相同</div>
        <div class="btn" @click="confirm_login()">确认登录</div>
        <div class="forget_pwd">忘记安全密码</div>
    </div>
</template>

<script>
export default {
    components:{
        
    },
    name: 'confirmLogin',
    data () {
        return {
           pwd: '',
        }
    },
    created() {
        
    },
    methods: {
        turn_center() {
            if (this.pwd != '') {
                this.$Api({
                api_name: 'kkl.user.checkPassword',
                safe_password: this.$MD5.hex_md5(this.pwd),
            }, (err, data) => {
                if (!err) {
                    this.$native.native_show_tabBar(4)
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
            } else {
                    this.$msg('请填写安全密码', 'cancel', 'middle', 1500)
            }
        }
    }
}
</script>

<style scoped>
    #confirmLogin{
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
        width:351px;
        height:48px;
        background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
        border-radius:0.08px;
        font-size:18px;
        font-weight:500;
        color:rgba(255,255,255,1);
        line-height:48px;
        margin: 0 auto;
        text-align: center;
    }
    .forget_pwd{
        font-size:16px;
        font-weight:500;
        color:rgba(51,51,51,1);
        line-height:22px;
        margin-top:12px;
        margin-left: 12px;
    }
</style>