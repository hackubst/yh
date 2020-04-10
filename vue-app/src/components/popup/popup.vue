<template>
  <div class="popup-wrap" v-if="show == 1">
    <div class="popup-box">
      <div class="head">{{title}}</div>
      <div class="content" v-html="content"></div>
      <div class="foot">
        <p @click="closePop">确定</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "popup",
  props: {
    title: String,
    content: String,
    show: Number
  },
  methods: {
    closePop () {
      this.$emit('closePop')
    }
  }
}
</script>

<style scoped lang='less'>
.popup-wrap {
  width: 100%;
  height: 100%;
  position: fixed;
  z-index: 1000;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.5);
  .popup-box {
    width: 90%;
    max-height: 80%;
    background: rgba(255, 255, 255, 1);
    border-radius: 4px;
    margin: auto;
    position: absolute;
    top: 0px;
    right: 0px;
    bottom: 0px;
    left: 0px;
    .head {
      width: 100%;
      height: 40px;
      line-height: 40px;
      font-size: 16px;
      font-family: PingFangSC-Regular, PingFangSC;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
      padding-left: 20px;
      border-bottom: 1px solid #eeeeee;
    }
    .content {
      height: calc(100% - 100px);
      box-sizing: border-box;
      font-size: 18px;
      font-family: PingFangSC-Regular, PingFangSC;
      font-weight: 400;
      color: rgba(102, 102, 102, 1);
      line-height: 25px;
      padding: 10px 20px;
      overflow-y: scroll;
    }
    .foot {
      border-top: 1px solid #eeeeee;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 60px;
      p {
        width: 128px;
        height: 40px;
        background: linear-gradient(
          360deg,
          rgba(209, 145, 60, 1) 0%,
          rgba(255, 209, 148, 1) 100%
        );
        border-radius: 8px;
        font-size: 16px;
        font-family: PingFangSC-Medium, PingFangSC;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
        line-height: 40px;
        text-align: center;
      }
    }
  }
}
</style>