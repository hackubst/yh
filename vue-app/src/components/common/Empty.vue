<template>
  <div class="empty flex-center">
    <!-- S 空白图标 -->
    <div class="img-box">
      <div class="empty-img">
        <img :src="icon" alt="">
      </div>
    </div>
    <!-- E 空白图标 -->
    <!-- S 空白提示 -->
    <p class="empty-tip">{{tip}}</p>
    <!-- E 空白提示 -->
  </div>
</template>

<script>
export default {
  name: "empty",
  props: {
    tip: {
      type: String,
      default: "暂无更多数据哦～"
    },
    icon: {
      type: String,
      default: require("@/assets/images/icon/icon_member_record@2x.png")
    }
  }
}
</script>

<style lang="less" scoped>
.empty {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 2;
  flex-direction: column;

  .img-box {
    margin-bottom: 16px;

    .empty-img {
      .wh(100%, 100%);

      img {
        width: 60px;
      }
    }
  }

  .empty-tip {
    max-width: 80%;
    .sc(16px, #909090);
    line-height: 0.4rem;
    text-align: center;
    white-space: nowrap;
  }
}
</style>
