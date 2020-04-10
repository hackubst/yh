<template>
  <div :class="tableType == 0 ? 'editTablea' : 'editTablea_1'">
    <!-- 表格左侧 -->
    <div :class="tableType == 0 ? 'box': 'box_1'">
      <table :class="tableType == 0 ? 'tableItem': 'tableItem tableItem_1'" border="1" cellpadding="0" cellspacing="0">
        <tr v-if="tableType == 0">
          <th style="width: 84px;">预测号码</th>
          <th style="width: 84px;">标准赔率</th>
          <th style="width: 52px;">选择</th>
          <th style="width: 113px;">投注</th>
          <th style="width: 108px;">倍数</th>
        </tr>
        <tr v-else>
          <th style="width: 42px;">号码</th>
          <th style="width: 79px;">上期赔率</th>
          <th style="width: 79px;">当前赔率</th>
          <th style="width: 69px;">已投注</th>
          <th style="width: 42px;">选择</th>
          <th style="width: 113px;">投注</th>
          <th style="width: 99px;">倍数</th>
        </tr>
        <tr v-for="(item, index) in leftTable" :key="index">
          <td>
            <div class="nums_icon" v-if="oneResultGame(choosedGame.game_type_id)">
              <div>{{item.key}}</div>
            </div>
            <div class="nums_icon" v-else>
               <div>{{item.name}}</div>
            </div>

          </td>
          <td v-if="tableType == 0">
            {{item.rate}}
          </td>
          <td v-else>
            {{item.last_bet_rate}}
          </td>
          <td v-if="tableType == 1">
            {{item.rate}}
          </td>
          <td v-if="tableType == 1">
            {{item.have_bet}}
          </td>
          <td>
            <div class="check_box" @click="chooseItem(item.key)">
              <img src="~images/icon/icon_xuanzhong@2x.png" alt="" v-if="item.chooseed">
              <img src="~images/icon/icon_weixuanzhong@2x.png" alt="" v-else>
            </div>
          </td>
          <td>
            <div class="inp_box">
              <input type="number" v-model="item.bet" @focus="inputFocus(item)" @keyup="inputChange(item)">
            </div>
          </td>
          <td>
            <div class="bet_btn">
              <div class="btn_item" @click="btnClick(0.5, item)">.5</div>
              <div class="btn_item" @click="btnClick(2, item)">2</div>
              <div class="btn_item" @click="btnClick(10, item)">10</div>
            </div>
          </td>
        </tr>
      </table>
    </div>
    <!-- 表格右侧 -->
    <div :class="tableType == 0 ? 'box': 'box_1'">
      <table :class="tableType == 0 ? 'tableItem': 'tableItem tableItem_1'" border="1" cellpadding="0" cellspacing="0">
        <tr v-if="tableType == 0">
          <th style="width: 84px;">预测号码</th>
          <th style="width: 84px;">标准赔率</th>
          <th style="width: 52px;">选择</th>
          <th style="width: 113px;">投注</th>
          <th style="width: 108px;">倍数</th>
        </tr>
        <tr v-else>
          <th style="width: 42px;">号码</th>
          <th style="width: 79px;">上期赔率</th>
          <th style="width: 79px;">当前赔率</th>
          <th style="width: 69px;">已投注</th>
          <th style="width: 42px;">选择</th>
          <th style="width: 113px;">投注</th>
          <th style="width: 99px;">倍数</th>
        </tr>
        <tr v-for="(item, index) in rightTable" :key="index">
          <td>
            <div class="nums_icon" v-if="oneResultGame(choosedGame.game_type_id)">
              <div>{{item.key}}</div>
            </div>
            <div class="nums_icon" v-else>
              <div>{{item.name}}</div>
            </div>
          </td>
          <td v-if="tableType == 0">
            {{item.rate}}
          </td>
          <td v-else>
            {{item.last_bet_rate}}
          </td>
          <td v-if="tableType == 1">
            {{item.rate}}
          </td>
          <td v-if="tableType == 1">
            {{item.have_bet}}
          </td>
          <td>
            <div class="check_box" @click="chooseItem(item.key)">
              <img src="~images/icon/icon_xuanzhong@2x.png" alt="" v-if="item.chooseed">
              <img src="~images/icon/icon_weixuanzhong@2x.png" alt="" v-else>
            </div>
          </td>
          <td>
            <div class="inp_box">
              <input type="number" v-model="item.bet" @focus="inputFocus(item)" @keyup="inputChange(item)">
            </div>
          </td>
          <td>
            <div class="bet_btn">
              <div class="btn_item" @click="btnClick(0.5, item)">.5</div>
              <div class="btn_item" @click="btnClick(2, item)">2</div>
              <div class="btn_item" @click="btnClick(10, item)">10</div>
            </div>
          </td>
        </tr>
      </table>
    </div>

  </div>
