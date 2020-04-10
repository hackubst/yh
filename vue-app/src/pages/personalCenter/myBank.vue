<template>
    <div id="myBank">
        <ul class="nav_list">
            <li class="nav_info" :class="{active: currentIndex == index}" v-for="(item, index) in list" :key="index" @click="change_index(index)">{{item.title}}</li>
        </ul>
        <div class="save_bean" v-if="currentIndex == 0">
            <div class="head_info">
                <img src="../../assets/images/icon/icon_bean@3x.png" alt="">
                <p>现有金豆</p>
                <span>{{bean | changeBigNum}}</span>
            </div>
            <input class="input_bean" type="text" placeholder="请输入您要存储的乐豆数量" v-model="bean_count">
            <div class="btn" @click="save()">存储</div>
        </div>
        <div class="save_bean" v-if="currentIndex == 1">
            <div class="head_info">
                <img src="../../assets/images/icon/icon_bean@3x.png" alt="">
                <p>可取金豆</p>
                <span>{{take_bean | changeBigNum}}</span>
            </div>
            <input class="input_bean" type="text" placeholder="请输入您要取出的乐豆数量" v-model="get_bean">
            <input class="input_bean" type="password" placeholder="请输入您的银行密码" v-model="bank_pwd">
            <div class="btn" @click="take()">取出</div>
            <div class="editor_pwd">
                <p @click="forget_pwd()">忘记银行密码</p>
                <p @click="editor_pwd()">修改银行密码</p>
            </div>
        </div>
    </div>
</template>

<script>
import { formCheck } from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'myBank',
    data () {
        return {
            list: [{
                title: '存乐豆'
            },{
                title: '取乐豆'
            }],
            currentIndex: 0,
            bean: '',
            take_bean: '',
            bean_count: '', 
            get_bean: '',
            bank_pwd: ''
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
                    let data= res.data
                    this.bean = data.left_money
                    this.take_bean = data.frozen_money
                } else {
                    this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        change_index(index) {
            this.currentIndex = index
        },
        // 存储乐豆
        save() {
            if (this.bean_count != '') {
                this.$Api({
                    api_name: 'kkl.user.accessBean',
                    type: 1,
                    number: this.bean_count
                }, (err, data) => {
                    if (!err) {
                        this.bean_count = ''
                        this.$msg(data.data, 'success', 'middle', 1500)
                        this.get_user_info()
                    } else {
                        this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                    }
                })
            } else {
                this.$msg('请填写存入数量', 'cancel', 'middle', 1500)
            }
        },
        // 取出乐豆
        take() {
            let checkItem = [{
                reg: 'noData',
                val: this.get_bean,
                errMsg: '请输入取出乐豆数量'
            }, {
                reg: 'noData',
                val: this.bank_pwd,
                errMsg: '请输入密码'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.accessBean',
                    type: 0,
                    number: this.get_bean,
                    bank_password: this.$MD5.hex_md5(this.bank_pwd),
                }, (err, data) => {
                    if (!err) {
                        this.get_bean = ''
                        this.bank_pwd = ''
                        this.$msg(data.data, 'success','middle', 1500)
                        this.get_user_info()
                    } else {
                        this.$msg(err.error_msg, 'cancel','middle', 1500)
                    }
                })
            } else {
                this.$msg(formCheck(checkItem), 'cancel', 'middle',1500)
            }
        },
        forget_pwd() {
            this.$router.push({
                path: '/retrievePwd',
                query: {
                    type: 3
                }
            })
        },
        editor_pwd() {
            this.$router.push({
                path: '/bankPwd'
            })
        }
    }
}
</script>

<style scoped>
    #myBank{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .nav_list{
        width: 100%;
        height: 48px;
        background-color: #4A4130;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .nav_info{
        width: 50%;
        height: 48px;
        line-height: 48px;
        text-align: center;
        font-size:17px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(255,237,212,1);
        position: relative;
    }
    .active{
        color: #FED093;
    }
    .active::after{
        content: '';
        width: 48px;
        height: 4px;
        background-color: #FED093;
        position: absolute;
        left: 50%;
        margin-left: -24px;
        bottom: 4px;
    }
    .save_bean {
        width: 100%;
        height: auto;
        position: absolute;
        top: 48px;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: #fff;
        overflow: hidden;
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
    .input_bean{
        width: 351px;
        height: 48px;
        background-color: #f2f2f2;
        font-size: 16px;
        color: #333;
        text-indent: 12px;
        margin-left: 12px;
        border: none;
        outline: none;
        border-radius: 4px;
        margin-bottom: 12px;
        padding: 0;
    }
    .input_bean::-webkit-input-placeholder{
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
        margin: 12px auto 0 auto;
        text-align: center;
    }
    .editor_pwd{
        width: 351px;
        margin: 12px auto 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .editor_pwd p{
        font-size:16px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(74,65,48,1);
        line-height:22px;
    }
</style>