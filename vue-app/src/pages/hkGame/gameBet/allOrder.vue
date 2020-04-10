<template>
  <div class="all-order">
    <div class="topBar flex">
      <div class="left-arrow flex-center">
        <img src="~images/icon/icon_backarrow@2x.png" alt="" @click="back()">
      </div>
      <div class="hk-title" @click="toShowSelect">
        <div class="select-div">
          <div>{{current_text}}</div>
          <img src="~images/icon/icon_sanjiao@2x.png" alt="">
        </div>
      </div>
      <div class="right-arrow" >
        <img src="~images/icon/icon_shuaxin@2x.png" alt="" class="more">
<!--        <ul v-if="showMore">-->
<!--          <li v-for="(item,index) in moreList" @click="toWhere(index)">{{item}}</li>-->
<!--        </ul>-->
      </div>
    </div>
    <!--    下拉菜单-->
    <div class="selected-box" v-if="showSelected">
      <ul>
        <li class="selected-option" :class="[currentTab === index  ? 'active':'' ]" v-for="(item,index) in list"
            @click="toChoose(item,index)">{{item}}
        </li>
      </ul>
    </div>
    <!--      投注记录-->
    <div class="list">
      <div class="card" v-for="item in 5">
        <div class="card-left">
          <div class="card-left-top">
            <div>香港六合彩 <span>2020009期</span></div>
            <p>投4.00元</p>
          </div>
          <div class="card-left-bottom">
            2020-01-27 10:57:32
            <p>未开奖</p>
          </div>
        </div>
        <div class="card-right">
          <img src="~images/icon/icon_you@2x.png" alt="">
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "allOrder",
    data() {
      return {
        current_text: '全部订单',
        currentTab: 0,
        showSelected: false,
        list: ['我的订单', '我的追号', '我的中奖', '待开奖', '我的撤单']
      }
    },
    methods: {
      toChoose(item, index) {
        this.current_text = item
        this.currentTab = index
        this.showSelected = false
      },
      toShowSelect() {
        this.showSelected = true
      },
    }
  }
</script>

<style scoped lang="less">
  .all-order {
    .topBar {
      height: 44px;
      background: rgba(74, 65, 48, 1);
      justify-content: space-between;
      font-size: 17px;
      color: #FED093;
      position: fixed;
      top: 0;
      width: 100%;
      padding: 0 12px;
      box-sizing: border-box;
      z-index: 999;

      .right-arrow {
        width: 24px;
        height: 44px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        position: relative;

        .more {
          .wh(15px, 15px)
        }

        ul {
          position: absolute;
          top: 44px;
          right: -11px;
          padding: 0 12px;
          width: 100px;
          background: #fff;
          z-index: 9999;
          box-sizing: border-box;
          box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, 0.1);

          li {
            .wh(100%, 37px);
            font-size: 14px;
            font-weight: 400;
            color: rgba(51, 51, 51, 1);
            line-height: 37px;
            text-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e8e8e8;
          }

          li:last-child {
            border-bottom: none;
          }
        }
      }

      .left-arrow {
        width: 24px;
        height: 44px;

        img {
          height: 24px;
          width: auto;
        }
      }

      .hk-title {
        display: flex;
        width: 150px;
        align-items: center;
        height: 28px;
        font-size: 12px;
        font-weight: 400;
        color: rgba(254, 208, 147, 1);
        line-height: 14px;

        .select-div {
          width: 130px;
          height: 32px;
          border-radius: 4px;
          border: 1px solid rgba(255, 255, 255, 1);
          display: flex;
          align-items: center;
          padding: 0 5px;
          box-sizing: border-box;
          font-size: 14px;
          font-weight: 400;
          color: rgba(254, 208, 147, 1);
          justify-content: space-between;

          div {
            .wh(100px, 100%);
            text-align: center;
            line-height: 32px;
            white-space: nowrap;
            overflow: hidden;
          }

          img {
            .wh(9px, 6px);
          }
        }

      }
    }
    .selected-box {
      .wh(100%, auto);
      background: #CCCCCC;
      position: fixed;
      top: 44px;
      width: 100%;
      padding: 12px;
      box-sizing: border-box;
      z-index: 999;
      left: 0;

      .title {
        text-align: center;
        font-size: 17px;
        font-weight: 400;
        color: rgba(51, 51, 51, 1);
      }

      ul {
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;

        .selected-option {
          margin-top: 10px;
          width: 104px;
          height: 36px;
          background: rgba(255, 255, 255, 1);
          border-radius: 4px;
          border: 1px solid rgba(151, 151, 151, 1);
          font-size: 14px;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          text-align: center;
          line-height: 36px;
          margin-right: 15px;
        }

        .selected-option:nth-child(3n) {
          margin-right: 0;
        }

        .active {
          background: rgba(74, 65, 48, 1);
          border-radius: 4px;
          border: 1px solid rgba(151, 151, 151, 1);
          font-size: 14px;
          font-weight: 400;
          color: rgba(255, 255, 255, 1);
        }
      }

    }
    .list{
      .card{
        padding: 0 4px 0 12px;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width:100%;
        height:69px;
        background:rgba(255,255,255,1);
        box-shadow:0px 0px 0px 0px rgba(225,225,225,1);
        border-bottom: 1px solid #e8e8e8;
        .card-left{
          width: 90%;
          .card-left-top{
            width: 100%;
            display: flex;
            font-size:15px;
            justify-content: space-between;
            font-weight:400;
            color:rgba(51,51,51,1);
            span{
              display: inline-block;
              margin-left: 32px;
            }
            p{
              font-size:15px;
              font-weight:400;
              color:rgba(255,30,30,1);
            }
          }
          .card-left-bottom{
            width: 100%;
            display: flex;
            margin-top: 5px;
            align-items: center;
            justify-content: space-between;
            color: #999999;
            font-weight: 400;
            font-size: 12px;
            p{
              font-size: 14px;
            }
          }
        }
        .card-right{
          img{
            width: 20px;
            height: 20px;
          }
        }
      }
    }

  }

</style>
