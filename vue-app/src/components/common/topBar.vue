<template>
<div class="topBar flex">
  <div class="left-arrow flex-center">
    <img src="~images/icon/icon_backarrow@2x.png" alt="" v-if="!noArrow" @click="back()">
  </div>
  <span>
      {{title}}
    </span>
  <div>
    <slot>
      <div class="btn pc-btn" v-if="hasPc" @click="changePc">电脑版</div>
    </slot>
  </div>
</div>
</template>
<script>
  export default {
    name: "topBar",
    data() {
      return {
        noArrow: true,
        hasPc: false,
        hasRanking: false,
        title: ''
      };
    },
    methods: {
      back(){
        console.log()
        if(this.$route.meta.appBack){
          this.$native.back()
        }else{
          this.$router.go(-1)
        }
      },
      changePc() {
        this.$Api(
          {
            api_name: "kkl.user.changeMobileWap",
            is_wap: 1
          },
          (err, data) => {
            if (data.code === 0) {
              window.location.reload();
            }
          }
        );
      },
      toRanking() {
        this.$router.push({
          path: '/trialRankList'
        })
      }
    },
    created () {
      this.noArrow = this.$route.meta.noArrow;
      this.title = this.$route.meta.title;
      this.hasPc = this.$route.meta.hasPc;
      this.hasRanking = this.$route.meta.hasRanking;
    },
    watch: {
      '$route'(){
        this.noArrow = this.$route.meta.noArrow
        this.title = this.$route.meta.title
        this.hasPc = this.$route.meta.hasPc || false;
        this.hasRanking = this.$route.meta.hasRanking || false;
      }
    }
  }
</script>
<style scoped lang='less'>
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

    .left-arrow {
      width: 24px;
      height: 44px;
      img {
        height: 24px;
        width: auto;
      }
    }
  }
</style>
