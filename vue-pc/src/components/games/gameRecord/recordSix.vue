<template>
  <div class="recordNine">
    <table class="game_table">
      <tr>
        <th width="10%">期号</th>
        <th width="20%">投注时间</th>
        <th width="25%">开奖结果</th>
        <th width="7%">投注数量</th>
        <th width="7%">获得数量</th>
        <th width="7%">赢取</th>
        <th width="5%">详情</th>
		  </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td>
          <div class="result_num_arr" v-if="item.game_result_info.is_open == 1" >
            <span v-for="(box, index) in getStrArr(item.game_result_info.result)" :key="index">
              <img :src="getNumImage(box)" class="active">
            </span>
            <!-- <div class="result_look" @click="view(item)">[查看]</div> -->
             <!-- 结果为数字 -->
            <div
              class="result_num_arr_result"
              v-if="choosedGame.game_type_id == 17 || choosedGame.game_type_id == 20 || choosedGame.game_type_id == 53 || choosedGame.game_type_id == 56"
            >{{getStrNum(item.game_log_info.result, 0)}}</div>
            <div class="result_num_arr_result" v-if="choosedGame.game_type_id == 19 || choosedGame.game_type_id == 55">
              <span v-if="getStrNum(item.game_log_info.result, 0) == 1">龙</span>
              <span v-if="getStrNum(item.game_log_info.result, 0) == 2">虎</span>
            </div>
          </div>
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
.recordNine {
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