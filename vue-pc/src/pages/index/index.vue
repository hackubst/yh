<template>
  <div class="container">
    <!-- 轮播图部分 -->
    <div class="topImg">
      <el-carousel height="374px" trigger="click" :autoplay="autoplay" :interval="interval">
        <el-carousel-item v-for="(item, index) in custList" :key="index">
          <img :src="item.pic" alt />
        </el-carousel-item>
      </el-carousel>
      <!-- 登录弹窗组件 -->
      <longin-box @register="register" @changeLoginStatu="changeLoginStatu"></longin-box>
    </div>
    <!-- 新闻公告 -->
    <div class="news_notice">
      <div class="top_title">
        <img src="~images/icon/icon_lift@2x.png" alt class="left" />
        新闻公告
        <img src="~images/icon/icon_right@2x.png" alt class="right" />
      </div>
      <div class="news_box">
        <div class="news_item" v-for="(item, index) in newsItems" :key="index">
          <img :src="item.path_img" alt />
          <div class="news_content">
            <div>
              <div class="news_name">{{item.title}}</div>
              <div class="news_time">{{item.addtime | formatDateYear}}</div>
            </div>
            <div class="news_btn" @click="view_new_detail(item.notice_id)">查看详情</div>
          </div>
        </div>
      </div>
      <div class="look_more pointer" @click="look_more()">查看更多</div>
    </div>
    <!-- 礼品兑换 -->
    <div class="gift_box">
      <div class="top_title">
        <img src="~images/icon/icon_lift@2x.png" alt class="left" />
        好礼兑换
        <img src="~images/icon/icon_right@2x.png" alt class="right" />
      </div>
      <div class="gift_list">
        <div class="item" v-for="item of materialList" :key="item.material_id">
          <div class="pic" :style="{backgroundImage:'url(' + item.img_url + ')'}"></div>
          <div class="text">
            <h2>{{item.name}}</h2>
            <div class="desc">
              <div class="money_title">兑换价</div>
              <div class="money">{{item.money | changeBigNum}}</div>
            </div>
          </div>
        </div>
      </div>
      <!-- <div class="gift_btn pointer" @click="giftexChange()">
        礼物兑换
      </div>-->
    </div>
    <!-- 热门游戏 -->
    <div class="hot_game">
      <div class="top_title">
        <img src="~images/icon/icon_lift@2x.png" alt class="left" />
        热门游戏
        <img src="~images/icon/icon_right@2x.png" alt class="right" />
      </div>
      <div class="game_box">
        <div
          class="game_item"
          v-for="(item, index) in goodsItems"
          :key="index"
          @click="chooseGameItem(item)"
        >
          <div class="game_img">
            <img :src="item.base_img" alt />
            <!-- <div class="game_name">{{item.game_type_name}}</div> -->
          </div>
          <div class="game_btn">开始游戏</div>
        </div>
      </div>
      <div class="look_more pointer" @click="moreGame()">查看更多</div>
    </div>
    <!-- 合作商家 -->
    <div class="business_partner">
      <div class="top_title">
        <img src="~images/icon/icon_lift@2x.png" alt class="left" />
        合作商家
        <img src="~images/icon/icon_right@2x.png" alt class="right" />
      </div>
      <div class="business_box">
        <img src="~images/icon/pic_mangguo@2x.png" alt />
        <img src="~images/icon/pic_wanmei@2x.png" alt />
        <!-- <img src="~images/icon/pic_wanmei@2x.png" alt /> -->
        <img src="~images/icon/pic_tengxun@2x.png" alt />
        <img src="~images/icon/pic_jiujiule@2x.png" alt />
        <img src="~images/icon/pic_ledou@2x.png" alt />
      </div>
    </div>
    <!-- 公司介绍 -->
    <div class="bus_introduce">
      <div class="top_title" style="color: #fff;">
        <img src="~images/icon/icon_lift@2x.png" alt class="left" />
        公司介绍
        <img src="~images/icon/icon_right@2x.png" alt class="right" />
      </div>
      <div
        class="introduce_box"
      >金龙28游戏站点，国内28行业龙头大站，北京28,韩国28,加拿大28,幸运28,蛋蛋28,急速28,魔豆皮,pk赛车，多种28平台，集合28游戏娱乐、北京28竞猜、魔豆皮广告体验的营销平台,并通过各种有奖游戏获得礼品</div>
    </div>
    <div class="qq">
        <img
          class="avatar"
          src="http://www.jinlong28.com/static/img/pic_logo@2x.2f3dde1.png"
          alt
        />
        <div class="online">
          <img src="../../assets/images/icon/Chat-smile.png" alt />
          在线客服
        </div>
        <p class="num" v-for="(item, index) in qq.sys_qq" :key="index">
          <a :href="'http://wpa.qq.com/msgrd?v=3&uin='+item+'&site=qq&menu=yes'" target="_blank">
            QQ:{{item}} <span class="btn">咨询</span>
          </a>
        </p>
        <!-- <div class="btn-wrap">
          <div class="btn">点击咨询</div>
        </div> -->
    </div>
    <popup :title="popupTitle" :content="popupContent" @closePop="closePop" :show="showPop"></popup>
  </div>
