<template>
  <!--  合肖-->
  <div class="add-shaw">
    <table class="game_table bb" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th>种类</th>
        <th>合肖</th>
      </tr>
      <tr>
        <td class="rate">赔率</td>
        <td class="f14 fcr">{{currentRate}}</td>
      </tr>
    </table>
    <!--      顶部-->
    <div class="even-tail-table bt">
      <div class="table-item">
        <div v-for="(item,index) in 2" :key="index" class="item">
          <div class="item-left flex-center">生肖</div>
          <div class="item-center flex-center bg">号</div>
          <div class="item-right flex-center bg" :style="{width:index%2 === 0?'91px':'89px'}">勾选</div>
        </div>
      </div>
    </div>
    <!--      内容-->
    <div class="even-tail-table">
      <div class="table-item bg" v-if="addShawList.length">
        <div
          v-for="(item,index) in addShawList"
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
          <div
            class="item-right flex-center"
            :style="{width:index%2 === 0?'91px':'89px'}"
            @click.stop
          >
            <el-checkbox v-model="item.checked" :disabled="item.disabled" @change="handleCheckboxChange"></el-checkbox>
          </div>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="addShawMoney" class="input" @input="handleBetInput" />
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from "vuex";
import { ALERT_TIME, SHENGXIAO_ARRAY } from "@/config/config";
import { getShengxiao, getBg } from "@/config/utils";
export default {
  name: "addShaw",
  data() {
    return {
      resultList: [],
      addShawList: [], //
      addShawMoney: "", // 下注金额
      currentRate: '--', // 当前赔率
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
  computed: {
    ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose", "gameResultId"])
  },
  props: {
    id: {
      type: String,
      default: () => ""
    }
  },
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
          this.resultList = jsonArr;
          let tempArr = [];
          for (let i = 0; i < SHENGXIAO_ARRAY.length; i++) {
            tempArr.push({
              key: i + 1,
              name: SHENGXIAO_ARRAY[i],
              checked: false,
              disabled: !!this.isPlateClose || false,
              list: []
            });
            for (let j = 1; j < 50; j++) {
              let animal = getShengxiao(j);
              let bg = getBg(j);
              if (SHENGXIAO_ARRAY[i] === animal) {
                tempArr[i].list.push({
                  number: j > 9 ? j : "0" + j,
                  bg
                });
              }
            }
          }
          this.addShawList = tempArr;
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
            this.gameResult = data.data;
            this.resultList = JSON.parse(data.data.game_type_info.bet_json);
            let tempArr = [];
            for (let i = 0; i < SHENGXIAO_ARRAY.length; i++) {
              tempArr.push({
                key: i + 1,
                name: SHENGXIAO_ARRAY[i],
                checked: false,
                disabled: !!this.isPlateClose || false,
                list: []
              });
              for (let j = 1; j < 50; j++) {
                let animal = getShengxiao(j);
                let bg = getBg(j);
                if (SHENGXIAO_ARRAY[i] === animal) {
                  tempArr[i].list.push({
                    number: j > 9 ? j : "0" + j,
                    bg
                  });
                }
              }
            }
            this.addShawList = tempArr;
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
          game_result_id: this.gameResult.game_type_info.issue_num,
          total_bet_money: this.addShawMoney,
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
      let tempArr = this.addShawList.filter(bet => bet.checked);
      console.log({tempArr})
      let clearDisable = (arr) => {
        for(let i = 0, len = arr.length; i < len; i++) {
          arr[i].disabled = false;
        }
      }
      if (tempArr.length > 10) {
        let temp = this.addShawList.filter(item => !item.checked);
        temp[0].disabled = true;
      } else if (tempArr.length > 1) {
        clearDisable(this.addShawList);
        let betJson = this.resultList[0].bet_json;
        for(let i = 0; i < betJson.length; i++) {
          if (+betJson[i].name === tempArr.length) {
            this.currentRate = betJson[i].rate;
          }
        }
      } else {
        clearDisable(this/addShawList);
        this.currentRate = '--';
      }
    },
    // 监听下注金额变化
    handleBetInput() {
      if (this.isPlateClose == 1) return;
    },
    // 复选框变化
    handleCheckboxChange() {
      if (this.isPlateClose == 1) return;
      let tempArr = this.addShawList.filter(bet => bet.checked);
      console.log({tempArr})
      let clearDisable = (arr) => {
        for(let i = 0, len = arr.length; i < len; i++) {
          arr[i].disabled = false;
        }
      }
      if (tempArr.length > 10) {
        let temp = this.addShawList.filter(item => !item.checked);
        temp[0].disabled = true;
      } else if (tempArr.length > 1) {
        clearDisable(this.addShawList)
        let betJson = this.resultList[0].bet_json;
        for(let i = 0; i < betJson.length; i++) {
          if (+betJson[i].name === tempArr.length) {
            this.currentRate = betJson[i].rate;
          }
        }
      } else {
        clearDisable(this.addShawList)
        this.currentRate = '--';
      }
    },
    // 辅助函数
    getStr(arr) {
      let str = '';
      let tempArr = []
      for(let i = 0; i < arr.length; i++) {
        tempArr.push(arr[i].key);
      }
      str = tempArr.join(',');
      return str;
    },
    // 清空已选
    clearChoose() {
      const clearFn = arr => {
        for (let i = 0; i < arr.length; i++) {
          arr[i].checked = false
          arr[i].disabled = false
        }
      }
      clearFn(this.addShawList);
    },
    // 获取投注时选中的json字符串
    getBetJSON() {
      let json_arr = [];
      this.resultList.forEach(result => {
        let temp_arr = this.addShawList.filter(bet => bet.checked);
        let bet_json = [];

        result.bet_json.map(item => {
          if (temp_arr.length === +item.name) {
            bet_json.push({
              key: item.key,
              money: this.addShawMoney,
              value: this.getStr(temp_arr)
            });
          }
        });

        if (temp_arr.length) {
          json_arr.push({
            part: result.part,
            name: result.name,
            bet_json: bet_json
          });
        }
      });
      return JSON.stringify(json_arr);
    },
    // 确认投注
    sureBet() {
      let bet_json = this.getBetJSON();
      let all_bet = this.addShawMoney;
      console.log({bet_json}, {all_bet});
      if (this.isPlateClose == 1) {
        this.$alert('已经封盘，请开盘后再投注', '无法投注', {
            confirmButtonText: '确定',
        });
        return;
      }
      if (isNaN(all_bet) || all_bet <= 0) {
        //说明没有输入任何金额，提示
        this.$alert('下注内容不对，请重新下注', '提示', {
            confirmButtonText: '确定',
        });
        return;
      }
      this.$Api(
        {
          api_name: "kkl.game.gameBet",
          bet_json: bet_json,
          game_result_id: this.gameResultId,
          total_bet_money: parseInt(all_bet),
          game_type_id: this.choosedGame.game_type_id,
          pankou: this.plateValue
        },
        (err, data) => {
          this.loading = false;
          if (!err) {
            this.refreshUserInfo();
            this.$msg("投注成功", "success", ALERT_TIME);
          } else {
            this.$msg(err.error_msg, "error", ALERT_TIME);
          }
        }
      );
    },
    ...mapActions([
      "refreshUserInfo",
    ])
  },
  mounted() {
    // this.getResult();
  }
};
</script>

