<template>
  <div id="app" :style="'padding-bottom:'+pbPx+'px'">
    <top-bar v-if="!$route.meta.noHead"></top-bar>
    <router-view/>
    <div v-if="is_app == 0">
      <foot-bar v-if="$route.meta.noBotBar"></foot-bar>
    </div>
  </div>
</template>

<script>
  import {
    getStore,
    setStore,
  } from './config/utils'
  var ua = navigator.userAgent
  var is_app = 0
  if (navigator['APP-TYPE']) {
    //ios
    is_app = 1
    setStore('is_app', is_app)
  } else {
    if (ua.indexOf('yrkj_app') != -1) {
      //anz
      is_app = 1
      setStore('is_app', is_app)
    } else {
      is_app = 0
      setStore('is_app', is_app)
    }
  }
  import topBar from "@/components/common/topBar";
  import footBar from "@/components/common/footBar";
  import { judgeBar } from "@/config/mixin";
  export default {
    name: "App",
    components: {
      footBar,
      topBar
    },
    mixins: [judgeBar],
    data() {
      return {
        pbPx: 0,
        is_app: null
      };
    },
    created() {
      let is_app = getStore('is_app')
      if (is_app) {
        if (is_app == '0') {
          // 非app
          this.is_app = 0
        } else {
          // app
          this.is_app = 1
        }
      }

      // if (this.$route.meta.noBotBar) {
      //   this.pbPx = 0;
      // } else {
      //   this.pbPx = 49;
      // }
    }
  };
</script>

<style>
  @import "../static/css/base.css";

  .vux-slider {
    width: 100%;
  }

  .vux-slider > .vux-swiper {
    width: 100%;
  }

  .vux-swiper-item {
    width: 100%;
  }

  .vux-slider {
    width: 100%;
  }
  #exchange .weui-dialog__btn_primary {
    color: #FED093 !important;
  }

  html,
  body {
    background-color: #fff;
  }

  #app {
    min-height: 100%;
    padding-top: 44px;
    box-sizing: border-box;
    /*background: #fff;*/
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
  }
</style>
