<template>
  <div id="beanDetail">
    <headBar head_title="乐豆明细" head_pro="金豆记录明细,银行存取、充值、转账等记录"></headBar>
    <div class="form_list">
      <p class="title">
        金豆记录：
        <span>仅保留30天记录</span>
      </p>
      <div class="table">
        <div class="head_top">
          <p>时间</p>
          <p>类型</p>
          <p>数量</p>
          <p>操作后额度</p>
          <p>操作后银行余额</p>
        </div>
        <ul class="list">
          <li class="list_info" v-for="(item, index) in list" :key="index">
            <!-- <p class="time">{{item.addtime | formatDateYearTime}}</p> -->
            <p class="time">{{item.addtime_str}}</p>
            <p class="type">{{item.change_type}}</p>
            <p class="num">{{item.change | changeBigNum}}</p>
            <p class="limit">{{item.amount_after_pay | changeBigNum}}</p>
            <p class="limit">{{item.bank_money_after | changeBigNum}}</p>
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
          next-text="下一页"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
import headBar from '../../../components/headBar/index'
export default {
  name: "beanDetail",
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
  created () {
    this.get_account_list()
  },
  methods: {
    // 分页功能
    changePage (page) {
      this.page = page
      this.firstRow = this.fetchNum * (page - 1)
      this.get_account_list()
    },
    // 获取乐豆明细列表
    get_account_list () {
      this.$Api({
        api_name: 'kkl.user.accountList',
        firstRow: this.firstRow,
        fetchNum: this.fetchNum
      }, (err, data) => {
        if (!err) {
          this.list = data.data.account_list
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
#beanDetail {
  .wh(100%, auto);
  .form_list {
    .wh(100%, 796px);
    background: rgba(245, 245, 245, 1);
    border-radius: 8px;
    overflow: hidden;
    .title {
      margin: 17px 0 10px 20px;
      font-size: 18px;
      color: rgba(74, 65, 48, 1);
      line-height: 25px;
      span {
        color: #ccc;
      }
    }
    .table {
      .wh(600px, auto);
      background-color: #fff;
      margin: 0 auto;
      .head_top {
        .wh(100%, 50px);
        background-color: #e8e8e8;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        p {
          font-size: 16px;
          color: rgba(74, 65, 48, 1);
          width: 20%;
          text-align: center;
          // &:first-child {
          //   margin-left: 30px;
          // }
          // &:nth-of-type(2) {
          //   margin-left: 40px;
          // }
          // &:nth-of-type(3) {
          //   margin-left: 50px;
          // }
          // &:nth-of-type(4) {
          //   margin-left: 50px;
          // }
          // &:last-child {
          //   margin-left: 78px;
          // }
        }
      }
      .list {
        .wh(100%, 600px);
        background-color: #f5f5f5;
        .list_info {
          .wh(100%, 40px);
          border-bottom: 1px solid #e8e8e8;
          box-sizing: border-box;
          display: flex;
          justify-content: flex-start;
          align-items: center;
          background-color: #fff;
          position: relative;

          p {
            width: 20%;
            text-align: center;
          }
          .time {
            .sc(14px, #4a4130);
            // margin-left: 36px;
          }
          .type {
            // .wh(100px, auto);
            text-align: center;
            // margin-left: 23px;
            .sc(14px, #d1913c);
          }
          .num {
            // .wh(80px, auto);
            // text-align: center;
            // margin-left: 40px;
            .sc(14px, #4a4130);
          }
          .limit {
            // .wh(80px, auto);
            // text-align: center;
            // margin-left: 65px;
            .sc(14px, #4a4130);
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
}
</style>