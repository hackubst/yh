<template>
    <div id="myPacket">
        <headBar head_title="我的红包" head_pro="发起红包,收获红包查询"></headBar>
        <div class="form_list">
            <ul class="tab_nav">
                <li :class="{active: current_index == index}" v-for="(item, index) in time_list" :key="index" @click="changeIndex(index)">{{item.title}}</li>
            </ul>
            <div class="lanch_packet" v-if="current_index == 0">
                <div class="boolear">
                    <p class="title_boolear">当前银行余额：</p>
                    <div class="content_boolear">{{userInfo.frozen_money | changeBigNum}}</div>
                </div>
                <div class="boolear">
                    <p class="title_boolear">选择红包类型：</p>
                    <ul class="open_boolear">
                        <li v-for="(item, index) in type_list" :key="index" @click="change_index(index)">
                            <img v-if="currentIndex != index" src="../../../assets/images/icon/icon_weixuanze@2x.png" alt="">
                            <img v-if="currentIndex == index" src="../../../assets/images/icon/icon_xuanze@2x.png" alt="">
                            <p>{{item.title}}</p>
                        </li>
                    </ul>
                </div>
                <div class="boolear">
                    <p class="title_boolear">红包总金额：</p>
                    <div class="money_box" :class="{blank: money!=''}">
                        <input type="text" v-model="money">
                        <p>金豆</p>
                    </div>
                </div>
                <div class="boolear">
                    <p class="title_boolear">红包数量：</p>
                    <input class="input_text" type="text" :class="{blank: count!=''}" v-model="count">
                </div>
                <div class="boolear">
                    <p class="title_boolear">短信验证码：</p>
                    <div class="message_box">
                        <input type="text" :class="{blank: code!=''}" v-model="code">
                        <div v-if="!sendAuthCode" @click="get_verify()">获取验证码</div>
                        <div v-if="sendAuthCode">{{auth_time}}s后重新获取</div>
                    </div>
                </div>
                <div class="boolear">
                    <p class="title_boolear">红包标题：</p>
                    <input class="input_text" type="text" :class="{blank: title!=''}" v-model="title" placeholder="恭喜发财，大吉大利">
                </div>
                <div class="confirm" @click="produce()">生成红包</div>
            </div>
            <getPacket v-if="current_index == 1" :list="list"></getPacket>
            <sendPacket v-if="current_index == 2" :send_list="send_list" @cancle="cancle" @copy="copy"></sendPacket>
            <!--分页按钮  -->
            <div class="paging_box" v-if="current_index == 1 && total !=0">
                <el-pagination 
                    @current-change="changePage"
                    :page-size="15"
                    :current-page="page"
                    layout="prev, pager, next" 
                    :total="total" 
                    prev-text="上一页" 
                    next-text="下一页">
                </el-pagination>
            </div>
            <div class="paging_box" v-if="current_index == 2 && totals != 0">
                <el-pagination 
                    @current-change="change_page"
                    :page-size="10"
                    :current-page="page"
                    layout="prev, pager, next" 
                    :total="totals" 
                    prev-text="上一页" 
                    next-text="下一页">
                </el-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import {formCheck} from '@/config/checkout'
