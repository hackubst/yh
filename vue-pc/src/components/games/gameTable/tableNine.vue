<template>
  <div class="tableFour">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th width="65">期 号</th>
        <th width="116">开奖时间</th>
        <th width="310">开奖结果</th>
        <th width="200" colspan="3">冠亚</th>
        <th width="300" colspan="5">
          <table width="100%" class="longhu" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td colspan="5">龙虎</td>
              </tr>
              <tr class="longhunum">
                <td>
                  <span>1</span>
                </td>
                <td>
                  <span>2</span>
                </td>
                <td>
                  <span>3</span>
                </td>
                <td>
                  <span>4</span>
                </td>
                <td>
                  <span>5</span>
                </td>
              </tr>
            </tbody>
          </table>
        </th>
        <th width="120">投注额/中奖额</th>
        <th width="120">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="result_num_arr" v-if="gameItem.is_open == 1" >
            <span v-for="(item, index) in getStrArr(gameItem.result)" :key="index">
              <img
                :src="getNumImage(item)"
                class="active"
              >
            </span>
            <div class="result_look" v-if="choosedGame.game_type_id == 46" @click="view(gameItem)">[查看]</div>
          </div>
        </td>
        <td class="guanya">
          <span class="result_number" v-if="gameItem.game_log_info.result">{{getStrNum(gameItem.game_log_info.result, 0)}}</span>
        </td>
        <td class="guanya">
          <span v-if="gameItem.game_log_info.result">
            <span class="result_size_small" v-if="getStrNum(gameItem.game_log_info.result, 1) == 1">小</span>
            <span class="result_size_big" v-if="getStrNum(gameItem.game_log_info.result, 1) == 2">大</span>
          </span>
        </td>
        <td class="guanya">
          <span v-if="gameItem.game_log_info.result">
            <span class="result_even_dan" v-if="getStrNum(gameItem.game_log_info.result, 2) == 1">单</span>
            <span class="result_even_shuang" v-if="getStrNum(gameItem.game_log_info.result, 2) == 2">双</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="gameItem.game_log_info.result">
            <span class="long" v-if="getStrNum(gameItem.game_log_info.result, 3) == 1">龙</span>
            <span class="hu" v-if="getStrNum(gameItem.game_log_info.result, 3) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="gameItem.game_log_info.result">
            <span class="long" v-if="getStrNum(gameItem.game_log_info.result, 4) == 1">龙</span>
            <span class="hu" v-if="getStrNum(gameItem.game_log_info.result, 4) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="gameItem.game_log_info.result">
            <span class="long" v-if="getStrNum(gameItem.game_log_info.result, 5) == 1">龙</span>
            <span class="hu" v-if="getStrNum(gameItem.game_log_info.result, 5) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="gameItem.game_log_info.result">
            <span class="long" v-if="getStrNum(gameItem.game_log_info.result, 6) == 1">龙</span>
            <span class="hu" v-if="getStrNum(gameItem.game_log_info.result, 6) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="gameItem.game_log_info.result">
            <span class="long" v-if="getStrNum(gameItem.game_log_info.result, 7) == 1">龙</span>
            <span class="hu" v-if="getStrNum(gameItem.game_log_info.result, 7) == 2">虎</span>
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
        <td class="state">
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
import { mapGetters } from "vuex";
export default {
  name: "tableNine",
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
  },
  computed: {
    ...mapGetters(["choosedGame"])
  }
};
</script>
<style scoped lang='less'>
.tableFour {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;
  .result_look {
    font-size: 12px;
    color: #D1913C;
    margin-left: 10px;
    width: 40px;
    cursor: pointer;
  }
  .result_number {
    color: #018796;
  }
  .result_size_small {
    color: #FD00FF;
  }
  .result_size_big {
    color: #14D4D0;
  }
  .result_even_dan {
    color: #1909F7;
  }
  .result_even_shuang {
    color: #C00;
  }
  .long {
    color: #1200FF;
  }
  .hu {
    color: #F00;
  }
}
</style>