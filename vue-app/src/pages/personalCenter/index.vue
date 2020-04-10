<template>
  <div id="personalCenter">
    <div class="user_info">
      <div class="user_img">
        <img :src="user_img" alt />
      </div>
      <div class="user_name">
        <p class="nickname">{{nickname}} <span class="id">(ID:{{userId}})</span></p>
        <!-- <span class="id">ID：{{userId}}</span> -->
        <div class="bean">
          <img src="../../assets/images/icon/icon_lightbean@2x.png" alt />
          <p>乐豆</p>
          <span>{{bean | changeBigNum}}</span>
        </div>
      </div>
      <div class="edit_info" @click="editor_info()">
        <p>修改资料</p>
        <img src="../../assets/images/icon/icon_rightarrow2@2x.png" alt />
      </div>
    </div>
    <ul class="user_list">
      <li class="user_detail" v-for="(item, index) in list" :key="index">
        <p class="user_title">{{item.title}}</p>
        <ul class="child_list">
          <li
            class="child_info"
            v-for="(i, idx) in item.child_list"
            :key="idx"
            @click="turn_page(i.url)"
          >
            <img :src="i.img" alt />
            <p>{{i.title}}</p>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>

<script>
import { fiveIndex } from '@/config/mixin.js'
import {
  mapGetters,
  mapMutations
} from 'vuex'
export default {
  name: "personalCenter",
  mixins: [fiveIndex],
  data () {
    return {
      list: [
        {
          title: "资产信息",
          child_list: [
            {
              img: require("../../assets/images/icon/icon_member_inform@2x.png"),
              title: "基本信息",
              url: "/userInfo"
            },
            {
              img: require("../../assets/images/icon/icon_member_rank@2x.png"),
              title: "我的银行",
              url: "/myBank"
            },
            {
              img: require("../../assets/images/icon/icon_member_detail@2x.png"),
              title: "乐豆明细",
              url: "/beanDetail"
            },
            {
              img: require("../../assets/images/icon/icon_member_record@2x.png"),
              title: "兑换记录",
              url: "/exchangeRecord"
            }
          ]
        },
        {
          title: "红包管理",
          child_list: [
            {
              img: require("../../assets/images/icon/icon_member_redpacket@2x.png"),
              title: "发红包",
              url: "/sendPacket"
            },
            {
              img: require("../../assets/images/icon/icon_member_redpackeget@2x.png"),
              title: "收到红包记录",
              url: "/receiveRecord"
            },
            {
              img: require("../../assets/images/icon/icon_member_redpacketto@2x.png"),
              title: "我发的红包",
              url: "/mySend"
            }
          ]
        },
        {
          title: "会员福利",
          child_list: [
            {
              img: require("../../assets/images/icon/icon_member_relief@2x.png"),
              title: "每日救济",
              url: "/dailyRelief"
            },
            {
              img: require("../../assets/images/icon/icon_member_@2x.png"),
              title: "排行榜奖励",
              url: "/rankReward"
            },
            {
              img: require("../../assets/images/icon/icon_member_invite@2x.png"),
              title: "邀请好友",
              url: "/extensionIncome"
            }
          ]
        },
        {
          title: "信息与设置",
          child_list: [
            {
              img: require("../../assets/images/icon/icon_member_message@2x.png"),
              title: "站内信息",
              url: "/information"
            },
            {
              img: require("../../assets/images/icon/icon_member_password@2x.png"),
              title: "密码修改",
              url: "/modifyPwd"
            },
            {
              img: require("../../assets/images/icon/icon_member_securitycode@2x.png"),
              title: "安全密码修改",
              url: "/safePwd"
            },
            {
              img: require("../../assets/images/icon/icon_member_logout@2x.png"),
              title: "退出登录",
              url: ""
            }
          ]
        }
      ],
      nickname: "",
      bean: "",
      user_img: "",
      userId: ''
    };
  },
  computed: {
    ...mapGetters([
      'haveLogin',
      'userInfo'
    ])
  },
  created () {
    this.get_user_info();
  },
  methods: {
    get_user_info () {
      this.$Api(
        {
          api_name: "kkl.user.getUserInfo"
        },
        (erra, data) => {
          if (!erra) {
            this.nickname = data.data.nickname;
            this.bean = data.data.left_money;
            this.user_img = data.data.headimgurl;
            this.userId = data.data.id
          } else {
            this.$msg(erra.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    },
    // 修改资料
    editor_info () {
      this.$router.push({
        path: "/myInfo"
      });
    },
    // 跳转页面
    turn_page (path) {
      if (path == "") {
        // app退出登录
        // this.$native.logout();
        this.$Api({
          api_name: 'kkl.user.logout',
        }, (erra, res) => {
          if (!erra) {
            this.delUser()
            this.$router.replace({
              path: '/login'
            })
            localStorage.setItem('loginStatu',1)
          } else {
            this.$msg(erra.error_msg, 'cancel', 'middle', 1500)
          }
        })
        return;
      }
      this.$router.push({
        path: path
      });
    },
    ...mapMutations({
      delUser: 'DEL_USER'
    })
  }
};
</script>

<style scoped lang="less">
#personalCenter {
  width: 100%;
  /* height: 100%; */
  position: absolute;
  top: 44px;
  left: 0;
  right: 0;
  bottom: 0;
  .user_info {
    .wh(351px, 112px);
    margin: 8px auto;
    background: linear-gradient(
      90deg,
      rgba(255, 209, 148, 1) 0%,
      rgba(209, 145, 60, 1) 100%
    );
    border-radius: 4px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    position: relative;
    .user_img {
      .wh(64px, 64px);
      border-radius: 64px;
      border: 2px solid #4a4130;
      box-sizing: border-box;
      margin-left: 12px;
      margin-right: 12px;
      box-sizing: border-box;
      overflow: hidden;
      img {
        .wh(64px, 64px);
      }
    }
    .user_name {
      display: flex;
      // align-items: center;
      justify-content: flex-start;
      flex-direction: column;
      .nickname {
        font-size: 16px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(74, 65, 48, 1);
        line-height: 22px;
        margin-bottom: 8px;
      }
      .id {
        font-size: 12px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(74, 65, 48, 1);
        line-height: 22px;
      }
      .bean {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        img {
          .wh(24px, 24px);
          margin-right: 4px;
        }
        p {
          font-size: 14px;
          font-family: PingFangSC-Regular;
          font-weight: 400;
          color: rgba(74, 65, 48, 1);
          line-height: 20px;
          margin-right: 8px;
        }
        span {
          font-size: 14px;
          font-family: PingFangSC-Medium;
          font-weight: 500;
          color: rgba(255, 30, 30, 1);
          line-height: 20px;
        }
      }
    }
    .edit_info {
      .wh(88px, 32px);
      background: rgba(74, 65, 48, 1);
      border-radius: 16px;
      position: absolute;
      right: 12px;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      p {
        font-size: 14px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(254, 208, 147, 1);
        line-height: 20px;
        margin-left: 12px;
      }
      img {
        .wh(16px, 16px);
      }
    }
  }
  .user_list {
    .wh(100%, auto);
    &:last-child {
      padding-bottom: 60px;
    }
    .user_detail {
      .wh(351px, 112px);
      background: rgba(255, 247, 236, 1);
      border-radius: 8px;
      margin: 0 auto 8px auto;
      overflow: hidden;
      .user_title {
        font-size: 16px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(74, 65, 48, 1);
        line-height: 22px;
        margin-top: 12px;
        margin-left: 8px;
        margin-bottom: 14px;
      }
      .child_list {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        .child_info {
          .wh(87.75px, auto);
          display: flex;
          align-items: center;
          justify-content: center;
          flex-direction: column;
          img {
            .wh(32px, 32px);
            margin-bottom: 5px;
          }
          p {
            font-size: 12px;
            font-family: PingFangSC-Medium;
            font-weight: 500;
            color: rgba(74, 65, 48, 1);
            line-height: 17px;
          }
        }
      }
    }
  }
}
</style>