<template>
    <div id="loginTest">
        <headBar head_title="登录验证" head_pro="设置登陆短信或邮箱验证!"></headBar>
        <div class="form_list">
            <div class="boolear">
                <p class="title_boolear">是否开启登录验证功能？</p>
                <ul class="open_boolear">
                    <li v-for="(item, index) in list" :key="index" @click="change_index(index)">
                        <img v-if="currentIndex != index" src="../../../assets/images/icon/icon_weixuanze@2x.png" alt="">
                        <img v-if="currentIndex == index" src="../../../assets/images/icon/icon_xuanze@2x.png" alt="">
                        <p>{{item.title}}</p>
                    </li>
                </ul>
            </div>
            <div class="boolear" v-if="userInfo.mobile">
                <p class="title_boolear">手机号码：</p>
                <div class="content_boolear">{{userInfo.mobile}}</div>
            </div>
            <div class="confirm" @click="confirm()">确定</div>
        </div>
    </div>
</template>

<script>
import headBar from '../../../components/headBar/index'
import { mapGetters, mapMutations } from 'vuex'
export default {
  name: "loginTest",
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
        list:[{
            title: '不开启'
        },{
            title: '开启'
        }],
        currentIndex: 0,
    }
  },
  created() {
    this.currentIndex = this.userInfo.open_chenck_login
  },
  methods: {
    change_index(index) {
        this.currentIndex = index
    },
    // 确定是否开启验证
    confirm() {
        this.$Api({
            api_name: 'kkl.user.loginSwitch',
            switch: this.currentIndex
        }, (err, data) => {
            if (!err) {
                this.$msg(data.data, 'success', 1500)
                this.$Api({
                    api_name: 'kkl.user.getUserInfo',
                }, (erra, res) => {
                    if (!erra) {
                    this.setUser(res.data)
                    } else {
                    this.$msg(erra.error_msg, 'error', 1500)
                    }
                }) 
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    ...mapMutations({
        setUser: 'SET_USER',
        delUser: 'DEL_USER'
    })
  }
}
</script>

<style scoped lang='less'>
    #loginTest{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background:rgba(245,245,245,1);
            border-radius:8px;
        }
        .boolear{
            .wh(100%, 65px);
            border-bottom: 1px solid #E8E8E8;
            box-sizing: border-box;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            position: relative;
            .title_boolear{
                margin-left: 20px;
                font-size:18px;
                color:rgba(74,65,48,1);
                line-height:25px;
            }
            .content_boolear{
                .wh(auto, 65px);
                position: absolute;
                left: 297px;
                font-size:18px;
                color:rgba(153,153,153,1);
                line-height:65px;
            }
            .open_boolear{
                .wh(auto, 65px);
                position: absolute;
                left: 297px;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                li{ 
                    .wh(auto, 65px);
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    margin-right: 40px;
                    img{
                        .wh(22px, 22px);
                        margin-right: 10px;
                    }
                    p{
                        font-size:18px;
                        color:rgba(74,65,48,1);
                        line-height:25px;
                    }
                }
            }
        }
        .confirm{
            margin-left: 20px;
            margin-bottom: 40px;
            margin-top: 40px;
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
</style>