<style scoped lang='less'>
.add-shaw {
  .wh(920px, auto);
  margin: 0 auto;
  margin-top: 23px;
  margin-bottom: 10px;
  .f14 {
    font-size: 14px;
  }
  .fcr {
    color: #e02020;
  }
  .bt {
    border-top: 2px solid #f5a623;
  }
  .bb {
    margin-bottom: 12px;
  }
  .bg {
    background: #ffefd4 !important;
  }
  .game_table {
    .wh(920px, auto);
    .rate {
      font-size: 14px;
      font-weight: 600;
      color: rgba(133, 86, 9, 1);
    }
  }
  .even-tail-table {
    .wh(920px, auto);
    display: flex;
    border-left: 1px solid #e8e8e8;

    .table-item {
      display: flex;
      flex-wrap: wrap;

      .item {
        font-size: 14px;
        display: flex;
        align-items: center;
        height: 32px;
        border-bottom: 1px solid #e8e8e8;

        .item-left {
          width: 53px;
          height: 100%;
          background: #ffefd4;
          border-right: 1px solid #e8e8e8;
        }

        .item-center {
          .wh(315px, 100%);
          padding: 0 10px;
          box-sizing: border-box;
          background: #ffffff;
          border-right: 1px solid #e8e8e8;
          .bg {
            background: #ffefd4 !important;
          }
          ul {
            display: flex;
            background: #ffffff;

            li {
              width: 30px;
              text-align: center;
              margin-right: 4px;
              flex: 0;
              background: #ffffff;

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
          .wh(90px, 100%);
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

    input {
      width: 60px;
      height: 20px;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.2);
      border-radius: 2px;
      border: 1px solid rgba(204, 204, 204, 1);
      margin: 0 10px;
      box-sizing: border-box;
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
