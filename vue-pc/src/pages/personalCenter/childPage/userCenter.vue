<template>
    <div id="userCenter">
        <!-- 用户中心 -->
        <div v-if="show">
            <headBar :head_title="username" :head_info="user_info"></headBar>
            <div class="user_center">
                <ul class="info_list">
                    <li class="info_detail" v-for="(item, index) in user_list" :key="index">
                        <img :src="item.bg_img" alt="">
                        <p class="title">{{item.title}}</p>
                        <p class="content">{{item.number | changeBigNum}}</p>
                    </li>
                </ul>
                <ul class="user_mode">
                    <li class="mode_info">
                        <img src="../../../assets/images/img/Mail-error@2x.png" alt="">
                        <p>{{email != ''?'已填写邮箱':'未填写邮箱'}}</p>
                        <div @click="turn_page()">{{email != ''?'查看':'去填写'}}</div>
                    </li>
                    <li class="mode_info">
                        <img src="../../../assets/images/img/iPhone@2x.png" alt="">
                        <p>{{mobile != ''?'已绑定手机':'未绑定手机'}}</p>
                        <div @click="turn_page()">{{mobile != ''?'查看':'去绑定'}}</div>
                    </li>
                </ul>
                <div class="login_info">
                    <div class="login_head">
                        <img src="../../../assets/images/img/Clock@2x.png" alt="">
                        <span>最后一次</span>
                    </div>
                    <p>登录时间</p>
                    <div class="login_time">{{login_time | formatDateYearTime}}</div>
                    <p>ip地址</p>
                    <div class="login_time">{{ip_address}}</div>
                </div>  
            </div>
        </div>
        <!-- 安全验证 -->
        <div v-if="!show">
            <headBar head_title="需要确认登录"></headBar>
            <div class="form_info">
                <p class="form_title">安全密码：<span>默认和登录密码一样</span></p>
                <input type="password" :class="{blank: pwd!=''}" v-model="pwd">
                <div class="btn" @click="view_center()">确定</div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import headBar from '../../../components/headBar/index'
