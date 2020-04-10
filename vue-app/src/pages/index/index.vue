<template>
  <div id="index" class="is_pad">
    <div class="swiper-box">
      <swiper :list="list" auto loop :show-dots="false" height="224px"></swiper>
    </div>
    <div class="title_box flex">
      <router-link to="/newsNotice" tag="div">
        <img src="~images/icon/icon_news@2x.png" alt>
        <p>新闻公告</p>
      </router-link>
      <router-link to="/activity" tag="div">
        <img src="~images/icon/icon_activity@2x.png" alt>
        <p>最新活动</p>
      </router-link>
      <router-link to="/cooperativeBusiness" tag="div">
        <img src="~images/icon/icon_shop@2x.png" alt>
        <p>合作商家</p>
      </router-link>
      <div @click="toExeBean()">
        <img src="~images/icon/icon_bean@2x.png" alt>
        <p>经验换豆</p>
      </div>
      <div @click="toTrial()">
        <img src="~images/icon/icon_trail.png" alt>
        <p>争霸赛</p>
      </div>
    </div>
    <div class="game_box flex-center">
      <div class="start_game" @click="startGame()">开始游戏</div>
    </div>
    <div class="banner">
      <img src="~images/bg/Bitmap.png" alt>
    </div>
    <div class="counsel flex" v-if="sysQQ">
      <div class="other_item">
        <p>官方客服</p>
        <p v-for="(item,index) in sysQQ.sys_qq" :key="index">{{item}} <span class="btn" @click="consultQQ(item)">咨询</span></p>
        <!-- <a class="btn" target="blank" :href="'http://wpa.qq.com/msgrd?V=1&Uin='+sysQQ.sys_qq+'&Site=http://www.xxxx.com&Menu=yes'">咨询</a> -->
        <!-- <div class="btn" @click="consultQQ(sysQQ.sys_qq)">咨询</div> -->
      </div>
      <div class="mid_item">
        <p>在线QQ咨询</p>
        <img src="~images/bg/QQ.png" alt>
      </div>
      <div class="other_item">
        <p>官方交流群</p>
        <p v-for="(item,index) in sysQQ.sys_qq_group" :key="index">{{item}} <span class="btn" @click="joinQQ(item)">加入</span></p>
        <!-- <a class="btn" target="blank" :href="'//shang.qq.com/wpa/qunwpa?idkey='+sysQQ.sys_qq_key">加入</a> -->
        <!-- <div class="btn" @click="joinQQ(sysQQ.sys_qq_group, sysQQ.sys_qq_key)">加入</div> -->
      </div>
    </div>
    <red-dialog v-model="dialog_show" v-if="dialog_show" :jiami_id="jiami_id"></red-dialog>
    <popup :title="popupTitle" :content="popupContent" @closePop="closePop" :show="showPop"></popup>
  </div>
</template>