</template>
<script>
import longinBox from "@/components/loginBox/loginBox"
import popup from "@/components/popup/popup"
import {
  defalutImg
} from '@/config/mixin'
import {
  mapMutations,
  mapGetters,
  mapActions
} from 'vuex'

export default {
  name: "index",
  components: {
    longinBox,
    popup
  },
  mixins: [defalutImg],
  data () {
    return {
      goodsItems: [],
      newsItems: [],
      custList: [],
      materialList: [],
      qq: {},
      id: '',
      autoplay: true,
      interval: 5000,
      popupTitle: '信息',
      popupContent: '内容加载中...',
      showPop: 0,
      jumpUrl: '' // 体验版地址
    };
  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'checkState'
    ])
  },
  async created () {
    await this.getJumpUrl();

    // 获取首页弹框数据
    this.$Api({
      api_name: 'kkl.index.getArticleInfo',
      article_tag: 'index_notice'
    }, (err, data) => {
      if (!err) {
        const {description} = data.data
        let html = `<p style="color:red;font-size:16px;">争霸赛及虚拟体验已正式上线，让新老玩家能了解游戏玩法.<br><a href="${this.jumpUrl}" target="_blank" style="cursor:pointer;color:red;">&gt;&gt;点击跳转</a></p>`
        this.popupContent = description + html
      }
    })

    // 首页轮播图接口
    this.$Api({
      api_name: 'kkl.game.homePage'
    }, (err, data) => {
      if (!err) {
        this.goodsItems = data.data.game_type_list
        this.newsItems = data.data.notice_list
        this.custList = data.data.cust_flash_list
        this.materialList = data.data.material_list
      }
    })

    // 获取qq
    this.$Api({
      api_name: 'kkl.index.getSysqq'
    }, (err, data) => {
      this.qq = data.data
    })

    // if (this.haveLogin) {
      // 获取用户信息
      this.$Api({
        api_name: 'kkl.user.getUserInfo'
      }, (err, data) => {
        if (!err) {
          this.setUser(data.data)
          // 判断是否需要校验
          if (data.data.open_chenck_login == 1 && !this.checkState) {
            this.setLoginCheck(true)
          }
          // 判断是否有红包id
          if (this.$route.query.jiami_id) {
            this.setRedPacket(this.$route.query.jiami_id)
          }
        }
      })
    // }

    if (this.$route.query.id) {
      this.id = this.$route.query.id
    }
  },
  mounted () {
    let loginStatu = localStorage.getItem('loginStatu')
    if (loginStatu) {
      if (Number(loginStatu) == 1) {
        this.showPop = 1
      }
    }
  },
  methods: {
    changeLoginStatu () {
      this.showPop = 1
      localStorage.setItem('loginStatu',1)
    },
    // 关闭弹窗
    closePop () {
      this.showPop = 0
      localStorage.setItem('loginStatu',0)
    },
    // 跳转注册页面
    register () {
      if (this.id) {
        this.$router.push({
          path: '/register',
          query: {
            id: this.id
          }
        })
      } else {
        this.$router.push({
          path: '/register',
        })
      }
    },
    // 查看新闻详情
    view_new_detail (id) {
      this.$router.push({
        path: '/detail',
        query: {
          notice_id: id,
          type: 1
        }
      })
    },
    // 查看更多新闻
    look_more () {
      this.$router.push({
        path: '/activityArea',
        query: {
          index: 1
        }
      })
    },
    // 礼物兑换按钮
    giftexChange () {
      this.$router.push({
        path: '/moneyChanger'
      })
    },
    // 查看更多游戏
    moreGame () {
      this.$router.push({
        path: '/gameIndex'
      })
    },
    // 选择不同的游戏类型
    chooseGameItem (item) {
      this.setIndexGame(item)
      this.$router.push({
        path: '/gameIndex'
      })
    },
    // 获取体验版地址
    async getJumpUrl() {
      try {
        await this.$Api({
          api_name: 'kkl.user.getTestUrl'
        }, (err, data) => {
          console.table(data);
          if (data.code === 0) {
            const {url} = data.data;
            this.jumpUrl = url;
          }
        });
      } catch (error) {
        throw error;
      }
    },
    ...mapMutations({
      setUser: 'SET_USER',
      setIndexGame: 'SET_INDEX_GAME',
      setLoginCheck: "SET_LOGIN_CHECK"
    }),
    ...mapActions([
      'setRedPacket'
    ])
  }
}
</script>
<style scoped lang='less'>
.container {
  min-width: @main-width;
}

