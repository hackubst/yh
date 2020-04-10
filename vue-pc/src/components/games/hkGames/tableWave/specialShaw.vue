<template>
  <!--  特码-->
  <div class="special-shaw">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 53px;">生肖</th>
        <th style="width: 224px;">号</th>
        <th style="width: 89px;">赔率</th>
        <th style="width: 89px;">金额</th>
        <th style="width: 53px;">生肖</th>
        <th style="width: 224px;">号</th>
        <th style="width: 89px;">赔率</th>
        <th style="width: 89px;">金额</th>
      </tr>
    </table>
    <div class="even-tail-table">
      <div class="table-item" v-if="resultListD && resultListD.bet_json">
        <div
          :class="{active: item.checked}"
          v-for="(item,index) in resultListD.bet_json"
          :key="index"
          class="item"
          @click.stop="chooseItem(item)"
        >
          <div class="item-left flex-center">{{item.name}}</div>
          <div class="item-center">
            <ul>
              <li v-for="(child_,secindex) in item.list" class="item-li" :key="secindex">
                <div
                  class="result-number"
                  :class="[child_.bg === 1 ? 'bgr':child_.bg === 2 ? 'bgb':'bgg']"
                >{{child_.number}}</div>
              </li>
            </ul>
          </div>
          <div class="item-rate flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
          <div
            class="item-right flex-center"
            :style="{width:(index + 1) % 2 === 0 ? '90px':'89px'}"
            @click.stop
          >
            <el-input v-model="item.money" :disabled="!!isPlateClose" style="width:70px" @focus="handleFocus(item)"></el-input>
          </div>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="specialShawMoney" class="input" @input="handleBetInput" />
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import { markSixMixin } from "@/config/mixin";
import { getShengxiao, getBg } from "@/config/utils";
export default {
  name: "specialShaw",
  data() {
    return {
      specialShawMoney: "", //金额
      resultListD: [], //请求的数组
      list_: [
        {
          name: "鼠",
          numberList: [
            { number: "01", bg: 1 },
            { number: "13", bg: 1 },
            { number: "25", bg: 2 },
            {
              number: "27",
              bg: 2
            },
            { number: "49", bg: 3 }
          ]
        },
        {
          name: "牛",
          numberList: [
            { number: "12", bg: 1 },
            { number: "24", bg: 1 },
            { number: "36", bg: 2 },
            {
              number: "48",
              bg: 2
            }
          ]
        },
        {
          name: "虎",
          numberList: [
            { number: "11", bg: 3 },
            { number: "23", bg: 1 },
            { number: "35", bg: 2 },
            {
              number: "47",
              bg: 2
            }
          ]
        },
        {
          name: "兔",
          numberList: [
            { number: "10", bg: 2 },
            { number: "22", bg: 3 },
            { number: "34", bg: 1 },
            {
              number: "46",
              bg: 1
            }
          ]
        },
        {
          name: "龙",
          numberList: [
            { number: "09", bg: 2 },
            { number: "21", bg: 3 },
            { number: "33", bg: 3 },
            {
              number: "45",
              bg: 1
            }
          ]
        },
        {
          name: "蛇",
          numberList: [
            { number: "08", bg: 1 },
            { number: "20", bg: 2 },
            { number: "32", bg: 3 },
            {
              number: "44",
              bg: 1
            }
          ]
        },
        {
          name: "马",
          numberList: [
            { number: "07", bg: 1 },
            { number: "19", bg: 1 },
            { number: "31", bg: 2 },
            {
              number: "43",
              bg: 3
            }
          ]
        },
        {
          name: "羊",
          numberList: [
            { number: "06", bg: 3 },
            { number: "18", bg: 1 },
            { number: "30", bg: 1 },
            {
              number: "42",
              bg: 2
            }
          ]
        },
        {
          name: "猴",
          numberList: [
            { number: "05", bg: 3 },
            { number: "17", bg: 3 },
            { number: "29", bg: 1 },
            {
              number: "47",
              bg: 2
            }
          ]
        },
        {
          name: "鸡",
          numberList: [
            { number: "04", bg: 2 },
            { number: "16", bg: 3 },
            { number: "28", bg: 2 },
            {
              number: "40",
              bg: 1
            }
          ]
        },
        {
          name: "狗",
          numberList: [
            { number: "03", bg: 2 },
            { number: "15", bg: 2 },
            { number: "27", bg: 2 },
            {
              number: "39",
              bg: 2
            }
          ]
        },
        {
          name: "猪",
          numberList: [
            { number: "02", bg: 1 },
            { number: "14", bg: 2 },
            { number: "26", bg: 2 },
            {
              number: "38",
              bg: 3
            }
          ]
        }
      ]
    };
  },
  props: {
    id: {
      type: String,
      default: () => ""
    }
  },
  mixins: [markSixMixin],
  methods: {
    // 初始化表格
    initTable() {
      this.$Api(
        {
          api_name: "kkl.game.getLastBetInfo",
          game_result_id: this.gameResultId,
          game_type_id: this.choosedGame.game_type_id,
          pan_type: this.plateValue
        },
        (err, data) => {
          const { new_bet_rate } = data.data;
          let jsonArr = JSON.parse(new_bet_rate);
          jsonArr.forEach((item, index) => {
            item.bet_json.map(child => {
              child.list = [];
              child.checked = false;
              child.money = "";
              for (let j = 1; j < 50; j++) {
                let animal = getShengxiao(j);
                let bg = getBg(j);
                if (child.name === animal) {
                  child.list.push({
                    number: j > 9 ? j : "0" + j,
                    bg
                  });
                }
              }
            });
          });
          this.resultList = jsonArr;
          this.resultListD = this.resultList[0];
        }
      );
    },
    //获取开奖结果和赔率
    getResult() {
      this.$Api(
        {
          api_name: "kkl.game.nowResult",
          game_type_id: this.id
        },
        (err, data) => {
          if (!err) {
            // this.gameResult = data.data;
            let jsonArr = JSON.parse(data.data.game_type_info.bet_json);
            jsonArr.forEach((item, index) => {
              item.bet_json.map(child => {
                child.list = [];
                for (let j = 1; j < 50; j++) {
                  let animal = getShengxiao(j);
                  let bg = getBg(j);
                  if (child.name === animal) {
                    child.list.push({
                      number: j > 9 ? j : "0" + j,
                      bg
                    });
                  }
                }
              });
            });
            this.resultList = jsonArr;
            this.resultListD = this.resultList[0];
          }
        }
      );
    },
    //投注
    getGameBet() {
      let list = [];
      list = this.resultListD.bet_json.filter((item, index) => item.money > 0);
      var newArray2 = [];
      for (var i = 0; i < list.length; i++) {
        var newObject = {};
        newObject.key = list[i].key;
        newObject.money = list[i].money;
        newArray2.push(newObject);
      }
      let obj = [];
      obj.push({
        part: this.resultListD.part,
        bet_json: newArray2,
        name: this.resultListD.name
      });

      console.log(obj, "OBJ");
      this.$Api(
        {
          api_name: "kkl.game.gameBet",
          bet_json: obj,
          game_result_id: this.gameResultId,
          total_bet_money: this.moneyD,
          game_type_id: this.id
        },
        (err, data) => {
          if (!err) {
            console.log("chenggongla ");
          }
        }
      );
    },
    // 选择某一项
    chooseItem(item) {
      if (this.isPlateClose == 1) return;
      item.checked = !item.checked;
      if (this.specialShawMoney) {
        if (!item.checked) {
          item.money = "";
        } else {
          item.money = this.specialShawMoney;
        }
      }
    },
    // 处理聚焦
    handleFocus(item) {
      if (this.isPlateClose == 1) return;
      item.checked = true;
      if (this.specialShawMoney) {
        item.money = this.specialShawMoney;
      }
    },
    // 监听下注金额变化
    handleBetInput() {
      if (this.isPlateClose == 1) return;
      this.resultList.forEach(item => {
        item.bet_json.map(child => {
          if (child.checked) {
            child.money = this.specialShawMoney;
          }
        });
      });
    }
  },
  mounted() {
    // this.getResult();
  }
};
</script>

