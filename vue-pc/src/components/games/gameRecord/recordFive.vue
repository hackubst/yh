<template>
  <div class="recordTwo">
    <table class="game_table">
      <tr>
        <th width="8%">期号</th>
        <th width="14%">投注时间</th>
        <th width="25%">开奖结果</th>
        <th width="10%" colspan="3">总和</th>
        <th width="5%">龙虎</th>
        <th width="5%">前三</th>
        <th width="5%">中三</th>
        <th width="5%">后三</th>
        <th width="7%">投注数量</th>
			  <th width="7%">获得数量</th>
			  <th width="5%">赢取</th>
			  <th width="15%">详情</th>
		  </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td class="regular" valign="middle">
          <div class="result_box" v-if="item.game_log_info.result">
            <div
              v-if="item.game_log_info.part_one_result"
            >{{item.game_log_info.part_one_result}}</div>
            <div
              v-if="item.game_log_info.part_two_result"
            >{{item.game_log_info.part_two_result}}</div>
            <div
              v-if="item.game_log_info.part_three_result"
            >{{item.game_log_info.part_three_result}}</div>
            <div
              v-if="item.game_log_info.part_four_result"
            >{{item.game_log_info.part_four_result}}</div>
            <div
              v-if="item.game_log_info.part_five_result"
            >{{item.game_log_info.part_five_result}}</div>
            <div
              v-if="item.game_log_info.part_six_result"
            >{{item.game_log_info.part_six_result}}</div>
          </div>
        </td>
        <td>
          <span v-if="item.game_log_info.result">{{getStrNum(item.game_log_info.result, 0)}}</span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span style="color: #F239E8;" v-if="getStrNum(item.game_log_info.result, 1) == 1">小</span>
            <span style="color: #14D4D0;" v-if="getStrNum(item.game_log_info.result, 1) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span style="color: #1F70FF;" v-if="getStrNum(item.game_log_info.result, 2) == 1">单</span>
            <span style="color: #C00;" v-if="getStrNum(item.game_log_info.result, 2) == 2">双</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span style="color: #FB3A3A;" v-if="getStrNum(item.game_log_info.result, 3) == 1">龙</span>
            <span style="color: #1F70FF;" v-if="getStrNum(item.game_log_info.result, 3) == 2">虎</span>
            <span style="color: #018796;" v-if="getStrNum(item.game_log_info.result, 3) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 4) == 1">豹</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 2">顺</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 3">对</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 4">半</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 5) == 1">豹</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 2">顺</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 3">对</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 4">半</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 6) == 1">豹</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 2">顺</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 3">对</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 4">半</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 5">杂</span>
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