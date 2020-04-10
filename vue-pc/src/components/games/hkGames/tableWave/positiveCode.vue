<template>
  <!--  正码-->
  <div class="positive-code">
    <div class="special-box">
      <div class="content">
        <div class="first-table" v-for="(firstItem,firstIndex) in 5">
          <div class="first-table-title">
            <div class="first-table-title-left br1">号码</div>
            <div class="first-table-title-center br1">赔率</div>
            <div
              class="first-table-title-right br1"
              :style="{width:firstIndex === 4 ? '70px':'69px'}"
            >金额</div>
          </div>
          <div
            class="first-table-content"
            v-if="resultListI && resultListI[1] && resultListI[1].bet_json"
          >
            <div
              class="item"
              v-for="(item,index) in (resultListI[0].bet_json).slice(firstIndex*10,firstIndex*10 + 10)"
              :class="{active: item.checked}"
            >
              <div class="item-left br1 flex-center" @click="toCheck(firstIndex,0,index)">
                <div
                  class="result-number"
                  :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                >{{firstIndex > 0 ?item.name:index < 9 ?`0${item.name}`:item.name}}</div>
              </div>
              <div
                class="item-center br1 flex-center"
                @click="toCheck(firstIndex,0,index)"
              >{{isPlateClose == 1 ? '--' : item.rate}}</div>
              <div
                class="item-right br1 flex-center"
                :style="{width:firstIndex === 4 ? '70px':'69px'}"
              >
                <el-input
                  v-model="item.money"
                  style="width: 60px"
                  @focus="handleFocus(item)"
                  :disabled="!!isPlateClose"
                ></el-input>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--总大，总小，总双，总单-->
    <div class="chief-shaw-header bt">
      <div v-for="(item,index) in 4" :key="index" class="item">
        <div class="item-left flex-center">种类</div>
        <div class="item-center flex-center bg">赔率</div>
        <div class="item-right flex-center bg">金额</div>
      </div>
    </div>
    <div class="chief-shaw-content" v-if="resultListI && resultListI[1] && resultListI[1].bet_json">
      <div
        v-for="(item,index) in resultListI[1].bet_json"
        :key="index"
        class="item"
        :class="{active: item.checked}"
      >
        <div class="item-left flex-center" @click="toCheck(0,1,index)">{{item.name}}</div>
        <div
          class="item-center flex-center bg"
          @click="toCheck(0,1,index)"
        >{{isPlateClose == 1 ? '--' : item.rate}}</div>
        <div class="item-right flex-center bg">
          <el-input v-model="item.money" @focus="handleFocus(item)" :disabled="!!isPlateClose"></el-input>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="moneyI" class="input" @input="handleBetInput" />
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @clcik="clearChoose">重置</button>
    </div>
    <!--      快选-->
    <div class="code-footer">
      <div class="choose">
        <span>快选</span>
        <ul>
          <li
            v-for="(item,index) in chooseList"
            @click="toChoose(index,item,1)"
            :class="[item.checked ?'active':'']"
          >{{item.title}}</li>
        </ul>
      </div>
      <div class="head-tail">
        <div class="choose">
          <span>头</span>
          <ul>
            <li
              v-for="(item,index) in headList"
              @click="toChoose(index,item,2)"
              :class="[item.checked ?'active':'']"
            >{{item.title}}</li>
          </ul>
        </div>
        <div class="choose">
          <span>尾</span>
          <ul>
            <li
              v-for="(item,index) in tailList"
              @click="toChoose(index,item,3)"
              :class="[item.checked ?'active':'']"
            >{{item.title}}</li>
          </ul>
        </div>
      </div>
      <div class="choose">
        <span>半波</span>
        <ul>
          <li
            v-for="(item,index) in halfWaveList"
            @click="toChoose(index,item,4)"
            :class="[item.checked ?'active':'']"
          >{{item.title}}</li>
        </ul>
      </div>
      <div class="head-tail">
        <div class="choose">
          <span>生肖</span>
          <ul>
            <li
              v-for="(item,index) in zodiacSignList"
              :class="[item.checked ?'active':'']"
              @click="toChoose(index,item,5)"
            >{{item.title}}</li>
          </ul>
        </div>
        <div class="count">
          <span>金额：</span>
          <input type="number" v-model="moneyI" @input="handleBetInput" />
          <!--          <button class="sure-btn ml10">转送</button>-->
          <button class="sure-btn ml10" @click="sureBet">下注</button>
          <button class="sure-btn ml10" @click="clearChoose">取消</button>
        </div>
      </div>
      <div class="tip">
        <p>*快选 有关两面的号码并不包含 49，合尾大小则不包含 25，如有需要请在转送自行补填，一起确认。</p>
        <p>*半波 绿大、绿单并不包含 49，如果需要请在转送自行补填，一起确认。</p>
        <div class="flex-start">
          <p>快速下单：格式->01=100 02=200（01号100元，02号200，每一注以空格分隔，等于号前面为号码，后面为金额）</p>
          <button class="sure-btn ml10" @click="toSend">发送</button>
        </div>
        <textarea name id cols="30" rows="10" v-model="gameBet"></textarea>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapMutations, mapActions } from "vuex";
