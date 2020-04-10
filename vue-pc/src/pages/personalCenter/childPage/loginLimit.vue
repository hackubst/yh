<template>
    <div id="loginTest">
        <headBar head_title="登录地区限制" head_pro="设置限制登录地区"></headBar>
        <div class="form_list">
            <div class="boolear">
                <p class="title_boolear">当前登录地区：</p>
                <div class="content_boolear">{{userInfo.ip_address}}</div>
            </div>
            <div class="boolear">
                <p class="title_boolear">是否开启登录地区限制？</p>
                <ul class="open_boolear">
                    <li v-for="(item, index) in list" :key="index" @click="change_index(index)">
                        <img v-if="currentIndex != index" src="../../../assets/images/icon/icon_weixuanze@2x.png" alt="">
                        <img v-if="currentIndex == index" src="../../../assets/images/icon/icon_xuanze@2x.png" alt="">
                        <p>{{item.title}}</p>
                    </li>
                </ul>
            </div>
            <div class="boolear">
                <p class="title_boolear">常用登录地区1：</p>
                <div class="content_boolear">
                    <el-cascader expand-trigger="hover" :options="options" v-model="selectedOptions1" :props="props" @change="handleChangeOne"></el-cascader>
                </div>
            </div>
            <div class="boolear">
                <p class="title_boolear">常用登录地区2：</p>
                <div class="content_boolear">
                    <el-cascader expand-trigger="hover" :options="options" v-model="selectedOptions2" :props="props" @change="handleChangeTwo"></el-cascader>
                </div>
            </div>
            <div class="confirm" @click="confirm()">确定</div>
            <div class="title">登录记录：<span>可筛选时间查看</span></div>
            <ul class="tab_nav">
                <li :class="{active: current_index == index}" v-for="(item, index) in time_list" :key="index" @click="changeIndex(index)">{{item.title}}</li>
            </ul>
            <div class="table">
                <div class="head_top">
                    <p>登录时间</p>
                    <p>IP地址</p>
                    <p>登录地区</p>
                    <p>登录状态</p>
                </div>
                <ul class="list">
                    <li class="list_info" v-for="(item, index) in table_list" :key="index">
                        <p class="time">{{item.login_time | formatDateYearTime}}</p>
                        <p class="address">{{item.ip}}</p>
                        <p class="location">{{item.ip_address}}</p>
                        <p class="state">{{item.status == 1?'登录成功':'登录失败'}}</p>
                    </li>
                </ul>
            </div>
            <!--分页按钮  -->
            <div class="paging_box" v-if="total!=0">
                <el-pagination 
                    @current-change="changePage"
                    :page-size="6"
                    :current-page="page"
                    layout="prev, pager, next" 
                    :total="total" 
                    prev-text="上一页" 
                    next-text="下一页">
                </el-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import headBar from '../../../components/headBar/index'
import District from '../../../config/yr_address_data.js'
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
        time_list: [{
            title: '7天'
        },{
            title: '30天'
        },{
            title: '半年'
        },{
            title: '一年'
        }],
        currentIndex: 0,
        current_index: 0,
        table_list: [],
        firstRow: 0,
        fetchNum: 6,
        page: 1,
        total: 0,
        selectedOptions1: [],
        selectedOptions2: [],
        options: [],
        props: {
            value: 'v',
            label: 'n',
            children: 'c'
        },
        login_limit_province_id: '', //登陆省id
        login_limit_city_id: '', //登陆市id
        another_limit_province_id: '', //第二登陆省id
        another_limit_city_id: '',  //第二登陆市id
    }
  },
  created() {
    this.options = District
    this.get_login_list()
    this.currentIndex = this.userInfo.open_login_limit
  },
  methods: {
    // 获取常用登陆地区1的省市
    handleChangeOne(value) {
        this.login_limit_province_id = value[0]
        this.login_limit_city_id = value[1]
    },
    // 获取常用登陆地区2的省市
    handleChangeTwo(value) {
        this.another_limit_province_id = value[0]
        this.another_limit_city_id = value[1]
    },
    // 是否开启登陆限制
    confirm() {
        if (this.login_limit_province_id != '' && this.another_limit_province_id != '') {
            this.$Api({
                api_name: 'kkl.user.loginAreaSwitch',
                open_login_limit: this.currentIndex,
                login_limit_province_id: this.login_limit_province_id,
                login_limit_city_id: this.login_limit_city_id,
                another_limit_province_id: this.another_limit_province_id,
                another_limit_city_id: this.another_limit_city_id
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
        } else {
            this.$msg("请选择常用登陆地区", 'error', 1500)
        }
    },
    change_index(index) {
        this.currentIndex = index
    },
    changeIndex(index) {
        this.current_index = index
        this.firstRow = 0
        this.page = 1
        this.get_login_list()
    },
    // 分页功能
    changePage(page) {
        this.page = page
        this.firstRow = this.fetchNum*(page-1)
        this.get_login_list()
    },
    // 获取登录记录列表
    get_login_list() {
        this.$Api({
            api_name: 'kkl.user.loginLog',
            firstRow: this.firstRow,
            fetchNum: this.fetchNum,
            type: this.current_index + 1
        }, (err, data) => {
            if (!err) {
                this.table_list = data.data.login_log_list
                this.total = Number(data.data.total)
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
                display: flex;
                align-items: center;
                justify-content: flex-start;
                .input_info{
                    .wh(136px, 41px);
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    margin-right: 10px;
                    border:1px solid rgba(204,204,204,1);
                    input{
                        width: 98px;
                        text-indent: 10px;
                        margin-right: 4px;
                        .sc(16px, #4A4130);
                    }
                    img{
                        .wh(24px, 20px);
                    }
                }
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
        .title{
            margin: 0px 0 10px 20px;
            font-size:18px;
            color:rgba(74,65,48,1);
            line-height:25px;
            span{
                color: #ccc;
            }
        }
        .tab_nav{
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 6px;
            margin-left: 20px;
            li{
                .wh(62px, 24px);
                margin-right: 10px;
                background-color: #E8E8E8;
                text-align: center;
                line-height: 24px;
                .sc(14px, #4A4130);
            }
            .active{
                background-color: #D1913C;
                .sc(14px, #FFF8EF);
            }
        }
        .table{
            .wh(600px, auto);
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
                        margin-left: 95px;
                    }
                    &:nth-of-type(3) {
                        margin-left: 52px;
                    }
                    &:last-child{
                        margin-left: 69px;
                    }
                }
            }
            .list{
                .wh(100%, 240px);
                background-color: #f5f5f5;
                .list_info{
                    .wh(100%, 40px);
                    border-bottom: 1px solid #E8E8E8;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    position: relative;
                    background-color: #ffffff;
                    .time{
                        .sc(14px, #4A4130);
                        margin-left: 38px;
                    }
                    .address{
                        .wh(93px, auto);
                        text-align: center;
                        margin-left: 42px;
                        .sc(14px, #4A4130);
                    }
                    .location{
                        .wh(61px, auto);
                        text-align: center;
                        margin-left: 37px;
                        .sc(14px, #4A4130);
                    }
                    .state{
                        .wh(56px, auto);
                        text-align: center;
                        margin-left: 83px;
                        .sc(14px, #4A4130);
                    }   
                }
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