<template>
    <div id="myInfo">
        <headBar head_title="我的资料"></headBar>
        <div class="form_list">
            <div class="top_part">
                <ul class="info_detail">
                    <li>
                        <p>昵称：<span>6-15个字符以内</span></p>
                        <input type="text" :class="{blank: realname!=''}" v-model="realname">
                    </li>
                    <li>
                        <p>手机号：<span>11位手机号码</span></p>
                        <input type="number" :disabled ="mobile!=''" :class="{disabled: mobile!=''}" v-model="mobile">
                    </li>
                </ul>
                <div class="user_img">
                    <img :src="user_img" alt="">
                    <div>更换头像</div>
                    <input ref="upload" type="file" @change="change_img($event)" accept="image/*">
                </div>  
            </div>  
            <ul class="center_part">
                <li>
                    <p>邮箱：<span>输入有效的邮箱地址</span></p>
                    <input type="text" :disabled ="disable_boolean" :class="{disabled: disable_boolean}" v-model="email">
                </li>
                <li>
                    <p>收款人姓名：<span>必须与收款的支付宝姓名一致，填写后不能再修改</span></p>
                    <input type="text" :class="{blank: username!=''}" v-model="username">
                </li>
                <li>
                    <p>QQ号码：<span>输入有效的QQ号码</span></p>
                    <input type="text" :class="{blank: number!=''}" v-model="number">
                </li>
                <li>
                    <p>微信：<span>绑定微信后收验证码更方便</span></p>
                    <img :src="wx_img"/>
                </li>
            </ul>
            <div class="confirm" @click="confirm()">确定</div>
        </div> 
    </div>
</template>

<script>
import axios from "axios";
import { mapGetters, mapMutations } from 'vuex'
import { formCheck } from '@/config/checkout'
import headBar from '../../../components/headBar/index'
export default {
  name: "myInfo",
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
        realname: '', // 昵称
        user_img: '',
        mobile: '',  // 手机号
        email: '', // 邮箱
        username: '', // 收款人姓名
        number: '', // QQ号
        disable_boolean: false, // 是否有邮箱
        wx_img: '',// 微信
        token: '',
        domain: '',
    }
  },
  created() {
    if (this.userInfo.email) {
        this.disable_boolean = true
    }
    this.realname = this.userInfo.nickname
    this.email = this.userInfo.email
    this.mobile = this.userInfo.mobile
    this.user_img = this.userInfo.headimgurl
    this.username = this.userInfo.alipay_account_name
    this.number = this.userInfo.qq
    this.wx_img = this.userInfo.wx_account
    this.get_token()
  },
  methods: {
    get_token() {   
        this.$Api({
            api_name: 'kkl.index.getQiniuToken',
        }, (err, data) => {
            if (!err) {
                this.token = data.data.token
                this.domain = data.data.image_domain
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },  
    // 跟换头像
    change_img(e) {
        if (this.$refs.upload.files[0]) {
            let self = this
            const axiosInstance = axios.create({withCredentials: false})
            var data = new FormData();
            data.append("token",this.token);
            data.append("file", this.$refs.upload.files[0]);
            axiosInstance({
                method: "POST",
                url: "http://upload.qiniu.com/",
                data: data,
            }).then(function(res) {
                let { hash, key } = res.data
                self.user_img = `${self.domain}${key}?imageView2/1/w/114/h/114`
                }).catch(function(err) {
                console.log("err", err);
            });
        }
    },
    // 修改用户信息
    confirm() {
        let checkItem = [{
          reg: 'noData',
          val: this.realname,
          errMsg: '请输入正确的昵称'
        }, {
          reg: 'mobile',
          val: this.mobile,
          errMsg: '请输入正确的手机号'
        },{
          reg: 'noData',
          val: this.email,
          errMsg: '请输入正确的邮箱号'
        },{
          reg: 'qq',
          val: this.number,
          errMsg: '请输入正确的qq号'
        },{
          reg: 'noData',
          val: this.username,
          errMsg: '请输入正确的收款人'
        },]
        if (formCheck(checkItem).result) {
            this.$Api({
                api_name: 'kkl.user.editUserInfo',
                nickname: this.realname,
                qq: this.number,
                wx_account: this.wx_img,
                headimgurl: this.user_img,
                alipay_account_name: this.username,
                email: this.email,
                mobile: this.mobile
            }, (err, data) => {
                if (!err) {
                    this.$msg(data.data, 'success', 1500)
                    this.$Api({
                        api_name: 'kkl.user.getUserInfo',
                    }, (erra, res) => {
                        if (!erra) {
                            this.setUser(res.data)
                        } else {
                            this.$msg(err.error_msg, 'error', 1500)
                        }
                    })
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        } else {
            this.$msg(formCheck(checkItem), 'error', 1500)
        }
    },
    ...mapMutations({
        setUser: 'SET_USER',
        delUser: 'DEL_USER'
    })
  }
}
</script>

<style scoped lang='less'>
    #myInfo{
        .wh(100%,auto);
        .form_list{
            .wh(100%, auto);
            background-color: #F5F5F5;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 50px;
            .top_part{
                margin-left: 20px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                margin-top: 17px;
                .info_detail{
                    .wh(405px, auto);
                    margin-right: 70px;
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
                        .disabled{
                            background-color: #E8E8E8;
                            border: none;
                            color: #bdbdbd;
                        }
                    }
                }
                .user_img{
                    .wh(114px, auto);
                    position: relative;
                    img{
                        .wh(114px, 114px);
                        margin-bottom: 26px;
                    }
                    div{
                        width:100px;
                        height:41px;
                        background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                        border-radius:4px;
                        .sc(18px, #fff);
                        text-align: center;
                        line-height: 41px;
                        margin: 0 auto;
                    }
                    input{
                        width:100px;
                        height:41px;
                        position: absolute;
                        opacity: 0;
                        bottom: 0;
                        left: 50%;
                        margin-left: -50px;
                        cursor: pointer;
                    }
                }
            }
            .center_part{
                margin-left: 20px;
                .wh(599px, auto);
                margin-bottom: 20px;
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
                        .wh(599px, 54px);
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
                    .disabled{
                        background-color: #E8E8E8;
                        border: none;
                        color: #bdbdbd;
                    }
                    img{
                        .wh(128px, 128px);
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