import { markSixMixin } from "@/config/mixin";
import { ALERT_TIME } from "@/config/config";
export default {
  mixins: [markSixMixin],
  computed: {
    ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose","gameResultId"])
  },
  name: "positiveCode",
  data() {
    return {
      gameBet: "",
      moneyI: "",
      //bg1 是红波 bg2 是蓝波 bg3是绿波
      // add 1是合单 2是合双
      // isTail 1是家禽 2是野兽
      //ispoultry 1特尾大 2特尾小
      //isHead:1-5 分别代表0-4头
      //tail:1-10 分别代表0-9尾
      // shaw:1-12 分别代表生肖顺序
      list: [
        {
          name: "01",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 1,
          tail: 2,
          shaw: 1,
          key: ""
        },
        {
          name: "02",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 1,
          tail: 3,
          shaw: 12,
          key: ""
        },
        {
          name: "03",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 1,
          tail: 4,
          shaw: 11,
          key: ""
        },
        {
          name: "04",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 1,
          tail: 5,
          shaw: 10,
          key: ""
        },
        {
          name: "05",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 1,
          tail: 6,
          shaw: 9,
          key: ""
        },
        {
          name: "06",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 1,
          tail: 7,
          shaw: 8,
          key: ""
        },
        {
          name: "07",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 1,
          tail: 8,
          shaw: 7,
          key: ""
        },
        {
          name: "08",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 1,
          tail: 9,
          shaw: 6,
          key: ""
        },
        {
          name: "09",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 1,
          tail: 10,
          shaw: 5,
          key: ""
        },
        {
          name: "10",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 2,
          tail: 1,
          shaw: 4,
          key: ""
        },
        {
          name: "11",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 2,
          tail: 2,
          shaw: 3,
          key: ""
        },
        {
          name: "12",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 2,
          tail: 3,
          shaw: 2,
          key: ""
        },
        {
          name: "13",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 2,
          tail: 4,
          shaw: 1,
          key: ""
        },
        {
          name: "14",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 2,
          tail: 5,
          shaw: 12,
          key: ""
        },
        {
          name: "15",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 2,
          tail: 6,
          shaw: 11,
          key: ""
        },
        {
          name: "16",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 2,
          tail: 7,
          shaw: 10,
          key: ""
        },
        {
          name: "17",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 2,
          tail: 8,
          shaw: 9,
          key: ""
        },
        {
          name: "18",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 2,
          tail: 9,
          shaw: 8,
          key: ""
        },
        {
          name: "19",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 2,
          tail: 10,
          shaw: 7,
          key: ""
        },
        {
          name: "20",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 3,
          tail: 1,
          shaw: 6,
          key: ""
        },
        {
          name: "21",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 3,
          tail: 2,
          shaw: 5,
          key: ""
        },
        {
          name: "22",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 3,
          tail: 3,
          shaw: 4,
          key: ""
        },
        {
          name: "23",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 3,
          tail: 4,
          shaw: 3,
          key: ""
        },
        {
          name: "24",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 3,
          tail: 5,
          shaw: 2,
          key: ""
        },
        {
          name: "25",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 3,
          tail: 6,
          shaw: 1,
          key: ""
        },
        {
          name: "26",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 3,
          tail: 7,
          shaw: 12,
          key: ""
        },
        {
          name: "27",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 3,
          tail: 8,
          shaw: 11,
          key: ""
        },
        {
          name: "28",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 3,
          tail: 9,
          shaw: 10,
          key: ""
        },
        {
          name: "29",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 3,
          tail: 10,
          shaw: 9,
          key: ""
        },
        {
          name: "30",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 4,
          tail: 1,
          shaw: 8,
          key: ""
        },
        {
          name: "31",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 4,
          tail: 2,
          shaw: 7,
          key: ""
        },
        {
          name: "32",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 4,
          tail: 3,
          shaw: 6,
          key: ""
        },
        {
          name: "33",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 4,
          tail: 4,
          shaw: 5,
          key: ""
        },
        {
          name: "34",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 4,
          tail: 5,
          shaw: 4,
          key: ""
        },
        {
          name: "35",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 4,
          tail: 6,
          shaw: 3,
          key: ""
        },
        {
          name: "36",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 4,
          tail: 7,
          shaw: 2,
          key: ""
        },
        {
          name: "37",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 4,
          tail: 8,
          shaw: 1,
          key: ""
        },
        {
          name: "38",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 4,
          tail: 9,
          shaw: 12,
          key: ""
        },
        {
          name: "39",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 4,
          tail: 10,
          shaw: 11,
          key: ""
        },
        {
          name: "40",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 5,
          tail: 1,
          shaw: 10,
          key: ""
        },
        {
          name: "41",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 5,
          tail: 2,
          shaw: 9,
          key: ""
        },
        {
          name: "42",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 5,
          tail: 3,
          shaw: 8,
          key: ""
        },
        {
          name: "43",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 1,
          isHead: 5,
          tail: 4,
          shaw: 7,
          key: ""
        },
        {
          name: "44",
          rate: "",
          bg: 3,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 2,
          isTail: 2,
          isHead: 5,
          tail: 5,
          shaw: 6,
          key: ""
        },
        {
          name: "45",
          rate: "",
          bg: 1,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 5,
          tail: 6,
          shaw: 5,
          key: ""
        },
        {
          name: "46",
          rate: "",
          bg: 1,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 5,
          tail: 7,
          shaw: 4,
          key: ""
        },
        {
          name: "47",
          rate: "",
          bg: 2,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 5,
          tail: 8,
          shaw: 3,
          key: ""
        },
        {
          name: "48",
          rate: "",
          bg: 2,
          add: 2,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 1,
          isHead: 5,
          tail: 9,
          shaw: 2,
          key: ""
        },
        {
          name: "49",
          rate: "",
          bg: 3,
          add: 1,
          money: "",
          checked: false,
          ispoultry: 1,
          isTail: 2,
          isHead: 5,
          tail: 10,
          shaw: 1,
          key: ""
        }
      ],
      headList: [
        { title: 0, checked: false },
        { title: 1, checked: false },
        {
          title: 2,
          checked: false
        },
        { title: 3, checked: false },
        { title: 4, checked: false }
      ],
      tailList: [
        { title: 0, checked: false },
        { title: 1, checked: false },
        {
          title: 2,
          checked: false
        },
        { title: 3, checked: false },
        { title: 4, checked: false },
        { title: 5, checked: false },
        {
          title: 6,
          checked: false
        },
        { title: 7, checked: false },
        { title: 8, checked: false },
        { title: 9, checked: false }
      ],
      resultListI: [],
      zodiacSignList: [
        { title: "鼠", checked: false },
        { title: "牛", checked: false },
        {
          title: "虎",
          checked: false
        },
        { title: "兔", checked: false },
        { title: "龙", checked: false },
        {
          title: "蛇",
          checked: false
        },
        { title: "马", checked: false },
        { title: "羊", checked: false },
        {
          title: "猴",
          checked: false
        },
        { title: "鸡", checked: false },
        { title: "狗", checked: false },
        { title: "猪", checked: false }
      ],
      halfWaveList: [
        {
          title: "红大",
          checked: false
        },
        {
          title: "红小",
          checked: false
        },
        {
          title: "红单",
          checked: false
        },
        {
          title: "红双",
          checked: false
        },
        {
          title: "蓝大",
          checked: false
        },
        {
          title: "蓝小",
          checked: false
        },
        {
          title: "蓝单",
          checked: false
        },
        {
          title: "蓝双",
          checked: false
        },
        {
          title: "绿大",
          checked: false
        },
        {
          title: "绿小",
          checked: false
        },
        {
          title: "绿单",
          checked: false
        },
        {
          title: "绿双",
          checked: false
        }
      ],

      chooseList: [
        {
          title: "单",
          checked: false
        },
        {
          title: "双",
          checked: false
        },
        {
          title: "大",
          checked: false
        },
        {
          title: "小",
          checked: false
        },
        {
          title: "合单",
          checked: false
        },
        {
          title: "合双",
          checked: false
        },
        {
          title: "红",
          checked: false
        },
        {
          title: "蓝",
          checked: false
        },
        {
          title: "绿",
          checked: false
        },
        {
          title: "家禽",
          checked: false
        },
        {
          title: "野兽",
          checked: false
        },
        {
          title: "特尾大",
          checked: false
        },
        {
          title: "特尾小",
          checked: false
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
  watch: {
    moneyI(val) {
      this.resultListI.map((item, index) => {
        item.bet_json.map((child, index) => {
          if (child.checked) {
            child.money = val ? val : "";
          }
        });
      });
    }
  },
  methods: {
    // 监听下注金额变化
    handleBetInput() {
      if (this.isPlateClose == 1) return;
      this.resultListI.forEach(item => {
        item.bet_json.map(child => {
          if (child.checked) {
            child.money = this.moneyI;
          }
        });
      });
    },
    //初始化
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
              if (item.part == 1) {
                for (let i = 0; i < this.list.length; i++) {
                  if ((this.list[i].name = item.bet_json[i].name)) {
                    this.list[i].name = item.bet_json[i].name;
                    this.list[i].rate = item.bet_json[i].rate;
                    this.list[i].key = item.bet_json[i].key;
                  }
                }
                item.bet_json = this.list;
              } else {
                item.bet_json.map(secItem => {
                  this.$set(secItem, "checked", false);
                });
              }
            });
          });
          this.resultList = jsonArr;
          this.resultListI = this.resultList;
        }
      );
    },
    //重置
    toReset() {
      this.resultListI.map((item, index) => {
        item.bet_json.map((child, index) => {
          if (child.checked) {
            child.money = "";
            child.checked = false;
          }
        });
      });
    },
    //发送
    toSend() {
      let totalMoney = 0;
      let newArray2 = [];
      let checkList = this.gameBet.split(" ");
      for (var i = 0; i < checkList.length; i++) {
        var newObject = {};
        newObject.key = checkList[i].split("=")[0] >>> 0;
        newObject.money = checkList[i].split("=")[1];
        totalMoney += +newObject.money;
        newArray2.push(newObject);
      }
      let obj = [];
      obj.push({
        part: this.resultListI[0].part,
        bet_json: newArray2,
        name: this.resultListI[0].name
      });
      if (this.isPlateClose == 1) {
        this.$alert("已经封盘，请开盘后再投注", "无法投注", {
          confirmButtonText: "确定"
        });
        return;
      }
      if (isNaN(totalMoney) || totalMoney <= 0) {
        //说明没有输入任何金额，提示
        this.$alert("下注内容不对，请重新下注", "提示", {
          confirmButtonText: "确定"
        });
        return;
      }
      this.$Api(
        {
          api_name: "kkl.game.gameBet",
          bet_json: JSON.stringify(obj),
          game_result_id: this.gameResultId,
          total_bet_money: parseInt(totalMoney),
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
    toCheck(secIndex, firstIndex, index_) {
      if (this.isPlateClose == 1) return;
      let index = secIndex > 0 ? index_ + secIndex * 10 : index_;
      this.resultListI[firstIndex].bet_json[index].checked = !this.resultListI[
        firstIndex
      ].bet_json[index].checked;
      if (!this.resultListI[firstIndex].bet_json[index].checked) {
        //取消选择后，金额也取消
        this.resultListI[firstIndex].bet_json[index].money = "";
      } else {
        //选中时，底下的金额有就自动填充
        if (this.moneyI) {
          this.resultListI[firstIndex].bet_json[index].money = this.moneyI;
        }
      }
    },
    //获取焦点
      handleFocus(item) {
          if (this.isPlateClose == 1) return;
          item.checked = true;
          if (this.moneyI) {
              item.money = this.moneyI;
          }
      },
    onFocus(secIndex, firstIndex, index_) {
      if (this.isPlateClose == 1) return;
      let index = secIndex > 0 ? index_ + secIndex * 10 : index_;
      this.resultListI[firstIndex].bet_json[index].checked = true;
    },
    initList(type) {
      this.chooseList.map((item, childIndex) => {
        item.checked = false;
      });
      this.headList.map((item, childIndex) => {
        item.checked = false;
      });
      this.tailList.map((item, childIndex) => {
        item.checked = false;
      });
      this.halfWaveList.map((item, childIndex) => {
        item.checked = false;
      });
      if (type !== 5) {
        this.zodiacSignList.map((item, childIndex) => {
          item.checked = false;
        });
      }
    },
    //选择
    toChoose(index, chooseItem, type) {
      switch (type) {
        case 1:
          this.initList(type);
          this.chooseList.map((item, childIndex) => {
            if (childIndex === index) {
              item.checked = true;
            } else {
              item.checked = false;
            }
          });
          break;
        case 2:
          this.initList(type);
          this.headList.map((item, childIndex) => {
            if (childIndex === index) {
              item.checked = true;
            } else {
              item.checked = false;
            }
          });
          break;
        case 3:
          this.initList(type);
          this.tailList.map((item, childIndex) => {
            if (childIndex === index) {
              item.checked = true;
            } else {
              item.checked = false;
            }
          });
          break;
        case 4:
          this.initList(type);
          this.halfWaveList.map((item, childIndex) => {
            if (childIndex === index) {
              item.checked = true;
            } else {
              item.checked = false;
            }
          });
          break;
        case 5:
          this.initList(type);
          if (
            !this.zodiacSignList.filter(item => item.checked === true).length
          ) {
            this.toReset();
          }
          this.zodiacSignList[index].checked = !this.zodiacSignList[index]
            .checked;

          break;
        default:
          break;
      }
      // 快捷选择
      if (type !== 5) {
        this.toReset();
      }
      this.resultListI[0].bet_json.map((item, index) => {
        if (this.isPlateClose == 1) return;
        if (chooseItem.title === "单") {
          if (item.name % 2 === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title === "双") {
          if (item.name % 2 === 0) {
            item.checked = true;
          }
        } else if (chooseItem.title === "大") {
          if (index > 23) {
            item.checked = true;
          }
        } else if (chooseItem.title === "小") {
          if (index < 24) {
            item.checked = true;
          }
        } else if (chooseItem.title === "红") {
          if (item.bg === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title === "蓝") {
          if (item.bg === 2) {
            item.checked = true;
          }
        } else if (chooseItem.title === "绿") {
          if (item.bg === 3) {
            item.checked = true;
          }
        } else if (chooseItem.title === "合单") {
          if (item.add === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title === "合双") {
          if (item.add === 2) {
            item.checked = true;
          }
        } else if (chooseItem.title === "家禽") {
          if (item.isTail === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title === "野兽") {
          if (item.isTail === 2) {
            item.checked = true;
          }
        } else if (chooseItem.title === "特尾大") {
          if (item.ispoultry === 1) {
            item.checked = true;
          }
          // #ffefd4
        } else if (chooseItem.title === "特尾小") {
          if (item.ispoultry === 2) {
            item.checked = true;
          }
        } else if (chooseItem.title == "0") {
          if (type === 2) {
            //0头
            if (item.isHead === 1) {
              item.checked = true;
            }
          } else {
            // 0尾
            if (item.tail === 1) {
              item.checked = true;
            }
          }
        } else if (chooseItem.title == "1") {
          if (type === 2) {
            //2头
            if (item.isHead === 2) {
              item.checked = true;
            }
          } else {
            // 2尾
            if (item.tail === 2) {
              item.checked = true;
            }
          }
        } else if (chooseItem.title == "2") {
          if (type === 2) {
            //3头
            if (item.isHead === 3) {
              item.checked = true;
            }
          } else {
            // 3尾
            if (item.tail === 3) {
              item.checked = true;
            }
          }
        } else if (chooseItem.title == "3") {
          if (type === 2) {
            //3头
            if (item.isHead === 4) {
              item.checked = true;
            }
          } else {
            // 3尾
            if (item.tail === 4) {
              item.checked = true;
            }
          }
        } else if (chooseItem.title == "4") {
          // 4尾
          if (type === 2) {
            //3头
            if (item.isHead === 5) {
              item.checked = true;
            }
          } else {
            // 3尾
            if (item.tail === 5) {
              item.checked = true;
            }
          }
        } else if (chooseItem.title == "5") {
          // 5尾
          if (item.tail === 6) {
            item.checked = true;
          }
        } else if (chooseItem.title == "6") {
          // 6尾
          if (item.tail === 7) {
            item.checked = true;
          }
        } else if (chooseItem.title == "7") {
          // 7尾
          if (item.tail === 8) {
            item.checked = true;
          }
        } else if (chooseItem.title == "8") {
          // 8尾
          if (item.tail === 9) {
            item.checked = true;
          }
        } else if (chooseItem.title == "9") {
          // 9尾
          if (item.tail === 10) {
            item.checked = true;
          }
        } else if (chooseItem.title == "红大") {
          if (item.bg === 1 && index > 23) {
            item.checked = true;
          }
        } else if (chooseItem.title == "红小") {
          if (item.bg === 1 && index < 24) {
            item.checked = true;
          }
        } else if (chooseItem.title == "红单") {
          if (item.bg === 1 && item.name % 2 === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title == "红双") {
          if (item.bg === 1 && item.name % 2 === 0) {
            item.checked = true;
          }
        } else if (chooseItem.title == "蓝大") {
          if (item.bg === 2 && index > 23) {
            item.checked = true;
          }
        } else if (chooseItem.title == "蓝小") {
          if (item.bg === 2 && index < 24) {
            item.checked = true;
          }
        } else if (chooseItem.title == "蓝单") {
          if (item.bg === 2 && item.name % 2 === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title == "蓝双") {
          if (item.bg === 2 && item.name % 2 === 0) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "绿大") {
          if (item.bg === 3 && index > 23) {
            item.checked = true;
          }
        } else if (chooseItem.title == "绿小") {
          if (item.bg === 3 && index < 24) {
            item.checked = true;
          }
        } else if (chooseItem.title == "绿单") {
          if (item.bg === 3 && item.name % 2 === 1) {
            item.checked = true;
          }
        } else if (chooseItem.title == "绿双") {
          if (item.bg === 3 && item.name % 2 === 0) {
            item.checked = true;
          }
        } else if (chooseItem.title == "鼠") {
          if (item.shaw === 1) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "牛") {
          if (item.shaw === 2) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "虎") {
          if (item.shaw === 3) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "兔") {
          if (item.shaw === 4) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "龙") {
          if (item.shaw === 5) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "蛇") {
          if (item.shaw === 6) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "马") {
          if (item.shaw === 7) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "羊") {
          if (item.shaw === 8) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "猴") {
          if (item.shaw === 9) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "鸡") {
          if (item.shaw === 10) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "狗") {
          if (item.shaw === 11) {
            item.checked = !item.checked;
          }
        } else if (chooseItem.title == "猪") {
          if (item.shaw === 12) {
            item.checked = !item.checked;
          }
        }
        if (this.moneyI && item.checked) {
          item.money = this.moneyI;
        }
      });
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
            this.resultList.forEach((item, index) => {
              if (item.name === "号码") {
                for (let i = 0; i < this.list.length; i++) {
                  if ((this.list[i].name = item.bet_json[i].name)) {
                    this.list[i].name = item.bet_json[i].name;
                    this.list[i].rate = item.bet_json[i].rate;
                    this.list[i].key = item.bet_json[i].key;
                  }
                }

                item.bet_json = this.list;
              } else {
                item.bet_json.map(secItem => {
                  this.$set(secItem, "checked", false);
                });
              }
            });

            this.resultListI = this.resultList;
            console.log(this.resultListI[0]);
          }
        }
      );
    },
    //投注
    getGameBet() {
      let list = [];
      let bet_json = [];
      let totalMoney = 0;
      let obj = {};
      //筛选出选择的数组
      this.resultListI.map((item, index) => {
        item.bet_json.map((child, index) => {
          if (child.checked && child.money) {
            list.push(item);
          }
        });
      });
      //在筛选出相应的选择的号把 key,money提出来

      list.map((item, index) => {
        this.$set(item, "newArray", []);
        item.bet_json.map((child, index) => {
          if (child.checked && child.money) {
            //选择了 并填写了金额
            var newObject = {};
            newObject.key = child.key;
            newObject.money = child.money;
            item.newArray.push(newObject);
            totalMoney = Number(totalMoney) + Number(child.money);
          }
        });
        //拼成后端需要的格式
        obj = {
          part: item.part,
          name: item.name,
          bet_json: item.newArray
        };
        bet_json.push(obj);
      });
      if (!list.length) {
        //说明没有输入任何金额，提示
        this.$alert("下注内容不对，请重新下注", "提示", {
          confirmButtonText: "确定"
        });
        return;
      }

      //  利用对象访问属性的方法,去重
      let newArr = [];
      let obj_ = {};
      for (var i = 0; i < bet_json.length; i++) {
        if (!obj_[bet_json[i].name]) {
          newArr.push(bet_json[i]);
          obj_[bet_json[i].name] = true;
        }
      }

      this.$Api(
        {
          api_name: "kkl.game.gameBet",
          bet_json: newArr,
          game_result_id: this.gameResultId,
          total_bet_money: totalMoney,
          game_type_id: this.id
        },
        (err, data) => {
          if (!err) {
            console.log("chenggongla ");
          }
        }
      );
    }
  },
  mounted() {
    // this.getResult()
  }
};
</script>

<style scoped lang='less'>
.positive-code {
  .wh(920px, auto);
  margin: 0 auto;
  margin-top: 23px;
  margin-bottom: 10px;
  .flex-start {
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }
  .special-box {
    .mt10 {
      margin-top: 10px;
    }

    .content {
      display: flex;
      border: 1px solid #e8e8e8;

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

          .first-table-title-left {
            text-align: center;
            .wh(60px, 100%);
          }

          .first-table-title-center {
            text-align: center;
            .wh(52px, 100%);
          }

          .first-table-title-right {
            text-align: center;
            .wh(68px, 100%);
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
            background: #ffffff;

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
              text-align: center;
              .wh(60px, 100%);
              background: #ffefd4;
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
              /*background: #ffffff;*/
            }

            .item-right {
              text-align: center;
              .wh(68px, 100%);
              /*background: #ffffff;*/
            }
          }
        }
      }
    }
  }

  .flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .fz12 {
    font-size: 12px !important;
    font-weight: 600;
    color: rgba(74, 65, 48, 1) !important;
  }

  .game_table {
    .wh(920px, auto);
  }

  .bb {
    border-bottom: 1px solid #e8e8e8;
  }

  .bt {
    border-top: 2px solid #f5a623;
    margin-top: 10px;
  }

  .even-code-table {
    .wh(920px, auto);
    display: flex;
    flex-wrap: wrap;
    background: rgba(255, 239, 212, 1);
    border-left: 1px solid #e8e8e8;
    border-right: 1px solid #e8e8e8;

    .table-item {
      display: flex;
      height: 32px;
      border-bottom: 1px solid #e8e8e8;

      .item-left {
        .wh(61px, 100%);
        padding: 0 10px;
        box-sizing: border-box;
        background: #ffffff;
        border-right: 1px solid #e8e8e8;

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

      .item-center {
        .wh(52px, 100%);
        font-size: 12px;
        font-weight: 600;
        color: rgba(224, 32, 32, 1);
        background: #ffffff;
        border-right: 1px solid #e8e8e8;
      }

      .item-right {
        .wh(70px, 100%);
        background: #ffffff;
        /*border-right: 1px solid #e8e8e8;*/
      }

      .bg {
        background: #ffefd4;
      }
    }
  }

  .chief-shaw-header {
    .wh(920px, 24px);
    background: #ffefd4;
    border-top: 2px solid #f5a623;
    font-size: 12px;
    font-weight: 600;
    color: rgba(74, 65, 48, 1);
    display: flex;

    .item {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      border-bottom: 1px solid #e8e8e8;

      .item-left {
        .wh(79px, 100%);
        border-right: 1px solid #e8e8e8;
      }

      .item-center {
        .wh(65px, 100%);
        border-right: 1px solid #e8e8e8;
      }

      .item-right {
        .wh(83px, 100%);
        border-right: 1px solid #e8e8e8;
      }
    }
  }

  .chief-shaw-content {
    .wh(920px, 24px);
    font-size: 12px;
    font-weight: 600;
    color: rgba(74, 65, 48, 1);
    display: flex;

    .item {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
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
        font-size: 14px;
        font-weight: 600;
        color: rgba(133, 86, 9, 1);
        .wh(79px, 100%);
        border-right: 1px solid #e8e8e8;
        background: #ffefd4;
      }

      .item-center {
        .wh(65px, 100%);
        border-right: 1px solid #e8e8e8;
        font-size: 12px;
        font-weight: 600;
        color: rgba(224, 32, 32, 1);
      }

      .item-right {
        .wh(83px, 100%);
        border-right: 1px solid #e8e8e8;
      }
    }
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

    .mr10 {
      margin-right: 10px;
    }
  }

  .code-footer {
    .wh(920px, auto);
    border: 1px solid #e8e8e8;
    margin-top: 20px;
    border-top: 2px solid #f5a623;

    .choose {
      padding: 0 10px;
      box-sizing: border-box;
      border-bottom: 1px solid #e8e8e8;
      display: flex;
      font-size: 14px;
      font-weight: 600;
      color: rgba(133, 86, 9, 1);
      align-items: center;

      ul {
        display: flex;
        margin-left: 10px;
        li {
          margin-right: 4px;
          padding: 8px 6px;
          font-size: 14px;
          font-weight: 600;
          color: rgba(255, 248, 239, 1);
          background: linear-gradient(
            360deg,
            rgba(209, 145, 60, 1) 0%,
            rgba(255, 209, 148, 1) 100%
          );
          border-radius: 4px;

          &.active {
            background: #d1913c;
          }
        }
      }
    }

    .head-tail {
      display: flex;
      border-bottom: 1px solid #e8e8e8;

      .count {
        margin-left: 56px;
        display: flex;
        font-size: 14px;
        font-weight: 600;
        color: rgba(133, 86, 9, 1);
        align-items: center;

        input {
          width: 76px;
          height: 20px;
          background: rgba(255, 255, 255, 1);
          box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.2);
          border-radius: 2px;
          border: 1px solid rgba(204, 204, 204, 1);
          box-sizing: border-box;
        }

        .sure-btn {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 65px;
          height: 30px;
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

        .ml10 {
          margin-left: 10px;
        }
      }
    }

    .tip {
      padding: 9px 10px;
      box-sizing: border-box;
      .wh(100%, auto);
      font-weight: 400;
      line-height: 24px;
      color: rgba(51, 51, 51, 1);

      textarea {
        border: 1px solid #e8e8e8;
        width: 100%;
        height: 172px;
        padding: 5px;
        font-size: 14px;
      }
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
    padding:0 5px;
    height: 20px !important;
  }
</style>
