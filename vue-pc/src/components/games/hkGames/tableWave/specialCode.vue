<template>
  <!--  特码-->
  <div class="special-code">
    <div class="special-box">
      <ul class="tab_nav_first">
        <li
          :class="{active: current_index == index}"
          v-for="(item, index) in list"
          :key="index"
          @click="changeIndex(index)"
        >{{item.title}}</li>
      </ul>
      <div class="content">
        <div class="first-table" v-for="(firstItem,firstIndex) in 5" :key="firstIndex">
          <div class="first-table-title">
            <div class="first-table-title-left br1">号码</div>
            <div class="first-table-title-center br1">赔率</div>
            <div
              class="first-table-title-right br1"
              :style="{width:firstIndex === 4 ? '70px':'69px'}"
            >金额</div>
          </div>
          <template v-if="current_index === 0">
            <div class="first-table-content" v-if="resultListA  && resultListA.bet_json">
              <div
                class="item"
                :class="{active: item.checked}"
                v-for="(item,index) in (resultListA && resultListA.bet_json).slice(firstIndex*10,firstIndex*10 + 10)"
                :key="index"
                @click.stop="chooseItem(item)"
              >
                <div class="item-left br1 flex-center">
                  <div
                    class="result-number"
                    :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                    v-if="firstIndex === 0"
                  >{{ (index + 1) < 10 ? '0' + (index + 1) : (index + 1)}}</div>
                  <div
                    class="result-number"
                    :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                    v-if="firstIndex > 0"
                  >{{firstIndex * 10 + index + 1}}</div>
                </div>
                <div class="item-center br1 flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
                <div
                  class="item-right br1 flex-center"
                  :style="{width:firstIndex === 4 ? '70px':'69px'}"
                  @click.stop
                >
                  <el-input
                    v-model="item.money"
                    :disabled="!!isPlateClose"
                    style="width: 60px"
                    @focus="handleFocus(item)"
                  ></el-input>
                </div>
              </div>
            </div>
          </template>
          <template v-if="current_index === 1">
            <div class="first-table-content" v-if="resultListB  && resultListB.bet_json">
              <div
                class="item"
                :class="{active: item.checked}"
                v-for="(item,index) in (resultListB && resultListB.bet_json).slice(firstIndex*10,firstIndex*10 + 10)"
                :key="index"
                @click.stop="chooseItem(item)"
              >
                <div class="item-left br1 flex-center">
                  <div
                    class="result-number"
                    :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                    v-if="firstIndex === 0"
                  >{{ (index + 1) < 10 ? '0' + (index + 1) : (index + 1)}}</div>
                  <div
                    class="result-number"
                    :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                    v-if="firstIndex > 0"
                  >{{firstIndex * 10 + index + 1}}</div>
                </div>
                <div class="item-center br1 flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
                <div
                  class="item-right br1 flex-center"
                  :style="{width:firstIndex === 4 ? '70px':'69px'}"
                  @click.stop
                >
                  <el-input
                    v-model="item.money"
                    :disabled="!!isPlateClose"
                    style="width: 60px"
                    @focus="handleFocus(item)"
                  ></el-input>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
    <!--    特大特小title-->
    <div class="chief-shaw-table bt mt10">
      <div v-for="(item,index) in 4" :key="index" class="chief-shaw-header">
        <div class="item">
          <div class="item-left flex-center">种类</div>
          <div class="item-center flex-center bg">赔率</div>
          <div class="item-right flex-center bg">金额</div>
        </div>
        <!--  特大特小content  -->
        <div class="chief-shaw-content" v-if="resultListC && resultListC.bet_json">
          <div
            v-for="(childItem,childIndex) in resultListC.bet_json.slice(index*4,index*4 + 4)"
            :key="childIndex"
            class="item"
            :class="{active: childItem.checked}"
            @click.stop="chooseItem(childItem)"
          >
            <div class="item-left flex-center">{{childItem.name}}</div>
            <div class="item-center flex-center bg">{{isPlateClose == 1 ? '--' : childItem.rate}}</div>
            <div class="item-right flex-center bg" @click.stop>
              <el-input
                v-model="childItem.money"
                :disabled="!!isPlateClose"
                style="width: 70px"
                @focus="handleFocus(childItem)"
              ></el-input>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--   下注金额-->
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="moneyOne" class="input" @input="handleBetInput" />
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
    <!--      快选-->
    <div class="code-footer">
      <div class="choose">
        <span>快选</span>
        <ul>
          <li
            :class="{active: activeQuickChoose === index}"
            v-for="(item,index) in chooseList"
            :key="index"
            @click="filterQuickChoose(index, item.type)"
          >{{item.title}}</li>
        </ul>
      </div>
      <div class="head-tail">
        <div class="choose">
          <span>头</span>
          <ul>
            <li
              :class="{active: activeHead === index}"
              v-for="(item,index) in 5"
              :key="index"
              @click="filterHead(index)"
            >{{index}}</li>
          </ul>
        </div>
        <div class="choose">
          <span>尾</span>
          <ul>
            <li
              :class="{active: activeTail === index}"
              v-for="(item,index) in 10"
              :key="index"
              @click="filterTail(index)"
            >{{index}}</li>
          </ul>
        </div>
      </div>
      <div class="choose">
        <span>半波</span>
        <ul>
          <li
            :class="{active: activeHalfWave === index}"
            v-for="(item,index) in halfWaveList"
            :key="index"
            @click="filterHalfWave(index, item.wave, item.size, item.parity)"
          >{{item.title}}</li>
        </ul>
      </div>
      <div class="head-tail">
        <div class="choose">
          <span>生肖</span>
          <ul>
            <li
              :class="{active: selectedZodiacSign.indexOf(item) !== -1}"
              v-for="(item,index) in zodiacSignList"
              :key="index"
              @click="filterZodiacSign(item)"
            >{{item}}</li>
          </ul>
        </div>
        <div class="count">
          <span>金额：</span>
          <input type="number" v-model="moneyOne" @input="handleBetInput" />
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
import {
  ALERT_TIME,
  SHENGXIAO_ARRAY,
  RED_WAVE,
  BLUE_WAVE,
  GREEN_WAVE
} from "@/config/config";
import { getShengxiao } from "@/config/utils";
export default {
  name: "specialCode",
  computed: {
    ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose", "gameResultId"])
  },
  data() {
    return {
      gameBet: "",
      gameResult: [], //开奖结果
      moneyOne: "", //总金额
      resultList: [],
      resultListA: [], //特码A
      resultListB: [], //特码B
      resultListC: [], //快选
      zodiacSignList: SHENGXIAO_ARRAY,
      halfWaveList: [
        {
          title: "红大",
          wave: "red",
          size: "big",
          parity: ""
        },
        {
          title: "红小",
          wave: "red",
          size: "small",
          parity: ""
        },
        {
          title: "红单",
          wave: "red",
          size: "",
          parity: "odd"
        },
        {
          title: "红双",
          wave: "red",
          size: "",
          parity: "even"
        },
        {
          title: "蓝大",
          wave: "blue",
          size: "big",
          parity: ""
        },
        {
          title: "蓝小",
          wave: "blue",
          size: "small",
          parity: ""
        },
        {
          title: "蓝单",
          wave: "blue",
          size: "",
          parity: "odd"
        },
        {
          title: "蓝双",
          wave: "blue",
          size: "",
          parity: "even"
        },
        {
          title: "绿大",
          wave: "green",
          size: "big",
          parity: ""
        },
        {
          title: "绿小",
          wave: "green",
          size: "small",
          parity: ""
        },
        {
          title: "绿单",
          wave: "green",
          size: "",
          parity: "odd"
        },
        {
          title: "绿双",
          wave: "green",
          size: "",
          parity: "even"
        }
      ],
      chooseList: [
        {
          title: "单",
          type: "odd"
        },
        {
          title: "双",
          type: "even"
        },
        {
          title: "大",
          type: "big"
        },
        {
          title: "小",
          type: "small"
        },
        {
          title: "合单",
          type: "sum_odd"
        },
        {
          title: "合双",
          type: "sum_even"
        },
        {
          title: "红",
          type: "red"
        },
        {
          title: "蓝",
          type: "blue"
        },
        {
          title: "绿",
          type: "green"
        },
        {
          title: "家禽",
          type: "poultry"
        },
        {
          title: "野兽",
          type: "beast"
        },
        {
          title: "尾大",
          type: "tail_big"
        },
        {
          title: "尾小",
          type: "tail_small"
        }
      ],
      current_index: 0,
      list: [{ title: "特码A" }, { title: "特码B" }],
      selectedZodiacSign: [], // 已选生肖
      activeHead: -1, // 当前选择的头筛选项
      activeTail: -1, // 当前选择的尾筛选项
      activeQuickChoose: -1, // 当前选择的快选
      activeHalfWave: -1 // 当前选择的半波
    };
  },
  props: {
    id: {
      type: String,
      default: () => ""
    }
  },
  methods: {
    ...mapMutations({
      chooseGame: "CHOOSE_GAME"
    }),
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
              if (index !== 2) {
                if (RED_WAVE.indexOf(+child.name) !== -1) {
                  child.bg = 1;
                }
                if (BLUE_WAVE.indexOf(+child.name) !== -1) {
                  child.bg = 2;
                }
                if (GREEN_WAVE.indexOf(+child.name) !== -1) {
                  child.bg = 3;
                }
              }
              child.checked = false;
              child.money = "";
            });
          });
          this.resultList = jsonArr;
          this.resultListA = this.resultList[0];
          this.resultListB = this.resultList[1];
          this.resultListC = this.resultList[2];
        }
      );
    },
    //resultListA
    changeIndex(index) {
      this.current_index = index;
      this.clearFilter();
      this.clearChoose();
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
            let jsonArr = JSON.parse(this.gameResult.game_type_info.bet_json);
            // let len = jsonArr.length;
            // let initArr = arr => {
            //   arr.forEach(item => {
            //     item.checked = false;
            //     item.money = '';
            //   });
            // };
            // for(let i = 0; i < len; i++) {
            //   this.resultList.push(initArr(jsonArr[i].bet_json));
            // }
            jsonArr.forEach((item, index) => {
              item.bet_json.map(child => {
                if (index !== 2) {
                  if (RED_WAVE.indexOf(+child.name) !== -1) {
                    child.bg = 1;
                  }
                  if (BLUE_WAVE.indexOf(+child.name) !== -1) {
                    child.bg = 2;
                  }
                  if (GREEN_WAVE.indexOf(+child.name) !== -1) {
                    child.bg = 3;
                  }
                  // if(child.name == '1' || child.name == '2' || child.name == '7' || child.name == '8' || child.name == '12'||child.name == '13' || child.name == '18' || child.name == '19' ||child.name == '23' ||child.name == '24' || child.name == '29' ||child.name == '30' || child.name == '34' ||child.name == '35' ||child.name == '40' ||child.name == '45' ||child.name == '46'){
                  //     //红波球
                  //     this.$set(child, 'bg', 1)
                  // }else if(child.name == '3' ||child.name == '4' ||child.name == '9' ||child.name == '10' ||child.name == '14' ||child.name == '15' ||child.name == '20' ||child.name == '25' ||child.name == '26' ||child.name == '31' ||child.name == '36' ||child.name == '37' ||child.name == '41' ||child.name == '42' ||child.name == '47' ||child.name == '48'){
                  //     //蓝波
                  //     this.$set(child, 'bg', 2)
                  // }else{
                  //     //绿波
                  //     this.$set(child, 'bg', 3)
                  // }
                }
                child.checked = false;
                child.money = "";
                child.part = item.part;
                child.Pname = item.name;

                // this.$set(child, 'checked', false)
                // this.$set(child, 'money', '')
                // this.$set(child, 'part', item.part)
                // this.$set(child, 'Pname', item.name)
              });
            });
            this.resultList = jsonArr;
            this.resultListA = this.resultList[0];
            this.resultListB = this.resultList[1];
            this.resultListC = this.resultList[2];
          }
        }
      );
    },
    //投注
    getGameBet() {
      let list = [];
      let obj = {
        part: "",
        name: "",
        bet_json: []
      };
      list[0] = this.resultListA.bet_json.filter(
        (item, index) => item.money > 0
      );
      list.forEach((item, index) => {});
      // let bet_json = this.getBetJSON(this.choosedGame.game_type_id)
      console.log(list);
      // this.$Api(
      //     {
      //         api_name: "kkl.game.gameBet",
      //         bet_json:bet_json,
      //         game_result_id:this.gameResult.game_type_info.issue_num,
      //         total_bet_money:this.moneyOne,
      //         game_type_id:this.id
      //     },
      //     (err, data) => {
      //         if (!err) {
      //
      //             this.resultList = JSON.parse(data.data.game_type_info.bet_json)
      //             this.resultList.forEach((item, index) => {
      //                 item.bet_json.map(child => {
      //                     this.$set(child, 'money', '')
      //                 })
      //             })
      //             this.resultListA = this.resultList[0].bet_json
      //             console.log(this.resultListA.length)
      //             this.resultListB = this.resultList[1].bet_json
      //             this.resultListC = this.resultList[2].bet_json
      //         }
      //     }
      // );
    },
    // 选择某一项
    chooseItem(item) {
      if (this.isPlateClose == 1) return;
      item.checked = !item.checked;
      if (this.moneyOne) {
        if (!item.checked) {
          item.money = "";
        } else {
          item.money = this.moneyOne;
        }
      }
    },
    // 处理聚焦
    handleFocus(item) {
      if (this.isPlateClose == 1) return;
      item.checked = true;
      if (this.moneyOne) {
        item.money = this.moneyOne;
      }
    },
    // 监听下注金额变化
    handleBetInput() {
      if (this.isPlateClose == 1) return;
      this.resultList.forEach(item => {
        item.bet_json.map(child => {
          if (child.checked) {
            child.money = this.moneyOne;
          }
        });
      });
    },
    // 清空已选
    clearChoose() {
      const clearFn = arr => {
        for (let i = 0; i < arr.length; i++) {
          arr[i].checked = false;
          arr[i].money = "";
        }
      };
      for (let i = 0; i < this.resultList.length; i++) {
        clearFn(this.resultList[i].bet_json);
      }
    },
    // 清空其他筛选项
    clearFilter() {
      this.activeQuickChoose = -1;
      this.activeHead = -1;
      this.activeTail = -1;
      this.activeHalfWave = -1;
      this.selectedZodiacSign = [];
    },
    // 获取当前操作列表
    getOperateList() {
      let operate_list = [];
      if (this.current_index === 0) {
        operate_list = this.resultListA;
      } else {
        operate_list = this.resultListB;
      }
      return operate_list;
    },
    // 获取波的类型
    getWave(val) {
      let wave = "";
      if (RED_WAVE.indexOf(val) !== -1) {
        wave = "red";
      }
      if (BLUE_WAVE.indexOf(val) !== -1) {
        wave = "blue";
      }
      if (GREEN_WAVE.indexOf(val) !== -1) {
        wave = "green";
      }
      return wave;
    },
    // 获取大小
    getSize(val, mid) {
      let size = "";
      if (val > mid) {
        size = "big";
      } else {
        size = "small";
      }
      return size;
    },
    // 获取单双
    getParity(val) {
      let parity = "";
      if (val % 2 === 0) {
        parity = "even";
      } else {
        parity = "odd";
      }
      return parity;
    },
    // 获取和为单还是双
    getSumParity(val) {
      let sum_parity = "";
      let temp_arr = val.split("");
      let temp_sum = 0;
      temp_sum = temp_arr.reduce((a, b) => +a + +b, 0);
      sum_parity = this.getParity(temp_sum);
      return `sum_${sum_parity}`;
    },
    // 获取家禽还是野兽
    getAnimalType(val) {
      let animal_type = "";
      let poultry_list = ["牛", "马", "羊", "鸡", "狗", "猪"];
      let beast_list = ["鼠", "虎", "龙", "蛇", "兔", "猴"];
      let animal = getShengxiao(val);
      if (poultry_list.indexOf(animal) !== -1) {
        animal_type = "poultry";
      }
      if (beast_list.indexOf(animal) !== -1) {
        animal_type = "beast";
      }
      return animal_type;
    },
    // 获取尾是大还是小
    getTailSize(val) {
      let tail_size = "";
      let tail = val % 10;
      let mid = 4;
      tail_size = this.getSize(tail, mid);
      return `tail_${tail_size}`;
    },
    // 筛选快选
    filterQuickChoose(index, type) {
      if (this.isPlateClose == 1) return;
      let operate_list = this.getOperateList();
      let mid = Math.floor(operate_list.bet_json.length / 2);
      this.clearFilter();
      this.clearChoose();
      this.activeQuickChoose = +index;
      switch (type) {
        case "odd":
        case "even":
          operate_list.bet_json.map(bet => {
            let parity = this.getParity(+bet.name);
            if (parity === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        case "big":
        case "small":
          operate_list.bet_json.map(bet => {
            let size = this.getSize(+bet.name, mid);
            if (size === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        case "sum_odd":
        case "sum_even":
          operate_list.bet_json.map(bet => {
            let sum_parity = this.getSumParity(bet.name);
            if (sum_parity === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        case "red":
        case "blue":
        case "green":
          operate_list.bet_json.map(bet => {
            let wave = this.getWave(+bet.name);
            if (wave === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        case "poultry":
        case "beast":
          operate_list.bet_json.map(bet => {
            let animal_type = this.getAnimalType(+bet.name);
            if (animal_type === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        case "tail_big":
        case "tail_small":
          operate_list.bet_json.map(bet => {
            let tail_size = this.getTailSize(+bet.name);
            if (tail_size === type) {
              bet.checked = true;
              if (this.moneyOne) {
                bet.money = this.moneyOne;
              }
            }
          });
          break;
        default:
          break;
      }
    },
    // 筛选头
    filterHead(head) {
      if (this.isPlateClose == 1) return;
      let operate_list = this.getOperateList();
      this.clearFilter();
      this.clearChoose();
      this.activeHead = +head;
      operate_list.bet_json.map(bet => {
        let temp_arr = bet.name.split("");
        if (temp_arr.length === 1) {
          if (+head === 0) {
            bet.checked = true;
            if (this.moneyOne) {
              bet.money = this.moneyOne;
            }
          }
        } else {
          if (+head === +temp_arr[0]) {
            bet.checked = true;
            if (this.moneyOne) {
              bet.money = this.moneyOne;
            }
          }
        }
      });
    },
    // 筛选尾
    filterTail(tail) {
      if (this.isPlateClose == 1) return;
      let operate_list = this.getOperateList();
      this.clearFilter();
      this.clearChoose();
      this.activeTail = +tail;
      operate_list.bet_json.map(bet => {
        let temp_arr = bet.name.split("");
        if (temp_arr.length === 1) {
          if (+tail === +temp_arr[0]) {
            bet.checked = true;
            if (this.moneyOne) {
              bet.money = this.moneyOne;
            }
          }
        } else {
          if (+tail === +temp_arr[1]) {
            bet.checked = true;
            if (this.moneyOne) {
              bet.money = this.moneyOne;
            }
          }
        }
      });
    },
    // 筛选半波
    filterHalfWave(index, wave, size, parity) {
      if (this.isPlateClose == 1) return;
      let operate_list = this.getOperateList();
      let mid = Math.floor(operate_list.bet_json.length / 2);
      this.clearFilter();
      this.clearChoose();
      this.activeHalfWave = +index;
      operate_list.bet_json.map(bet => {
        let _wave = this.getWave(+bet.name);
        let _size = this.getSize(+bet.name, mid);
        let _parity = this.getParity(+bet.name);
        if (_wave === wave && (_size === size || _parity === parity)) {
          bet.checked = true;
          if (this.moneyOne) {
            bet.money = this.moneyOne;
          }
        }
      });
    },
    // 筛选生肖
    filterZodiacSign(item) {
      if (this.isPlateClose == 1) return;
      let index = this.selectedZodiacSign.indexOf(item);
      let operate_list = this.getOperateList();
      if (!this.selectedZodiacSign.length) {
        this.clearFilter();
        this.clearChoose();
      }
      if (index !== -1) {
        this.selectedZodiacSign.splice(index, 1);
        operate_list.bet_json.map(bet => {
          let zodiac_sign = getShengxiao(+bet.name);
          if (zodiac_sign === item) {
            bet.checked = false;
            bet.money = "";
          }
        });
      } else {
        this.selectedZodiacSign.push(item);
        operate_list.bet_json.map(bet => {
          let zodiac_sign = getShengxiao(+bet.name);
          if (zodiac_sign === item) {
            bet.checked = true;
            if (this.moneyOne) {
              bet.money = this.moneyOne;
            }
          }
        });
      }
    },
    // 获取投注时选中的json字符串
    getBetJSON() {
      let json_arr = [];
      this.resultList.forEach(result => {
        let temp_arr = result.bet_json.filter(bet => bet.checked);
        let bet_json = [];
        temp_arr.forEach(item => {
          bet_json.push({
            key: item.key,
            money: item.money
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
      this.resultList.forEach(result => {
        let temp_arr = result.bet_json.filter(bet => bet.checked);
        temp_arr.forEach(item => {
          all_bet += +item.money;
        });
      });
      return all_bet;
    },
    // 确认投注
    sureBet() {
      let bet_json = this.getBetJSON();
      let all_bet = this.getAllBet();
      console.log({ bet_json }, { all_bet });
      if (this.isPlateClose == 1) {
        this.$alert("已经封盘，请开盘后再投注", "无法投注", {
          confirmButtonText: "确定"
        });
        return;
      }
      if (isNaN(all_bet) || all_bet <= 0) {
        //说明没有输入任何金额，提示
        this.$alert("下注内容不对，请重新下注", "提示", {
          confirmButtonText: "确定"
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
        part: this.resultList[0].part,
        bet_json: newArray2,
        name: this.resultList[0].name
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
    ...mapActions([
      "refreshUserInfo",
    ])
  },
  watch: {
    // count: {
    //     handler(newVal, objVal){
    //         console.log(newVal)    //打印为33
    //     },
    // }
  },
  mounted() {
    // this.initTable();
    // this.getResult()
  }
};
</script>

<style scoped lang="less">
.special-code {
  width: 100%;
  margin: 0 auto;

  .special-box {
    .tab_nav_first {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      border-left: 1px solid #e8e8e8;

      li {
        .wh(78px, 30px);
        margin-right: 2px;
        background-color: #e8e8e8;
        text-align: center;
        line-height: 30px;
        border-radius: 4px 4px 0px 0px;
        .sc(14px, #4a4130);
      }

      .active {
        background-color: #d1913c;
        .sc(14px, #fff8ef);
      }
    }

    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

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
              text-align: center;
              .wh(68px, 100%);
              background: #ffffff;
            }
          }
        }
      }
    }
  }

  .tab_nav_first {
    margin-top: 30px;
    display: flex;
    justify-content: flex-start;
    align-items: center;

    li {
      .wh(78px, 30px);
      margin-right: 10px;
      background-color: #e8e8e8;
      text-align: center;
      line-height: 30px;
      border-radius: 4px 4px 0px 0px;
      .sc(14px, #4a4130);
    }

    .active {
      background-color: #d1913c;
      .sc(14px, #fff8ef);
    }
  }

  .bb {
    border-bottom: 1px solid #e8e8e8;
  }

  .mt10 {
    margin-top: 10px;
  }

  .bt {
    border-top: 2px solid #f5a623;
  }

  .flex-start {
    display: flex;
    align-items: center;
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
                background: url("~images/bg/red_circle.png") no-repeat;
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
  .chief-shaw-table {
    .wh(920px, auto);
    background: #ffefd4;
    border-top: 2px solid #f5a623;
    font-size: 12px;
    font-weight: 600;
    color: rgba(74, 65, 48, 1);
    display: flex;
    flex-wrap: wrap;

    .chief-shaw-header {
      .wh(230px, auto);

      .item {
        .wh(230px, auto);
        display: flex;
        align-items: center;
        border-bottom: 1px solid #e8e8e8;

        .item-left {
          .wh(79px, 32px);
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
      font-size: 12px;
      font-weight: 600;
      color: rgba(74, 65, 48, 1);
      display: flex;
      flex-wrap: wrap;

      .item {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
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
          background: #ffffff;
        }

        .item-right {
          .wh(83px, 100%);
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
      }
    }

    .ml10 {
      margin-left: 10px;
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
    height: 20px !important;
    line-height: 20px !important;
    padding: 0 5px !important;
    box-sizing: border-box !important;
  }
</style>