.topImg {
  position: relative;
}

.el-carousel__item {
  img {
    width: 100%;
    height: 374px;
  }
}

.gift_box {
  width: 100%;
  height: 716px;
  background: url('~images/bg/bg_haoliduihuan.png') no-repeat;
  background-size: cover;
  background-position: center;
  box-sizing: border-box;
  padding-top: 90px;
  .gift_list {
    width: @main-width;
    margin: 0 auto;
    // display: flex;
    // flex-wrap: wrap;
    // align-items: center;
    // justify-content: center;
    overflow: hidden;
    .item {
      width: 25%;
      float: left;
      // margin-right: 56px;
      margin-bottom: 40px;
      &:nth-child(4n) {
        margin-right: 0;
      }
      .pic {
        width: 188px;
        height: 146px;
        background-repeat: no-repeat;
        background-size: cover;
        margin: 0 auto 7px;
        border-radius: 5px;
      }
      .text {
        width: 188px;
        margin: 0 auto;
        h2 {
          font-size: 16px;
          font-family: PingFangSC;
          font-weight: 500;
          color: rgba(51, 51, 51, 1);
          line-height: 22px;
          margin-bottom: 6px;
        }
        .desc {
          display: flex;
          align-items: center;
          .money_title {
            width: 52px;
            height: 24px;
            background: rgba(212, 149, 46, 1);
            border-radius: 4px;
            display: block;
            font-size: 14px;
            font-family: PingFangSC;
            font-weight: 400;
            color: rgba(255, 255, 255, 1);
            line-height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 7px;
          }
          .money {
            font-size: 14px;
            font-family: PingFangSC;
            font-weight: 500;
            color: rgba(199, 131, 20, 1);
            line-height: 20px;
          }
        }
      }
    }
  }
  .gift_btn {
    margin: 0 auto;
    .commonBtn(56px, 187px);
  }
}

.hot_game {
  .set_main_box(964px);
  background: url('~images/bg/_bg_1.png') no-repeat center;
  background-size: cover;

  .game_box {
    width: @main-width;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    // align-items: center;
    // justify-content: center;
    .game_item {
      width: 200px;
      height: 254px;
      border-radius: 6px;
      margin-right: 80px;
      overflow: hidden;
      margin-bottom: 40px;

      .game_img {
        width: 200px;
        height: 216px;
        position: relative;

        img {
          width: 200px;
          height: 216px;
          position: absolute;
          top: 0;
          left: 0;
        }

        .game_name {
          font-size: 20px;
          color: #fff;
          font-family: HiraKakuStdN-W8;
          text-align: center;
          position: absolute;
          z-index: 99;
          width: 200px;
          bottom: 25px;
        }
      }

      .game_btn {
        height: 38px;
        background: linear-gradient(
          270deg,
          rgba(209, 145, 60, 1) 0%,
          rgba(255, 209, 148, 1) 100%
        );
        line-height: 38px;
        text-align: center;
        font-size: 16px;
        color: #fff;
      }
    }

    .game_item:nth-child(4n + 4) {
      margin-right: 0px;
    }
  }
}

