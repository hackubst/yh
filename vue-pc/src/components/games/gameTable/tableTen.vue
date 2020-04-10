<template>
  <div class="tableSix">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 72px;">期号</th>
        <th style="width: 122px;">开奖时间</th>
        <th style="min-width: 243px;">开奖结果</th>
        <th colspan="4">中奖</th>
        <th style="width: 145px;">投注额/中奖额</th>
        <th style="width: 83px;">投注</th>
      </tr>
      <tr v-for="(gameItem, index) in gameResultList" :key="index">
        <td>{{gameItem.issue}}</td>
        <td>{{gameItem.addtime_str}}</td>
        <td>
          <!-- 开奖结果为数字 -->
          <div
            class="result_num_arr"
            v-if="numsResultGame(choosedGame.game_type_id) && gameItem.is_open == 1"
          >
            <span v-for="(item, index) in getStrArr(gameItem.result)" :key="index">
              <img
                :src="getNumImage(item)"
                alt
                :class="ifHaveItem(item, gameItem.game_log_info.result) ? 'active':''"
              />
            </span>
            <!-- 结果为数字 -->
            <div
              class="result_num_arr_result"
              v-if="choosedGame.game_type_id == 17 || choosedGame.game_type_id == 20 || choosedGame.game_type_id == 53 || choosedGame.game_type_id == 56"
            >{{getStrNum(gameItem.game_log_info.result, 0)}}</div>
            <!-- 结果为龙虎和 -->
            <div
              class="result_num_arr_result"
              v-if="choosedGame.game_type_id == 19 || choosedGame.game_type_id == 55"
            >
              <span v-if="getStrNum(gameItem.game_log_info.result, 0) == 1">龙</span>
              <span v-if="getStrNum(gameItem.game_log_info.result, 0) == 2">虎</span>
              <span v-if="getStrNum(gameItem.game_log_info.result, 0) == 3">和</span>
            </div>
          </div>
          <div
            class="result_num_str"
            v-else-if="choosedGame.game_type_id == 34 && gameItem.is_open == 1"
          >
            <div>{{gameItem.game_log_info.result}}</div>
            <span class="result_look" @click="view(gameItem)">[查看]</span>
          </div>
          <div class="result" v-else-if="gameItem.is_open == 1">
            <img :src="getResultNum(gameItem.game_log_info.part_one_result)" alt />
            <span>+</span>
            <img
              :src="getResultNum(gameItem.game_log_info.part_three_result)"
              alt
              v-if="onlyTwoGame(choosedGame.game_type_id)"
            />
            <img :src="getResultNum(gameItem.game_log_info.part_two_result)" alt v-else />
            <span v-if="!onlyTwoGame(choosedGame.game_type_id)">+</span>
            <img
              :src="getResultNum(gameItem.game_log_info.part_three_result)"
              alt
              v-if="!onlyTwoGame(choosedGame.game_type_id)"
            />
            =
            <div class="result_num" v-if="oneResultGame(choosedGame.game_type_id)">
              <span>{{gameItem.game_log_info.result}}</span>
            </div>
            <!-- 结果为汉字的游戏表格 -->
            <div v-else-if="strResultGame(choosedGame.game_type_id)">
              <div
                class="result_str"
                style="background: #66ff33;"
                v-if="gameItem.game_log_info.result == 1"
              >豹</div>
              <div
                class="result_str"
                style="background: #B822DD;"
                v-else-if="gameItem.game_log_info.result == 2"
              >顺</div>
              <div
                class="result_str"
                style="background: #3C3CC4;"
                v-else-if="gameItem.game_log_info.result == 3"
              >对</div>
              <div
                class="result_str"
                style="background: #EE1111;"
                v-else-if="gameItem.game_log_info.result == 4"
              >半</div>
              <div class="result_str" style="background: #1AE6E6;" v-else>杂</div>
            </div>
            <span class="result_look" @click="view(gameItem)">[查看]</span>
          </div>
        </td>
        <td>
          <div class="result" v-if="gameItem.game_log_info.result">
            <div
              class="result_str"
              style="background: #66ff33;"
              v-if="judgeJunko(gameItem.game_log_info.part_one_result, gameItem.game_log_info.part_two_result, gameItem.game_log_info.part_three_result) === 1"
            >豹</div>
            <div
              class="result_str"
              style="background: #B822DD;"
              v-else-if="judgeJunko(gameItem.game_log_info.part_one_result, gameItem.game_log_info.part_two_result, gameItem.game_log_info.part_three_result) === 2"
            >顺</div>
            <div
              class="result_str"
              style="background: #3C3CC4;"
              v-else-if="judgeJunko(gameItem.game_log_info.part_one_result, gameItem.game_log_info.part_two_result, gameItem.game_log_info.part_three_result) === 3"
            >对</div>
            <div
              class="result_str"
              style="background: #EE1111;"
              v-else-if="judgeJunko(gameItem.game_log_info.part_one_result, gameItem.game_log_info.part_two_result, gameItem.game_log_info.part_three_result) === 4"
            >半</div>
            <div class="result_str" style="background: #1AE6E6;" v-else>杂</div>
          </div>
        </td>
        <td>
          <div class="result" v-if="gameItem.game_log_info.result">
            <div
              class="result_str"
              style="background: #EE1111;"
            >{{judgeSize(gameItem.game_log_info.result)}}</div>
          </div>
        </td>
        <td>
          <div class="result" v-if="gameItem.game_log_info.result">
            <div
              class="result_str"
              style="background: #EE1111;"
            >{{judgeEven(gameItem.game_log_info.result)}}</div>
          </div>
        </td>
        <td>
          <div class="result" v-if="gameItem.game_log_info.result">
            <div
              class="result_str"
              style="font-size: 12px;background: #EE1111;"
            >{{judgeSize(gameItem.game_log_info.result)}}{{judgeEven(gameItem.game_log_info.result)}}</div>
          </div>
        </td>
        <td>
          <div
            :class="['douzi_all', { win_douzi: parseInt(gameItem.game_log_info.win_reward) > parseInt(gameItem.game_log_info.bet_reward) }, { lose_douzi: parseInt(gameItem.game_log_info.win_reward) < parseInt(gameItem.game_log_info.bet_reward) }, { pointer: parseInt(gameItem.game_log_info.bet_reward) > 0 }]"
            :title="parseInt(gameItem.game_log_info.bet_reward) > 0 ? '查看' : ''"
            @click="parseInt(gameItem.game_log_info.bet_reward) > 0 ? lookRecord() : ''"
          >
            <img src="~images/icon/icon_douzi@2x.png" alt />
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
import { filterNum, defalutImg } from "@/config/mixin.js";
import { mapGetters } from "vuex";
export default {
  name: "tableTen",
  mixins: [filterNum, defalutImg],
  data() {
    return {
      numArr: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    };
  },
  props: {
    gameResultList: {
      type: Array,
      default: () => []
    },
    gameTableType: {
      type: Number,
      default: 6
    }
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
.tableSix {
  width: @main-width;
  margin: 0 auto;
  margin-bottom: 40px;
  .result_look {
    font-size: 12px;
    color: #d1913c;
    margin-left: 10px;
  }
  .result_look:hover {
    cursor: pointer;
  }
  .result_num_str {
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    div {
      .result_ball(24px, 14px);
    }
  }
  td {
    .result {
      height: 100%;
      .result_str {
        width: 30px;
        height: 30px;
        line-height: 30px;
      }
    }
  }
  
}
</style>