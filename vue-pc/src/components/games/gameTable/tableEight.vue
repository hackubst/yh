<template>
  <div class="tableEight">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th>开奖结果</th>
        <th>前三</th>
        <th>中三</th>
        <th>后三</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 83px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <!-- <td>{{gameItem.addtime | formatDateMonth}}</td> -->
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="result_box" v-if="gameItem.game_log_info.result">
             <img :src="getNumImage(gameItem.game_log_info.part_one_result)" alt="">
             <img :src="getNumImage(gameItem.game_log_info.part_two_result)" alt="">
             <img :src="getNumImage(gameItem.game_log_info.part_three_result)" alt="">
             <img :src="getNumImage(gameItem.game_log_info.part_four_result)" alt="">
             <img :src="getNumImage(gameItem.game_log_info.part_five_result)" alt="">
          </div>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span>{{filterPard(getStrNum(gameItem.game_log_info.result, 0))}}</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span>{{filterPard(getStrNum(gameItem.game_log_info.result, 1))}}</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span>{{filterPard(getStrNum(gameItem.game_log_info.result, 2))}}</span>
          </span>
        </td>
        <td>
          <div :class="['douzi_all', { win_douzi: parseInt(gameItem.game_log_info.win_reward) > parseInt(gameItem.game_log_info.bet_reward) }, { lose_douzi: parseInt(gameItem.game_log_info.win_reward) < parseInt(gameItem.game_log_info.bet_reward) }, { pointer: parseInt(gameItem.game_log_info.bet_reward) > 0 }]"
                :title="parseInt(gameItem.game_log_info.bet_reward) > 0 ? '查看' : ''"
                @click="parseInt(gameItem.game_log_info.bet_reward) > 0 ? lookRecord() : ''"
          >
            <img src="~images/icon/icon_douzi@2x.png" alt>
            {{gameItem.game_log_info.bet_reward | changeBigNum}}/{{gameItem.game_log_info.win_reward | changeBigNum}}
          </div>
        </td>
        <td>
          <div class="table_btn">
            <div class="guess" v-if="gameItem.is_open == 0" @click="guessing(gameItem)">竞猜</div>
            <!-- <div class="guess" v-if="gameItem.is_open == 0 && gameItem.game_log_info.is_bet  == 1" @click="lookRecord()">查看</div> -->
            <div class="haveaward" v-if="gameItem.is_open == 1">已开奖</div>
            <div class="awarding" v-if="gameItem.is_open == 2">开奖中</div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { defalutImg, filterNum } from "@/config/mixin.js";
import { trendChart } from '@/config/trendChart.js'
export default {
  name: "tableEight",
  mixins: [defalutImg, trendChart, filterNum],
  data() {
    return {};
  },
  props: {
    gameResultList: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    // 竞猜
    guessing(gameItem) {
      this.$emit("guessing", gameItem);
    },
    lookRecord(){
      this.$router.replace({
        path: '/gameIndex/bettingRecord'
      })
    }
  }
};
</script>
<style scoped lang='less'>
.tableEight {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;

  .result_box {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 45px;
    img{
        width: 25px;
        height: 25px;
        margin-right: 6px;
    }
  }
}
</style>