<template>
  <div class="recordDialog">
    <div class="main">
      <div class="title">
        <span>第{{recordItem.issue}}期中奖名单</span>
        <span class="pointer" @click="close()">关闭</span>
      </div>
      <div class="table_box">
        <div class="table_title">第{{recordItem.issue}}期投注详细结果</div>
        <div class="table_info">
          开奖时间：{{recordItem.opentime | formatDateYearTime}} 投注：{{recordItem.total_bet_money | changeBigNum }}
          <img
            src="~images/icon/icon_douzi@2x.png"
            alt
          />
          获得：{{recordItem.win_loss}}
          <img src="~images/icon/icon_douzi@2x.png" alt />
        </div>
        <table border="1" cellpadding="0" cellspacing="0" class="game_table" style="width: 100%;">
          <tr>
            <th>号码</th>
            <th>标准赔率</th>
            <th>开奖赔率</th>
            <th>投注数量</th>
            <th>获得数量</th>
          </tr>
          <tr v-for="(item, index) in infoList" :key="index">
            <td>
              <div
                style="width: 100%; height: 45px; display: flex; align-items: center; justify-content: center;"
              >
                <div class="ball">{{item.name}}</div>
              </div>
            </td>
            <td>
              <span>{{item.rate}}</span>
            </td>
            <td>
              <span v-if="item.now_rate">{{item.now_rate.toFixed(4)}}</span>
            </td>
            <td>
              <span v-if="item.money">{{item.money}}</span>
              <span v-else>0</span>
            </td>
            <td>{{item.win}}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "recordDialog",
  model: {
    prop: "showDialog",
    event: "change"
  },
  props: {
    showDialog: {
      type: Boolean,
      default: false
    },
    recordItem: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      infoList: []
    };
  },
  methods: {
    close() {
      this.$emit("change", false);
    },
    // 将两个数组融合成一个
    blendArr(childArr, rootArr) {
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
    getInfoList(arr) {
      for (let i = 0; i < arr.length; i++) {
        this.infoList = [...this.infoList, ...arr[i].bet_json];
      }
    }
  },
  created() {
    let bet_json = JSON.parse(this.recordItem.bet_json);
    let old_bet_rate = JSON.parse(this.recordItem.old_bet_rate);
    if (old_bet_rate.length > 1) {
      for (let i = 0; i < old_bet_rate.length; i++) {
        let bet_json = old_bet_rate[i].bet_json;
        bet_json.forEach(element => {
          element.name = `${element.name} (${old_bet_rate[i].name})`;
        });
      }
    }
    this.blendArr(bet_json, old_bet_rate);
  }
};
</script>
<style scoped lang='less'>
.recordDialog {
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
  .main {
    width: @main-width;
    background: #fff;
    .title {
      height: 28px;
      padding: 0px 15px;
      font-size: 16px;
      color: #4a4130;
      display: flex;
      justify-content: space-between;
      box-sizing: border-box;
      align-items: center;
      margin-bottom: 14px;
    }
    .table_box {
      padding: 0 14px 14px;
      box-sizing: border-box;
      max-height: 600px;
      overflow-y: scroll;
      .table_title {
        height: 32px;
        font-size: 18px;
        width: 100%;
        border: 1px solid #e8e8e8;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
      }
      .table_info {
        height: 28px;
        font-size: 16px;
        width: 100%;
        border: 1px solid #e8e8e8;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        img {
          width: 16px;
          height: 16px;
          margin-left: 4px;
          margin-right: 4px;
        }
      }
      .ball {
        font-size: 16px;
        // .result_ball(28px, 12px);
      }
    }
  }
}
</style>