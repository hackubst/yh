<template>
  <div class="gameRecordInfo">
    <div class="top">
      <h2 class="title">第{{item.issue}}期投注详情结果</h2>
      <p>开奖时间: {{item.opentime | formatDateYearTime}} 投注: {{item.total_bet_money | changeBigNum }} 获得: {{item.win_loss}}</p>
    </div>
    <div class="tab">
      <div class="head">
        <p>号码</p>
        <p>标准赔率</p>
        <p>开奖赔率</p>
        <p>投注数量</p>
        <p>获得数量</p>
      </div>
      <div class="body">
        <div class="item" v-for="(list, index) of infoList" :key="index">
          <p class="number">
            <span class="box">{{list.name}}</span>
          </p>
          <p class="standard">{{list.rate}}</p>
          <p class="opening">
            <span v-if="list.now_rate">{{list.now_rate.toFixed(4)}}</span>
            <span v-else></span>
          </p>
          <p class="bets">
            <span v-if="list.money">{{list.money}}</span>
            <span v-else>0</span>
          </p>
          <p class="quantity">{{list.win}}</p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import {
  setStore,
  getStore
} from '../../config/utils'
export default {
  name: "gameRecordInfo",
  data () {
    return {
      item: {},
      infoList: []
    };
  },
  methods: {
    // 获取数据
    getData () {
      let data = JSON.parse(getStore('gameRecordInfo'))
      this.item = data
      console.log(data)
      this.resultArr()
    },
    resultArr () {
      let bet_json = JSON.parse(this.item.bet_json);
      let old_bet_rate = JSON.parse(this.item.old_bet_rate);
      if (old_bet_rate.length > 1) {
        for (let i = 0; i < old_bet_rate.length; i++) {
          let bet_json = old_bet_rate[i].bet_json;
          bet_json.forEach(element => {
            element.name = `${element.name} (${old_bet_rate[i].name})`;
          });
        }
      }
      this.blendArr(bet_json, old_bet_rate);
    },
    // 将两个数组融合成一个
    blendArr (childArr, rootArr) {
      for (let i = 0; i < childArr.length; i++) {
        if (childArr[i].bet_json.length > 0) {
          let childJson = childArr[i].bet_json;
          let rootJson = rootArr.filter(
            item => item.part == childArr[i].part
          )[0].bet_json;
          for (let j = 0; j < childJson.length; j++) {
            let index = rootJson.findIndex(
              item => item.key == childJson[j].key
            );
            rootJson[index] = Object.assign(rootJson[index], childJson[j]);
          }
        }
      }
      this.getInfoList(rootArr);
    },
    getInfoList (arr) {
      for (let i = 0; i < arr.length; i++) {
        this.infoList = [...this.infoList, ...arr[i].bet_json];
      }
    }
  },
  mounted () {
    this.getData()
  },
  created () {

  }
};
</script>
<style scoped lang='less'>
.gameRecordInfo {
  .top {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    .title {
      font-size: 18px;
      font-family: PingFangSC-Medium, PingFangSC;
      font-weight: 500;
      color: rgba(51, 51, 51, 1);
      line-height: 50px;
    }
    p {
      font-size: 14px;
      font-family: PingFangSC-Regular, PingFangSC;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
      line-height: 20px;
    }
  }
  .tab {
    margin-top: 10px;
    .head {
      height: 38px;
      background: rgba(254, 208, 147, 1);
      display: flex;
      align-items: center;
      justify-content: center;
      p {
        width: 20%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-family: PingFangSC-Regular, PingFangSC;
        font-weight: 400;
        color: rgba(74, 65, 48, 1);
      }
    }
    .body {
      .item {
        height: 48px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        &:nth-child(2n) {
          background: #fff7ec;
        }
        p {
          width: 20%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 12px;
          font-family: PingFangSC-Regular, PingFangSC;
          font-weight: 400;
          color: rgba(74, 65, 48, 1);
          text-align: center;
          .box {
            // width: 28px;
            // height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            // background: rgba(255, 30, 30, 1);
            // border-radius: 50%;
            // color: #fff;
          }
        }
        .number {
        }
        .standard {
          color: rgba(74, 65, 48, 1);
        }
        .opening {
          // color: #ff1e1e;
          color: rgba(74, 65, 48, 1);
        }
        .bets {
          // color: #1979ff;
          color: rgba(74, 65, 48, 1);
        }
        .quantity {
          // color: #1979ff;
          color: rgba(74, 65, 48, 1);
        }
      }
    }
  }
}
</style>