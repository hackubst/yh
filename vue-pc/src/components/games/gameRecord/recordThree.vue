<template>
  <div class="recordTwo">
    <table class="game_table">
      <tr>
        <th width="8%">期号</th>
        <th width="14%">投注时间</th>
        <th width="25%">开奖结果</th>
        <th width="10%">球一</th>
        <th width="10%">球二</th>
        <th width="7%">投注数量</th>
			  <th width="7%">获得数量</th>
			  <th width="7%">赢取</th>
			  <th width="10%">详情</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td class="regular" valign="middle">
          <!-- <div class="ball_result_box" v-if="item.game_log_info.result">
            <img :src="getBallIcon('blue', item.game_log_info.part_one_result)" alt>
            <img :src="getBallIcon('yellow', item.game_log_info.part_two_result)" alt>
            <img :src="getBallIcon('pink', item.game_log_info.part_three_result)" alt>
            <div class="ball_result_look" @click="view(item)">[查看]</div>
          </div> -->
          <div class="only_num" v-if="item.game_log_info.result">
            <img :src="getResultNum(item.game_log_info.part_one_result)">
            +
            <img :src="getResultNum(item.game_log_info.part_two_result)">
            +
            <img :src="getResultNum(item.game_log_info.part_three_result)">
          </div>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="bg_red" v-if="getStrNum(item.game_log_info.result, 0) == 1">庄</span>
            <span class="bg_blue" v-if="getStrNum(item.game_log_info.result, 0) == 2">闲</span>
            <span class="bg_green" v-if="getStrNum(item.game_log_info.result, 0) == 3">和</span>
          </span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="bg_red" v-if="getStrNum(item.game_log_info.result, 1) == 1">庄</span>
            <span class="bg_blue" v-if="getStrNum(item.game_log_info.result, 1) == 2">闲</span>
            <span class="bg_green" v-if="getStrNum(item.game_log_info.result, 1) == 3">和</span>
          </span>
        </td>
        <td>{{item.total_bet_money | changeBigNum}}</td>
        <td>{{item.total_after_money | changeBigNum}}</td>
        <td>{{item.win_loss}}</td>
        <td style="color: rgba(209,145,60,1); cursor: pointer;" @click="view_bet(item)">查看</td>
      </tr>
    </table>
  </div>
</template>

<script>
import { mapMutations, mapActions } from "vuex";
import { getGameType, getTableType } from "@/config/config.js";
import { gameCuntDown, defalutImg, filterNum, judgeMixin } from "@/config/mixin.js";
export default {
  data () {
    return {
    }
  },
  props: {
    list: Array
  },
  mixins: [gameCuntDown, defalutImg, filterNum, judgeMixin],
  methods: {
    view(item) {
      console.log(item)
      this.$emit("view", item);
    },
    view_bet (item) {
      this.$emit('view_bet', item)
    }
  }
}
</script>

<style scoped lang='less'>
.recordTwo {
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
  .game_table {
    th {
      vertical-align: middle;
    }
    td {
      vertical-align: middle;
      .result_num_arr {
        span {
          margin-right: 0;
          img {
            width: 22px;
            height: 22px;
          }
        }
      }
    }
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