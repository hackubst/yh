<template>
  <div>
    <!-- 轮播图部分 -->
    <div class="topImg">
      <el-carousel height="374px" trigger="click">
        <el-carousel-item v-for="(item, index) in banner_list" :key="index">
          <img :src="item.pic">
        </el-carousel-item>
      </el-carousel>
    </div>
    <div class="detail_info">
        <!-- 面包屑 -->
        <div class="nav">
            <span @click="go_back()">{{type== 0?'活动专区':'新闻专区'}}</span>
            <img src="../../assets/images/icon/icon_right59@2x.png" alt="">
            <span>{{title}}</span>
        </div>
        <div class="title">{{title}}</div>
        <div class="rich_text" v-html="contents"></div>
    </div>
  </div>
</template>
<script>
  export default {
    name: "index",
    components: {

    },
    data() {
      return {
        banner_list: [],
        title: '', // 标题
        type: '', // 详情判断
        contents: '', // 富文本内容
        notice_id: '', // 新闻id
        marketing_rule_id: '', // 详情id
      };
    },
    created() {
        this.type = this.$route.query.type
        if (this.type == 0) {
            this.get_active_detail()
        } else {
            this.get_news_detail()
        }
        this.get_banner_list()
    }, 
    methods: {
        // 获取轮播投列表
        get_banner_list() {
            this.$Api({
                api_name: 'kkl.index.custFlashList'
            }, (err, data) => {
                if (!err) {
                    this.banner_list = data.data.cust_flash_list
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        },
        // 获取新闻详情
        get_news_detail() {
            this.notice_id = this.$route.query.notice_id
            this.$Api({
                api_name: 'kkl.index.noticeDetail',
                notice_id: this.notice_id
            }, (err, data) => {
                if (!err) {
                    this.title = data.data.info.title
                    this.contents = data.data.info.content
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        },
        // 获取活动专区详情
        get_active_detail() {
            this.marketing_rule_id = this.$route.query.marketing_rule_id
            this.$Api({
                api_name: 'kkl.index.getActivityInfo',
                marketing_rule_id: this.marketing_rule_id
            }, (err, data) => {
                if (!err) {
                    this.title = data.data.marketing_rule_name
                    this.contents = data.data.contents
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
        },
        go_back() {
            if (this.type == 0) {
                this.$router.push({
                    path: '/activityArea',
                    query: {
                        index: 0
                    }
                })
            } else {
                this.$router.push({
                    path: '/activityArea',
                    query: {
                        index: 1
                    }
                })
            }
        }
    }
  }
</script>
<style scoped lang='less'>
    .topImg{
        position: relative;     
    }
    .el-carousel__item {
        img {
        width: 100%;
        height: 374px;
        }
    }
    .detail_info{
        .wh(920px, 100%);
        margin: 0 auto;
        .nav{
            padding: 10px 20px;
            display: inline-block;
            box-shadow: 0 2px 6px 0 rgba(0, 0, 0, 0.1);
            margin: 30px 0;
            border-radius: 8px;
            span{
                .sc(16px, #D1913C);
            }
            img{
                .wh(5px, 8px);
                margin: 0 5px;
            }
        }
        .title{
            .sc(40px, #4A4130);
            line-height: 56px;
            margin-bottom: 10px;
        }
        .rich_text{
            margin-bottom: 55px;
        }
    }
</style> 