<script>
import { Swiper } from "vux";
import redDialog from "@/components/common/redDialog";
import popup from "@/components/popup/popup"
import { fiveIndex } from "@/config/mixin.js";
import {
  getStore,
} from '@/config/utils'
import SID from "@/sid.js";
export default {
  name: "index",
  data() {
    return {
      list: [],
      sysQQ: null,
      dialog_show: false,
      jiami_id: "",
      popupTitle: '信息',
      popupContent: '内容加载中...',
      showPop: 0
    };
  },
  mixins: [fiveIndex],
  components: {
    Swiper,
    popup,
    redDialog
  },
  methods: {
    // 关闭弹窗
    closePop () {
      this.showPop = 0
      localStorage.setItem('loginStatu',0)
    },
    toExeBean: function() {
      this.$Api(
        {
          api_name: "kkl.user.getUserInfo"
        },
        (err, data) => {
          if (data.code == 40011) {
            // this.$native.native_login();
            this.$router.replace({
              path: 'login'
            })
          } else {
            this.$router.push({
              path: "/exeBean"
            });
          }
        }
      );
    },
    startGame: function() {
      this.$Api(
        {
          api_name: "kkl.user.getUserInfo"
        },
        (err, data) => {
          if (data.code == 40011) {
            // this.$native.native_login();
            this.$router.replace({
              path: 'login'
            })
          } else {
            // this.$native.native_show_tabBar(2);
            this.$router.replace({
              path: 'gameIndex'
            })
          }
        }
      );
    },
    consultQQ: function(key) {
      console.log("咨询", key);
      var ua = window.navigator.userAgent.toLowerCase();
      
      if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        this.$msg('请在其他浏览器点击访问~', "cancel", "middle", 1500);
        // window.location.href = "http://wpa.qq.com/msgrd?v=3&uin="+key+"&site=qq&menu=yes"
      } else {
        window.location.href = "mqqwpa://im/chat?chat_type=wpa&uin="+key+"&version=1&src_type=web&web_src=oicqzone.com";
      }
      // this.$native.native_open_qq(key);
    },
    joinQQ: function(qqNumber, key) {
      console.log("加入", qqNumber);
      var ua = window.navigator.userAgent.toLowerCase();
      
      if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        this.$msg('请在其他浏览器点击访问~', "cancel", "middle", 1500);
        // window.location.href = "http://wpa.qq.com/msgrd?v=3&uin="+key+"&site=qq&menu=yes"
      } else {
        window.location.href = "mqqwpa://im/chat?chat_type=wpa&uin="+qqNumber+"&version=1&src_type=web&web_src=oicqzone.com";
      }
      // this.$native.native_join_qq(qqNumber, key);
    },
    // 获取剪切板的内容
    get_paste: function(url) {
      var url = url;
      var urlQuery = url.split("?")[1];
      if (urlQuery) {
        var queryArr = urlQuery.split("=");
        if (queryArr[0] == "jiami_id") {
          this.jiami_id = urlQuery.replace("jiami_id=", "");
          console.log("jiami_id", this.jiami_id);
          //  判断红包是否领取
          this.$Api(
            {
              api_name: "kkl.user.isReceivedRedPacket",
              red_packet_id: this.jiami_id
            },
            (err, data) => {
              console.log(data);
              if (data.code !== 0) {
                this.dialog_show = true;
              }
            }
          );
        }
      }
    },
    // 跳转体验版
    toTrial() {
      try {
        this.$Api({
          api_name: 'kkl.user.getTestUrl'
        }, (err, data) => {
          console.table(data);
          if (data.code === 0) {
            const {url} = data.data;
            location.href = url;
          }
        });
      } catch (error) {
        throw error;
      }
    }
  },
  created() {
    // 获取首页弹框数据
    this.$Api({
      api_name: 'kkl.index.getArticleInfo',
      article_tag: 'index_notice'
    }, (err, data) => {
      if (!err) {
        this.popupContent = data.data.description
        let localLoginStatu = localStorage.getItem('loginStatu')
        console.log(typeof localLoginStatu)
        if (localLoginStatu) {
          if (localLoginStatu == '0') {
            this.showPop = 0
          } else if (localLoginStatu == '1') {
            this.showPop = 1
          }
        }
      }
    })
    window.get_paste = this.get_paste;
    // 获取首页数据
    this.$Api(
      {
        api_name: "kkl.game.homePage",
        type: 1
      },
      (err, data) => {
        if (!err) {
          let list = [];
          for (let i = 0; i < data.data.cust_flash_list.length; i++) {
            list.push({
              img: data.data.cust_flash_list[i].pic
            });
          }
          this.list = list;
        }
      }
    );
    // 获取qq
    this.$Api(
      {
        api_name: "kkl.index.getSysqq"
      },
      (err, data) => {
        if (!err) {
          this.sysQQ = data.data;
        }
      }
    );

    // 如果是h5
    let is_app = getStore('is_app')
    if (is_app && Number(is_app) == 0) {
      var url = document.URL;
      console.log('current url:', url);
      var urlQuery = url.split("?")[1];
      if (urlQuery) {
        var queryArr = urlQuery.split("=");
        if (queryArr[0] == "jiami_id") {
          this.jiami_id = urlQuery.replace("jiami_id=", "");
          console.log("jiami_id", this.jiami_id);
          //  判断红包是否领取
          this.$Api(
            {
              api_name: "kkl.user.isReceivedRedPacket",
              red_packet_id: this.jiami_id
            },
            (err, data) => {
              this.dialog_show = true;
              if (data.code !== 0) {
                
              }
            }
          );
        }
      }
    }
  }
};
</script>

<style scoped lang='less'>
#index {
  width: 100%;
  height: 100%;
  .swiper-box {
    width: 375px;
  }
  .title_box {
    width: 100%;
    div {
      flex-grow: 1;
      text-align: center;
      background: #fff;
      padding: 10px 0;
      box-sizing: border-box;
      .sc(14px, #333333);
      img {
        width: 36px;
        height: 36px;
        margin-bottom: 4px;
      }
    }
  }
  .game_box {
    width: 100%;
    height: 80px;
    background: #fff;
    .start_game {
      width: 300px;
      height: 44px;
      border-radius: 8px;
      line-height: 44px;
      text-align: center;
      background: url("~images/bg/btn_stargame@2x.png") no-repeat;
      background-size: 100% 120%;
      .sc(16px, #fff);
    }
  }
  .banner {
    height: 84px;
    img {
      width: 100%;
      height: 84px;
    }
  }
  .counsel {
    width: 100%;
    min-height: 91px;
    padding: 10px 0;
    position: relative;
    background: #ffbb00;
    align-items: flex-start;
    div {
      flex-grow: 1;
    }
    .other_item {
      height: 100%;
      box-sizing: border-box;
      align-items: flex-start;
      p {
        text-align: center;
        .sc(13px, #fff);
      }
      .btn {
        display: inline-block;
        padding: 2px 5px;
        border-radius: 4px;
        background: #4a4130;
        text-align: center;
        margin: 4px auto;
        .sc(12px, #fff);
        margin-left: 5px;
      }
    }
    .mid_item {
      height: 100%;
      padding-top: 6px;
      box-sizing: border-box;
      .sc(18px, #fff);
      // position: relative;
      text-align: center;
      img {
        width: 48px;
        height: 46px;
        position: absolute;
        bottom: 0;
        left: 50%;
        margin-left: -24px;
      }
    }
  }
}
</style>