<template>
  <div id="moneyChanger">
    <div class="title">
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
      <p>商务合作</p>
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
    </div>
    <ul class="list">
      <li class="list_info" v-for="(item, index) in list" :key="index">
        <a :href="href_first+item.qq+href_last">
          <img class="bg_img" src="../../assets/images/bg/bg_dianka@2x.png" alt>
          <p class="top_title">
            <img src="../../assets/images/bg/icon_logo2@2x.png" alt>
          </p>
          <p class="number ellipsis">{{item.game_name}}</p>
          <p class="info ellipsis_more">({{item.introduce}})</p>
          <div class="explain">
            <img src="../../assets/images/icon/icon_qie@2x.png" alt>
            <p>{{item.qq}}</p>
          </div>
          <div class="btn">
            <p>点击咨询</p>
            <img src="../../assets/images/icon/icon_right59@2x.png" alt="箭头">
          </div>
        </a>
      </li>
    </ul>
    <!--分页按钮  -->
    <!-- <div class="paging_box" v-if="total!=0">
      <el-pagination
        @current-change="changePage"
        :page-size="12"
        :current-page="page"
        layout="prev, pager, next"
        :total="total"
        prev-text="上一页"
        next-text="下一页"
      ></el-pagination>
    </div> -->
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "moneyChanger",
  data() {
    return {
      list: [],
      href_first: "tencent://message/?uin=",
      href_last: "&Site=Sambow&Menu=yes",
      firstRow: 0,
      fetchNum: 12,
      total: 0,
      page: 1
    };
  },
  created() {
    this.get_agent_list();
    this.refreshUserInfo();
  },
  methods: {
    // 分页功能
    changePage(page) {
      this.page = page;
      this.firstRow = this.fetchNum * (page - 1);
      this.get_agent_list();
    },
    // 获取商务合作列表
    get_agent_list() {
      this.$Api(
        {
          api_name: "kkl.index.AgentList",
          firstRow: this.firstRow,
          fetchNum: this.fetchNum
        },
        (err, data) => {
          if (!err) {
            this.list = data.data.user_list;
            this.total = Number(data.data.total);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    ...mapActions([
      "refreshUserInfo"
    ])
  }
};
</script>

<style scoped lang='less'>
#moneyChanger {
  .wh(920px, auto);
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
    .wh(100%, auto);
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
        .wh(68px, 22px);
        margin: 7px 0 3px 10px;
        img {
          .wh(100%, 100%);
        }
      }
      .number {
        width: 194px;
        .sc(36px, #fff8ef);
        margin-left: 10px;
        margin-right: 10px;
        line-height: 50px;
      }
      .info {
        width: 194px;
        margin-left: 10px;
        margin-right: 10px;
        .sc(16px, #fff8ef);
        margin-bottom: 15px;
        height: 32px;
      }
      .explain {
        margin-left: 10px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        margin-bottom: 10px;
        height: 25px;
        img {
          .wh(15px, 17px);
          margin-right: 6px;
        }
        p {
          .sc(18px, #fff8ef);
          line-height: 25px;
        }
      }
      .btn {
        .wh(96px, 26px);
        margin-left: 10px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff8ef;
        border-radius: 13px;
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