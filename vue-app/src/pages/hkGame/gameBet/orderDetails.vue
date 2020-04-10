<template>
  <div class="order-details">
    <div class="head">
      <div class="title">香港六合彩 <span>第20202000期</span></div>
      <div>开奖号码：等待开奖</div>
    </div>
    <div class="order-content">
      <div class="sort-title">
        <div class="title-line"></div>
        订单内容
      </div>
      <ul>
        <li>
          <div class="li-left">订单号</div>
          <div class="li-right">382094839754897548</div>
        </li>
        <li>
          <div class="li-left">投注金额</div>
          <div class="li-right">2.00</div>
        </li>
      </ul>
      <div class="sort-title mt10">
        <div class="title-line"></div>
        投注号码
      </div>
      <ul>
        <li>
          <div class="li-left">[选码]</div>
          <div class="li-right">12：2</div>
        </li>
      </ul>
    </div>
    <div class="btn">
      <button>取消投注</button>
      <button>再来一注</button>
    </div>
  </div>
</template>

<script>
  export default {
    name: "orderDetails"
  }
</script>

<style scoped lang="less">
  .order-details {
    .mt10{
      margin-top: 10px;
    }
    .head {
      .wh(100%, 79px);
      border-bottom: 1px solid #e8e8e8;
      padding: 0 20px;
      box-sizing: border-box;
      color: #999999;
      font-size: 14px;
      display: flex;
      flex-direction: column;
      justify-content: center;

      .title {
        color: #333333;
        font-size: 16px;
        margin-bottom: 10px;

        span {
          display: inline-block;
          margin-left: 32px;
        }
      }

    }

    .order-content {
      padding: 20px;
      box-sizing: border-box;
      .wh(100%, auto);
      .sort-title {
        display: flex;
        color: #333333;
        width: 100%;
        font-size: 18px;
        align-items: center;

        .title-line {
          width: 6px;
          height: 15px;
          background: #FF851E;
          margin-right: 10px;
        }

      }

      li {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        margin-bottom:5px;
        .li-left {
          width: 80px;
          text-align: right;
          color: #999999;
          font-size: 14px;

        }

        .li-right {
          padding-left: 20px;
          box-sizing: border-box;
          text-align: right;
          color: #333333;
          font-size: 14px;
        }
      }
    }
    .btn{
      width: 100%;
      display: flex;
      padding: 30px;
      box-sizing: border-box;
      display: flex;
      align-items: center;
      justify-content: space-between;
      button{
        width: 45%;
        height: 44px;
        background: #FF851E;
        text-align: center;
        line-height: 44px;
        font-size: 18px;
        color: #fff;
        border: none;
        border-radius: 8px;
      }
    }
  }
</style>
