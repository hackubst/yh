<template>
    <div id="sendPacket">
        <div class="head_info">
            <img src="../../assets/images/icon/icon_bean@3x.png" alt="">
            <p>当前银行余额</p>
            <span>{{balance | changeBigNum}}(￥{{balance/1000}})</span>
        </div>
        <div class="packet_type">
            <p class="type">红包方式</p>
            <ul class="list_info">
                <li class="list_detail" v-for="(item, index) in type_list" :key="index" @click="changeIndex(index)">
                    <img v-if="current_index == index" src="../../assets/images/icon/icon_chooseon@3x.png" alt="">
                    <img v-if="current_index != index"  src="../../assets/images/icon/icon_chooseoff@3x.png" alt="">
                    <p>{{item.title}}</p>
                </li>
            </ul>
        </div>
        <div class="input_box">
            <input type="text" class="text_input" :placeholder="text_pro" v-model="number">
        </div>
        <div class="input_box">
            <input type="text" class="text_input" placeholder="红包个数" v-model="count">
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
            <div class="input_box">
                <input type="text" class="text_input" placeholder="恭喜发财，大吉大利" v-model="remark">
            </div>
        </div>
        <!-- 提交 -->
        <div class="btn" @click="produce">生成红包</div>
        <Confirm v-model="show" :title="title" :content="content_url" @on-confirm="confirm()" confirm-text='复制链接'></Confirm>
    </div>
</template>

<script>
import { formCheck} from '@/config/checkout'
import { Confirm } from 'vux'
export default {
    components:{
        Confirm
    },
    name: 'sendPacket',
    data () {
        return {
            balance: '', // 银行余额
            list: [{
                title: '手机短信'
            }],
            type_list: [{
                title: '普通红包'
            },{
                title: '手气红包'
            }],
            currentIndex: 0,
            current_index: 0,
            verify_code: '',
            mobile: '',
            remark: '',
            count: '',
            number: '',
            sendAuthCode: false,
            auth_time: 0,
            show: false,
            title: '已生成红包链接',  
            content_url: '',
            text_pro: '单个金额'
        }
    },
    created() {
        this.get_user_info()
    },
    methods: {
        // 复制链接
        confirm() {
            this.$native.send_paste(this.content_url)
        },
        // 获取银行余额
        get_user_info() {
            this.$Api({
                api_name: 'kkl.user.getUserInfo',
            }, (erra, res) => {
                console.log('请求结果', erra, res)
                if (!erra) {
                    this.balance = res.data.frozen_money
                    this.mobile = res.data.mobile
                } else {
                    this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
                }
            })
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
                        this.$msg(data.data, 'success', 'middle',1500)
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
        change_index(index) {
            this.currentIndex = index
        },
        changeIndex(index) {
            this.current_index = index
            if(index == 0) {
                this.text_pro = '单个金额'
            } else {
                this.text_pro = '总金额'
            }
        },
        // 生成红包
        produce() {
            let checkItem = [{
                reg: 'noData',
                val: this.number,
                errMsg: '金额不能为空'
            }, {
                reg: 'noData',
                val: this.count,
                errMsg: '数量不能为空'
            }, {
                reg: 'noData',
                val: this.verify_code,
                errMsg: '短信验证码不能为空'
            },{
                reg: 'noData',
                val: this.remark,
                errMsg: '标题不能为空'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                api_name: 'kkl.user.sendRedPacket',
                type: this.current_index + 1,
                total_money: this.number,
                num: this.count,
                code: this.verify_code,
                title: this.remark
                }, (err, data) => {
                if (!err) {
                    // this.count = ''
                    // this.number = ''
                    // this.verify_code = ''
                    // this.remark = ''
                    // this.$msg(data.data, 'success', 'middle', 1500)
                    let jiami_id = data.data
                    // document.documentElement.scrollTop = 0;
                    document.documentElement.scrollTop = document.body.scrollTop = 0
                    this.show = true
                    this.content_url = `${window.location.host}/#/index?jiami_id=${jiami_id}`
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
                })
            } else {
                this.$msg(formCheck(checkItem), 'cancel', 'middle', 1500)
            }
        },
    }
}
</script>

<style scoped>
    #sendPacket{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
    }
    .head_info{
        margin-top: 22px;
        margin-left: 15px;
        margin-bottom: 13px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .head_info img{
        width: 18px;
        height: 18px;
        margin-right: 9px;
    }
    .head_info p{
        font-size:16px;
        font-family:PingFangSC-Regular;
        font-weight:400;
        color:rgba(51,51,51,1);
        line-height:22px;
        margin-right: 4px;
    }
    .head_info span{
        font-size:16px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(255,30,30,1);
        line-height:22px;
    }
    .packet_type{
        margin-bottom: 23px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .type{
        margin: 0 24px 0 12px;
        font-size:16px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(74,65,48,1);
        line-height:22px;
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