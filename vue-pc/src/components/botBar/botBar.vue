<template>
<div class="botBar">
    <div class="bot_main">
        <div class="title_box">
            <router-link tag="div" to="/">网站首页</router-link>
            <router-link tag="div" to="/businessCooperation">商务合作</router-link>
            <router-link tag="div" to="/activityArea">活动专区</router-link>
            <router-link tag="div" to="/gameIndex">游戏乐园</router-link>
            <router-link tag="div" to="/moneyChanger">兑换中心</router-link>
            <router-link tag="div" to="/userCenter">用户中心</router-link>
            <router-link tag="div" to="/gameRanking">游戏排行</router-link>
            <router-link tag="div" to="/extensionIncome">推广收益</router-link>
        </div>
        <div class="bot_title">
            Copyright 2009-2019,www.jinlong28.com,All rights reserved 版权所有 金龙28 浙ICP备17035528号
        </div>
    </div>
</div>
</template>
<script>
export default {
  name: "botBar",
  data () {
    return {
    };
  }
}
</script>
<style scoped lang='less'>
.botBar{
   width: 100%;
   min-width: @main-width;
   height: 92px;
   background:rgba(74,65,48,1);
   padding-top: 20px;
   box-sizing: border-box;
   z-index: 99;
   .bot_main{
       width: @main-width;
       margin: 0 auto;
       .title_box{
          display: flex;
          align-items: center;
          padding: 0 30px;
          box-sizing: border-box;
          margin-bottom: 13px;
          div{
              font-size: 16px;
              color: #FED093;
              position: relative;
              flex-grow: 1;
              text-align: center;
              &:after{
                  content: "";
                  width: 1px;
                  height: 10px;
                  position: absolute;
                  right: 0px;
                  top: 50%;
                  margin-top: -5px;
                  background: #FED093;
              }
          }
          div:nth-child(8){
              &:after{
                  content: "";
                  width: 0px;
                  height: 0px;
              }
          }
          
       }
       .bot_title{
           text-align: center;
           font-size: 14px;
           color: #B29165;
       }
   }
}

</style>