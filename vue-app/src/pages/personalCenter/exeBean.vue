<template>
  <div id="exeBean">
    <div class="exe_info">
      <img src="../../assets/images/icon/icon_exp@2x.png" alt>
      <p>可兑换经验</p>
      <span>{{more_exp}}</span>
    </div>
    <template v-if="+more_exp > 0">
      <div class="input_box">
        <img src="../../assets/images/icon/icon_bean@3x.png" alt>
        <input type="text" placeholder="请输入您要交换的经验数量" v-model="exe_bean">
      </div>
      <p class="exchange">
        交换可获得
        <span>{{exe_bean}}</span>个乐豆
      </p>
      <div class="btn" @click="exchange()">交换</div>
      <div v-html="content"></div>
    </template>
  </div>
</template>

<script>
export default {
  components: {},
  name: "exeBean",
  data() {
    return {
      exe_bean: "", // 乐豆数量
      content: "", // 富文本
      more_exp: "" // 可兑换经验
    };
  },
  created() {
    this.get_user_info();
  },
  methods: {
    get_user_info() {
      this.$Api(
        {
          api_name: "kkl.user.getUserInfo"
        },
        (err, res) => {
          console.log('getUserInfo', err, res);
          if (!err) {
            this.more_exp = res.data.more_exp;
          } else {
            this.$msg(err.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    },
    exchange() {
      this.$Api(
        {
          api_name: "kkl.user.exChangeExp",
          bean: this.exe_bean
        },
        (err, data) => {
          if (!err) {
            this.$msg(data.data, "success", "middle", 1500);
          } else {
            this.$msg(err.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    }
  }
};
</script>

<style scoped lang='less'>
#exeBean {
  width: 100%;
  /* height: 100%; */
  position: absolute;
  top: 44px;
  left: 0;
  right: 0;
  bottom: 0;
  .exe_info {
    // .wh(100%,auto);
    margin-top: 10px;
    margin-left: 12px;
    margin-bottom: 10px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    img {
      .wh(28px, 24px);
      margin-right: 10px;
    }
    p {
      font-size: 16px;
      font-family: PingFangSC-Regular;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
      line-height: 22px;
      margin-right: 12px;
    }
    span {
      font-size: 16px;
      font-family: PingFangSC-Medium;
      font-weight: 500;
      color: rgba(254, 208, 147, 1);
      line-height: 22px;
    }
  }
  .input_box {
    width: 351px;
    height: 48px;
    background: rgba(242, 242, 242, 1);
    border-radius: 4px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    img {
      .wh(18px; 18px);
      margin-right: 10px;
      margin-left: 15px;
    }
    input {
      border: none;
      outline: none;
      flex: 1;
      font-size: 16px;
      font-family: PingFangSC-Regular;
      font-weight: 400;
      color: #333;
      line-height: 22px;
      background: rgba(242, 242, 242, 1);
    }
    input::-webkit-input-placeholder {
      color: rgba(153, 153, 153, 1);
    }
  }
  .exchange {
    width: 100%;
    text-align: center;
    font-size: 16px;
    font-family: PingFangSC-Medium;
    font-weight: 500;
    color: rgba(74, 65, 48, 1);
    line-height: 22px;
    margin: 8px 0 12px 0;
    span {
      color: #fed093;
    }
  }
  .btn {
    width: 351px;
    height: 48px;
    background: linear-gradient(
      180deg,
      rgba(255, 209, 148, 1) 0%,
      rgba(209, 145, 60, 1) 100%
    );
    border-radius: 4px;
    margin: 0 auto 44px auto;
    font-size: 18px;
    font-family: PingFangSC-Medium;
    font-weight: 500;
    color: rgba(255, 255, 255, 1);
    line-height: 48px;
    text-align: center;
  }
}
</style>