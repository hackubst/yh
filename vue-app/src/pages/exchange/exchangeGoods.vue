<template>
    <div id="exchangeGoods">
        <div class="title">兑换金龙体验卡 {{cash}} 元</div>
        <ul class="bill_list">
            <li class="bill_info" v-for="(item, index) in bill_list" :key="index">
                <p class="bill_num">{{item.num}}</p>
                <p class="bill_content">{{item.content}}</p>
            </li>
        </ul>
        <div class="mid_content">
            <img src="../../assets/images/icon/icon_bean@3x.png" alt="">
            <p>每张卡需要<span>{{money}}</span>乐豆兑换</p>
        </div>
        <input class="input_value" type="text" placeholder="输入您要兑换的数量" v-model="count">
        <input class="input_value" type="password" placeholder="输入安全密码" v-model="safe_pwd">
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
        <div class="btn" @click="exchange_now()">立即兑换</div>
    </div>
</template>

<script>
import { formCheck} from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'exchangeGoods',
    data () {
        return {
            bill_list: [{
                num: '',
                content: '7天内流水'
            },{
                num: '',
                content: '7天内已提'
            },{
                num: '',
                content: '7天内充值'
            }],
            list: [{
                title: '手机短信'
            },
            // {
            //     title: '微信验证'
            // },
            ],
            currentIndex: 0, // 索引
            cash: '', // 现金
            money: '', // 兑换乐豆
            count: '', // 兑换数量
            safe_pwd: '', // 安全密码
            verify_code: '', //短信验证码
            sendAuthCode: false,
            auth_time: 0,
            mobile: ''
        }
    },
    created() {
        this.$Api({
            api_name: 'kkl.index.giftCardInfo',
            gift_card_id: this.$route.query.id
        }, (err, data) => {
            if (!err) {
                let res = data.data.gift_card_info
                this.cash = res.cash
                this.bill_list[0].num = res.flow
                this.bill_list[1].num = res.deposit
                this.bill_list[2].num = res.recharge
                this.money = res.money
            } else {
                this.$msg(err.error_msg, 'cancel', 'middle', 1500)
            }
        })
        this.get_user_info()
    },
    methods: {
        // 获取银行余额
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
        change_index(index) {
            this.currentIndex = index
        },
        // 兑换卡券
        exchange_now() {
            let checkItem = [{
                reg: 'noData',
                val: this.count,
                errMsg: '兑换数量还不能为空'
            }, {
                reg: 'noData',
                val: this.safe_pwd,
                errMsg: '安全密码不能为空'
            }, {
                reg: 'noData',
                val: this.verify_code,
                errMsg: '验证码不能为空'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.index.exChangeCard',
                    safe_password: this.$MD5.hex_md5(this.safe_pwd),
                    number: this.count,
                    verify_code: this.verify_code,
                    gift_card_id: this.$route.query.id
                }, (err, data) => {
                    if (!err) {
                        this.$msg(data.data, 'success', 'middle', 1500)
                        this.safe_pwd = ''
                        this.count = ''
                        this.verify_code = ''
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

<style scoped lang="less">
    #exchangeGoods{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #fff;
        .title {
            margin: 12px 12px 23px 12px;
            font-size:17px;
            font-family:PingFangSC-Medium;
            font-weight:500;
            color:rgba(74,65,48,1);
            line-height:24px;
        }
        .bill_list{
            width: 100%;
            height: auto;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 25px;
            .bill_info {
                .wh(125px, auto);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                .bill_num{
                    width: 100px;
                    font-size:24px;
                    font-family:PingFangSC-Medium;
                    font-weight:500;
                    color:rgba(74,65,48,1);
                    line-height:33px;
                    margin-bottom: 4px;
                    overflow: hidden;
                    text-overflow:ellipsis;
                    white-space:nowrap;
                    text-align: center;
                }
                .bill_content{
                    font-size:14px;
                    font-family:PingFangSC-Medium;
                    font-weight:500;
                    color:rgba(74,65,48,1);
                    line-height:20px;
                }
            }
        }
        .mid_content{
            margin-left: 12px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 28px;
            img{
                .wh(18px, 18px);
                margin-right: 10px;
            }
            p{
                font-size:16px;
                font-family:PingFangSC-Regular;
                font-weight:400;
                color:rgba(51,51,51,1);
                line-height:22px;
                span{
                    display: inline-block;
                    color: #FED093;
                    padding: 0 8px;
                }
            }
        }
        .input_value{
            width: 351px;
            height: 48px;
            background-color: #f2f2f2;
            font-size: 16px;
            color: #333;
            text-indent: 12px;
            margin-left: 12px;
            margin-bottom: 12px;
            padding: 0;
            border: none;
            outline: none;
            border-radius: 4px;
        }
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
        margin-left: 12px;
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