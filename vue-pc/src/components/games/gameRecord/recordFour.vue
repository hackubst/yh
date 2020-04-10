<template>
  <div class="recordFour">
    <table class="game_table">
      <tr>
        <th width="70">期号</th>
        <th width="110">投注时间</th>
        <th width="270">开奖结果</th>
        <th width="30">1:3</th>
        <th width="30">2:3</th>
        <th width="30">豹对</th>
        <th width="30">五行</th>
        <th width="30">四季</th>
        <th width="30">星座</th>
        <th width="30">生肖</th>
        <th width="50" colspan="2">前二</th>
        <th width="50" colspan="2">后二</th>
        <th width="80">投注数量</th>
        <th width="80">获得数量</th>
        <th width="70">赢取</th>
        <th width="50">详情</th>
      </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td>
          <div class="result_two" v-if="item.game_log_info.result">
            <div class="result_two_top">
              <img :src="getResultNum(item.game_log_info.part_one_result)" alt>
              +
              <img :src="getResultNum(item.game_log_info.part_two_result)" alt>
              +
              <img :src="getResultNum(item.game_log_info.part_three_result)" alt>
              =
              <div class="result_num">
                <span>{{getStrNum(item.game_log_info.result, 0)}}</span>
              </div>
              <span class="result_look" @click="view(item)">[查看]</span>
            </div>
          </div>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 1) == 1">龙</span>
            <span v-if="getStrNum(item.game_log_info.result, 1) == 2">虎</span>
            <span v-if="getStrNum(item.game_log_info.result, 1) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 2) == 1">龙</span>
            <span v-if="getStrNum(item.game_log_info.result, 2) == 2">虎</span>
            <span v-if="getStrNum(item.game_log_info.result, 2) == 3">和</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 3) == 1">豹</span>
            <span v-if="getStrNum(item.game_log_info.result, 3) == 2">顺</span>
            <span v-if="getStrNum(item.game_log_info.result, 3) == 3">对</span>
            <span v-if="getStrNum(item.game_log_info.result, 3) == 4">半</span>
            <span v-if="getStrNum(item.game_log_info.result, 3) == 5">杂</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 4) == 1">金</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 2">木</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 3">水</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 4">火</span>
            <span v-if="getStrNum(item.game_log_info.result, 4) == 5">土</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 5) == 1">春</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 2">夏</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 3">秋</span>
            <span v-if="getStrNum(item.game_log_info.result, 5) == 4">冬</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 6) == 1">水瓶</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 2">双鱼</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 3">白羊</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 4">金牛</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 5">双子</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 6">巨蟹</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 7">狮子</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 8">处女</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 9">天秤</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 10">天蝎</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 11">射手</span>
            <span v-if="getStrNum(item.game_log_info.result, 6) == 12">摩羯</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 7) == 1">鼠</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 2">牛</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 3">虎</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 4">兔</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 5">龙</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 6">蛇</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 7">马</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 8">羊</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 9">猴</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 10">鸡</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 11">狗</span>
            <span v-if="getStrNum(item.game_log_info.result, 7) == 12">猪</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 8) == 1">小</span>
            <span v-if="getStrNum(item.game_log_info.result, 8) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 9) == 1">单</span>
            <span v-if="getStrNum(item.game_log_info.result, 9) == 2">双</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 10) == 1">小</span>
            <span v-if="getStrNum(item.game_log_info.result, 10) == 2">大</span>
          </span>
        </td>
        <td>
          <span v-if="item.game_log_info.result">
            <span v-if="getStrNum(item.game_log_info.result, 11) == 1">单</span>
            <span v-if="getStrNum(item.game_log_info.result, 11) == 2">双</span>
          </span>
        </td>
        <td>{{item.total_bet_money | changeBigNum}}</td>
        <td>{{item.total_after_money | changeBigNum}}</td>
        <td>{{item.win_loss}}</td>
        <td style="color: rgba(209,145,60,1); cursor: pointer;" @click="view_bet(item)">查看</td>
      </tr>
      <!-- <tr>
        <th width="8%">期号</th>
        <th width="14%">投注时间</th>
        <th width="25%">开奖结果</th>
        <th width="10%" colspan="3">前二</th>
        <th width="10%" colspan="3">后二</th>
        <th width="7%">龙虎和</th>
        <th width="7%">投注数量</th>
			  <th width="7%">获得数量</th>
			  <th width="7%">赢取</th>
			  <th width="10%">详情</th>
		  </tr>
      <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td class="regular" valign="middle">
          <div class="ball_result_box" v-if="item.game_log_info.result">
            <img :src="getBallIcon('blue', item.game_log_info.part_one_result)" alt>
            <img :src="getBallIcon('yellow', item.game_log_info.part_two_result)" alt>
            <img :src="getBallIcon('pink', item.game_log_info.part_three_result)" alt>
            <div class="ball_result_look" @click="view(item)">[查看]</div>
          </div>
        </td>
        <td class="guanya">
          <span class="result_number" v-if="item.game_log_info.result">{{getStrNum(item.game_log_info.result, 0)}}</span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_size_small" v-if="getStrNum(item.game_log_info.result, 1) == 1">小</span>
            <span class="result_size_big" v-if="getStrNum(item.game_log_info.result, 1) == 2">大</span>
          </span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_even_dan" v-if="getStrNum(item.game_log_info.result, 2) == 1">单</span>
            <span class="result_even_shuang" v-if="getStrNum(item.game_log_info.result, 2) == 2">双</span>
          </span>
        </td>
        <td class="guanya">
          <span class="result_number" v-if="item.game_log_info.result">{{getStrNum(item.game_log_info.result, 3)}}</span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_size_small" v-if="getStrNum(item.game_log_info.result, 4) == 1">小</span>
            <span class="result_size_big" v-if="getStrNum(item.game_log_info.result, 4) == 2">大</span>
          </span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_even_dan" v-if="getStrNum(item.game_log_info.result, 5) == 1">单</span>
            <span class="result_even_shuang" v-if="getStrNum(item.game_log_info.result, 5) == 2">双</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 6) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 6) == 2">虎</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 6) == 3">和</span>
          </span>
        </td>
        <td>{{item.total_bet_money | changeBigNum}}</td>
        <td>{{item.total_after_money | changeBigNum}}</td>
        <td>{{item.win_loss}}</td>
        <td style="color: rgba(209,145,60,1); cursor: pointer;" @click="view_bet(item)">查看</td>
      </tr> -->
      <!-- <tr v-for="(item, index) in list" :key="index">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatDateYearTime}}</td>
        <td>
          <div class="result_num_arr" v-if="item.game_result_info.is_open == 1" >
            <span v-for="(box, index) in getStrArr(item.game_result_info.result)" :key="index">
              <img :src="getNumImage(box)" class="active">
            </span>
            <div class="result_look" @click="view(item)">[查看]</div>
          </div>
        </td>
        <td class="guanya">
          <span class="result_number" v-if="item.game_log_info.result">{{getStrNum(item.game_log_info.result, 0)}}</span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_size_small" v-if="getStrNum(item.game_log_info.result, 1) == 1">小</span>
            <span class="result_size_big" v-if="getStrNum(item.game_log_info.result, 1) == 2">大</span>
          </span>
        </td>
        <td class="guanya">
          <span v-if="item.game_log_info.result">
            <span class="result_even_dan" v-if="getStrNum(item.game_log_info.result, 2) == 1">单</span>
            <span class="result_even_shuang" v-if="getStrNum(item.game_log_info.result, 2) == 2">双</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 3) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 3) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 4) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 4) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 5) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 5) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 6) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 6) == 2">虎</span>
          </span>
        </td>
        <td class="longhu">
          <span v-if="item.game_log_info.result">
            <span class="long" v-if="getStrNum(item.game_log_info.result, 7) == 1">龙</span>
            <span class="hu" v-if="getStrNum(item.game_log_info.result, 7) == 2">虎</span>
          </span>
        </td>
        <td>{{item.total_bet_money | changeBigNum}}</td>
        <td>{{item.total_after_money | changeBigNum}}</td>
        <td>{{item.win_loss}}</td>
        <td style="color: rgba(209,145,60,1); cursor: pointer;" @click="view_bet(item)">查看</td>
      </tr> -->
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
.recordFour {
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