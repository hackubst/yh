<template>
  <!--  连码-->
  <div class="even-code">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width:209px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[0]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[0].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
        <th style="width: 123px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[1]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[1].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
        <th style="width: 123px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[2]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[2].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
        <th style="width: 207px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[3]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[3].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
        <th style="width: 124px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[4]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[4].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
        <th style="width: 127px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[5]">
          <el-radio
            v-model="type"
            :label="evenCodeList.bet_json[5].key"
            @change="handleChange"
          >&nbsp;</el-radio>
        </th>
      </tr>
      <tr>
        <td
          style="width:209px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[0]"
        >{{evenCodeList.bet_json[0].name}}</td>
        <td
          style="width: 123px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[1]"
        >{{evenCodeList.bet_json[1].name}}</td>
        <td
          style="width: 123px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[2]"
        >{{evenCodeList.bet_json[2].name}}</td>
        <td
          style="width: 207px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[3]"
        >{{evenCodeList.bet_json[3].name}}</td>
        <td
          style="width: 124px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[4]"
        >{{evenCodeList.bet_json[4].name}}</td>
        <td
          style="width: 127px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[5]"
        >{{evenCodeList.bet_json[5].name}}</td>
      </tr>
      <tr>
        <td style="width:209px" v-if="evenCodeList.bet_json && evenCodeList.bet_json[0]">
          <div
            style="float:left;width:50%;border-right: 1px solid #e8e8e8"
          >{{evenCodeList.bet_json[0].rate[0]}}</div>
          <div style="flex:1">{{evenCodeList.bet_json[0].rate[1]}}</div>
        </td>
        <td
          style="width: 123px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[1]"
        >{{evenCodeList.bet_json[1].rate[0]}}</td>
        <td
          style="width: 123px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[2]"
        >{{evenCodeList.bet_json[2].rate[0]}}</td>
        <td style="width: 207px;" v-if="evenCodeList.bet_json && evenCodeList.bet_json[3]">
          <div
            style="float:left;width:50%;border-right: 1px solid #e8e8e8"
          >{{evenCodeList.bet_json[3].rate[0]}}</div>
          <div>{{evenCodeList.bet_json[3].rate[1]}}</div>
        </td>
        <td
          style="width: 124px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[4]"
        >{{evenCodeList.bet_json[4].rate[0]}}</td>
        <td
          style="width: 127px;"
          v-if="evenCodeList.bet_json && evenCodeList.bet_json[5]"
        >{{evenCodeList.bet_json[5].rate[0]}}</td>
      </tr>
    </table>
    <div class="content">
      <div class="first-table" v-for="(firstItem,firstIndex) in 5" :key="firstIndex">
        <div class="first-table-title">
          <div class="item-center bg flex-center">号码</div>
          <div class="item-right flex-center bg">勾选</div>
        </div>
        <div class="first-table-content">
          <div
            class="item"
            v-for="(item,index) in list.slice(firstIndex*10,firstIndex*10 + 10)"
            :key="index"
            @click.stop="chooseItem(item)"
          >
            <div class="item-left br1 flex-center">
              <div
                class="result-number"
                :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
              >{{item.number > 9 ? item.number : '0' + item.number}}</div>
            </div>
            <div class="item-right flex-center">
              <img src="~images/icon/icon_xuanzhong@2x.png" alt v-if="item.checked" />
              <img src="~images/icon/icon_weixuanzhong@2x.png" alt v-else />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="evenCodeMoney" class="input" @input="handleBetInput" />
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import { getBg, getCombination } from "@/config/utils";
import {
  ALERT_TIME
} from '@/config/config.js'
export default {
  name: "evenCode",
  data() {
    return {
      resultList: [],
      evenCodeMoney: "",
      list: [
        { number: "01" },
        { number: "02" },
        { number: "03" },
        { number: "04" },
        { number: "05" },
        { number: "06" },
        { number: "07" },
        { number: "08" },
        { number: "09" },
        { number: "10" },
        { number: "11" },
        { number: "12" },
        { number: "13" },
        { number: "14" },
        { number: "15" },
        { number: "16" },
        { number: "17" },
        { number: "18" },
        { number: "19" },
        { number: "20" },
        { number: "21" },
        { number: "22" },
        { number: "23" },
        { number: "24" },
        { number: "25" },
        { number: "26" },
        { number: "27" },
        { number: "28" },
        { number: "29" },
        { number: "30" },
        { number: "31" },
        { number: "32" },
        { number: "33" },
        { number: "34" },
        { number: "35" },
        { number: "36" },
        { number: "37" },
        { number: "38" },
        { number: "39" },
        { number: "40" },
        { number: "41" },
        { number: "42" },
        { number: "43" },
        { number: "44" },
        { number: "45" },
        { number: "46" },
        { number: "47" },
        { number: "48" },
        { number: "49" }
      ],
      evenCodeList: [],
      radio: "",
      radio1: "",
      radio2: "",
      radio3: "",
      radio4: "",
      radio5: "",
      type: 1
    };
  },
  props: {
    id: {
      type: String,
      default: () => ""
    }
  },
  computed: {
    ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose", "gameResultId"])
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
          let tempArr = [];
          jsonArr.forEach(item => {
            item.bet_json.map(child => {
              let len = child.rate.split(",");
              child.rate = len;
              child.checked = false;
              child.money = '';
            })
          })

          for(let i = 1; i < 50; i++) {
            let bg = getBg(i);
            tempArr.push({
              number: i,
              checked: false,
              bg
            });
          }

          this.list = tempArr;
          this.resultList = jsonArr;
          this.evenCodeList = this.resultList[0];
          this.type = this.evenCodeList.bet_json[0].key;
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
            this.resultList = JSON.parse(data.data.game_type_info.bet_json);
            this.resultList.forEach((item, index) => {
              item.bet_json.map(child => {
                let len = child.rate.split(",");
                child.rate = len;
                this.$set(child, "checked", false);
                this.$set(child, "money", "");
              });
            });

            let tempArr = [];
            for(let i = 1; i < 50; i++) {
              let bg = getBg(i);
              tempArr.push({
                number: i,
                checked: false,
                bg
              });
            }

            this.list = tempArr;
            this.evenCodeList = this.resultList[0];
            this.type = this.evenCodeList.bet_json[0].key;
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
    // 切换
    changeIndex(index) {
      this.current_index = index;
    },
    // 选择某一项
    chooseItem(item) {
      if (this.isPlateClose == 1) return;
      let { list, type } = this;
      let tempArr = list.filter(item => item.checked);
      let limit = 6;
      if (type === this.evenCodeList.bet_json[5].key) {
        limit = 4;
      }
      if (tempArr.length < limit) {
        item.checked = !item.checked;
      }
    },
    // 监听下注金额变化
    handleBetInput() {},
    // 监听radio变化
    handleChange(val) {
      this.clearChoose();
    },
    // 清空已选
    clearChoose() {
      const clearFn = arr => {
        for (let i = 0; i < arr.length; i++) {
          arr[i].checked = false;
        }
      };
      clearFn(this.list);
    },
    // 辅助函数： 获取选中号码的key值集合
    getCheckedNumArr() {
      const checkedArr = this.list.filter(item => item.checked);
      let resultArr = [];
      for(let i = 0, len = checkedArr.length; i < len; i++) {
        resultArr.push(checkedArr[i].number);
      }
      return resultArr;
    },
    // 辅助函数：获取key对应的取值
    getKeyToNum(key) {
      let num;
      switch (+key) {
        case 1:
        case 3:
          num = 3;
          break;
        case 4:
        case 5:
        case 7:
          num = 2;
          break;
        case 8:
          num = 4;
          break;
        default:
          break;
      }
      return num;
    },
    // 获取投注时选中的json字符串
    getBetJSON() {
      let json_arr = [];
      this.resultList.forEach(result => {

        let temp_arr = result.bet_json.filter(bet => +bet.key === +this.type);
        let bet_json = [];

        let checkedArr = this.getCheckedNumArr();

        temp_arr.forEach(item => {
          bet_json.push({
            key: item.key,
            money: this.evenCodeMoney,
            value: getCombination(checkedArr, this.getKeyToNum(item.key))
          });
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
    // 获取投注总金额
    getAllBet() {
      let all_bet = 0;
      let checkedArr = this.getCheckedNumArr();
      let combination = getCombination(checkedArr, this.getKeyToNum(this.type)).split('-');
      all_bet = combination.length * this.evenCodeMoney;
      return all_bet;
    },
    // 确认投注
    sureBet() {
      
      let check_arr = this.getCheckedNumArr();
      let limit_num = this.getKeyToNum(this.type);

      if (check_arr.length < limit_num) {
        //说明没有输入任何金额，提示
        this.$alert("下注内容不对，请重新下注", "提示", {
          confirmButtonText: "确定"
        });
        return;
      }

      let all_bet = this.getAllBet();
      let bet_json = this.getBetJSON();
      
      console.log({bet_json}, {all_bet}, {check_arr}, {limit_num});

      if (isNaN(all_bet) || all_bet <= 0) {
        //说明没有输入任何金额，提示
        this.$alert("下注内容不对，请重新下注", "提示", {
          confirmButtonText: "确定"
        });
        return;
      }
      if (this.isPlateClose == 1) {
        this.$alert('已经封盘，请开盘后再投注', '无法投注', {
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
.even-code {
  .wh(920px, auto);
  margin: 0 auto;
  margin-top: 23px;
  margin-bottom: 10px;
  .content {
    display: flex;
    border: 1px solid #e8e8e8;
    margin-top: 10px;

    .br1 {
      border-right: 1px solid #e8e8e8;
    }

    .first-table {
      flex: 1;

      .first-table-title {
        display: flex;
        height: 25px;
        background: #ffefd4;
        font-size: 12px;
        font-weight: 600;
        color: rgba(74, 65, 48, 1);
        line-height: 25px;
        border-top: 2px solid #f5a623;
        border-bottom: 1px solid #e8e8e8;

        .item-center {
          .wh(61px, 100%);
          padding: 0 10px;
          box-sizing: border-box;
          border-right: 1px solid #e8e8e8;
          border-left: 1px solid #e8e8e8;

          .result-number {
            width: 30px;
            height: 30px;
            /*border:1px solid #1F70FF;*/
            text-align: center;
            line-height: 30px;
            font-size: 16px;
            background: url("~images/bg/red_circle.png") no-repeat;
          }

          .bg {
            background: #ffefd4;
            width: auto;
          }
        }

        .item-right {
          .wh(123px, 100%);
          background: #ffffff;
        }
      }

      .first-table-content {
        .wh(100%, 330px);
        background: #ffefd4;

        .item {
          display: flex;
          .wh(100%, 32px);
          border-bottom: 1px solid #e8e8e8;
          align-items: center;

          .item-left {
            text-align: center;

            .wh(60px, 100%);

            .result-number {
              width: 30px;
              height: 30px;
              /*border:1px solid #1F70FF;*/
              text-align: center;
              line-height: 30px;
              font-size: 16px;
              background: url("~images/bg/red_circle.png") no-repeat;
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

          .item-center {
            text-align: center;
            .wh(52px, 100%);
            font-size: 12px;
            font-weight: 600;
            color: rgba(224, 32, 32, 1);
            background: #ffffff;
          }

          .item-right {
            .wh(123px, 100%);
            background: #ffffff;
            border-right: 1px solid #e8e8e8;

            img {
              width: 16px;
              height: 16px;
            }
          }
        }
      }
    }
  }
  .game_table {
    .wh(920px, auto);
  }

  .bt {
    border-top: 2px solid #f5a623;
    margin-top: 10px;
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
