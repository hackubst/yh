<template>
    <div id="dailyRelief">
        <headBar head_title="每日救济" head_pro="每日可领取系统救济!"></headBar>
        <div class="form_list">
            <p class="title">救济条件</p>
            <div class="table">
                <div class="head_top">
                    <p>等级</p>
                    <p>经验</p>
                    <p>救济乐豆</p>
                </div>
                <ul class="list">
                    <li class="list_info" v-for="(item, index) in list" :key="index">
                        <img class="level_img" :src="item.img"  alt="">
                        <div class="ex_info">
                            <span>{{item.min_exp}}</span>
                            <div></div>
                            <span>{{item.max_exp}}</span>
                        </div>
                        <p class="bean_num">{{item.sign_reward}}</p>
                    </li>
                </ul>
                <div class="confirm" @click="receive()">领取救济</div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import headBar from '../../../components/headBar/index'
export default {
  name: "dailyRelief",
  components: {
      headBar
  },
  data () {
    return {
        list: []
    }
  },
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  created() {
      this.get_level_list()
  },
  methods: {
    // 获取等级列表
    get_level_list() {
        this.$Api({
            api_name: 'kkl.user.levelList'
        }, (err, data) => {
            if (!err) {
                this.list = data.data.level_list
                for(var i = 0; i<data.data.level_list.length; i++) {
                    this.$set(this.list[i], 'img', require('../../../assets/images/icon/icon_lv' +i+ '@2x.png'))
                }
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 领取救济
    receive() {
        this.$Api({
            api_name: 'kkl.user.getRelief'
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
    },
    ...mapMutations({
        setUser: 'SET_USER',
        delUser: 'DEL_USER'
    })
  }
}
</script>

<style scoped lang='less'>
    #dailyRelief{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background:rgba(245,245,245,1);
            border-radius:8px;
            overflow: hidden;
            .title{
                margin: 17px 0 10px 20px;
                font-size:18px;
                color:rgba(74,65,48,1);
                line-height:25px;
            }
            .table{
                .wh(600px, 514px);
                background-color: #fff;
                margin: 0 auto;
                .head_top{
                    .wh(100%, 50px);
                    background-color: #E8E8E8;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    p{
                        font-size:18px;
                        color:rgba(74,65,48,1);
                        &:first-child{
                            margin-left: 64px;
                        }
                        &:nth-of-type(2) {
                            margin-left: 183px;
                        }
                        &:last-child{
                            margin-left: 157px;
                        }
                    }
                }
                .list{
                    .wh(100%, auto);
                    .list_info{
                        .wh(100%, 40px);
                        border-bottom: 1px solid #E8E8E8;
                        box-sizing: border-box;
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        position: relative;
                        .level_img{
                            .wh(63px, 32px);
                            margin-left: 50px;
                        }
                        .ex_info{
                            width: 260px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            margin-left: 62px;
                            position: relative;
                            span{
                                .sc(14px, #4A4130);
                                font-family:PingFangSC-Regular;
                                font-weight:400;
                                position: absolute;
                                &:first-child{
                                    right: 212px;
                                }
                                &:last-child{
                                    left: 215px;
                                }
                            }
                            div{
                                width:162px;
                                height:10px;
                                background:linear-gradient(270deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                                border-radius:8px;
                                margin-left: 2px;
                                margin-right: 2px;
                                position: absolute;
                                left: 48px;
                            }
                        }
                        .bean_num{
                            width: 25px;
                            text-align: center;
                            .sc(14px, #4A4130);
                            font-family:PingFangSC-Regular;
                            font-weight:400;
                            position: absolute;
                            right: 74px;
                        }
                    }
                }
                .confirm{
                    margin: 40px auto;
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
        
    }
</style>