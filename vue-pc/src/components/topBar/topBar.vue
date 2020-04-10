<template>
  <div class="topBar">
    <div class="top">
      <div class="top_main" v-if="!haveLogin">
        您好，欢迎来到金龙28！<span class="pointer" @click="changeMobile">手机版</span>
      </div>
      <div class="top_main have_longin" v-else>
        <div class="top_left">
          您好，{{userInfo.nickname}}(ID：{{userInfo.id}})
          <img :src="getUserGrad" alt="">
          <span @click="outLogin()" class="pointer">
            [退出]
          </span>
        </div>
        <div class="top_right">
          <span class="pointer" style="margin-right: 10px;" @click="toTrial">争霸赛</span>
          <span class="pointer" style="margin-right: 10px;" @click="changeMobile">手机版</span>
          <div class="right_item">
            <img src="~images/icon/icon_douzi@2x.png" alt="">
            <div>
              <span class="title">乐豆：</span>
              <span class="num_info">{{userInfo.left_money | changeBigNum}}</span>
            </div>
          </div>
          <div class="right_item">
            <img src="~images/icon/icon_douzi@2x.png" alt="">
            <div>
              <span class="title">银行：</span>
              <span class="num_info">{{userInfo.frozen_money | changeBigNum}}</span>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    <div class="bottom" v-if="!isExperience">
      <div class="bot_main">
        <router-link tag="div" to="/index" active-class="acitve">网站首页</router-link>
        <router-link tag="div" to="/businessCooperation" active-class="acitve">商务合作</router-link>
        <router-link tag="div" to="/activityArea" active-class="acitve">活动专区</router-link>
        <router-link tag="div" to="/gameIndex" active-class="acitve">游戏乐园</router-link>
        <div class="icon">
          <img src="@/assets/images/bg/pic_logo@2x.png" alt="" srcset="">
        </div>
        <router-link tag="div" to="/moneyChanger" active-class="acitve">兑换中心</router-link>
        <router-link tag="div" to="/userCenter" active-class="acitve">用户中心</router-link>
        <router-link tag="div" to="/gameRanking" active-class="acitve">游戏排行</router-link>
        <!-- <router-link tag="div" to="/trialGameRanking" active-class="acitve">战绩榜</router-link> -->
        <router-link tag="div" to="/extensionIncome" active-class="acitve">推广收益</router-link>
      </div>
    </div>
  </div>
</template>

<script>
  import {
    mapGetters,
    mapMutations
  } from 'vuex'
  import {
    defalutImg
  } from '@/config/mixin'
  export default {
    name: 'topBar',
    mixins: [defalutImg],
    data() {
      return {}
    },
    computed: {
      ...mapGetters([
        'haveLogin',
        'userInfo',
        'isExperience'
      ])
    },
    methods: {
      // 退出登录
      outLogin() {
        this.$Api({
          api_name: 'kkl.user.logout'
        }, (err, data) => {
           if(!err){
              this.delUser()
           }else{
             this.$Alert(err.error_msg, '提示')
           }
        })
      },
      ...mapMutations({
        delUser: 'DEL_USER'
      }),
      // 切换手机版
      changeMobile() {
        this.$Api(
          {
            api_name: "kkl.user.changeMobileWap",
            is_wap: 0
          },
          (err, data) => {
            if (data.code === 0) {
              window.location.reload();
            }
          }
        );
      },
      // 跳转体验版
      toTrial() {
        try {
          this.$Api({
            api_name: 'kkl.user.getTestUrl'
          }, (err, data) => {
            console.table(data);
            if (data.code === 0) {
              const {url} = data.data;
              location.href = url;
            }
          });
        } catch (error) {
          throw error;
        }
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped lang='less'>
  .topBar {
    width: 100%;
    min-width: @main-width;
    // height: 140px;
  }

  .top {
    background: #fff;
    font-size: 14px;
    color: rgba(51, 51, 51, 1);
    height: 40px;
    line-height: 40px;

    .top_main {
      width: @main-width;
      margin: 0 auto;
    }

    .have_longin {
      display: flex;
      align-items: center;
      justify-content: space-between;

      .top_left {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: #333333;

        img {
          width: 61px;
          height: 26px;
          margin-right: 12px;
          margin-left: 6px;
        }
      }

      .top_right {
        display: flex;
        align-items: center;

        .right_item {
          display: flex;
          align-items: center;
          font-size: 14px;

          img {
            width: 16px;
            height: 16px;
            margin-right: 4px;
          }

          .title {
            color: #4A4130;
          }

          .num_info {
            color: #FF0000;
          }
        }

        .right_item:first-child {
          margin-right: 28px;
        }
      }
    }
  }

  .bottom {
    height: 100px;
    width: 100%;
    background: url('~@/assets/images/bg/bg_toubu@2x.png') no-repeat;
    background-size: 100% 100px;

    .bot_main {
      display: flex;
      align-items: center;
      width: @main-width;
      margin: 0 auto;

      div {
        font-size: 16px;
        color: #FFDCAD;
        text-align: center;
        line-height: 100px;
        flex-grow: 1;
      }

      .acitve {
        position: relative;

        &:after {
          content: ' ';
          width: 64px;
          height: 4px;
          background: #FFDCAD;
          position: absolute;
          left: 50%;
          margin-left: -32px;
          bottom: 0;
        }
      }

      .icon {
        width: 100px;
        height: 81px;

        img {
          width: 60px;
          height: 81px;
        }
      }
    }
  }
</style>
