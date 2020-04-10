<template>
    <div class="result-box">
      <img src="~images/icon/icon_dui@2x.png" alt="">
        <p>投注成功，预祝您中奖</p>
      <span>当前投注彩种：香港六合彩</span>
      <span>当前投注期数：第2020009期</span>
      <button>确认</button>
    </div>
</template>

<script>
    export default {
        name: "hkGameResult"
    }
</script>

<style scoped lang="less">
    .result-box{
      width: 100%;
      padding: 48px 12px 0 12px;
      box-sizing: border-box;
      height: 100vh;
      background:rgba(242,242,242,1);
      display: flex;
      flex-direction: column;
      align-items: center;
      font-size:14px;
      font-weight:400;
      color:rgba(102,102,102,1);
      img{
        .wh(65px,65px)
      }
      p{
        font-size:18px;
        font-weight:400;
        margin-bottom: 4px;
        margin-top: 23px;
        color:rgba(51,51,51,1);
      }
      button{
        width:100%;
        height:48px;
        background:rgba(255,133,30,1);
        border-radius:4px;
        text-align: center;
        line-height: 48px;
        font-size:17px;
        font-weight:500;
        color:rgba(255,255,255,1);
        margin-top: 13px;
        border: none;
      }
    }
</style>
