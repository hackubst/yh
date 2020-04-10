<template>
  <div>
    <div class="red-dialog" v-if="haveRed">
      <div class="main">
        <!-- 没有登录的样式 -->
        <div class="no-login" v-if="!haveLogin">
          <p>领取该红包需要登录</p>
          <p>或注册成为用户</p>
        </div>
        <!-- 登录的样式 -->
        <div class="have-login" v-if="haveLogin">
          <img src="~@/assets/images/icon/icon_redpacket@2x.png" alt>
          <p>
            获得
            <span>{{each_money}}</span> 金豆红包(已存入银行)
          </p>
        </div>
        <div class="btn-box">
          <div class="btn-box" v-if="!isSuccess">
            <div class="btn-item left" @click="closeDialog()">取消</div>
            <div class="btn-item right" @click="goLogin()" v-if="!haveLogin">去登录</div>
            <div class="btn-item right" @click="getRedDialog()" v-if="haveLogin">领取</div>
          </div>
          <div v-if="isSuccess">
            <div class="btn-item right" @click="sendPacket()">我要发红包</div>
          </div>
        </div>
      </div>
    </div>
    <div class="red-dialog" v-if="!haveRed">
      <div class="main">
        <!-- 没有登录的样式 -->
        <div class="no-login" v-if="!haveLogin">
          <p>领取该红包需要登录</p>
          <p>或注册成为用户</p>
        </div>
        <!-- 登录的样式 -->
        <div class="have-login" v-if="haveLogin">
          <img src="~@/assets/images/icon/icon_redpacket@2x.png" alt>
          <p>
            红包已领完
          </p>
        </div>
        <div class="btn-box">
          <div class="btn-item left" @click="closeDialog()">取消</div>
          <div class="btn-item right" @click="goLogin()" v-if="!haveLogin">去登录</div>
          <!-- <div class="btn-item right" @click="getRedDialog()" v-if="haveLogin">领取</div> -->
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {
  getStore
} from '@/config/utils'
export default {
  name: "red-dialog",
  model: {
    prop: "dialog_show",
    event: "change"
  },
  props: {
    dialog_show: {
      type: Boolean,
      default: false
    },
    jiami_id: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      haveLogin: false,
      each_money: 0,
      isSuccess: false,
      haveRed: true
    };
  },
  created() {
    //   获取用户信息
    this.$Api(
      {
        api_name: "kkl.user.getUserInfo"
      },
      (err, data) => {
        if (data.code == 0) {
          this.haveLogin = true;
          // 获取红包详情
          this.$Api(
            {
              api_name: "kkl.index.getRedPacketInfo",
              red_packet_id: this.jiami_id
            },
            (err, data) => {
              if (!err) {
                this.haveRed = true
                this.each_money = data.data.red_packet_info.each_money;
              } else {
                this.haveRed = false
              }
            }
          );
        } else {
          this.haveLogin = false;
        }
      }
    );
  },
  methods: {
    sendPacket () {
      this.$router.push({
        path: '/sendPacket'
      })
    },
    closeDialog() {
      this.$emit("change", false);
    },
    getRedDialog() {
      this.$Api(
        {
          api_name: "kkl.index.getRedPacket",
          red_packet_id: this.jiami_id
        },
        (err, data) => {
          if (data.code == 0) {
            this.$toast({text: '领取成功'});
            this.isSuccess = true
            this.$native.native_clean_url();
            setTimeout(() => {
              this.$emit("change", false);
            }, 1500);
          } else {
            this.$msg(err.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    },
    goLogin() {
      // 如果是h5
      let is_app = getStore('is_app')
      if (is_app && Number(is_app) == 0) {
        this.$router.replace({
          path: 'login'
        });
      } else {
        this.$native.native_login();
      }
      
    }
  }
};
</script>
<style scoped lang='less'>
.red-dialog {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  .main {
    padding: 20px;
    box-sizing: border-box;
    border-radius: 8px;
    background: #fff;
    .no-login {
      margin-bottom: 24px;
      p {
        text-align: center;
        font-size: 18px;
        color: rgba(0, 0, 0, 0.87);
      }
    }
    .have-login {
      margin-bottom: 24px;
      text-align: center;
      img {
        width: 72px;
        height: 72px;
        border-radius: 72px;
        margin-bottom: 23px;
      }
      p {
        text-align: center;
        font-size: 18px;
        color: rgba(0, 0, 0, 0.87);
        span {
          color: #ff1e1e;
        }
      }
    }
  }
  .btn-box {
    width: 276px;
    display: flex;
    align-items: center;
    justify-content: center;
    .btn-item {
      font-size: 18px;
      text-align: center;
      width: 137px;
      height: 41px;
      line-height: 41px;
      border-radius: 4px;
    }
    .left {
      color: rgba(0, 0, 0, 0.87);
      background: rgba(242, 242, 242, 1);
    }
    .right {
      color: #fed093;
      background: #4a4130;
    }
  }
}
</style>