<template>
  <div class="tableFour">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th style="width: 130px;">开奖结果</th>
        <th style="width: 32px;">1:3</th>
        <th style="width: 32px;">2:3</th>
        <th style="width: 32px;">豹对</th>
        <th style="width: 32px;">五行</th>
        <th style="width: 32px;">四季</th>
        <th style="width: 32px;">星座</th>
        <th style="width: 32px;">生肖</th>
        <th colspan="2">前二</th>
        <th colspan="2">后二</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 82px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="result_two" v-if="gameItem.game_log_info.result">
            <div class="result_two_top">
              <img :src="getResultNum(gameItem.game_log_info.part_one_result)" alt>
              +
              <img :src="getResultNum(gameItem.game_log_info.part_two_result)" alt>
              +
              <img :src="getResultNum(gameItem.game_log_info.part_three_result)" alt>
              =
              <div class="result_num">
                <span>{{getStrNum(gameItem.game_log_info.result, 0)}}</span>
              </div>
            </div>
            <div class="result_look" @click="view(gameItem)">[查看]</div>
          </div>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 1) == 1">龙</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 1) == 2">虎</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 1) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 2) == 1">龙</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 2) == 2">虎</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 2) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 3) == 1">豹</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 3) == 2">顺</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 3) == 3">对</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 3) == 4">半</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 3) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 1">金</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 2">木</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 3">水</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 4">火</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 4) == 5">土</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 1">春</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 2">夏</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 3">秋</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 5) == 4">冬</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 1">水瓶</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 2">双鱼</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 3">白羊</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 4">金牛</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 5">双子</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 6">巨蟹</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 7">狮子</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 8">处女</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 9">天秤</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 10">天蝎</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 11">射手</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 6) == 12">摩羯</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 1">鼠</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 2">牛</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 3">虎</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 4">兔</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 5">龙</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 6">蛇</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 7">马</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 8">羊</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 9">猴</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 10">鸡</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 11">狗</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 7) == 12">猪</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 8) == 1">小</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 8) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 9) == 1">单</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 9) == 2">双</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 10) == 1">小</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 10) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="gameItem.game_log_info.result">
            <span v-if="getStrNum(gameItem.game_log_info.result, 11) == 1">单</span>
            <span v-if="getStrNum(gameItem.game_log_info.result, 11) == 2">双</span>
          </span>
        </td>
        <td class="please">
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
  name: "tableFour",
  mixins: [filterNum, defalutImg],
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
    lookRecord() {
      this.$router.replace({
        path: "/gameIndex/bettingRecord"
      });
    }
  }
};
</script>
<style scoped lang='less'>
.tableFour {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;
}
</style>