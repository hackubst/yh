<template>
  <div id="bg_img">
    <div class="pos_left">
      <img src="../../assets/images/bg/bg_left@2x.png" alt="背景图">
    </div>
    <div class="pos_right">
      <img src="../../assets/images/bg/bg_right@2x.png" alt="背景图">
    </div>
  </div>
</template>

<script>
  export default {
    name: "login",
    data() {
      return {

      }
    }
  }
</script>

<style scoped lang='less'>
  #bg_img {
    .wh(100%, 100%);
    flex: 1;
    position: absolute;
    overflow: hidden;
    z-index: -1;

    .pos_left {
      .wh(356px, 559px);
      position: absolute;
      left: 0;
      top: 0;

      img {
        .wh(100%, 100%);
      }
    }

    .pos_right {
      .wh(489px, 492px);
      position: absolute;
      right: 0;
      bottom: 0;

      img {
        .wh(100%, 100%);
      }
    }
  }
</style>