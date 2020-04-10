<template>
  <div class="bet_detail">
    <div class="mode_title">
        <span>标准投注选择</span>
    </div>
    <!--投注筛选类型  -->
    <div class="mode_types">
      <div class="type_item" v-for="(item, index) in chooseTypes" :key="index"
        @click="item.typeFnType == 1 ? filterDivisor(item.divisor, item.remainder, index): filterSize(item.size, item.sizeType, index)"
        :class="ModeTypeIndex == index ? 'active':''">
        {{item.typeName}}
      </div>
    </div>
    <!-- 选择倍数总投注等 -->
    <div class="mode_operate">
      <div class="mode_multiply">
        <div class="multiply_item" v-for="(item, index) in mode_multiply" :key="index" @click="allDouble(item)">
          <span>{{item}}倍</span>
        </div>
      </div>
      <div class="operate_btn">
        <div class="btn_item" @click="betMyAll()">梭哈</div>
        <div class="btn_item" @click="allChoose()">全包</div>
        <div class="btn_item" @click="reverseChoose()">反选</div>
        <div class="btn_item" @click="clearChoose()">清除</div>
      </div>
      <div class="mode_bet">
        <span>总投注:</span>
        <input type="number" disabled v-model="allBet">
        <img src="~images/icon/icon_douzi@2x.png" alt="">
      </div>
      <div class="save_mode" @click="save()">
        保存
      </div>
    </div>
    <!-- 编辑模式表格组件 -->
    <edit-tablea :modeHalf="modeHalf" :modeTable="modeNumsMore[0]" @countDefault="countDefault"></edit-tablea>
  </div>
</template>
<script>
  import {
    mapMutations, mapActions
  } from 'vuex'
  import {
    CHOOSE_TYPES
  } from '@/config/config.js'
  import {
    gameCuntDown, betMixin
  } from '@/config/mixin.js'
  import editTablea from '@/components/games/editTable/editTablea.vue'
  export default {
    name: "bet_detail",
    mixins: [betMixin, gameCuntDown],
    components: {
      editTablea
    },
    data() {
      return {
        chooseTypes: CHOOSE_TYPES,
      };
    },
    created() {
        let lastBetObj = JSON.parse(this.$route.query.bet_json)
        this.renderLastBet(lastBetObj)
    },
    methods: {
      ...mapMutations({
        chooseGame: 'CHOOSE_GAME',
      }),
    }
  }
</script>
<style scoped lang='less'>
  .bet_detail {
    width: @main-width;
    margin: 0 auto;
  }
  .mode_title {
    height: 22px;
    padding-left: 10px;
    box-sizing: border-box;
    position: relative;
    line-height: 22px;
    color: #4A4130;
    font-size: 16px;
    margin-bottom: 10px;

    &:before {
      content: "";
      width: 4px;
      height: 22px;
      background: #D1913C;
      left: 0;
      top: 0;
      position: absolute;
    }
  }
  .mode_types {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    .type_item {
      width: 48px;
      height: 30px;
      line-height: 30px;
      text-align: center;
      background: #FFEFD4;
      color: #D1913C;
      margin-right: 2px;
      margin-bottom: 2px;
      &:hover{
        cursor: pointer;
      }
    }

    .active {
      background: #D1913C;
      color: #fff;
    }
  }

  .mode_operate {
    display: flex;
    justify-content: space-between;
    height: 42px;
    margin-bottom: 20px;

    .mode_multiply {
      width: 498px;
      height: 42px;
      display: flex;
      background: #D1913C;
      border-radius: 4px;
      padding: 10px;
      box-sizing: border-box;
      align-items: center;

      .multiply_item {
        flex-grow: 1;
        font-size: 16px;
        color: #FFF8EF;
        position: relative;
        text-align: center;

        &:after {
          content: "";
          width: 1px;
          height: 12px;
          background: #FFF8EF;
          position: absolute;
          right: 0;
          top: 50%;
          margin-top: -6px;
        }
        &:hover {
          cursor: pointer;
        }
      }

      .multiply_item:last-child {
        &:after {
          content: "";
          width: 0px;
          height: 0px;
        }
      }
    }

    .operate_btn {
      display: flex;
      justify-content: space-between;

      .btn_item {
        width: 42px;
        height: 42px;
        border-radius: 4px;
        border: 1px solid rgba(209, 145, 60, 1);
        margin-left: 5px;
        background: rgba(255, 239, 212, 1);
        text-align: center;
        line-height: 42px;
        color: #D1913C;
        font-size: 16px;
        box-sizing: border-box;
        &:hover{
          cursor: pointer;
        }
      }
    }

    .mode_bet {
      width: 171px;
      height: 42px;
      background: #FFEFD4;
      border-radius: 4px;
      margin-left: 5px;
      margin-right: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid rgba(209, 145, 60, 1);
      white-space: nowrap;
      font-size: 16px;
      color: #D1913C;

      input {
        width: 76px;
        color: #FB3A3A;
        margin: 0 5px;
        font-size: 18px;
      }

      img {
        width: 16px;
        height: 16px;
      }
    }

    .save_mode {
      width: 52px;
      height: 42px;
      line-height: 42px;
      text-align: center;
      font-size: 16px;
      color: #FFF8EF;
      background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
      border-radius: 4px;
    }
  }
</style>