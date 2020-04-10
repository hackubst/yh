<template>
    <div id="exchangeRecords">
        <headBar head_title="兑奖记录"></headBar>
        <div class="form_list">
            <div class="title">兑换记录：<span>可筛选时间查看</span></div>
            <ul class="tab_nav_first">
                <li :class="{active: current_index == index}" v-for="(item, index) in time_list" :key="index" @click="changeIndex(index)">{{item.title}}</li>
            </ul>
            <tableChange :table_list="table_list" @view_secret="view_secret"></tableChange>
            <!--分页按钮  -->
            <div class="paging_box" v-if="total!=0">
                <el-pagination 
                    @current-change="changePage"
                    :page-size="5"
                    :current-page="page"
                    layout="prev, pager, next" 
                    :total="total" 
                    prev-text="上一页" 
                    next-text="下一页">
                </el-pagination>
            </div>
            <div class="title">我的点卡：<span>可筛选时间查看</span></div>
            <div class="tab_info">
                <ul class="tab_nav">
                    <li :class="{active: currentIndex == index}" v-for="(item, index) in time_list" :key="index" @click="change_index(index)">{{item.title}}</li>
                </ul>
                <p v-clipboard:copy="copy_list" v-clipboard:success="onCopy" v-clipboard:error="onError">复制选择的卡密</p>
            </div>
            <pointCard :card_list="card_list" :check_boolear="check_boolear" @all_check='all_check()' @check="check"></pointCard>
            <!--分页按钮  -->
            <div class="paging_box" v-if="totals != 0">
                <el-pagination 
                    @current-change="change_page"
                    :page-size="5"
                    :current-page="pages"
                    layout="prev, pager, next" 
                    :total="totals" 
                    prev-text="上一页" 
                    next-text="下一页">
                </el-pagination>
            </div>
        </div>
        <el-dialog :visible.sync="dialogVisible" width="460px" :show-close="boolear">
            <div class="alert">
                <p class="title">{{card_name}} {{num}}张</p>
                <p class="time">生成时间：{{addtime | formatDateYearTime}}</p>
                <div class="card_secret">
                    <div v-for="(item, index) in secret_list" :key="index">{{item}}</div>
                </div>
                <div class="btn" v-clipboard:copy="secret_list" v-clipboard:success="onCopy" v-clipboard:error="onError">复制</div>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import headBar from '../../../components/headBar/index'
