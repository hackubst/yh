<template>
  <div class="awardTableOne">
    <table
      border="1"
      cellpadding="0"
      cellspacing="0"
      style="border-collapse:collapse; table-layout: fixed;"
    >
      <tr>
        <th>期号</th>
        <th width='130'>时间</th>
        <th>投注</th>
      </tr>
      <tr v-for="(item, index) in gameResultList" :key="index" @click="gameInfo(item)">
        <td>{{item.issue}}</td>
        <td>
          <!-- <span v-if="item.is_open == 0 || item.is_open == 2">{{item.addtime | formatDateMonth}}</span> -->
          <span v-if="item.is_open == 0 || item.is_open == 2">{{item.addtime_str}}</span>
          <div class="poker_result flex" v-else-if="pokerResultGame(game_type_id)">
            <div
              class="poker_item"
              :style="'background:'+ filterPoker(result_item).color"
              v-for="(result_item, sec_index) in getStrArr(item.game_log_info.result)"
              :key="'01'+sec_index"
            >{{filterPoker(result_item).name}}</div>
          </div>
          <div v-else-if="numsResultGame(game_type_id)" class="flex">
            <div class="result_nums_box flex" style="width: 120px;">
              <div v-for="(numItem, secIndex) in getStrArr(item.result)" :key="secIndex">
                <img
                  :src="getNumImage(numItem)"
                  alt
                  class="active"
                  v-if="game_type_id == 46 || game_type_id == 51 || game_type_id == 57"
                >
                <img
                  :src="getNumImage(numItem)"
                  alt
                  :class="ifHaveItem(numItem, item.game_log_info.result) ? 'active':''"
                  v-else
                >
              </div>
              
              <!-- <span class="result_num_arr_result" v-if="game_type_id != 19">{{getStrNum(item.game_log_info.result, 0)}}</span> -->
              <!-- <span class="result_num_arr_result" v-if="game_type_id != 20">{{getStrNum(item.game_log_info.result, 0)}}</span> -->
            </div>
            <div class="ball" v-if="game_type_id == 19 || game_type_id == 55">{{filterTiger(getStrNum(item.game_log_info.result, 0))}}</div>
            <div class="ball" v-if="game_type_id == 17 || game_type_id == 53 || game_type_id == 20 || game_type_id == 56">{{getStrNum(item.game_log_info.result, 0)}}</div>
          </div>
          <div class="result_box flex" v-else-if="onlyOneGame(game_type_id)">
              <div class="ball">{{item.game_log_info.result}}</div>
          </div>
          <!-- 只有两个区 -->
          <div class="result_box flex" v-else-if="onlyTwoGame(game_type_id)">
            <span>{{item.game_log_info.part_one_result}}</span>
            +
            <span>{{item.game_log_info.part_three_result}}</span>
            =
            <div class="ball">{{item.game_log_info.result}}</div>
          </div>
          <div class="result_box flex" v-else>
            <span>{{item.game_log_info.part_one_result}}</span>
            +
            <span>{{item.game_log_info.part_two_result}}</span>
            +
            <span>{{item.game_log_info.part_three_result}}</span>
            =
            <div
              class="ball"
              :style="'background:' + filterPard(item.game_log_info.result).color"
              v-if="strResultGame(game_type_id)"
            >{{filterPard(item.game_log_info.result).name}}</div>
            <div
              class="ball"
              v-else-if="oneStrRestultGame(game_type_id)"
            >{{getStrNum(item.game_log_info.result, 0)}}</div>
            <div class="ball" v-else>{{item.game_log_info.result}}</div>
          </div>
        </td>
        <td>
          <div class="bet_box flex" :class="['douzi_all', { win_douzi: parseInt(item.game_log_info.win_reward) > parseInt(item.game_log_info.bet_reward) }, { lose_douzi: parseInt(item.game_log_info.win_reward) < parseInt(item.game_log_info.bet_reward) }, { pointer: parseInt(item.game_log_info.bet_reward) > 0 }]">
            <div class="bet_btn" v-if="item.is_open == 0" @click.stop="betClick(item)">投注</div>
            <div class="bet_btn" style="background: #FF1E1E;" v-if="item.is_open == 2">开奖中</div>
            <img src="~images/icon/icon_rightarrow@2x.png" alt class="right_arrow" v-else>
            {{item.game_log_info.bet_reward | changeBigNum}} / {{item.game_log_info.win_reward | changeBigNum}}
          </div>
        </td>
      </tr>
    </table>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import { gameTypeMixins } from "@/config/gameMixin.js";
import { resultMixins } from "@/config/resultMixin.js";
export default {
  name: "awardTableOne",
  data() {
    return {
      game_type_id: this.$route.query.game_type_id
    };
  },
  mixins: [gameTypeMixins, resultMixins],
  computed: {
    ...mapGetters(["gameResultList"])
  },
  methods: {
    betClick(item) {
      this.$router.push({
        path: "/gameBet",
        query: {
          game_type_id: this.$route.query.game_type_id,
          game_result_id: item.game_result_id
        }
      });
    },
    gameInfo(item) {
      if (item.is_open == 0 || item.is_open == 2) return;
        if (item.game_log_info.game_type_id == 7 || item.game_log_info.game_type_id == 29 || item.game_log_info.game_type_id == 39) {
          this.$router.push({
            path: "/awardDetailBar",
            query: {
              item: item
            }
          });
        } else {
          this.$router.push({
            path: "/awardDetail",
            query: {
              item: item
            }
          });
        }
    }
  }
};
</script>
<style scoped lang='less'>
.result_nums_box {
  position: relative;
}

.result_num_arr_result {
  width: 22px;
  height: 22px;
  border-radius: 20px;
  font-size: 13px;
  color: #fff;
  background: linear-gradient(180deg,#f9d423,#ff4e50);
  line-height: 22px;
  text-align: center;
  position: absolute;
  right: -30px;
  top: 50%;
  margin-top: -13px;
}
</style>