<template>
    <div id="bindWeChat">
        <div class="mobile_info">
            <input type="text" placeholder="请输入绑定的手机号" maxlength="6" v-model="mobile">
            <p v-if="!sendAuthCode" class="get_code" @click="get_verify()">获取验证码</p>
            <div class="get_code" v-if="sendAuthCode">{{auth_time}}s后重新获取</div>
        </div>
        <input class="verify_code" type="text" placeholder="请输入短信验证码" v-model="code">
        <div class="btn" @click="bind_now">马上绑定</div>
    </div>
</template>

<script>
import { formCheck} from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'bindWeChat',
    data () {
        return {
            mobile: '',
            code: '',
            sendAuthCode: false,
            auth_time: 0,
        }
    },
    created() {
        
    },
    methods: {
        // 发送验证码
        get_verify() {
            let checkItem = [{
                reg: 'mobile',
                val: this.mobile,
                errMsg: '请输入正确的手机号'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.sendVerifyCode',
                    mobile: this.mobile
                }, (err, data) => {
                    if (!err) {
                        this.$msg(data.data, 'success', 'middle', 1500)
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
                        this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                    }
                })
            } else {
                this.$msg(formCheck(checkItem), 'cancel', 'middle', 1500)
            }
        },
        bind_now() {
            let checkItem = [{
                reg: 'mobile',
                val: this.mobile,
                errMsg: '请输入正确的手机号'
            }, {
                reg: 'noData',
                val: this.code,
                errMsg: '验证码不能为空'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.bindWeixin',
                    mobile: this.mobile,
                    verify_code: this.code
                }, (err, data) => {
                    if (!err) {
                        this.$msg(data.data, 'success', 'middle', 1500)
                        this.mobile = ''
                        this.code = ''
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
    #bindWeChat{
        width: 100%;
        /* height: 100%; */
        background-color: #fff;
        overflow: hidden;
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .mobile_info{
        width: 351px;
        height: 48px;
        background-color: #f2f2f2;
        font-size: 16px;
        color: #333;
        text-indent: 12px;
        margin: 25px 0 0 12px;
        border-radius: 4px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .mobile_info input{
        flex: 1;
        height: 48px;
        background-color: #f2f2f2;
        font-size: 16px;
        color: #333;
        text-indent: 12px;
        border: none;
        outline: none;
        border-radius: 4px;
        padding: 0;
    }
    .mobile_info .get_code{
        font-size:16px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(74,65,48,1);
        line-height:22px;
        margin-right: 12px;
    }
    .verify_code{
        width: 351px;
        height: 48px;
        background-color: #f2f2f2;
        font-size: 16px;
        color: #333;
        text-indent: 12px;
        margin:12px 0 0 12px;
        border: none;
        outline: none;
        border-radius: 4px;
        padding: 0;
    }
    input::-webkit-input-placeholder{
        color: #999;
    }
    .btn{
        width:351px;
        height:48px;
        background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
        border-radius:4px;
        font-size:18px;
        font-weight:500;
        color:rgba(255,255,255,1);
        line-height:48px;
        margin: 24px auto 0 auto;
        text-align: center;
    }
</style>