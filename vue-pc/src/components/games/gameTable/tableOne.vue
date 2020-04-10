<template>
  <!-- 表格样式一 -->
  <div class="tableOne">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th style="width: 116px;">闲</th>
        <th style="width: 116px;">庄</th>
        <th style="width: 71px;">开奖结果</th>
        <th style="width: 187px;">开奖说明</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 83px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <div class="poker_box">
            <img
              :src="getPokerImg(item)"
              v-for="(item, oindex) in gameItem.game_log_info.xian_card"
              :key="oindex"
              alt
            >
          </div>
        </td>
        <td>
          <div class="poker_box">
            <img
              :src="getPokerImg(item)"
              v-for="(item, oindex) in gameItem.game_log_info.zhuang_card"
              :key="oindex"
              alt
            >
          </div>
        </td>
        <td>
          <div class="poker_result" v-if="gameItem.game_log_info.result">
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 1)">庄</div>
            <div class="bg_blue" v-if="judgeHaveNum(gameItem.game_log_info.result, 2)">闲</div>
            <div class="bg_green" v-if="judgeHaveNum(gameItem.game_log_info.result, 3)">和</div>
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 4)">大</div>
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 5)">小</div>
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 6)">庄对</div>
            <div class="bg_blue" v-if="judgeHaveNum(gameItem.game_log_info.result, 7)">闲对</div>
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 8)">任意对</div>
            <div class="bg_red" v-if="judgeHaveNum(gameItem.game_log_info.result, 9)">完美对</div>
          </div>
        </td>
        <td>
          <div
            class="moreline"
            v-if="gameItem.game_log_info.result"
          >{{gameItem.game_log_info.remark}}</div>
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
            <div class="haveAward_tow">
              <div
                class="haveAward_tow_btn"
                style="background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);"
                @click="guessing(gameItem)"
                v-if="gameItem.is_open == 0"
              >竞猜</div>
              <!-- <div
                class="haveAward_tow_btn"
                style="background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);"
                @click="lookRecord()"
                v-if="gameItem.is_open == 0 && gameItem.game_log_info.is_bet  == 1"
              >查看</div> -->
              <div class="haveAward_tow_btn" v-if="gameItem.is_open == 1">已开奖</div>
              <div
                class="haveAward_tow_btn"
                style="background: linear-gradient(360deg, rgba(255, 75, 31, 1) 0%, rgba(255, 144, 104, 1) 100%);"
                v-if="gameItem.is_open == 2"
              >开奖中</div>
              <div class="haveAward_tow_txt" @click="view(gameItem)" v-if="gameItem.is_open == 1">[点击查看]</div>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { defalutImg } from "@/config/mixin.js";
export default {
  name: "tableOne",
  mixins: [defalutImg],
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
.tableOne {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;
}
</style>