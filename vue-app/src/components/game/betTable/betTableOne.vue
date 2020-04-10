<template>
  <div class="betTableOne">
    <table border="1" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
      <tr>
        <th>号码</th>
        <th>赔率</th>
        <th>已投注</th>
        <th>选择</th>
        <th>投注</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td style="width: 74px;">
          <div class="ball_box">
            <div class="ball" v-if="game_type_id == 19 || game_type_id == 55">{{filterTiger(item.key, 0)}}</div>
            <div class="ball" v-if="game_type_id == 58 || game_type_id == 59 || game_type_id == 60 || game_type_id == 61">{{item.name}}</div>
            <div
              class="ball"
              :style="'background:' + filterPard(item.key).color"
              v-else-if="strResultGame(game_type_id)"
            >{{  (item.key).name}}</div>
            <div class="ball" v-else>{{item.key}}</div>
          </div>
        </td>
        <td>{{item.rate}}</td>
        <td>
          <span v-if="item.have_bet">{{item.have_bet}}</span>
          <span v-else>0</span>
        </td>
        <td>
          <div class="check_box" @click="chooseItem(item.key)">
            <img src="~images/icon/icon_xuanzhong@2x.png" alt="" v-if="item.chooseed">
            <img src="~images/icon/icon_weixuanzhong@2x.png" alt="" v-else>
          </div>
        </td>
        <td>
          <div class="inp_box">
            <input
              type="number"
              v-model="item.bet"
              @focus="inputFocus(item)"
              @keyup="inputChange(item)"
            >
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { gameMixns, gameTypeMixins } from "@/config/gameMixin.js";
import { resultMixins } from "@/config/resultMixin.js";
export default {
  name: "betTableOne",
  data() {
    return {
      game_type_id: this.$route.query.game_type_id
    };
  },
  mixins: [gameMixns, gameTypeMixins, resultMixins],
  props: {
    list: {
      type: Array,
      default: () => []
    }
  },
  
  mounted () {
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
.betTableOne {
  .ball_box {
    display: flex;
    padding: 10px 23px;
    box-sizing: border-box;
    .ball {
      width: 28px;
      height: 28px;
      border-radius: 28px;
      line-height: 28px;
    }
  }
  .check_box {
    width: 52px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;

    img {
      width: 16px;
      height: 16px;
    }
  }
  
  .inp_box {
    height: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
    input {
      width: 90px;
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