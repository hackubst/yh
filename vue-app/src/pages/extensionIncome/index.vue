<template>
  <div id="extensionIncome">
    <div class="title">
      <img src="../../assets/images/icon/icon_lift@2x.png" alt />
      <p>推广收益</p>
      <img src="../../assets/images/icon/icon_lift@2x.png" alt />
    </div>
    <div class="pro_info">
      <div class="top_img">
        <img src="../../assets/images/icon_tuiguang@2x.png" alt />
      </div>
      <p class="content">成功推荐一位好友可获得相应充值奖励</p>
      <div class="content_title">实力信誉站点,公平公正,不间断争霸赛期期送大奖,秒拍,送钻活动不断! 注册地址:{{address}}</div>
      <p class="remark">复制代码发给您的好友,丰厚回报等着你!</p>
      <div class="arrow_img">
        <img src="../../assets/images/icon/icon_20＊20@2x.png" alt="向下箭头" />
      </div>
    </div>
    <div
      class="btn"
      v-clipboard:copy="address"
      v-clipboard:success="onCopy"
      v-clipboard:error="onError"
    >复制链接</div>
  </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "extensionIncome",
  data () {
    return {
      address: ''
    }
  },
  created () {
    // 获取地址的url
    this.refreshUserInfo();
    this.$Api({
      api_name: 'kkl.index.getInviteUrl',
      url: "http://www.jinlong28.com/#/index"
    }, (err, data) => {
      if (!err) {
        this.address = data.data.url
      } else {
        this.$msg(err.error_msg, 'error', 1500)
      }
    })
  },
  methods: {
    onCopy () {
      this.$msg('复制成功', "success", "middle", 1500);
    },
    onError () {
      this.$msg('复制失败', 'error', "middle", 1500)
    },
    ...mapActions([
      "refreshUserInfo"
    ])
  }
}
</script>

<style scoped lang='less'>
#extensionIncome {
  .wh(100%, 100%);
  margin: 0 auto;
  .title {
    .wh(100%, 30px);
    margin-top: 20px;
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    img {
      .wh(30px, auto);
      margin: 0 20px;
    }
    p {
      .sc(16px, #4a4130);
      font-weight: 500;
    }
  }
  .pro_info {
    .wh(90%, auto);
    margin: 0 auto 20px auto;
    background: -webkit-linear-gradient(
      #ffd194,
      #d1913c
    ); /* Safari 5.1 - 6.0 */
    background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
    background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
    background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    .top_img {
      .wh(248px, 140px);
      margin: 18px 0 20px 0;
      img {
        .wh(100%, 100%);
      }
    }
    .content {
      .sc(18px, #fff);
      margin-bottom: 20px;
    }
    .content_title {
      .wh(320px, auto);
      box-sizing: border-box;
      padding: 10px 10px;
      background-color: rgba(255, 248, 239, 0.18);
      border-radius: 16px;
      .sc(14px, #fff8ef);
      margin-bottom: 20px;
      text-align: center;
      line-height: 35px;
    }
    .remark {
      .sc(18px, #fff);
      margin-bottom: 20px;
    }
    .arrow_img {
      .wh(20px, 20px);
      img {
        .wh(100%, 100%);
        opacity: 0.5;
      }
    }
  }
  .btn {
    .wh(90%, 48px);
    background: -webkit-linear-gradient(
      #ffd194,
      #d1913c
    ); /* Safari 5.1 - 6.0 */
    background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
    background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
    background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
    text-align: center;
    line-height: 48px;
    .sc(18px, #ffffff);
    border-radius: 8px;
    margin: 0 auto 25px auto;
  }
}
</style>