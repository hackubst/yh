<template>
  <div class="awardTableTwo">
    <table
      border="1"
      cellpadding="0"
      cellspacing="0"
      style="border-collapse:collapse; table-layout: fixed;"
    >
      <tr>
        <th>期号</th>
        <th>时间</th>
        <th>投注</th>
      </tr>
      <tr v-for="(item, index) in gameResultList" :key="index" @click="gameInfo(item)">
        <td>{{item.issue}}</td>
        <td>
          <!-- <span v-if="item.is_open == 0 || item.is_open == 2">{{item.addtime | formatDateMonth}}</span> -->
          <span v-if="item.is_open == 0 || item.is_open == 2">{{item.addtime_str}}</span>
          <div v-else-if="numsResultGame(game_type_id)">
            <div class="result_nums_box flex" style="height: 48px;">
              <div v-if="item.game_log_info.part_one_result">
                <img :src="getNumImage(item.game_log_info.part_one_result)" alt class="active" />
              </div>
              <div v-if="item.game_log_info.part_two_result">
                <img :src="getNumImage(item.game_log_info.part_two_result)" alt class="active" />
              </div>
              <div v-if="item.game_log_info.part_three_result">
                <img :src="getNumImage(item.game_log_info.part_three_result)" alt class="active" />
              </div>
              <div v-if="item.game_log_info.part_four_result">
                <img :src="getNumImage(item.game_log_info.part_four_result)" alt class="active" />
              </div>
              <div v-if="item.game_log_info.part_five_result">
                <img :src="getNumImage(item.game_log_info.part_five_result)" alt class="active" />
              </div>
            </div>
          </div>
          <div v-else>
            <div class="flex-center result_box">
              <div class="ball">{{item.game_log_info.part_one_result}}</div>
              <div class="ball">{{item.game_log_info.part_two_result}}</div>
              <div class="ball">{{item.game_log_info.part_three_result}}</div>
              <div
                class="ball"
                v-if="item.game_log_info.part_four_result"
              >{{item.game_log_info.part_four_result}}</div>
              <div
                class="ball"
                v-if="item.game_log_info.part_five_result"
              >{{item.game_log_info.part_five_result}}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="bet_box flex" :class="['douzi_all', { win_douzi: parseInt(item.game_log_info.win_reward) > parseInt(item.game_log_info.bet_reward) }, { lose_douzi: parseInt(item.game_log_info.win_reward) < parseInt(item.game_log_info.bet_reward) }, { pointer: parseInt(item.game_log_info.bet_reward) > 0 }]">
            <div class="bet_btn" v-if="item.is_open == 0" @click.stop="betClick(item)">投注</div>
            <div class="bet_btn" style="background: #FF1E1E;" v-if="item.is_open == 2">开奖中</div>
            <img src="~images/icon/icon_rightarrow@2x.png" alt class="right_arrow" v-else />
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
  name: "awardTableTwo",
  data() {
    return {
      game_type_id: this.$route.query.game_type_id
    };
  },
  computed: {
    ...mapGetters(["gameResultList"])
  },
  mixins: [gameTypeMixins, resultMixins],
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
      if (
        item.game_log_info.game_type_id == 7 ||
        item.game_log_info.game_type_id == 29 ||
        item.game_log_info.game_type_id == 39
      ) {
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
  
</style>