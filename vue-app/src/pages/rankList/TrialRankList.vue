<template>
  <div id="rankList">
    <ul class="nav_list">
      <li
        class="nav_info"
        :class="{active: currentIndex == index}"
        v-for="(item, index) in list"
        :key="index"
        @click="change_index(index)"
      >{{item.title}}</li>
    </ul>
    <ul class="list_info is_pad">
      <li class="info_detail" :class="{dual: idx%2 != 0}" v-for="(i, idx) in rank_list" :key="idx">
        <div class="icon">
          <img v-if="idx == 0" src="../../assets/images/icon/img_rankfirst@2x.png" alt>
          <img v-if="idx == 1" src="../../assets/images/icon/img_ranksecond@2x.png" alt>
          <img v-if="idx == 2" src="../../assets/images/icon/img_rankthrid@2x.png" alt>
          <p v-if="idx>2">{{idx + 1}}</p>
        </div>
        <div class="nickname">{{i.nickname}}</div>
        <div class="total_fee">{{i.total | changeBigNum}}</div>
      </li>
    </ul>
  </div>
</template>

<script>
import { fiveIndex } from '@/config/mixin.js'
export default {
  name: "rankList",
  mixins: [fiveIndex],
  data() {
    return {
      list: [
        {
          title: "今日排行"
        },
        {
          title: "七日排行"
        }
      ],
      currentIndex: 0,
      rank_list: []
    };
  },
  created() {
    this.get_rank_list();
  },
  methods: {
    get_rank_list() {
      this.$Api(
        {
          api_name: "kkl.index.rankList"
        },
        (err, data) => {
          if (!err) {
            let res = data.data;
            if (this.currentIndex == 0) {
              this.rank_list = res.today_list;
            } else if (this.currentIndex == 1) {
              this.rank_list = res.sevenday_list;
            }
          } else {
            this.$msg(err.error_msg, "cancel", "middle", 1500);
          }
        }
      );
    },
    change_index(index) {
      this.rank_list = [];
      this.currentIndex = index;
      this.get_rank_list();
    }
  }
};
</script>

<style scoped lang="less">
#rankList {
  width: 100%;
  /* height: 100%; */
  position: absolute;
  top: 44px;
  left: 0;
  right: 0;
  bottom: 0;
}
.nav_list {
  width: 100%;
  height: 48px;
  background-color: #4a4130;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
.nav_info {
  // width: 125px;
  flex: 1;
  height: 48px;
  line-height: 48px;
  text-align: center;
  font-size: 17px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(255, 237, 212, 1);
  position: relative;
}
.active {
  color: #fed093;
}
.active::after {
  content: "";
  width: 48px;
  height: 4px;
  background-color: #fed093;
  position: absolute;
  left: 50%;
  margin-left: -24px;
  bottom: 4px;
}
.list_info {
  .wh(100%, auto);
  .info_detail {
    .wh(100%, 72px);
    display: flex;
    align-items: center;
    justify-content: flex-start;
    position: relative;
    .icon {
      .wh(48px, 48px);
      display: flex;
      justify-content: center;
      align-items: center;
      margin-left: 12px;
      margin-right: 12px;
      img {
        .wh(100%; 100%);
      }
      p {
        .wh(32px, 32px);
        background-color: #4a4130;
        border-radius: 50%;
        margin-right: 12px;
        text-align: center;
        line-height: 32px;
        font-size: 24px;
        font-family: PingFangSC-Medium;
        font-weight: 500;
        color: rgba(254, 208, 147, 1);
      }
    }
    .nickname {
      width: 195px;
      font-size: 16px;
      font-family: PingFangSC-Regular;
      font-weight: 400;
      color: rgba(74, 65, 48, 1);
      line-height: 22px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    .total_fee {
      width: 95px;
      font-size: 16px;
      font-family: PingFangSC-Medium;
      font-weight: 500;
      color: rgba(255, 30, 30, 1);
      line-height: 22px;
      position: absolute;
      right: 12px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
  }
  .dual {
    background-color: #fff7ec;
  }
}
</style>