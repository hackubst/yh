<template>
    <div id="personalCenter">
        <!-- 左边栏 -->
        <div class="personal_info">
            <div class="user_info">
                <img class="bg_img" src="../../assets/images/img/bg_wode@2x.png" alt="">
                <div class="head_img">
                    <img :src="userInfo.headimgurl" v-if="userInfo.headimgurl">
                    <img src="@/assets/images/bg/icon_moren.png" v-else>
                </div>
                <p class="username">{{userInfo.nickname}}</p>
                <div class="user_assets">
                    <div class="user_bean">
                        <p>乐豆</p>
                        <span>{{userInfo.left_money | changeBigNum}}</span>
                    </div>
                    <div class="underline"></div>
                    <div class="user_bean">
                        <p>银行</p>
                        <span>{{userInfo.frozen_money | changeBigNum}}</span>
                    </div>
                </div>
            </div>
            <div class="user_detail">
                <div class="user_sort" v-for="(item, idx) in list" :key="idx">
                    <div class="sort_info">
                        <p>{{item.title}}</p>
                        <div></div>
                    </div>
                    <div class="sort_detail" v-if=" idx == 0" :class="{acitve: currentIndex == index}" v-for="(i, index) in top_list" :key="index" @click="jump_top(index, i.url)">
                        <img class="icon" :src="i.img" alt="">
                        <p class="label">{{i.content}}</p>
                        <img class="arrow_right" src="../../assets/images/icon/icon_16＊16@2x.png" alt="">
                    </div>
                    <div class="sort_detail" v-if=" idx == 1" :class="{acitve: current_index == index}" v-for="(i, index) in cen_list" :key="index" @click="jump_cen(index, i.url)">
                        <img class="icon" :src="i.img" alt="">
                        <p class="label">{{i.content}}</p>
                        <img class="arrow_right" src="../../assets/images/icon/icon_16＊16@2x.png" alt="">
                    </div>
                    <div class="sort_detail" v-if=" idx == 2" :class="{acitve: current_Index == index}" v-for="(i, index) in bot_list" :key="index" @click="jump_bot(index, i.url)">
                        <img class="icon" :src="i.img" alt="">
                        <p class="label">{{i.content}}</p>
                        <img class="arrow_right" src="../../assets/images/icon/icon_16＊16@2x.png" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- 右边内容 -->
        <div class="right_content">
            <!-- 二级路由 -->
            <router-view/>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from 'vuex'
export default {
  name: "personalCenter",
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  data () {
    return {
        currentIndex: -1,
        current_index: -1,
        current_Index: -1,
        list:[{
            title: "会员信息",
        },{
            title: "安全管理",
        },{
            title: "福利管理",
        }],
        top_list: [{
            img: require('../../assets/images/icon/icon_1@2x.png'),
            content: '我的资料',
            url: '/myInfo'
        },{
            img: require('../../assets/images/icon/icon_2@2x.png'),
            content: '乐豆明细',
            url: '/beanDetail'
        },{
            img: require('../../assets/images/icon/icon_3@2x.png'),
            content: '兑换记录',
            url: '/exchangeRecords'
        },{
            img: require('../../assets/images/icon/icon_4@2x.png'),
            content: '我的银行',
            url: '/myBank'
        }],
        cen_list:[{
            img: require('../../assets/images/icon/icon_5@2x.png'),
            content: '密码修改',
            url: '/pwdModify'
        },{
            img: require('../../assets/images/icon/icon_6@2x.png'),
            content: '登录验证设置',
            url: '/loginTest'
        },{
            img: require('../../assets/images/icon/icon_7@2x.png'),
            content: '登录地区限制',
            url: '/loginLimit'
        },{
            img: require('../../assets/images/icon/icon_8@2x.png'),
            content: '用户中心验证设置',
            url: '/userTest'
        }],
        bot_list: [{
            img: require('../../assets/images/icon/icon_9@2x.png'),
            content: '每日救济',
            url: '/dailyRelief'
        },{
            img: require('../../assets/images/icon/icon_10@2x.png'),
            content: '排行榜奖励',
            url: '/listAwards'
        },{
            img: require('../../assets/images/icon/icon_11@2x.png'),
            content: '推广收益',
            url: '/income'
        },{
            img: require('../../assets/images/icon/icon_12@2x.png'),
            content: '我的红包',
            url: '/myPacket'
        },{
            img: require('../../assets/images/icon/icon_13@2x.png'),
            content: '领取返利',
            url: '/rebate'
        },]
    }
  },
  created() {
     this.refreshUserInfo();
  },
  methods: {
    jump_top(index, url) {
        if (localStorage.show == 'true') {
            this.currentIndex = index
            this.current_index = -1
            this.current_Index = -1
            this.$router.push({
                path: url
            })
        }
    },
    jump_cen(index, url) {
        if (localStorage.show == 'true') {
            this.current_index = index
            this.currentIndex = -1
            this.current_Index = -1
            this.$router.push({
                path: url
            })
        }
    },
    jump_bot(index, url) {
        if (localStorage.show == 'true') {
            this.current_Index = index
            this.current_index = -1
            this.currentIndex = -1
            this.$router.push({
                path: url
            })
        }
    },
    ...mapActions([
      "refreshUserInfo"
    ])
  }
}
</script>

