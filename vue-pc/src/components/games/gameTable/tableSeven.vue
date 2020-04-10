<template>
  <div class="tableSeven">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th>开奖结果</th>
        <th colspan="3">总和</th>
        <th>龙虎</th>
        <th>前三</th>
        <th>中三</th>
        <th>后三</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 83px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="result_box" v-if="gameItem.game_log_info.result">
            <div
              v-if="gameItem.game_log_info.part_one_result"
            >{{gameItem.game_log_info.part_one_result}}</div>
            <div
              v-if="gameItem.game_log_info.part_two_result"
            >{{gameItem.game_log_info.part_two_result}}</div>
            <div
              v-if="gameItem.game_log_info.part_three_result"
            >{{gameItem.game_log_info.part_three_result}}</div>
            <div
              v-if="gameItem.game_log_info.part_four_result"
            >{{gameItem.game_log_info.part_four_result}}</div>
            <div
              v-if="gameItem.game_log_info.part_five_result"
            >{{gameItem.game_log_info.part_five_result}}</div>
            <div
              v-if="gameItem.game_log_info.part_six_result"
            >{{gameItem.game_log_info.part_six_result}}</div>
          </div>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">{{getStrNum(gameItem.game_log_info.result, 0)}}</span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span style="color: #F239E8;" v-if="getStrNum(gameItem.game_log_info.result, 1) == 1">小</span>
            <span style="color: #14D4D0;" v-if="getStrNum(gameItem.game_log_info.result, 1) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span style="color: #1F70FF;" v-if="getStrNum(gameItem.game_log_info.result, 2) == 1">单</span>
            <span style="color: #C00;" v-if="getStrNum(gameItem.game_log_info.result, 2) == 2">双</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span style="color: #FB3A3A;" v-if="getStrNum(gameItem.game_log_info.result, 3) == 1">龙</span>
            <span style="color: #1F70FF;" v-if="getStrNum(gameItem.game_log_info.result, 3) == 2">虎</span>
            <span style="color: #018796;" v-if="getStrNum(gameItem.game_log_info.result, 3) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 1">豹</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 2">顺</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 3">对</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 4">半</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 1">豹</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 2">顺</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 3">对</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 4">半</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 1">豹</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 2">顺</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 3">对</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 4">半</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 5">杂</span>
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
import { defalutImg } from "@/config/mixin.js";
export default {
  name: "tableSeven",
  mixins: [defalutImg],
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
.tableSeven {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;

  .result_box {
    display: flex;
    align-items: center;
    justify-content: space-around;
    height: 45px;

    div {
      .result_ball(30px, 15px);
    }
  }
}
</style>