import {formCheck} from '@/config/checkout'
export default {
  name: "userCenter",
  components: {
      headBar
  },
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  data () {
    return {
        user_list: [{
            bg_img: require('../../../assets/images/img/bg_ledou@2x.png'),
            title: '当前乐豆',
            number: ''
        },{
            bg_img: require('../../../assets/images/img/bg_ledou@2x.png'),
            title: '银行乐豆',
            number: ''
        },{
            bg_img: require('../../../assets/images/img/bg_jingyan@2x.png'),
            title: '当前经验',
            number: ''
        },{
            bg_img: require('../../../assets/images/img/bg_choongzhi@2x.png'),
            title: '今日充值',
            number: ''
        },{
            bg_img: require('../../../assets/images/img/bg_kuiying@2x.png'),
            title: '今日盈亏',
            number: ''
        },{
            bg_img: require('../../../assets/images/img/bg_liushui@2x.png'),
            title: '今日流水',
            number: ''
        }],
        show: false,
        pwd: '', //安全密码 
        login_time: '', // 登录时间
        ip_address: '', // ip地址
        user_info: '', // 用户信息
        email: '', // 邮箱
        mobile: '', // 手机号
        username: '', // 用户昵称
    }
  },
  created() {
    this.get_user_info()
    this.user_info = '(ID：' + this.userInfo.id + '，账号：' + this.userInfo.mobile + ')'
    if (this.userInfo.open_chenck_personal == 1) {
        localStorage.show = false
        this.show = false
    } else {
        localStorage.show = true
        this.show =  true
    }
  },
  methods: {
    // 获取用户信息
    get_user_info() {
        this.$Api({
            api_name: 'kkl.user.getUserHomeInfo'
        }, (err, data) => {
            let res = data.data
            this.user_list[0].number = res.left_money
            this.user_list[1].number = res.frozen_money
            this.user_list[2].number = res.exp
            this.user_list[3].number = res.account_info.recharge
            this.user_list[4].number = res.account_info.daily_win
            this.user_list[5].number = res.account_info.daily_flow
            this.ip_address = res.login_log_info.ip
            this.login_time = res.login_log_info.login_time
            this.username = '你好, ' + res.nickname
            this.email = res.email
            this.mobile = res.mobile
        })
    },
    // 跳转用户中心
    view_center() {
        let checkItem = [{
          reg: 'noData',
          val: this.pwd,
          errMsg: '请输入密码'
        }]
        if (formCheck(checkItem).result) {
            this.$Api({
                api_name: 'kkl.user.checkPassword',
                safe_password: this.$MD5.hex_md5(this.pwd),
            }, (err, data) => {
                if (!err) {
                    this.show = !this.show
                    localStorage.show = this.show
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        } else  {
            this.$msg(formCheck(checkItem), 'error', 1500)
        }
    },
    // 跳转我的资料
    turn_page() {
        this.$router.push({
            path: '/myInfo',
        })
    }
  }
}
</script>

<style scoped lang='less'>
    #userCenter{
        .wh(100%,auto);
        .user_center{
            .wh(100%, auto);
            .info_list{
                .wh(490px, auto);
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-start;
                align-items: center;
                .info_detail{
                    .wh(150px, 78px);
                    margin-right: 20px;
                    margin-bottom: 20px;
                    border-radius: 8px;
                    position: relative;
                    &:nth-of-type(3n) {
                        margin-right: 0;
                    }
                    img{
                        .wh(100%, 100%);
                        position: absolute;
                        z-index: -1;
                    }
                    .title{
                        margin-top: 10px;
                        margin-left: 14px;
                        .sc(14px, #4A4130);
                        margin-bottom: 15px;
                    }
                    .content{
                        margin-left: 14px;
                        .sc(20px, #D1913C);
                    }
                }
            }
            .user_mode{
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin-bottom: 20px;
                .mode_info{
                    .wh(150px, 176px);
                    background-color: #F5F5F5;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: center;
                    margin-right: 20px;
                    border-radius: 8px;
                    img{
                        .wh(70px, 70px);
                        margin-top: 12px;
                    }
                    p{
                        font-size:18px;
                        color:rgba(74,65,48,1);
                        line-height:28px;
                        margin-bottom: 15px;
                    }
                    div{
                        .wh(90px, 32px);
                        border-radius:16px;
                        border:1px solid rgba(209,145,60,1);
                        line-height: 32px;
                        .sc(14px, rgba(209,145,60,1));
                        text-align: center;
                        cursor: pointer;
                    }
                }
            }
            .login_info{
                .wh(243px, 176px);
                background-color: #F5F5F5;
                border-radius: 8px;
                overflow: hidden;
                .login_head {
                    margin-top: 10px;
                    margin-left: 13px;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    margin-bottom: 10px;
                    img{
                        .wh(24px, 24px);
                        margin-right: 4px;
                    }
                    span{
                        .sc(14px, #4A4130);
                    }
                }
                p{
                    font-size:14px;
                    margin-left: 13px;
                    color:rgba(74,65,48,1);
                    line-height:24px;
                }
                .login_time{
                    margin-left: 13px;
                    margin-bottom: 11px;
                    font-size:20px;
                    color:rgba(209,145,60,1);
                    line-height:24px;
                }
            }
        }
        .form_info{
            .wh(100%, 796px);
            background-color: #F5F5F5;
            border-radius: 8px;
            overflow: hidden;
            .form_title{
                .sc(18px, rgba(74,65,48,1));
                margin-bottom: 10px;
                margin-left: 20px;
                margin-top: 17px;
                span{
                    .sc(18px, #ccc);
                }
            }
            input{
                .wh(405px, 54px);
                margin-bottom: 40px;
                margin-left: 20px;
                background-color: #fff;
                border-radius:4px;
                text-indent: 14px;
                border:1px solid rgba(204,204,204,1);
                font-size: 18px;
                &:focus{
                    border:1px solid rgba(209,145,60,1);
                }
            }
            .blank{
                border:1px solid rgba(209,145,60,1);
            }
            .btn{
                width:187px;
                height:56px;
                background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                border-radius:8px;
                text-align: center;
                line-height: 56px;
                .sc(18px, #fff);
                margin-left: 20px;
                cursor: pointer;
            }
        }
    }
</style>