<style scoped lang='less'>
    #personalCenter{
        .wh(920px, auto);
        overflow: hidden;
        margin: 0 auto;
        display: flex;
        justify-content: flex-start;
        align-items: flex-start;
        .personal_info{
            .wh(260px, auto);
            margin-top: 30px;
            margin-right: 20px;
            .user_info{
                .wh(260px, 179px);
                margin-bottom: 10px;
                position: relative;
                overflow: hidden;
                .bg_img{
                    .wh(100%, 100%);
                    position: absolute;
                    top: 0;
                    z-index: -1;
                }
                .head_img{
                    .wh(50px, 50px);
                    margin: 14px auto 6px auto;
                    // background: url("")
                    img{
                        .wh(100%, 100%);
                    }
                }
                .username{
                    width: 100%;
                    text-align: center;
                    .sc(16px, #FFF8EF);
                    margin-bottom: 27px;
                }
                .user_assets{
                    .wh(100%, auto);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    .user_bean{
                        .wh(129.5px, 42px);
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        p{
                            .sc(14px, #FFF8EF);
                            line-height: 20px;
                        }
                        span{
                            .sc(14px, #FED093);
                            line-height: 20px;
                        }
                    }
                    .underline{
                        width:1px;
                        height:20px;
                        background:rgba(121,107,80,1);
                    }
                }
            }
            .user_detail{
                .wh(230px, 632px);
                margin-bottom: 55px;
                background: -webkit-linear-gradient( #FFD194,#D1913C); /* Safari 5.1 - 6.0 */
                background: -o-linear-gradient( #FFD194,#D1913C); /* Opera 11.1 - 12.0 */
                background: -moz-linear-gradient( #FFD194,#D1913C); /* Firefox 3.6 - 15 */
                background: linear-gradient( #FFD194,#D1913C); /* 标准的语法 */
                border-radius: 8px;
                padding: 20px 15px;
                .user_sort{
                    .wh(100%, auto);
                    &:first-child .sort_info{
                        margin-top: 0;
                    }
                    .sort_info {
                        margin-top: 20px;
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        p{
                            .sc(20px, #fff);
                            margin-right: 12px;
                        }
                        div{
                            width:138px;
                            height:1px;
                            background:linear-gradient(270deg,rgba(255,252,249,0) 0%,rgba(255,248,239,1) 100%);
                        }
                    }
                    .sort_detail{
                        .wh(100%, auto);
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        position: relative;
                        margin-top: 21px;
                        cursor: pointer;
                        .icon{
                            .wh(20px, 20px);
                            margin-right: 20px;
                        }
                        .label{
                            .sc(16px, #FFF8EF);
                        }
                        .arrow_right{
                            .wh(16px, 16px);
                            position: absolute;
                            right: 0;
                        }
                    }
                    .acitve{
                        position: relative;
                        &:after{
                            content: ' ';
                            width:4px;
                            height:28px;
                            background:rgba(177,114,29,1);
                            position: absolute;
                            left: -15px;
                            top: 50%;
                            margin-top: -14px;
                        }
                    }
                }
            }
        }
        .right_content{
            .wh(639px, auto);
            margin-top: 30px;
        }
    }
</style>