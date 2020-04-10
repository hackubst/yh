<template>
  <div class="gameInfo">
    <div class="top_box flex">
      <div
        class="top_item"
        :class="[{active: currentValue == item.value}, {small: topItem.length === 6}]"
        v-for="(item, index) in topItem"
        :key="index"
        @click="chooseItem(item.value)"
      >{{item.label}}</div>
    </div>
    <game-home v-if="currentValue == 'home'" :tableType="tableType" ref="gameHome"></game-home>
    <game-mode v-if="currentValue == 'mode'"></game-mode>
    <game-auto v-if="currentValue == 'auto'"></game-auto>
    <game-record v-if="currentValue == 'record'"></game-record>
    <game-rule v-if="currentValue == 'rule'"></game-rule>
    <game-statistic v-if="currentValue == 'statistic'"></game-statistic>
  </div>
</template>
<script>
import gameHome from "@/components/game/gameHome";
import gameRecord from "@/components/game/gameRecord";
import gameRule from "@/components/game/gameRule";
import gameStatistic from "@/components/game/gameStatistic";
import gameMode from "@/components/game/gameMode";
import gameAuto from "@/components/game/gameAuto";
import { getTableType, getLotteryTime } from "@/config/config.js";
import { judgeMixin } from "@/config/mixin.js";
import {mapMutations} from "vuex";
export default {
  name: "gameInfo",
  mixins: [judgeMixin],
  data() {
    return {
      game_type_id: this.$route.query.game_type_id,
      currentValue: 'home',
      topItem: [],
      tableType: 0 // 表格样式类型
    };
  },
  components: {
    gameHome,
    gameRecord,
    gameRule,
    gameStatistic,
    gameMode,
    gameAuto
  },
  methods: {
    ...mapMutations({
      setLotteryTime: "SET_LOTTERY_TIME"
    }),
    chooseItem(value) {
      if (value == this.currentValue) return;
      this.currentValue = value;
    }
  },
  beforeRouteEnter: (to, from, next) => {
    if (to.meta.title) {
      to.meta.title = to.query.game_type_name;
    }
    next();
  },
  beforeRouteLeave(to, from, next) {
    if (to.name !== "gameBet") {
      if(this.$refs.gameHome && this.$refs.gameHome.waitTimer){
        clearInterval(this.$refs.gameHome.waitTimer);
      }
    }
    next();
  },
  created() {
    if (this.judgeEdit(this.game_type_id)) {
      this.topItem = [
        {
          value: 'home',
          label: '首页'
        },
        {
          value: 'mode',
          label: '模式'
        },
        {
          value: 'auto',
          label: '自动'
        },
        {
          value: 'record',
          label: '记录'
        },
        {
          value: 'rule',
          label: '规则'
        },
        {
          value: 'statistic',
          label: '统计'
        }
      ];
    } else {
      this.topItem = [
        {
          value: 'home',
          label: '首页'
        },
        {
          value: 'record',
          label: '记录'
        },
        {
          value: 'rule',
          label: '规则'
        },
        {
          value: 'statistic',
          label: '统计'
        }
      ];
    }
    this.tableType = getTableType(this.$route.query.table_type);
    let lotteryTime = getLotteryTime(this.$route.query.game_type_id);
    this.setLotteryTime(lotteryTime);
  }
};
</script>
<style scoped lang='less'>
.gameInfo {
  // padding-top: 48px;
  box-sizing: border-box;

  .top_box {
    height: 48px;
    background: #4a4130;
    // position: fixed;
    width: 100%;
    top: 44px;
    z-index: 999;

    .top_item {
      flex-grow: 1;
      text-align: center;
      line-height: 48px;
      .sc(17px, #ffedd4);
      position: relative;
      &.active {
        color: #fed093;

        &::after {
          content: "";
          position: absolute;
          width: 48px;
          height: 4px;
          background: #fed093;
          left: 50%;
          margin-left: -24px;
          bottom: 4px;
        }
      }
      &.small {
        font-size: 14px;
      }
    }

    
  }
}
</style>