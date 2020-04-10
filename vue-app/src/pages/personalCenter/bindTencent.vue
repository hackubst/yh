<template>
    <div id="bindTencent">
        <div class="input_box">
            <input type="text" class="text_input" placeholder="请输入QQ号码" v-model="qq">
        </div>
        <!-- 获取短信验证码 -->
        <div class="get_code_box">
            <p class="code_title">请选择获取验证码途径</p>
            <ul class="list_info">
                <li class="list_detail" v-for="(item, index) in list" :key="index" @click="change_index(index)">
                    <img v-if="currentIndex == index" src="../../assets/images/icon/icon_chooseon@3x.png" alt="">
                    <img v-if="currentIndex != index"  src="../../assets/images/icon/icon_chooseoff@3x.png" alt="">
                    <p>{{item.title}}</p>
                </li>
            </ul>
            <div class="input_box">
                <input type="text" class="text_input" placeholder="请输入短信验证码" maxlength="6" v-model="verify_code">
                <p v-if="!sendAuthCode" class="get_code" @click="get_verify()">获取验证码</p>
                <div class="get_code" v-if="sendAuthCode">{{auth_time}}s后重新获取</div>
            </div>
        </div>
        <!-- 提交 -->
        <div class="btn" @click="editor_now()">马上修改</div>
    </div>
</template>

<script>
import { formCheck} from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'bindTencent',
    data () {
        return {
            list: [{
                title: '手机短信'
            },
            // {
            //     title: '微信验证'
            // },
            ],
            currentIndex: 0,
            qq: '',
            verify_code: '',
            sendAuthCode: false,
            auth_time: 0,
            mobile: ''
        }
    },
    created() {
        this.get_user_info()
    },
    methods: {
        get_user_info() {
            this.$Api({
                api_name: 'kkl.user.getUserInfo',
            }, (erra, res) => {
                if (!erra) {
                    this.mobile = res.data.mobile
                } else {
                    this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        change_index(index) {
            this.currentIndex = index
        },
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
        // 立刻绑定
        editor_now() {
            let checkItem = [{
                reg: 'noData',
                val: this.qq,
                errMsg: 'QQ号码不能为空'
            }, {
                reg: 'noData',
                val: this.verify_code,
                errMsg: '验证码不能为空'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.bindQQ',
                    qq: this.qq,
                    verify_code: this.verify_code
                }, (err, data) => {
                    if (!err) {
                        this.$msg(data.data, 'success', 'middle', 1500)
                        this.qq = ''
                        this.verify_code = ''
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
    #bindTencent{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
    }
    .input_box{
        width: 351px;
        height: 48px;
        background-color: #F2F2F2;
        margin: 12px auto 0 auto;    
        display: flex;
        align-items: center;
        justify-content: flex-start;
        border-radius: 4px;
    }
    .text_input{
        outline: none;
        color: #333;
        font-size: 16px;
        flex: 1;
        border: none;
        background-color: #F2F2F2;
        text-indent: 12px;
        padding: 0;
    }
    .text_input::-webkit-input-placeholder{
        color: #999;
    }
    .get_code{
        color: #4A4130;
        font-size: 16px;
        margin-right: 12px;
        line-height: 22px;
        font-weight: 500;
    }
    .get_code_box{
        width: 351px;
        margin: 24px auto 0 auto;
    }
    .get_code_box .code_title{
        color: #4A4130;
        font-size: 16px;
        font-weight: 500;
        line-height: 22px;
        margin-bottom: 12px;
    }
    .list_info{
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .list_detail{
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .list_detail img{
        width: 14px;
        height: 14px;
        margin-right: 11px;
    }
    .list_detail p{
        color: #4A4130;
        font-size: 16px;
        line-height: 22px;
        margin-right: 15px;
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