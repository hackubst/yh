<template>
  <div class="tableTwo">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th style="width: 493px;">开奖结果</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 83px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="result" v-if="gameItem.game_log_info.result">
            <img :src="getResultNum(gameItem.game_log_info.part_one_result)" alt>
            +
            <img :src="getResultNum(gameItem.game_log_info.part_two_result)" alt>
            +
            <img :src="getResultNum(gameItem.game_log_info.part_three_result)" alt>
            =
            <div class="result_num">
              <!-- <span v-if=""></span> -->
              <span>{{gameItem.game_log_info.result}}</span>
            </div>
            <div class="result_look" @click="view(gameItem)">[查看]</div>
          </div>
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
import { filterNum } from "@/config/mixin.js";
export default {
  name: "tableTwo",
  mixins: [filterNum],
  props: {
    gameResultList: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {};
  },
  methods: {
    view(item) {
      this.$emit("view", item);
    },
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
.tableTwo {
  margin-bottom: 40px;
}
</style>