import tableChange from '../../../components/table/tableChange'
import pointCard from '../../../components/table/pointCard'
export default {
  name: "exchangeRecords",
  components: {
      headBar,
      tableChange,
      pointCard
  },
  data () {
    return {
        boolear: false, // 查看卡密弹出框
        time_list: [{
            title: '7天'
        },{
            title: '30天'
        },{
            title: '半年'
        },{
            title: '一年'
        }], // 时间选择列表
        current_index: 0, // 索引
        currentIndex: 0, // 索引
        firstRow: 0, 
        first_row: 0,
        fetchNum: 5,
        page: 1,
        total: 0,
        pages: 1,
        totals: 0,
        table_list: [], // 兑换记录列表
        card_list:[], // 我的点卡列表
        dialogVisible: false, // 判断是否弹框
        card_name: '', // 卡密标题
        num: '', // 卡密数量
        addtime: '', // 卡密时间
        check_boolear: false, // 是否全选
        secret_list: [], // 卡密列表
        copy_list: [], // 选择的卡密列表
    }
  },
  created() {
    this.get_exchange_list()
    this.get_card_list()
  },
  methods: {
    // 分页功能
    changePage(page) {
        this.page = page
        this.firstRow = this.fetchNum*(page-1)
        this.get_exchange_list()
    },
    // 切换兑换记录时间
    changeIndex(index) {
        this.current_index = index
        this.firstRow = 0
        this.page = 1
        this.get_exchange_list()
    },
    // 获取兑换记录列表
    get_exchange_list() {
        this.$Api({
            api_name: 'kkl.user.userGiftList',
            firstRow: this.firstRow,
            fetchNum: this.fetchNum,
            type: this.current_index + 1
        }, (err, data) => {
            if (!err) {
                this.table_list = data.data.user_gift_list
                this.total = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 切换我的点卡时间
    change_index(index) {
        this.currentIndex = index
        this.first_row = 0
        this.pages = 1
        this.get_card_list()
    },
    // 全选
    all_check() {
        this.check_boolear = !this.check_boolear
        this.copy_list = []
        if (this.check_boolear == true) {
            this.card_list.map((item, index) => {
                item.check = true
                this.copy_list.push(item.card_password)
            })
        } else {
            this.card_list.map((item, index) => {
                item.check = false
            })
        }       
    },
    // 单选
    check(index) {
        let boolean = this.card_list[index].check
        let pwd = this.card_list[index].card_password
        if (boolean == true) {
            this.card_list[index].check = false
            this.copy_list.splice(index,1)
        } else  {
            this.card_list[index].check = true
            this.copy_list.push(pwd)
        }
    },
    // 点卡分页
    change_page(page) {
        this.pages = page
        this.first_row = this.fetchNum*(page-1)
        this.get_card_list()
    },
    // 获取我的点卡列表
    get_card_list() {
        this.$Api({
            api_name: 'kkl.user.myGiftList',
            firstRow: this.first_row,
            fetchNum: this.fetchNum,
            type: this.currentIndex + 1
        }, (err, data) => {
            if (!err) {
                this.card_list = data.data.user_gift_list
                data.data.user_gift_list.map((item, index) => {
                    this.$set(this.card_list[index], 'check', false)
                })
                this.totals = Number(data.data.total)
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    // 查看卡密
    view_secret(item) {
        this.$Api({
            api_name: 'kkl.user.giftPasswordInfo',
            user_gift_id: item.user_gift_id
        }, (err, data) => {
            if (!err) {
                this.card_name = data.data.card_name
                this.num = data.data.number
                this.addtime = data.data.addtime
                this.secret_list = data.data.card_password
                this.dialogVisible = true
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    onCopy() {
        this.dialogVisible = false
        this.$msg('复制成功', 'success', 1500)
    },
    onError() {
        this.$msg('复制失败', 'error', 1500)
    }
  },
  // 监听是否全选
  watch: {
    card_list: {
        handler(value) {
            let count = 0
            value.map((item) => {
                if (item.check == true) {
                    count++
                }
            })
            if (count == value.length && value.length != 0) {
                this.check_boolear = true
            } else {
                this.check_boolear = false
            }
        },
        deep: true
    }
  }
}
</script>

<style scoped lang='less'>
    #exchangeRecords{
        .wh(100%,auto);
        .form_list{
            .wh(100%, 796px);
            background:rgba(245,245,245,1);
            border-radius:8px;
            overflow: hidden;
            .title{
                margin: 20px 0 10px 20px;
                font-size:18px;
                color:rgba(74,65,48,1);
                line-height:25px;
                span{
                    color: #ccc;
                }
            }
            .tab_nav_first{
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
            .paging_box {
                width: 100%;
                display: flex;
                justify-content: center;
                margin: 15px auto;
            }
            .tab_info{
                .wh(600px, auto);
                display: flex;
                justify-content:space-between;
                align-items: center;
                margin-bottom: 6px;
                margin-left: 20px;
                .tab_nav{
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
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
                p{
                    .sc(14px, #D1913C);
                    cursor: pointer;
                }
            }
        }
        .alert{
            .wh(460px, 555px);
            background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
            border-radius:8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
            .title{
                font-size:18px;
                font-weight:600;
                color:rgba(255,248,239,1);
                line-height:25px;
                margin-top: 30px;
                margin-bottom: 4px;
            }
            .time{
                font-size:14px;
                color:rgba(255,248,239,1);
                line-height:20px;
                margin-bottom: 30px;
            }
            .card_secret{
                .wh(348px, 328px);
                background:rgba(255,248,239,1);
                border-radius:8px;
                padding: 12px;
                margin-bottom: 20px;
            }
            .btn{
                width:360px;
                height:56px;
                background:rgba(255,248,239,1);
                border-radius:8px;
                text-align: center;
                line-height: 56px;
                font-size:18px;
                font-weight:500;
                color:rgba(209,145,60,1);
            }
        }
    }
</style>