<template>
  <div class="betTableTwo">
    <div v-for="(betList, index) in list" :key="'00'+index" style="border-bottom: 1px solid #e8e8e8;">
      <div class="mode_title">{{modeNames[index]}}</div>
      <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        <tr>
          <th>号码</th>
          <th>赔率</th>
          <th>投注</th>
        </tr>
        <tr v-for="(item, index) in betList" :key="index" class="tr">
          <td style="width: 74px;">
            <div class="ball_box">
              <div class="ball">{{item.name}}</div>
            </div>
          </td>
          <td>{{item.rate}}</td>
          <td>
            <div class="inp_box">
              <input
                type="number"
                v-model="item.bet"
                :placeholder="item.have_bet"
                @focus="inputFocus(item)"
                @keyup="inputChange(item)"
              >
            </div>
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>
<script>
import { gameMixns,gameTypeMixins } from "@/config/gameMixin.js";
export default {
  name: "betTableTwo",
  data() {
    return {};
  },
  mixins: [gameMixns, gameTypeMixins],
  props: {
    list: {
      type: Array,
      default: () => []
    },
    modeNames: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    chooseItem: function (key) {
      let chooseIndex = this.list.findIndex(item => item.key == key)
      this.list[chooseIndex].chooseed = !this.list[chooseIndex].chooseed
      if (this.list[chooseIndex].chooseed) {
        this.$emit('countDefault', chooseIndex)
      } else {
        this.list[chooseIndex].bet = ''
      }
    }
  }
};
</script>
<style scoped lang='less'>
.betTableTwo {
  .ball_box {
    display: flex;
    padding: 10px 23px;
    box-sizing: border-box;
    .ball {
      width: 28px;
      height: 28px;
      border-radius: 28px;
      line-height: 28px;
      font-size: 14px;
      white-space: nowrap;
    }
  }
  .mode_title{
      width: 100%;
      height: 34px;
      line-height: 34px;
      text-align: center;
      font-size: 15px;
  }

  .inp_box {
    height: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
    input {
      width: 220px;
      // flex: 1;
      height: 32px;
      background: #f2f2f2;
      border-radius: 4px;
      padding-left: 4px;
      box-sizing: border-box;
      border: none;
    }
  }
}
</style>