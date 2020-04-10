<template>
  <div class="tableFive">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 123px;">开奖时间</th>
        <th style="width: 387px;" class="text_left">闲庄</th>
        <th>球一</th>
        <th>球二</th>
        <th>投注额/中奖额</th>
        <th>投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="only_num" v-if="gameItem.game_log_info.result">
            <img :src="getResultNum(gameItem.game_log_info.part_one_result)" alt>
            +
            <img :src="getResultNum(gameItem.game_log_info.part_two_result)" alt>
            +
            <img :src="getResultNum(gameItem.game_log_info.part_three_result)" alt>
          </div>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span style="color: #FB3A3A;" v-if="getStrNum(gameItem.game_log_info.result, 0) == 1">庄</span>
            <span style="color: #1F70FF;" v-if="getStrNum(gameItem.game_log_info.result, 0) == 2">闲</span>
            <span style="color: #7ED321;" v-if="getStrNum(gameItem.game_log_info.result, 0) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span style="color: #FB3A3A;" v-if="getStrNum(gameItem.game_log_info.result, 1) == 1">庄</span>
            <span style="color: #1F70FF;" v-if="getStrNum(gameItem.game_log_info.result, 1) == 2">闲</span>
            <span style="color: #7ED321;" v-if="getStrNum(gameItem.game_log_info.result, 1) == 3">和</span>
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
            <div
              class="guess"
              v-if="gameItem.is_open == 0"
              @click="guessing(gameItem)"
            >竞猜</div>
            <!-- <div
              class="guess"
              v-if="gameItem.is_open == 0 && gameItem.game_log_info.is_bet  == 1"
              @click="lookRecord()"
            >查看</div> -->
            <div class="haveaward" v-if="gameItem.is_open == 1">已开奖</div>
            <div class="awarding" v-if="gameItem.is_open == 2">开奖中</div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { filterNum, defalutImg } from "@/config/mixin.js";
export default {
  name: "tableFive",
  mixins: [filterNum, defalutImg],
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
    lookRecord() {
      this.$router.replace({
        path: "/gameIndex/bettingRecord"
      });
    }
  }
};
</script>
<style scoped lang='less'>
.tableFive {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;
}
</style>