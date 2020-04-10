<template>
  <div class="footBar flex">
    <router-link to="/index" tag="div" active-class="active">
      <img src="~images/icon/icon_home@2x.png" alt />
      首页
    </router-link>
    <router-link to="/gameIndex" tag="div" active-class="active">
      <img src="~images/icon/icon_game@2x.png" alt />
      游戏乐园
    </router-link>
    <router-link to="/rankList" tag="div" active-class="active">
      <img src="~images/icon/icon_rank@2x.png" alt />
      牛人榜
    </router-link>
    <!-- <router-link to="/trialRankList" tag="div" active-class="active">
      <img src="~images/icon/icon_rank@2x.png" alt />
      战绩榜
    </router-link> -->
    <router-link to="/exchange" tag="div" active-class="active">
      <img src="~images/icon/icon_reward@2x.png" alt />
      兑奖
    </router-link>
    <router-link to="/personalCenter" tag="div" active-class="active">
      <img src="~images/icon/icon_member@2x.png" alt />
      会员
    </router-link>
  </div>
</template>
<script>
export default {
  name: "footBar",
  data () {
    return {

    };
  }
}
</script>
<style scoped lang='less'>
.footBar {
  height: 49px;
  width: 100%;
  position: fixed;
  bottom: 0;
  z-index: 999;
  background: linear-gradient(
    180deg,
    rgba(255, 209, 148, 1) 0%,
    rgba(209, 145, 60, 1) 100%
  );
  div {
    flex-grow: 1;
    text-align: center;
    height: 49px;
    // line-height: 49px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-family: PingFangSC-Regular, PingFangSC;
    font-weight: 400;
    color: rgba(255, 255, 255, 1);
    line-height: 14px;
    img {
      width: 17px;
      height: 17px;
      margin-bottom: 5px;
    }
  }
  .active {
    background: #4A4130;
  }
}
</style>