<style scoped lang="less">
.special-shaw {
  .wh(920px, auto);
  margin: 0 auto;
  margin-top: 23px;
  margin-bottom: 10px;

  .flex-center {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .game_table {
    .wh(920px, auto);
  }

  .even-tail-table {
    .wh(920px, auto);
    display: flex;
    border-left: 1px solid #e8e8e8;

    .table-item {
      display: flex;
      flex-wrap: wrap;

      .item {
        display: flex;
        align-items: center;
        height: 32px;
        border-bottom: 1px solid #e8e8e8;

        &.active > div {
          background: #ffc214 !important;
        }

        &:hover {
          background: #ffefd4;
          & > div {
            background: #ffefd4;
          }
        }

        .item-left {
          width: 53px;
          height: 100%;
          background: #ffefd4;
          font-size: 14px;
          font-weight: 600;
          color: rgba(133, 86, 9, 1);
          border-right: 1px solid #e8e8e8;
        }

        .item-center {
          .wh(225px, 100%);
          padding: 0 10px;
          box-sizing: border-box;
          background: #ffffff;
          border-right: 1px solid #e8e8e8;

          ul {
            display: flex;

            li {
              width: 30px;
              text-align: center;
              margin-right: 4px;
              flex: 0;
              .result-number {
                width: 30px;
                height: 30px;
                /*border:1px solid #1F70FF;*/
                text-align: center;
                line-height: 30px;
                font-size: 16px;
              }

              .bgr {
                background: url("~images/bg/red_circle.png") no-repeat;
              }

              .bgb {
                background: url("~images/bg/blue_circle.png") no-repeat;
              }

              .bgg {
                background: url("~images/bg/green_circle.png") no-repeat;
              }
            }
          }
        }

        .item-rate {
          border-right: 1px solid #e8e8e8;
          .wh(89px, 100%);
          background: #ffffff;
          color: #e02020;
          font-size: 14px;
          border-right: 1px solid #e8e8e8;
        }

        .item-right {
          .wh(89px, 100%);
          background: #ffffff;
          border-right: 1px solid #e8e8e8;
        }
      }
    }
  }

  .wave-footer {
    display: flex;
    justify-content: center;
    margin-top: 24px;
    align-items: center;
    font-size: 14px;
    input {
      width: 60px;
      height: 20px;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.2);
      border-radius: 2px;
      border: 1px solid rgba(204, 204, 204, 1);
      margin: 0 10px;
      box-sizing:border-box;
    }

    .sure-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 52px;
      height: 22px;
      background: linear-gradient(
        360deg,
        rgba(209, 145, 60, 1) 0%,
        rgba(255, 209, 148, 1) 100%
      );
      border-radius: 2px;
      font-size: 12px;
      font-weight: 500;
      color: rgba(255, 255, 255, 1);
    }

    .mr10 {
      margin-right: 10px;
    }
  }
}
</style>
<style>
  .el-input{
    display: flex !important;
    align-items: center;
    justify-content: center;
  }
  .el-input__inner {
    height: 20px !important;
    line-height: 20px !important;
    padding: 0 5px !important;
    box-sizing: border-box !important;
  }
</style>