</template>
<script>
  import { filterNum } from '@/config/mixin.js'
  import { mapGetters } from 'vuex'
  export default {
    name: "editTablea",
    props: {
      modeHalf: {
        type: Number,
        default: 0
      },
      modeTable: {
        type: Array,
        default: () => []
      },
      tableType: {
        type: Number,
        default: 0 // 表格样式，0： 编辑模式时的表格样式, 1: 投注时的表格样式 
      }
    },
    mixins: [filterNum],
    computed: {
      leftTable: function () {
        console.log(this.modeTable)
        return this.modeTable.filter((item, index) => index < this.modeHalf)
      },
      rightTable: function () {
        return this.modeTable.filter((item, index) => index >= this.modeHalf)
      },
      ...mapGetters([
        'choosedGame'
      ])
    },
    data() {
      return {};
    },
    methods: {
      btnClick: function(time, item){
        console.log(item)
        if(!item.chooseed) return
        let num = Math.floor(item.bet * time)
        if(num == 0) return
        item.bet = num
      },
      // 当输入框聚焦时
      inputFocus: function (item) {
        if (!item.chooseed) {
          if (this.rationBet && this.rationBet !== 0) {
            item.chooseed = true
            item.bet = parseInt(this.rationBet)
          } else {
            item.chooseed = false
            item.bet = ''
          }
        }
      },
      // 当键盘输入内容时
      inputChange: function (item) {
        let bet = parseInt(item.bet)
        if (bet) {
          item.bet = bet
          item.chooseed = true
        } else {
          if (bet == 0) {
            item.bet = 0
          } else {
            item.bet = ''
          }
          item.chooseed = false
        }
      },
      chooseItem: function (key) {
        let chooseIndex = this.modeTable.findIndex(item => item.key == key)
        this.modeTable[chooseIndex].chooseed = !this.modeTable[chooseIndex].chooseed
        if (this.modeTable[chooseIndex].chooseed) {
          this.$emit('countDefault', chooseIndex)
        } else {
          this.modeTable[chooseIndex].bet = ''
        }
      }
    }
  }
</script>
<style scoped lang='less'>
  .editTablea {
    width: @main-width;
    display: flex;
    justify-content: space-between;
    margin-bottom: 50px;
  }

  .box {
    border: 1px solid #F5A623;
    box-sizing: border-box;
  }

  .box_1 {
    border: 1px solid #F5A623;
    box-sizing: border-box;
    border-top: 2px solid #F5A623;
  }

  .box_1:last-child {
    border-left: none;
  }

  .editTablea_1 {
    width: @main-width;
    display: flex;
    justify-content: space-between;
    margin-bottom: 50px;
    box-sizing: border-box;
    position: relative;
    align-items: flex-start;
  }

  .tableItem {
    width: 446px;

    th {
      background: #FFEFD4;
      height: 42px;
      text-align: center;
      line-height: 42px;
      font-size: 16px;
      color: #4A4130;
      border: 1px solid #E8E8E8;
    }

    th:first-child {
      border-left: none;
    }

    td {
      height: 44px;
      border: 1px solid #E8E8E8;
      text-align: center;
      line-height: 44px;
      font-size: 14px;
      vertical-align: bottom;
    }

    .nums_icon {
      height: 42px;
      display: flex;
      align-items: center;
      justify-content: center;

      div {
        .result_ball(30px, 16px);
      }
    }

    .inp_box {
      width: 113px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;

      input {
        width: 103px;
        height: 34px;
        border-radius: 4px;
        padding-left: 5px;
        box-sizing: border-box;
        color: #FB3A3A;
        font-size: 14px;
        border: 1px solid #CCCCCC;
      }
    }

    .bet_btn {
      width: 108px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: space-around;

      .btn_item {
        width: 26px;
        height: 33px;
        text-align: center;
        line-height: 33px;
        font-size: 16px;
        color: #FFF8EF;
        background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
        border-radius: 4px;

        &:hover {
          cursor: pointer;
        }
      }
    }

    .check_box {
      width: 52px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;

      img {
        width: 16px;
        height: 16px;
      }
    }
  }

  .tableItem_1 {
    width: 529px;
  }
</style>