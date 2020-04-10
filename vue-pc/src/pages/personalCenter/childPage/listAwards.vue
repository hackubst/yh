<template>
    <div id="listAwards">
        <headBar head_title="排行榜奖励" head_pro="排行榜可每日可领排行榜奖励"></headBar>
        <div class="form_list">
            <p class="title">领奖</p>
            <div class="table">
                <div class="head_top">
                    <p>日期</p>
                    <p>奖金额</p>
                    <p>领取</p>
                </div>
                <ul class="list">
                    <li class="list_info" v-for="(item, index) in list" :key="index">
                        <!-- <p class="time">{{item.addtime | formatDateYearTime}}</p> -->
                        <p class="time">{{item.addtime_str}}</p>
                        <p class="num">{{item.reward | changeBigNum}}</p>
                        <p class="btn" :class="{active: item.is_received == 0}" @click="receive()">{{item.is_received == 0? '领取': (item.is_received == 1? '已领取':'已过期')}}</p>
                    </li>
                </ul>
            </div>
            <!--分页按钮  -->
            <div class="paging_box" v-if="total != 0">
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
        </div>
    </div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex'
import headBar from '../../../components/headBar/index'
export default {
  name: "listAwards",
  components: {
      headBar
  },
  data () {
    return {
        list: [],
        firstRow: 0,
        fetchNum: 15,
        total: 0,
        page: 1,
    }
  },
  computed: {
    ...mapGetters([
        'haveLogin',
        'userInfo'
    ])
  },
  created() {
      this.get_list()
  },
  methods: {
    // 分页功能
    changePage(page) {
        this.page = page
        this.firstRow = this.fetchNum*(page-1)
        this.get_list()
    },
    // 获取排行榜奖列表
    get_list() {
        this.$Api({
            api_name: 'kkl.user.rewardList',
            firstRow: this.firstRow,
            fetchNum: this.fetchNum
        }, (err, data) => {
            if (!err) {
                this.list = data.data.rank_list_list
                this.total = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 领取奖励
    receive() {
        this.$Api({
            api_name: 'kkl.user.getReward'
        }, (err, data) => {
            if (!err) {
                this.$msg(data.data, 'success', 1500)
                this.get_list()
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
    #listAwards{
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
                            margin-left: 82px;
                        }
                        &:nth-of-type(2) {
                            margin-left: 156px;
                        }
                        &:last-child{
                            margin-left: 156px;
                        }
                    }
                }
                .list{
                    .wh(100%, 600px);
                    background-color: #f5f5f5;
                    .list_info{
                        .wh(100%, 40px);
                        border-bottom: 1px solid #E8E8E8;
                        box-sizing: border-box;
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        position: relative;
                        background-color: #fff;
                        .time{
                            .sc(14px, #4A4130);
                            margin-left: 36px;
                        }
                        .num{
                            .wh(50px, auto);
                            text-align: center;
                            margin-left: 109px;
                            .sc(14px, #D1913C);
                        }
                        .btn{
                            .wh(71px, 32px);
                            text-align: center;
                            line-height: 32px;
                            margin-left: 142px;
                            .sc(14px, #FFF8EF);
                            background:rgba(204,204,204,1);
                            border-radius:4px;
                            cursor: pointer;
                        }   
                        .active{
                            background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                        }
                    }
                }
            }
        }
        .paging_box {
            width: 100%;
            display: flex;
            justify-content: center;
            margin: 25px auto;
        }
    }
</style>