import headBar from '../../../components/headBar/index'
import getPacket from '../../../components/table/getPacket'
import sendPacket from '../../../components/table/sendPacket'
export default {
  name: "myPacket",
  components: {
      headBar,
      getPacket,
      sendPacket
  },
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  data () {
    return {
        money: '', // 红包金额
        count: '', // 红包数量
        code: '', // 短信验证码
        title: '', // 红包标题
        time_list: [{
            title: '发起红包'
        },{
            title: '收到的红包'
        },{
            title: '发起的红包'
        }],
        type_list:[{
            title: '普通红包'
        },{
            title: '手气红包'
        }],
        current_index: 0,
        currentIndex: 0,
        list: [],
        send_list: [],
        firstRow: 0,
        fetchNum: 10,
        fetch_num: 15,
        total: 0,
        totals: 0,
        page: 1,
        sendAuthCode: false,
        auth_time: 0,
    }
  },
  methods: {
    // 头部切换
    changeIndex(index) {
        this.current_index = index
        if (index == 1) {
            this.get_receive_list()
        } else if (index== 2) {
            this.get_send_list()
        }
    },
    // 红包类型
    change_index(index) {
        this.currentIndex = index
    },
    // 分页功能
    changePage(page) {
        this.page = page
        this.firstRow = this.fetch_num*(page-1)
        this.get_receive_list()
    },
    change_page(page) {
        this.page = page
        this.firstRow = this.fetchNum*(page-1)
        this.get_send_list()
    },
    // 获取领取的红包列表
    get_receive_list() {
        this.$Api({
            api_name: 'kkl.user.packetLogList',
            firstRow: this.firstRow,
            fetchNum: this.fetch_num,
        }, (err, data) => {
            if (!err) {
                this.list = data.data.red_packet_log_list
                this.total = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 获取发送的红包列表
    get_send_list() {
        this.$Api({
            api_name: 'kkl.user.redPacketList',
            firstRow: this.firstRow,
            fetchNum: this.fetchNum,
        }, (err, data) => {
            if (!err) {
                this.send_list = data.data.red_packet_list
                this.totals = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 发送验证码
    get_verify() {
        this.$Api({
            api_name: 'kkl.user.sendVerifyCode',
            mobile: this.userInfo.mobile
        }, (err, data) => {
            if (!err) {
                this.$msg(data.data, 'success', 1500)
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
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 生成红包 
    produce() {
        let checkItem = [{
            reg: 'noData',
            val: this.money,
            errMsg: '金额不能为空'
        }, {
            reg: 'noData',
            val: this.count,
            errMsg: '数量不能为空'
        }, {
            reg: 'noData',
            val: this.code,
            errMsg: '短信验证码不能为空'
        },{
            reg: 'noData',
            val: this.title,
            errMsg: '标题不能为空'
        }]
        if (formCheck(checkItem).result) {
            this.$Api({
              api_name: 'kkl.user.sendRedPacket',
              type: this.currentIndex + 1,
              total_money: this.money,
              num: this.count,
              code: this.code,
              title: this.title
            }, (err, data) => {
              if (!err) {
                this.count = ''
                this.money = ''
                this.code = ''
                this.title = ''
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
    // 撤销红包
    cancle(id) {
        this.$Api({
            api_name: 'kkl.user.cancelRedPacket',
            red_packet_id: id
        }, (err, data) => {
            if (!err) {
                this.$msg(data.data, 'success', 1500)
                this.get_send_list()
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
    // 复制红包链接
    copy(jiami_id) {
        let url = `${window.location.host}/#/index?jiami_id=${jiami_id}`
        let _this = this
        this.$copyText(url).then(function (e) {
          _this.$msg('复制成功', 'success', 1500)
        }, function (e) {
          _this.$msg('复制失败', 'error', 1500)
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
    #myPacket{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background:rgba(245,245,245,1);
            border-radius:8px;
            overflow: hidden;
            .tab_nav{
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin: 20px 0 20px 20px;
                li{
                    .wh(112px, 41px);
                    margin-right: 20px;
                    background-color: #E8E8E8;
                    text-align: center;
                    line-height: 41px;
                    .sc(18px, #4A4130);
                }
                .active{
                    background-color: #D1913C;
                    .sc(18px, #FFF8EF);
                }
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
                .money_box{
                    .wh(322px, 54px);
                    border-radius:4px;
                    border:1px solid #ccc;
                    position: absolute;
                    left: 297px;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    box-sizing: border-box;
                    input{
                        .wh(260px, 54px);
                        margin-right: 10px;
                        text-indent: 14px;
                        .sc(18px, #4A4130);
                    }

                    p{
                        .sc(18px, #D1913C);
                    }
                }
                .input_text{
                    .wh(322px, 54px);
                    border-radius:4px;
                    border:1px solid #ccc;
                    position: absolute;
                    left: 297px;
                    text-indent: 14px;
                    .sc(18px, #4A4130);
                    &::-webkit-input-placeholder{
                        color: #cccccc;
                    }
                    &:focus{
                        border:1px solid rgba(209,145,60,1);
                    }
                }
                .blank{
                    border:1px solid rgba(209,145,60,1);
                }
                .message_box{
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    .wh(322px, 54px);
                    position: absolute;
                    left: 297px;
                    input{
                        .wh(190px, 54px);
                        .sc(18px, #4A4130);
                        text-indent: 14px;
                        border:1px solid #ccc;
                        box-sizing: border-box;
                        border-radius:4px;
                        &:focus{
                            border:1px solid rgba(209,145,60,1);
                        }
                    }
                    .blank{
                        border:1px solid rgba(209,145,60,1);
                    }
                    div{
                        width:122px;
                        height:54px;
                        background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                        border-radius:4px;
                        text-align: center;
                        line-height: 54px;
                        .sc(16px, #FFF8EF);
                        cursor: pointer;
                    }
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
                margin-bottom: 20px;
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
        .paging_box {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 15px auto;
        }
    }
</style>