.news_notice {
  padding: 90px 0;
  box-sizing: border-box;
  // background: url('~images/bg/bg_2@2x.png') no-repeat;
  background: #f5f5f5;
  background-size: 100% 709px;

  .news_box {
    width: @main-width;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;

    .news_item {
      width: @main-width / 2;
      height: 138px;
      padding-right: 24px;
      box-sizing: border-box;
      display: flex;
      align-items: center;
      margin-bottom: 40px;

      img {
        width: 138px;
        height: 138px;
        margin-right: 24px;
      }

      .news_content {
        height: 138px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;

        .news_name {
          .sc(18px, #4a4130);
          margin-bottom: 4px;
        }

        .news_time {
          .sc(14px, #999999);
        }
      }

      .news_btn {
        width: 90px;
        height: 32px;
        border: 1px solid #d1913c;
        .sc(14px, #d1913c);
        border-radius: 4px;
        text-align: center;
        line-height: 32px;
      }
    }
  }
}

.business_partner {
  .set_main_box(412px);
  background: url('~images/bg/bg_3@2x.png') no-repeat;
  background-size: 100% 412px;

  .business_box {
    width: @main-width;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;

    img {
      width: 180px;
      height: 105px;
      border-radius: 12px;
    }
  }
}

.bus_introduce {
  .set_main_box(317px);
  background: url('~images/bg/bg_4@2x.png') no-repeat;
  background-size: 100% 317px;

  .introduce_box {
    width: @main-width;
    margin: 17px auto;
    font-size: 16px;
    color: #fff;
    text-align: center;
    line-height: 27px;
  }
}

.top_title {
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: #4a4130;
  margin-bottom: 50px;

  img {
    width: 50px;
    height: 27px;
  }

  .left {
    margin-right: 20px;
  }

  .right {
    margin-left: 20px;
  }
}

.look_more {
  margin: 27px auto 0;
  .commonBtn(56px, 187px);
}

// 首页模块大盒子公用样式
.set_main_box(@height) {
  height: @height;
  padding: 90px 0;
  box-sizing: border-box;
}

.qq {
  width: 183px;
  height: 257px;
  background: rgba(255, 255, 255, 1);
  box-shadow: 0px 7px 22px 0px rgba(0, 0, 0, 0.15);
  border-radius: 12px 0 0 12px;
  position: fixed;
  top: 50%;
  margin-top: -128.5px;
  right: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  // justify-content: center;
  flex-direction: column;
  overflow: hidden;
  padding-top: 10px;
  box-sizing: border-box;
  a {
    // display: flex;
    // align-items: center;
    // flex-direction: column;
    color: #999999;
  }
  .avatar {
    width: 57px;
    margin-bottom: 10px;
  }
  .online {
    display: flex;
    align-items: center;
    justify-content: center;
    img {
      width: 18px;
      height: 18px;
      margin-right: 5px;
    }
    font-size: 18px;
    font-family: PingFangSC;
    font-weight: 500;
    color: rgba(51, 51, 51, 1);
    line-height: 25px;
    margin-top: -20px;
    margin-bottom: 5px;
  }
  .num {
    font-size: 14px;
    font-family: PingFangSC;
    font-weight: 400;
    color: rgba(153, 153, 153, 1);
    line-height: 20px;
    margin-top: 2px;
    display: flex;
    align-items: center;
    justify-content: center;
    a {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    span {
      color: #333;
      display: inline-block;
        padding: 1px 5px;
        border-radius: 4px;
        background: #4a4130;
        text-align: center;
        margin: 4px auto;
        .sc(12px, #fff);
        margin-left: 5px;
    }
  }
  .btn-wrap {
    width: 136px;
    height: 202px;
    background: rgba(10, 156, 238, 0.1);
    border-radius: 68px;
    position: absolute;
    bottom: -101px;
    left: 50%;
    margin-left: -68px;
    .btn {
      width: 115px;
      height: 178px;
      background: rgba(10, 156, 238, 1);
      border-radius: 58px;
      position: absolute;
      top: 10px;
      left: 50%;
      margin-left: -57.5px;
      display: flex;
      justify-content: center;
      font-size: 14px;
      font-family: PingFangSC;
      font-weight: 500;
      color: rgba(255, 255, 255, 1);
      line-height: 89px;
      cursor: pointer;
    }
  }
}
</style>