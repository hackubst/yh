<template>
  <div class="gameRecord">
  <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
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
        <th>获得</th>
        <th>赢取</th>
      </tr>
      <tr v-for="(item, index) in recordList" :key="index" @click="goDetail(item)">
        <td>{{item.issue}}</td>
        <td>{{item.addtime | formatTimeDay}}</td>
        <td>
          <span class="red">{{item.total_bet_money | changeBigNum}}</span>
        </td>
        <td>
          <span class="blue">{{item.total_after_money | changeBigNum}}</span>
        </td>
        <td>
          <span class="red" v-if="item.is_win == 1">{{item.win_loss | changeBigNum}}</span>
          <span class="blue" v-else>{{item.win_loss | changeBigNum}}</span>
        </td>
      </tr>
    </table>
   </mescroll-vue>
  </div>
</template>
<script>
import MescrollVue from "mescroll.js/mescroll.vue";
import { Promise } from 'q';
import {
  setStore,
  getStore
} from '../../config/utils'
export default {
  name: "gameRecord",
  data() {
    return {
      recordList: [],
      mescroll: null,
      mescrollDown: {}, //下拉刷新的配置.
      mescrollUp: {
        callback: this.upCallback,
        page: {
          num: 0,
          size: 20
        },
        htmlNodata: '<p class="upwarp-nodata">-- END --</p>'
      }
    };
  },
  methods: {
    getRecordList(firstRow, pageSize){
       return new Promise((resolve, reject) => {
          this.$Api({
            api_name: 'kkl.game.getBetLogList',
            game_type_id: this.$route.query.game_type_id,
            firstRow: firstRow,
            pageSize: pageSize
          }, (err, data) => {
            if(!err){
              this.listTotal = parseInt(data.data.total)
              resolve(data.data.bet_log_list ||[])
            }
          })
       })
    },
    // 上拉加载
    upCallback(e) {
      this.getRecordList((e.num - 1) * e.size, e.size).then(res => {
        let list = [];
        if (e.num == 1) {
          list = [...res];
        } else {
          list = [...this.recordList, ...res];
        }
        this.recordList = list;
        this.$nextTick(() => {
          this.mescroll.endSuccess(list.length);
          this.mescroll.endBySize(list.length, this.listTotal);
        });
      });
    },
    mescrollInit(mescroll) {
      this.mescroll = mescroll;
    },
    goDetail (item) {
      this.$router.push({
        path: 'gameRecordInfo'
      })
      setStore('gameRecordInfo', item)
    }
  },
  components: {
    MescrollVue
  }
};
</script>
<style scoped lang='less'>
.gameRecord {
  .mescroll {
    position: fixed;
    top: 92px;
    bottom: 0;
    height: auto;
  }
  .red {
    color: #ff1e1e;
  }
  .blue {
    color: #1979ff;
  }
}
</style>