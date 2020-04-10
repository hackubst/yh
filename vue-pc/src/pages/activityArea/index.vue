<template>
  <div id="active">
    <div class="down_list">
      <ul>
        <li
          :class="{dynamic: current == index}"
          v-for="(item, index) in down_list"
          :key="index"
          @click="switch_tab(index)"
        >{{item.title}}</li>
      </ul>
    </div>
    <!-- 轮播图部分 -->
    <div class="topImg">
      <el-carousel height="374px" trigger="click">
        <el-carousel-item v-for="(item, index) in banner_list" :key="index">
          <img :src="item.pic" alt>
        </el-carousel-item>
      </el-carousel>
    </div>
    <!-- 列表 -->
    <div class="title">
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
      <p>{{current==0?"活动专区":"新闻专区"}}</p>
      <img src="../../assets/images/icon/icon_lift@2x.png" alt>
    </div>
    <!-- tab栏 -->
    <ul class="tab_nav" v-if="current==0">
      <li
        v-for="(item, index) in list"
        :key="index"
        :class="{active : currentIndex==index}"
        @click="changeIndex(index)"
      >{{item.title}}</li>
    </ul>
    <!-- 列表详情 -->
    <ul class="list_info" v-if="current == 0">
      <li v-for="(item, index) in active_list" :key="index" class="info_detail">
        <img :src="item.imgurl" alt>
        <p class="list_title ellipsis">{{item.marketing_rule_name}}</p>
        <p
          class="list_content"
        >活动期限：{{item.start_time | formatDateYearStyle}} - {{item.end_time | formatDateYearStyle}}</p>
        <div @click="view_active_detail(item.marketing_rule_id)">查看详情</div>
      </li>
    </ul>
    <ul class="list_info" v-if="current == 1">
      <li v-for="(item, index) in news_list" :key="index" class="info_detail">
        <img :src="item.path_img" alt>
        <p class="list_title ellipsis">{{item.title}}</p>
        <p class="list_content">{{item.addtime | formatDateYearStyle}}</p>
        <div @click="view_news_detail(item.notice_id)">查看详情</div>
      </li>
    </ul>
    <!--分页按钮  -->
    <div class="paging_box" v-if="total != 0">
      <el-pagination
        @current-change="changePage"
        :page-size="9"
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
  name: "index",
  data() {
    return {
      down_list: [
        {
          title: "活动专区"
        },
        {
          title: "新闻公告"
        }
      ],
      list: [{ title: "正在进行的活动" }, { title: "已经结束的活动" }],
      banner_list: [], // 轮播图列表
      active_list: [], // 活动专区列表
      news_list: [], // 新闻专区列表
      current: 0, // 切换活动新闻
      currentIndex: 0, // 切换进行中已结束
      type: 1, // 是否进行或结束
      firstRow: 0,
      fetchNum: 9,
      total: 0,
      page: 1
    };
  },
  created() {
    this.get_banner_list();
    this.refreshUserInfo();
    let index = this.$route.query.index;
    if (index == undefined || index == 0) {
      this.get_active_list();
    }
    if (index) {
      this.current = index;
      if (index == 1) {
        this.get_news_list();
      }
    }
  },
  methods: {
    // 分页功能
    changePage(page) {
      this.page = page;
      this.firstRow = this.fetchNum * (page - 1);
      if (this.current == 0) {
        this.get_active_list();
      } else {
        this.get_news_list();
      }
    },
    // 获取轮播投列表
    get_banner_list() {
      this.$Api(
        {
          api_name: "kkl.index.custFlashList"
        },
        (err, data) => {
          if (!err) {
            this.banner_list = data.data.cust_flash_list;
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 获取活动列表
    get_active_list() {
      this.$Api(
        {
          api_name: "kkl.index.activityList",
          type: this.type,
          firstRow: this.firstRow,
          fetchNum: this.fetchNum
        },
        (err, data) => {
          if (!err) {
            this.active_list = data.data.marketing_rule_list;
            this.total = Number(data.data.total);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 获取新闻列表
    get_news_list() {
      this.$Api(
        {
          api_name: "kkl.index.noticeList",
          firstRow: this.firstRow,
          fetchNum: this.fetchNum
        },
        (err, data) => {
          if (!err) {
            this.news_list = data.data.notice_list;
            this.total = Number(data.data.total);
          } else {
            this.$msg(err.error_msg, "error", 1500);
          }
        }
      );
    },
    // 切换活动和新闻
    switch_tab(index) {
      this.current = index;
      this.firstRow = 0;
      if (index == 0) {
        this.get_active_list();
      } else {
        this.get_news_list();
      }
    },
    // 改变索引
    changeIndex(index) {
      this.firstRow = 0;
      this.currentIndex = index;
      this.type = index + 1;
      this.get_active_list();
    },
    // 查看详情
    view_active_detail(id) {
      this.$router.push({
        path: "/detail",
        query: {
          marketing_rule_id: id,
          type: 0
        }
      });
    },
    // 查看新闻详情
    view_news_detail(id) {
      this.$router.push({
        path: "/detail",
        query: {
          notice_id: id,
          type: 1
        }
      });
    },
    ...mapActions(["refreshUserInfo"])
  }
};
</script>
<style scoped lang='less'>
.down_list {
  .wh(920px, auto);
  margin: 0 auto;
  position: absolute;
  left: 50%;
  margin-left: -460px;
  ul {
    .wh(124px, 82px);
    position: absolute;
    top: 10px;
    left: 210px;
    border-radius: 8px;
    z-index: 999;
    li {
      cursor: pointer;
      .wh(100%, 41px);
      background-color: #4a4130;
      text-align: center;
      line-height: 41px;
      .sc(16px, #fed093);
      &:first-child {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
      }
      &:last-child {
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
      }
    }
    .dynamic {
      background: rgba(209, 145, 60, 1)
        linear-gradient(
          360deg,
          rgba(255, 221, 116, 1) 0%,
          rgba(209, 145, 60, 1) 40%
        );
    }
  }
}
.topImg {
  position: relative;
}
.paging_box {
  width: @main-width;
  display: flex;
  justify-content: center;
  margin: 0 auto 55px auto;
}
.el-carousel__item {
  img {
    width: 100%;
    height: 374px;
  }
}
.title {
  .wh(100%, 56px);
  margin-top: 30px;
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
.tab_nav {
  .wh(100%, auto);
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 55px;
  li {
    .wh(152px, 42px);
    background-color: #d3d0c4;
    .sc(16px, #fff);
    text-align: center;
    line-height: 42px;
    cursor: pointer;
    &:first-child {
      border-top-left-radius: 8px;
      border-bottom-left-radius: 8px;
    }
    &:last-child {
      border-top-right-radius: 8px;
      border-bottom-right-radius: 8px;
    }
  }
  .active {
    background: -webkit-linear-gradient(
      #ffd194,
      #d1913c
    ); /* Safari 5.1 - 6.0 */
    background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
    background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
    background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
  }
}
.list_info {
  .wh(920px, 1074px);
  margin: 0 auto 55px auto;
  display: flex;
  justify-content: flex-start;
  align-items: flex-start;
  align-content: flex-start;
  flex-wrap: wrap;
  .info_detail {
    .wh(270px, 303px);
    background-color: #fff;
    border-radius: 8px;
    margin-right: 55px;
    margin-bottom: 55px;
    box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.1);
    &:nth-of-type(3n) {
      margin-right: 0;
    }
    img {
      .wh(100%, 180px);
      margin-bottom: 10px;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }
    .list_title {
      margin-left: 10px;
      .sc(16px, #4a4130);
      margin-bottom: 6px;
    }
    .list_content {
      margin-left: 10px;
      .sc(12px, #999);
      margin-bottom: 22px;
    }
    div {
      .wh(90px, 32px);
      border: 1px solid#D1913C;
      box-sizing: border-box;
      text-align: center;
      line-height: 32px;
      .sc(14px, #d1913c);
      margin-left: 10px;
      border-radius: 16px;
      cursor: pointer;
    }
  }
}
</style> 