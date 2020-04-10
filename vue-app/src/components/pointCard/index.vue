<template>
  <div id="record">
    <ul class="relief_list">
      <li
        class="relief_info"
        :class="{dual: index%2 != 0}"
        v-for="(i, index) in relief_list"
        :key="index"
      >
        <div class="img_click" @click="check(index)">
          <img v-if="!i.check" src="../../assets/images/icon/icon_chooseoff@3x.png" alt />
          <img v-if="i.check" src="../../assets/images/icon/icon_chooseon@3x.png" alt />
        </div>
        <div class="wrap">{{i.card_password}}</div>
        <div>{{i.name}}</div>
        <div class="change_color" :class="{red_color:i.isuse == 0}">{{i.isuse == 1?'未使用':'已使用'}}</div>
      </li>
    </ul>
    <!-- <div class="foot">
      <img
        @click="check_all()"
        v-if="!check_boolean"
        src="../../assets/images/icon/icon_chooseoff@3x.png"
        alt
      />
      <img
        @click="check_all()"
        v-if="check_boolean"
        src="../../assets/images/icon/icon_chooseon@3x.png"
        alt
      />
      <p>全选未选中点卡</p>
      <div
        v-clipboard:copy="copy_list"
        v-clipboard:success="onCopy"
        v-clipboard:error="onError"
      >复制卡密</div>
    </div> -->
  </div>
</template>

<script>
export default {
  components: {

  },
  props: {
    relief_list: {
      type: Array,
      default: ''
    },
    copy_list: {
      type: Array,
      default: ''
    },
    check_boolean: {
      type: Boolean,
      default: false
    }
  },
  name: 'record',
  data () {
    return {

    }
  },
  created () {

  },
  methods: {
    check (index) {
      this.$emit('check', index)
    },
    check_all () {
      this.$emit('check_all')
    },
    onCopy () {
      this.$emit('onCopy')
    },
    onError () {
      this.$msg('复制失败', 'cancel', 'middle', 1500)
    }
  }
}
</script>

<style scoped>
#record {
  width: 100%;
  height: auto;
  /* position: absolute;
        top: 82px;
        left: 0;
        right: 0;
        bottom: 0; */
}
.mescroll {
  height: auto;
}
.relief_list {
  width: 100%;
  height: auto;
  background-color: #fff;
}
.relief_info {
  width: 100%;
  height: 64px;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
.dual {
  background-color: #fff7ec;
}
.relief_info div {
  width: 93.75px;
  height: 64px;
  line-height: 64px;
  text-align: center;
  font-size: 14px;
  font-weight: 400;
  color: rgba(74, 65, 48, 1);
}
.relief_info .wrap {
  line-height: 20px;
  white-space: normal;
  word-break: break-all;
  word-wrap: break-word;
  display: flex;
  align-items: center;
  text-align: left;
}
.img_click {
  width: 93.75px;
  height: 64px;
  line-height: 64px;
  text-align: center;
  font-size: 14px;
  font-weight: 400;
  color: rgba(74, 65, 48, 1);
}
.img_click img {
  width: 14px;
  height: 14px;
}
.relief_info .change_color {
  color: #1979ff;
}
.relief_info .red_color {
  color: #ff1e1e;
}
.foot {
  width: 100%;
  height: 60px;
  background-color: #fed093;
  position: fixed;
  bottom: 0;
  left: 0;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  z-index: 9999;
}
.foot img {
  width: 14px;
  height: 14px;
  margin: 0 8px 0 24px;
}
.foot p {
  font-size: 16px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(74, 65, 48, 1);
  line-height: 22px;
}
.foot div {
  width: 108px;
  height: 36px;
  background: rgba(255, 133, 30, 1);
  border-radius: 4px;
  font-size: 16px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(255, 255, 255, 1);
  line-height: 36px;
  text-align: center;
  position: absolute;
  right: 10px;
}
</style>