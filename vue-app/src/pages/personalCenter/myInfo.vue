<template>
    <div id="myInfo">
        <div class="user_img">
            <img :src="user_img" alt="">
            <div class="change_img" @click="change_img()">更换头像</div>
            <!-- <input ref="upload" type="file" @change="change_img($event)" accept="image/*">  -->
        </div>
        <div class="user_info">
            <p class="label">游戏昵称</p>
            <input class="normal_input" type="text" placeholder="请输入游戏昵称" v-model="nickname" @blur="inpBlur()">
        </div>
        <div class="user_info">
            <p class="label">电子邮箱</p>
            <input class="normal_input" type="text" placeholder="请输入常用电子邮箱" v-model="email" @blur="inpBlur()">
        </div>
        <div class="user_info">
            <p class="label">手机号码</p>
            <input class="normal_input" type="number" placeholder="请输入手机号码" v-model="mobile" @blur="inpBlur()">
        </div>
        <div class="user_info">
            <p class="label">绑定QQ</p>
            <div class="input_box">
                <input  type="number" placeholder="请输入QQ" v-model="qq">
                <p @click="bind_again">重新绑定</p>
            </div>
        </div>
        <!-- <div class="chat_info">
            <p class="label">绑定微信</p>
            <img class="weChat" :src="wechat_img" alt="">
        </div> -->
        <div class="btn" @click="editor()">立马修改</div>
    </div>
</template>

<script>
import axios from "axios";
import { formCheck} from '@/config/checkout'
export default {
    components:{
        
    },
    name: 'myInfo',
    data () {
        return {
            user_img: '',
            nickname: '',
            email: '',
            mobile: '',
            qq: '',
            wechat_img: '',
            // token: '',
            // domain: '',
        }
    },
    created() {
        this.get_user_info()
        // this.get_token()
        window.showImg = this.showImg;
    },
    methods: {
        inpBlur(){
            document.documentElement.scrollTop = document.body.scrollTop = 0
        },
        // 获取token, domain
        // get_token() {   
        //     this.$Api({
        //         api_name: 'kkl.index.getQiniuToken',
        //     }, (err, data) => {
        //         if (!err) {
        //             this.token = data.data.token
        //             this.domain = data.data.image_domain
        //         } else {
        //             this.$msg(err.error_msg, 'cancel', 'middle', 1500)
        //         }
        //     })
        // },
        // 更换头像
        change_img() {
            // if (this.$refs.upload.files[0]) {
            //     let self = this
            //     const axiosInstance = axios.create({withCredentials: false})
            //     var data = new FormData();
            //     data.append("token",this.token);
            //     data.append("file", this.$refs.upload.files[0]);
            //     axiosInstance({
            //         method: "POST",
            //         url: "http://upload.qiniu.com/",
            //         data: data,
            //     }).then(function(res) {
            //         let { hash, key } = res.data
            //         self.user_img = `${self.domain}${key}?imageView2/1/w/64/h/64`
            //         }).catch(function(err) {
            //         console.log("err", err);
            //     });
            // }
            this.$native.nativeUploadImg(1, 1, 1);
        },
        // 上传图片的回调
        showImg(imgArr) {
            console.log('上传图片的回调', imgArr);
            this.user_img = imgArr[0];
        },
        // 重新绑定qq
        bind_again() {
            this.$router.push({
                path:'/bindTencent'
            })
        },
        // 获取个人信息
        get_user_info() {
            this.$Api({
                api_name: 'kkl.user.getUserInfo',
            }, (erra, res) => {
                if (!erra) {
                    let data= res.data
                    this.nickname = data.nickname
                    this.mobile = data.mobile 
                    this.email = data.email
                    this.user_img = data.headimgurl
                    this.qq = data.qq
                    this.wechat_img = data.wx_account
                } else {
                    this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        // 保存
        editor() {
            let checkItem = [{
                reg: 'noData',
                val: this.nickname,
                errMsg: '昵称不能为空'
            }, {
                reg: 'noData',
                val: this.qq,
                errMsg: 'QQ不能为空'
            }, {
                reg: 'noData',
                val: this.email,
                errMsg: '邮箱不能为空'
            }, {
                reg: 'noData',
                val: this.mobile,
                errMsg: '手机不能为空'
            }]
            if (formCheck(checkItem).result) {
                this.$Api({
                    api_name: 'kkl.user.editUserInfo',
                    nickname: this.nickname,
                    qq: this.qq,
                    wx_account: this.wechat_img,
                    headimgurl: this.user_img,
                    email: this.email,
                    mobile: this.mobile
                }, (err, data) => {
                    if (!err) {
                        this.$msg(data.data, 'success', 'middle', 1500)
                    } else {
                        this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                    }
                })
            } else {
                this.$msg(formCheck(checkItem), 'cancel', 'middle', 1500)
            }
        }
    },
    
}
</script>

<style scoped lang="less">
    #myInfo{
        width: 100%;
        /* height: 100%; */
        background-color: #fff;
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        .user_img{
            .wh(100%, 152px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            img{
                .wh(64px, 64px);
                border-radius: 50%;
                margin-bottom: 12px;
                border: 2px solid #4A4130;
                box-sizing: border-box;
            }
            .change_img{
                .wh(90px, 28px);
                background-color: #FED093;
                border-radius: 4px;
                font-size:14px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(74,65,48,1);
                line-height:28px;
                text-align: center;
            }
            input{
                width:90px;
                height:28px;
                position: absolute;
                opacity: 0;
                bottom: 24px;
                left: 50%;
                margin-left: -45px;
                cursor: pointer;
            }
        }
        .user_info{
            .wh(100%, 48px);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 12px;
            .label{
                width: 68px;
                font-size:16px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(74,65,48,1);
                line-height:22px;
                margin: 0 12px;
            }
            .normal_input{
                .wh(270px, 48px);
                background-color: #F2F2F2;
                border-radius: 4px;
                font-size:16px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(74,65,48,1);
                border: none;
                outline: none;
                text-indent: 12px;
                padding: 0;
            }
            .normal_input::-webkit-input-placeholder{
                color: #999;
            }
            .input_box{
                .wh(274px, 48px);
                background-color: #F2F2F2;
                border-radius: 4px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-right: 12px;
                box-sizing: border-box;
                input{
                    background-color: #F2F2F2;
                    font-size:16px;
                    font-family:PingFangSC-Medium;
                    font-weight:500;
                    color:rgba(74,65,48,1);
                    border: none;
                    outline: none;
                    text-indent: 12px;
                    width: 170px;
                    padding: 0;
                }
                input:disabled{
                    -webkit-text-fill-color: rgba(74,65,48,1);
                    opacity: 1;
                }
                input::-webkit-input-placeholder{
                    color: #999;
                    -webkit-text-fill-color: #999;
                }
                p{
                    font-size:16px;
                    font-family:PingFangSC-Medium;
                    font-weight:500;
                    color:rgba(74,65,48,1);
                    line-height:22px;
                    white-space: nowrap;
                }
            }
            
        }
        .chat_info{
            .wh(100%, auto);
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            margin-bottom: 12px;
            .label{
                width: 68px;
                font-size:16px;
                font-family:PingFangSC-Medium;
                font-weight:500;
                color:rgba(74,65,48,1);
                line-height:22px;
                margin: 0 12px;
            }
            .weChat{
                .wh(139px, 139px);
            }
        }
        .btn{
            .wh(100%, 48px);
            background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
            position: fixed;
            bottom: 0;
            font-size:18px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            line-height:48px;
            text-align: center;
        }
    }
</style>