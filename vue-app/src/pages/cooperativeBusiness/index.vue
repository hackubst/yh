<template>
  <div id="cooperativeBusiness">
    <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
      <div class="clearfix"></div>
      <ul class="list">
        <li class="list_info" v-for="(item, index) in list" :key="index">
          <p class="title">{{item.game_name}}</p>
          <p class="number">QQ：{{item.qq}}</p>
          <div class="btn" @click="consultQQ(item.qq)">点击咨询</div>
        </li>
      </ul>
    </mescroll-vue>
  </div>
</template>

<script>
import MescrollVue from "mescroll.js/mescroll.vue";
export default {
  name: "cooperativeBusiness",
  components: {
    MescrollVue
  },
  data () {
    return {
      list: [],
      mescroll: null, // mescroll实例对象
      mescrollDown: {
        use: false
      },
      mescrollUp: {
        // 上拉加载的配置.
        callback: this.upCallback, // 上拉回调,
        page: {
          num: 0,
          size: 12
        },
        htmlNodata: '<p class="upwarp-nodata">-- END --</p>'
      }
    };
  },
  methods: {
    mescrollInit (mescroll) {
      this.mescroll = mescroll; // 如果this.mescroll对象没有使用到,则mescrollInit可以不用配置
    },
    get_agent_list (firstRow, fetchNum) {
      this.$Api(
        {
          api_name: "kkl.index.AgentList",
          firstRow: firstRow,
          fetchNum: fetchNum
        },
        (err, data) => {
          if (!err) {
            let arr = data.data.user_list;
            // 如果是第一页需手动制空列表
            if (firstRow === 1) this.list = [];
            // 把请求到的数据添加到列表
            this.list = this.list.concat(arr);
            this.$nextTick(() => {
              this.mescroll.endSuccess(arr.length);
              this.mescroll.endBySize(arr.length, data.data.total);
            });
          } else {
            this.$msg(err.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    },
    // 上拉加载
    upCallback (page, mescroll) {
      this.get_agent_list((page.num - 1) * page.size, page.size);
    },
    consultQQ: function (key) {
      console.log("咨询", key);
      // this.$native.native_open_qq(key);
      var ua = window.navigator.userAgent.toLowerCase();
      
      if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        this.$msg('请在其他浏览器点击访问~', "cancel", "middle", 1500);
        // window.location.href = "http://wpa.qq.com/msgrd?v=3&uin="+key+"&site=qq&menu=yes"
      } else {
        window.location.href = "mqqwpa://im/chat?chat_type=wpa&uin="+key+"&version=1&src_type=web&web_src=oicqzone.com";
      }
      // window.location.href="mqqwpa://im/chat?chat_type=wpa&uin="+key+"&version=1&src_type=web&web_src=oicqzone.com";
      // window.location.href="tencent://message/?uin="+key+"&Site=Sambow&Menu=yes"
      // window.open("http://wpa.qq.com/msgrd?v=3&uin="+key+"&site=qq&menu=yes");
    }
  }
};
</script>

<style scoped lang='less'>
#cooperativeBusiness {
  width: 100%;
  // min-height: 100%;
  .clearfix {
    width: 100%;
    height: 44px;
  }
  .mescroll {
    background-color: #4a4130;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    // height: auto;
  }
  .list {
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    flex-wrap: wrap;
    margin-top: 12px;
    .list_info {
      .wh(169px, 112px);
      background: linear-gradient(
        180deg,
        rgba(255, 209, 148, 1) 0%,
        rgba(209, 145, 60, 1) 100%
      );
      border-radius: 8px;
      margin-left: 12px;
      margin-bottom: 12px;
      .title {
        width: 145px;
        font-size: 18px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
        line-height: 25px;
        margin-top: 16px;
        margin-bottom: 5px;
        margin-left: 12px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
      .number {
        font-size: 14px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
        line-height: 20px;
        margin-left: 12px;
        margin-bottom: 10px;
      }
      .btn {
        width: 72px;
        height: 24px;
        background: rgba(255, 255, 255, 1);
        border-radius: 12px;
        text-align: center;
        line-height: 24px;
        font-size: 12px;
        font-family: PingFangSC-Regular;
        font-weight: 400;
        color: rgba(74, 65, 48, 1);
        margin-left: 12px;
      }
    }
  }
}
</style>