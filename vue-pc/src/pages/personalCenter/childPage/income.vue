<template>
    <div id="listAwards">
        <headBar head_title="推广收益" head_pro="查看推广收益"></headBar>
        <div class="form_list">
            <p class="title">收益统计</p>
            <div class="table">
                <div class="head_top">
                    <p>用户名称</p>
                    <p>创建时间</p>
                    <p>获得收益</p>
                </div>
                <ul class="list">
                    <li class="list_info" v-for="(item, index) in list" :key="index">
                        <p class="user">{{item.nickname}}({{item.id}})</p>
                        <p class="time">{{item.addtime | formatDateYearStyle}}</p>
                        <p class="num">{{item.reward | changeBigNum}}</p>
                    </li>
                </ul>
            </div>
            <!--分页按钮  -->
            <div class="paging_box" v-if="total!= 0">
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
  created() {
      this.get_income_list()
  },
  methods: {
    // 分页功能
    changePage(page) {
        this.page = page
        this.firstRow = this.fetchNum*(page-1)
        this.get_income_list()
    },
    get_income_list() {
        this.$Api({
            api_name: 'kkl.index.recommendList',
            firstRow: this.firstRow,
            fetchNum: this.fetchNum
        }, (err, data) => {
            if (!err) {
                this.list = data.data.invite_log_list
                this.total = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    }
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
                            margin-left: 64px;
                        }
                        &:nth-of-type(2) {
                            margin-left: 129px;
                        }
                        &:last-child{
                            margin-left: 129px;
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
                        .user{
                            .wh(200px, auto);
                            text-align: center;
                            .sc(14px, #4A4130);
                        }
                        .time{
                            .wh(200px, auto);
                            text-align: center;
                            .sc(14px, #4A4130);
                        }
                        .num{
                            .wh(200px, auto);
                            text-align: center;
                            .sc(14px, #D1913C);
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