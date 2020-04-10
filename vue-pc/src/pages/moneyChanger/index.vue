<template>
  <div id="moneyChanger">
    <div class="title">
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
      <p>兑换中心</p>
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
    </div>
    <ul class="list">
      <li class="list_info" v-for="(item, index) in list" :key="index">
        <img class="bg_img" src="../../assets/images/bg/bg_kami@2x.png" alt>
        <p class="top_title">中国移动</p>
        <p class="number">
          <span>￥</span>
          {{item.cash}}
        </p>
        <p class="info">手机充值卡{{item.cash}}元</p>
        <p class="explain">(七天未回收，此卡作废)</p>
        <div class="btn" @click="cash_exchange(item.gift_card_id)">
          <p>点击兑换</p>
          <img src="../../assets/images/icon/icon_right59@2x.png" alt="箭头">
        </div>
      </li>
    </ul>
    <!--分页按钮  -->
    <div class="paging_box" v-if="total != 0">
      <el-pagination
        @current-change="changePage"
        :page-size="12"
        :current-page="page"
        layout="prev, pager, next"
        :total="total"
        prev-text="上一页"
        next-text="下一页"
      ></el-pagination>
    </div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "moneyChanger",
  components: {},
  data() {
    return {
      list: [],
      firstRow: 0,
      fetchNum: 12,
      total: 0,
      page: 1
    };
  },
  created() {
    this.refreshUserInfo();
    this.get_card_list();
  },
  methods: {
    // 分页功能
    changePage(page) {
      this.page = page;
      this.firstRow = this.fetchNum * (page - 1);
      this.get_card_list();
    },
    // 获取兑换列表
    get_card_list() {
      this.$Api(
        {
          api_name: "kkl.index.giftCardList",
          firstRow: this.firstRow,
          fetchNum: this.fetchNum
        },
        (err, data) => {
          if (!err) {
            this.list = data.data.gift_card_list;
            this.total = Number(data.data.total);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 确认兑换
    cash_exchange(id) {
      this.$router.push({
        path: "/cashExchange",
        query: {
          id: id
        }
      });
    },
    ...mapActions([
      "refreshUserInfo"
    ])
  }
};
</script>

<style scoped lang='less'>
#moneyChanger {
  .wh(920px, 950px);
  margin: 0 auto;
  overflow: hidden;
  .title {
    .wh(100%, 56px);
    margin-top: 50px;
    margin-bottom: 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    img {
      .wh(50px, 27px);
      margin: 0 20px;
    }
    p {
      .sc(40px, #4a4130);
      font-weight: 500;
    }
  }
  .list {
    .wh(100%, 675px);
    display: flex;
    justify-content: flex-start;
    align-items: flex-start;
    align-content: flex-start;
    flex-wrap: wrap;
    .list_info {
      .wh(214px, 205px);
      position: relative;
      margin-right: 20px;
      margin-bottom: 20px;
      &:nth-of-type(4n) {
        margin-right: 0;
      }
      .bg_img {
        .wh(100%, 100%);
        position: absolute;
        top: 0;
        z-index: -1;
      }
      .top_title {
        width: 100%;
        .sc(16px, #fff8ef);
        text-align: center;
        line-height: 22px;
        margin-top: 8px;
        margin-bottom: 10px;
      }
      .number {
        .sc(36px, #fff8ef);
        margin-left: 10px;
        line-height: 50px;
        span {
          .sc(20px, #fff8ef);
        }
      }
      .info {
        margin-left: 10px;
        .sc(16px, #fff8ef);
        margin-bottom: 23px;
      }
      .explain {
        margin-left: 10px;
        .sc(12px, #fff8ef);
        margin-bottom: 10px;
      }
      .btn {
        .wh(96px, 26px);
        margin-left: 10px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff8ef;
        border-radius: 13px;
        cursor: pointer;
        p {
          .sc(14px, #d1913c);
          margin-left: 14px;
          margin-right: 7px;
        }
        img {
          .wh(5px, 10px);
        }
      }
    }
  }
  .paging_box {
    width: @main-width;
    display: flex;
    justify-content: center;
    margin: 20px auto 55px auto;
  }
}
</style>