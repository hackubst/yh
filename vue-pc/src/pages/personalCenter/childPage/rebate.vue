<template>
  <div id="rebate">
    <headBar :head_title="title" head_pro="亏损返利"></headBar>
    <div class="form_list">
      <p class="title">经验换分</p>
      <ul class="exper_list">
        <li>
          <p class="label">当前经验：</p>
          <p class="count">{{userInfo.exp}}</p>
        </li>
        <li>
          <p class="label">可兑换经验：</p>
          <p class="count">{{userInfo.more_exp}}</p>
        </li>
      </ul>
      <div class="exchange_btn" @click="exchange()">兑换</div>
      <p class="title">领取记录</p>
      <div class="table">
        <div class="head_top">
          <p>领取时间</p>
          <p>返利类型</p>
          <p>返利金额</p>
        </div>
        <ul class="list">
          <li class="list_info" v-for="(item, index) in list" :key="index">
            <!-- <p class="time">{{item.addtime | formatDateYearTime}}</p> -->
            <p class="time">{{item.addtime_str}}</p>
            <p class="type">{{item.return_type}}</p>
            <p class="number">{{item.money | changeBigNum}}</p>
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
          next-text="下一页"
        ></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
import headBar from '../../../components/headBar/index'
import { mapGetters, mapMutations } from 'vuex'
export default {
  name: "rebate",
  components: {
    headBar
  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'userInfo'
    ]),
    title () {
      return '剩余工资' + '(' +this.win_loss_left+ ')'
    }
  },
  data () {
    return {
      list: [], //列表
      firstRow: 0,
      fetchNum: 6,
      total: 0,
      page: 1,
      win_loss_left: '',
    }
  },
  created () {
    this.get_rebate_list()
    this.get_user_info()
  },
  methods: {
    // 分页功能
    changePage (page) {
      this.page = page
      this.firstRow = this.fetchNum * (page - 1)
      this.get_rebate_list()
    },
    // 兑换经验
    exchange () {
      this.$Api({
        api_name: 'kkl.user.exChangeExp'
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
    // 获取返利列表
    get_rebate_list () {
      this.$Api({
        api_name: 'kkl.user.returnLogList',
        firstRow: this.firstRow,
        fetchNum: this.fetchNum
      }, (err, data) => {
        if (!err) {
          this.list = data.data.return_log_list
          this.total = Number(data.data.total)
        } else {
          this.$msg(err.error_msg, 'error', 1500)
        }
      })
    },
    get_user_info () {
      this.$Api({
        api_name: 'kkl.user.getUserInfo'
      }, (err, data) => {
        if (!err) {
          console.log(data.data)
          this.win_loss_left = data.data.win_loss_left
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
#rebate {
  .wh(100%, auto);
  .form_list {
    .wh(100%, 796px);
    background: rgba(245, 245, 245, 1);
    border-radius: 8px;
    overflow: hidden;
    .title {
      .wh(112px, 41px);
      background-color: #d1913c;
      text-align: center;
      line-height: 41px;
      margin: 17px 0 0 20px;
      font-size: 18px;
      color: #fff8ef;
    }
    .exper_list {
      .wh(100%, auto);
      margin-bottom: 40px;
      li {
        .wh(100%, 65px);
        border-bottom: 1px solid #e8e8e8;
        box-sizing: border-box;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        position: relative;
        .label {
          margin-left: 20px;
          .sc(18px, #4a4130);
        }
        .count {
          position: absolute;
          left: 297px;
          .sc(18px, #999999);
        }
      }
    }
    .exchange_btn {
      .wh(187px, 56px);
      background: linear-gradient(
        360deg,
        rgba(209, 145, 60, 1) 0%,
        rgba(255, 209, 148, 1) 100%
      );
      border-radius: 8px;
      font-size: 18px;
      font-weight: 500;
      color: rgba(255, 255, 255, 1);
      line-height: 56px;
      text-align: center;
      margin-left: 20px;
      margin-bottom: 38px;
      cursor: pointer;
    }
    .table {
      .wh(600px, auto);
      background-color: #fff;
      margin: 0 auto;
      margin-top: 20px;
      .head_top {
        .wh(100%, 50px);
        background-color: #e8e8e8;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        p {
          font-size: 18px;
          color: rgba(74, 65, 48, 1);
          &:first-child {
            margin-left: 64px;
          }
          &:nth-of-type(2) {
            margin-left: 129px;
          }
          &:last-child {
            margin-left: 129px;
          }
        }
      }
      .list {
        .wh(100%, 240px);
        background-color: #f5f5f5;
        .list_info {
          .wh(100%, 40px);
          border-bottom: 1px solid #e8e8e8;
          box-sizing: border-box;
          display: flex;
          justify-content: flex-start;
          align-items: center;
          position: relative;
          background-color: #fff;
          .time {
            .sc(14px, #4a4130);
            margin-left: 36px;
          }
          .number {
            .wh(50px, auto);
            text-align: center;
            margin-left: 149px;
            .sc(14px, #d1913c);
          }
          .type {
            .wh(60px, auto);
            text-align: center;
            margin-left: 105px;
            .sc(14px, #4a4130);
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