<template>
  <div class="box">
    <div class="gameIndex">
      <!--    更改后的tab选项-->
      <!-- tab栏 -->
      <ul class="tab_nav">
        <li
          v-for="(item, index) in list"
          :key="index"
          :class="{active : currentIndex == index}"
          @click="changeIndex(index)"
        >{{item.title}}</li>
      </ul>
      <!--    28系列-->
      <div v-if="currentIndex === 0">
        <!-- 游戏类型 -->
        <div class="gameType">
          <div class="game_line" v-for="(item, index) in gamesType" :key="index">
            <div class="game_type">{{item.game_series_name}}</div>
            <div class="game_items">
              <div
                class="game_name"
                :class="index == fristIndex && secindex == secondIndex ? 'game_active':''"
                v-for="(game, secindex) in item.game_type_list"
                :key="secindex"
                @click="chooseItem(index, secindex)"
              >{{game.game_type_name}}</div>
            </div>
          </div>
        </div>
        <!-- 游戏相关二级页面 -->
        <div style="display: flex; padding-bottom: 20px;">
          <div class="gameTitle">
            <router-link
              tag="div"
              :to="{path: '/gameIndex/gameHome'}"
              active-class="acitve"
              class="title_item"
              replace
            >{{activeGame.game_type_name}}首页</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/gameRule'}"
              active-class="acitve"
              class="title_item"
              replace
            >游戏规则</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/bettingRecord'}"
              active-class="acitve"
              class="title_item"
              replace
            >投注记录</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/editMode'}"
              active-class="acitve"
              class="title_item"
              v-if="judgeEdit(activeGame.game_type_id)"
              replace
            >模式编辑</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/autoBet'}"
              active-class="acitve"
              class="title_item"
              v-if="judgeEdit(activeGame.game_type_id)"
              replace
            >自动投注</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/trendChart'}"
              active-class="acitve"
              class="title_item"
              replace
            >走势图</router-link>
            <router-link
              tag="div"
              :to="{path: '/gameIndex/profitStatistics'}"
              active-class="acitve"
              class="title_item"
              replace
            >盈利统计</router-link>
          </div>
        </div>
        <router-view ref="gameView" />
      </div>
      <!--    香港六合彩-->
      <div v-if="currentIndex === 1" style="display: flex" class="hk-game">
        <!--  还差正码特,规则(富文本)-->
        <div class="hk-game-header">
          <!--  开奖结果-->
          <div class="hk-games-tab">
            <!--      六合彩种类-->
            <div class="hk-game flex-center">香港六合彩</div>
            <div class="gameType">
              <div class="game_items">
                <div
                  class="game_name"
                  :class="secindex == currentTab ? 'game_active':''"
                  v-for="(game, secindex) in hkGamesType"
                  :key="secindex"
                  @click="chooseHkItem(game,secindex)"
                >{{game.game_type_name}}</div>
              </div>
            </div>
          </div>
          <div class="winResult">
            <div class="homeTop">
              <div class="tip">
                <p>第{{lastIssue}}期</p>
                <p>开奖结果</p>
              </div>
              <div class="result">
                <ul v-if="resultNumber.length">
                  <li v-for="(item,index) in resultNumber" :key="index">
                    <div
                      class="result-number"
                      :class="[item.bg === 1 ? 'bgr':item.bg === 2 ? 'bgb':'bgg']"
                    >{{item.number}}</div>
                    <p>{{item.name}}</p>
                  </li>
                </ul>
                <div class="none" v-else>-暂无数据-</div>
              </div>
            </div>
          </div>
          <!--    选择操作-->
          <div class="more-options">
            <div
              class="game_name"
              :class="index == firstIndex ? 'game_active':''"
              v-for="(item, index) in optionsList"
              :key="index"
              @click="chooseOption(index)"
            >{{item}}</div>
          </div>
          <div v-if="firstIndex!== 0">
            <div v-if="firstIndex=== 1" v-html="rich_text" class="rich-text"></div>
            <!--    会员-->
            <gameVip v-if="firstIndex=== 3" :list="vipList"></gameVip>
            <!--    历史-->
            <gameHistory v-if="firstIndex=== 2" :list="lastSevenList"></gameHistory>
            <!--    结果-->
            <hkGameResult v-if="firstIndex=== 4" :list="resultList"></hkGameResult>
          </div>
          <div style="display: flex;min-width:1349px;margin:0 auto" v-if="firstIndex=== 0">
            <!--    顶部六合彩信息-->
            <div style="width:15%">
              <left-nav :hkUserInfo="userInfo" :plate="plate"></left-nav>
            </div>
            <div class="table-info" style="width:70%;">
              <div style="margin: 0 auto;width:920px">
                <div class="game-title">
                  <div class="game-title-left">
                    <span>香港六合彩 - {{currentText}} &nbsp;&nbsp;当前彩种输赢：</span>
                    <div>0.0000</div>
                  </div>
                  <div class="game-title-right">
                    <div class="game-date">
                      <span>{{currentIssue}} 期 距离封盘：</span>
                      <!-- <div class="plate red-plate">未开盘</div> -->
                      <yd-countdown
                        :time="closeTime"
                        timetype="timestamp"
                        format="{%d}:{%h}:{%m}:{%s}"
                        doneText="已封盘"
                        :callback="handleClose"
                      ></yd-countdown>
                      <span>距离开奖：</span>
                      <!-- <div class="plate green-plate">未开盘</div> -->
                      <yd-countdown
                        :time="lotteryTime"
                        timetype="timestamp"
                        format="{%d}:{%h}:{%m}:{%s}"
                        :callback="handleLottery"
                      ></yd-countdown>
                    </div>
                    <div>
                      <el-dropdown>
                        <div class="area-plate">
                          {{plate}}
                          <i class="el-icon-arrow-down el-icon--right"></i>
                        </div>
                        <el-dropdown-menu slot="dropdown">
                          <el-dropdown-item @click.native="toCheck('A盘')">A盘</el-dropdown-item>
                          <el-dropdown-item @click.native="toCheck('B盘')">B盘</el-dropdown-item>
                          <el-dropdown-item @click.native="toCheck('C盘')">C盘</el-dropdown-item>
                          <el-dropdown-item @click.native="toCheck('D盘')">D盘</el-dropdown-item>
                        </el-dropdown-menu>
                      </el-dropdown>
                    </div>
                  </div>
                </div>
                <!--    特码-->
                <specialCode v-if="currentTab === 0" ref="specialCode" :id="currentId"></specialCode>
                <!--    特双面，两面-->
                <doubleSided v-if="currentTab === 1" ref="doubleSided" :id="currentId"></doubleSided>
                <!--    色波-->
                <colorWave v-if="currentTab === 2" ref="colorWave" :id="currentId"></colorWave>
                <!--    特肖-->
                <specialShaw v-if="currentTab === 3" ref="specialShaw" :id="currentId"></specialShaw>
                <!--  合肖  -->
                <addShaw v-if="currentTab === 4" ref="addShaw" :id="currentId"></addShaw>
                <!--  特码头尾数  -->
                <headAndTail v-if="currentTab === 5" ref="headAndTail" :id="currentId"></headAndTail>
                <!--    正码-->
                <positiveCode v-if="currentTab === 6" ref="positiveCode" :id="currentId"></positiveCode>
                <!--  正码特  -->
                <positiveCodeSpecial
                  v-if="currentTab === 7"
                  ref="positiveCodeSpecial"
                  :id="currentId"
                ></positiveCodeSpecial>
                <!--    正码1-6-->
                <oneToSix v-if="currentTab === 8" ref="oneToSix" :id="currentId"></oneToSix>
                <!--    五行-->
                <fiveElement v-if="currentTab === 9" ref="fiveElement" :id="currentId"></fiveElement>
                <!--    平特肖-->
                <PinterShaw v-if="currentTab === 10" ref="PinterShaw" :id="currentId"></PinterShaw>
                <!--    正肖-->
                <positiveShaw v-if="currentTab === 11" ref="positiveShaw" :id="currentId"></positiveShaw>
                <!--    七色波-->
                <sevenWave v-if="currentTab === 12" ref="sevenWave" :id="currentId" />
                <!--    总肖-->
                <chiefShaw v-if="currentTab === 13" ref="chiefShaw" :id="currentId"></chiefShaw>
                <!--    自选不中-->
                <selfChoose v-if="currentTab === 14" ref="selfChoose" :id="currentId"></selfChoose>
                <!--    连肖连尾-->
                <evenTail v-if="currentTab === 15" ref="evenTail" :id="currentId"></evenTail>
                <!--    连码-->
                <evenCode v-if="currentTab === 16" ref="evenCode" :id="currentId"></evenCode>
              </div>
            </div>
            <div style="width:15%">
              <right-nav></right-nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapMutations, mapGetters, mapActions } from "vuex";
import { judgeMixin } from "@/config/mixin.js";
import hkHeader from "@/components/games/hkGames/hkHeader";
import leftNav from "@/components/games/hkGames/leftNav";
import rightNav from "@/components/games/hkGames/rightNav";
import sevenWave from "@/components/games/hkGames/tableWave/sevenColorWave";
import doubleSided from "@/components/games/hkGames/tableWave/doubleSided";
import fiveElement from "@/components/games/hkGames/tableWave/fiveElements";
import evenTail from "@/components/games/hkGames/tableWave/evenTail";
import evenCode from "@/components/games/hkGames/tableWave/evenCode";
import addShaw from "@/components/games/hkGames/tableWave/addShaw";
import chiefShaw from "@/components/games/hkGames/tableWave/chiefShaw";
import PinterShaw from "@/components/games/hkGames/tableWave/PinterShaw";
import positiveCode from "@/components/games/hkGames/tableWave/positiveCode";
import selfChoose from "@/components/games/hkGames/tableWave/selfChoose";
import specialCode from "@/components/games/hkGames/tableWave/specialCode";
import specialShaw from "@/components/games/hkGames/tableWave/specialShaw";
import colorWave from "@/components/games/hkGames/tableWave/colorWave";
import positiveShaw from "@/components/games/hkGames/tableWave/positiveShaw";
import oneToSix from "@/components/games/hkGames/tableWave/oneToSix";
import headAndTail from "@/components/games/hkGames/tableWave/headAndTail";
import gameVip from "@/components/games/hkGames/gameOperation/gameVip";
import gameHistory from "@/components/games/hkGames/gameOperation/gameHistory";
import hkGameResult from "@/components/games/hkGames/gameOperation/hkGameResult";
import positiveCodeSpecial from "@/components/games/hkGames/tableWave/positiveCodeSpecial";
import countdown from "@/components/countdown/countdown";
import { PLATE_MAP, RED_WAVE, BLUE_WAVE, GREEN_WAVE } from "@/config/config";
import { getShengxiao } from "@/config/utils";
export default {
  name: "gameIndex",
  components: {
    hkHeader,
    leftNav,
    rightNav,
    sevenWave,
    doubleSided,
    fiveElement,
    evenTail,
    evenCode,
    addShaw,
    chiefShaw,
    PinterShaw,
    positiveCode,
    selfChoose,
    specialCode,
    specialShaw,
    colorWave,
    positiveShaw,
    oneToSix,
    headAndTail,
    gameVip,
    gameHistory,
    hkGameResult,
    positiveCodeSpecial,
    "yd-countdown": countdown
  },
  data() {
    return {
      vipList: [], //会员
      lastSevenList: [], //历史
      resultList: [], //结果，
      userInfo: {}, //六合彩用户信息
      rich_text: "", //游戏规则富文本
      plate: "A盘", //当前盘
      currentId: "", //当前游戏id
      optionsList: ["香港六合彩", "游戏规则", "历史", "会员", "结果"],
      hkGamesType: [],
      firstIndex: 0,
      secondIndex: 0,
      list: [{ title: "28系列" }, { title: "香港六合彩" }],
      currentIndex: 0, // 是28系列还是六合彩系列
      currentTab: 7, //当前游戏的下标
      fristIndex: 0,
      activeGame: {}, // 选中的游戏
      gamesType: [],
      currentText: "", //当前游戏名称
      lastIssue: "99999", // 上期期号
      resultNumber: [], // 上期开奖结果
      currentIssue: "200000", // 当前游戏期号
      closeTime: "", // 封盘时间
      lotteryTime: "" // 开奖时间
    };
  },
  mixins: [judgeMixin],
  methods: {
    //获取结果
    getLastSevenListResult() {
      this.$Api(
        {
          api_name: "kkl.user.getLastSevenListResult"
        },
        (err, data) => {
          if (!err) {
            let arr = [];
            data.data.forEach(item => {
              item.result = item.result.split(",");
              item.result.map(child => {
                arr.push({
                  title: child,
                  bg: ""
                });
                item.result = arr;
              });
            });
            this.resultList = data.data;
            this.resultList.map(item => {
              item.result.map(child => {
                if (RED_WAVE.indexOf(+child.title) !== -1) {
                  child.bg = 1;
                }
                if (BLUE_WAVE.indexOf(+child.title) !== -1) {
                  child.bg = 2;
                }
                if (GREEN_WAVE.indexOf(+child.title) !== -1) {
                  child.bg = 3;
                }
              });
            });
          }
        }
      );
    },
    //获取会员
    getVipList() {
      this.$Api(
        {
          api_name: "kkl.user.liuhecaiVip"
        },
        (err, data) => {
          if (!err) {
            this.vipList = data.data;
            console.log(data);
          }
        }
      );
    },
    //获取六合彩最近7天下注
    getLastSevenList() {
      this.$Api(
        {
          api_name: "kkl.user.getLastSevenList",
          game_type_id: this.id
        },
        (err, data) => {
          if (!err) {
            this.lastSevenList = data.data;
          }
        }
      );
    },
    //获取游戏规则
    getGameRule() {
      this.$Api(
        {
          api_name: "kkl.game.getGameRule",
          game_type_id: this.currentId
        },
        (err, data) => {
          if (!err) {
            this.rich_text = data.data.game_rule;
          }
        }
      );
    },
    //选择盘
    toCheck(name) {
      this.plate = name;
      let value = PLATE_MAP.get(name);
      this.setPlateVaule(value);
    },
    //切换游戏规则，结果之类的
    chooseOption(index) {
      this.firstIndex = index;
      switch (this.firstIndex + 1) {
        case 1:
          this.initHKGame();
          break;
        case 2:
          this.getGameRule();
          break;
        case 3:
          this.getLastSevenList();
          break;
        case 4:
          this.getVipList();
          break;
        case 5:
          this.getLastSevenListResult();
          break;
      }
    },
    // 改变索引
    changeIndex(index) {
      this.currentIndex = index;
      if (index === 0) {
        this.getGameTypes().then(res => {
          this.gamesType = res;
          if (this.indexGame) {
            this.fristIndex = this.gamesType.findIndex(
              item => item.game_series_id == this.indexGame.game_series_id
            );
            this.secondIndex = this.gamesType[
              this.fristIndex
            ].game_type_list.findIndex(
              item => item.game_type_id == this.indexGame.game_type_id
            );
            this.setIndexGame(null);
          }
          this.getGameTable();
        });
      } else {
        this.getHkGameType().then(res => {
          this.hkGamesType = res[0].game_type_list;
          this.currentTab = 0;
          this.currentId = res[0].game_type_list[0].game_type_id;
          this.activeGame = this.hkGamesType[this.currentTab];
          this.getHKLotteryInfo(this.currentId);
          this.getHkUserInfo();
        });
      }
    },
    chooseItem: function(currentText, secondIndex) {
      this.fristIndex = currentText;
      this.secondIndex = secondIndex;
      this.getGameTable();
    },
    //切换六合彩的游戏类型
    chooseHkItem: function(currentText, secondIndex) {
      this.currentText = currentText.game_type_name;
      this.currentId = currentText.game_type_id;
      this.currentTab = secondIndex;
      this.activeGame = this.hkGamesType[secondIndex];
      this.firstIndex = 0
    },
    //  获取游戏列表
    getGameTypes: function() {
      return new Promise((resolve, reject) => {
        this.$Api(
          {
            api_name: "kkl.game.getGameTypeList"
          },
          (err, data) => {
            if (!err) {
              resolve(data.data);
            }
          }
        );
      });
    },
    //获取香港六合彩的列表
    getHkGameType: function() {
      return new Promise((resolve, reject) => {
        this.$Api(
          {
            api_name: "kkl.game.getGameTypeListLIUHECAI"
          },
          (err, data) => {
            if (!err) {
              resolve(data.data);
            }
          }
        );
      });
    },
    // 获取六合彩最近开奖信息
    getHKLotteryInfo(id) {
      this.$Api(
        {
          api_name: "kkl.user.newLiuhecai",
          game_type_id: id
        },
        (err, res) => {
          if (!err) {
            const { issue, result, netx_isuse, netx_addtime, game_result_id } = res.data;
            let resultArr = [];
            this.lastIssue = issue;
            result.split(",").forEach(item => {
              let bg;
              if (RED_WAVE.indexOf(+item) !== -1) {
                bg = 1;
              }
              if (BLUE_WAVE.indexOf(+item) !== -1) {
                bg = 2;
              }
              if (GREEN_WAVE.indexOf(+item) !== -1) {
                bg = 3;
              }
              resultArr.push({
                number: item,
                bg,
                name: getShengxiao(+item)
              });
            });
            this.resultNumber = resultArr;
            this.currentIssue = netx_isuse;
            this.closeTime = +netx_addtime - 30;
            this.lotteryTime = +netx_addtime;
            this.setResultId(game_result_id)
          }
        }
      );
    },
    // 封盘后的回调
    handleClose() {
      this.setPlateClose(1);
    },
    //获取香港六合彩用户信息
    getHkUserInfo() {
      this.$Api(
        {
          api_name: "kkl.user.getLiuhecaiUser",
          game_type_id: this.currentId
        },
        (err, data) => {
          if (!err) {
            this.userInfo = data.data;
            console.log(this.userInfo);
          }
        }
      );
    },
    // 开奖倒计时结束的回调
    handleLottery() {
      console.log('开奖了')
      const tempIssue = this.currentIssue;
      let tryCount = 0;
      // 轮询获取最新开奖结果，直到下一期有数据
      clearInterval(this.waitTimer);
      this.waitTimer = setInterval(() => {
        if (tryCount < 60) {
          this.$Api(
            {
              api_name: "kkl.user.newLiuhecai",
              game_type_id: this.currentId
            },
            (err, res) => {
              tryCount++;
              if (!err) {
                const { issue, result, netx_isuse, netx_addtime, game_result_id } = res.data;
                // 已开奖，获取最新期
                if (issue === tempIssue && netx_addtime > +new Date() / 1000) {
                  this.getHkUserInfo();
                  console.log("六合彩最新期：", netx_isuse);
                  clearInterval(this.waitTimer);
                  this.setPlateClose(0);
                  // 重新获取游戏开奖结果
                  this.getAwardResult();
                  let resultArr = [];
                  this.lastIssue = issue;
                  result.split(",").forEach(item => {
                    let bg;
                    if (RED_WAVE.indexOf(+item) !== -1) {
                      bg = 1;
                    }
                    if (BLUE_WAVE.indexOf(+item) !== -1) {
                      bg = 2;
                    }
                    if (GREEN_WAVE.indexOf(+item) !== -1) {
                      bg = 3;
                    }
                    resultArr.push({
                      number: item,
                      bg,
                      name: getShengxiao(+item)
                    });
                  });
                  this.resultNumber = resultArr;
                  this.currentIssue = netx_isuse;
                  this.closeTime = netx_addtime - 30;
                  this.lotteryTime = netx_addtime;
                  this.setResultId(game_result_id)
                }
              }
            }
          );
        } else {
          setTimeout(() => {
            tryCount = 0;
          }, 30 * 60 * 1000);
        }
      }, 1000);
    },
    // 初始化六合彩游戏数据
    initHKGame() {
      const { currentTab } = this;
      switch (+currentTab) {
        case 0:
          this.$refs.specialCode.initTable();
          break;
        case 1:
          this.$refs.doubleSided.initTable();
          break;
        case 2:
          this.$refs.colorWave.initTable();
          break;
        case 3:
          this.$refs.specialShaw.initTable();
          break;
        case 4:
          this.$refs.addShaw.initTable();
          break;
        case 5:
          this.$refs.headAndTail.initTable();
          break;
        case 6:
          this.$refs.positiveCode.initTable();
          break;
        case 7:
          this.$refs.positiveCodeSpecial.initTable();
          break;
        case 8:
          this.$refs.oneToSix.initTable();
          break;
        case 9:
          this.$refs.fiveElement.initTable();
          break;
        case 10:
          this.$refs.PinterShaw.initTable();
          break;
        case 11:
          this.$refs.positiveShaw.initTable();
          break;
        case 12:
          this.$refs.sevenWave.initTable();
          break;
        case 13:
          this.$refs.chiefShaw.initTable();
          break;
        case 14:
          this.$refs.selfChoose.initTable();
          break;
        case 15:
          this.$refs.evenTail.initTable();
          break;
        case 16:
          this.$refs.evenCode.initTable();
          break;
        default:
          break;
      }
    },
    // 获取kk28选中游戏,并且跳转到游戏首页
    getGameTable: function() {
      // if(this.currentIndex === 0){
      this.activeGame = this.gamesType[this.fristIndex].game_type_list[
        this.secondIndex
      ];
      this.chooseGame(this.activeGame);
      this.$router.replace({
        path: "/gameIndex/gameHome"
      });
      this.$nextTick(() => {
        this.$refs.gameView.initTable();
      });
      if (this.$store.state.timerOther) {
        clearInterval(this.$store.state.timerOther);
      }
    },
    // 当选中游戏时，获取开奖结果,并存入vuex
    getAwardResult: function() {
      if (this.$refs.gameView) {
        this.$refs.gameView.resultLoading = true;
      }
      this.setAwardResult(null);
      this.$Api(
        {
          api_name: "kkl.game.nowResult",
          game_type_id: this.activeGame.game_type_id
        },
        (err, data) => {
          if (!err) {
            this.setAwardResult(data.data);
            if (this.currentIndex === 1) {
              // 初始化六合彩表格数据
              this.initHKGame();
            }
            this.$refs.gameView.resultLoading = false;
          }
        }
      );
    },
    ...mapMutations({
      chooseGame: "CHOOSE_GAME",
      setAwardResult: "SET_AWAED_RESULT",
      setIndexGame: "SET_INDEX_GAME",
      setPlateVaule: "SET_PLATE_VALUE",
      setPlateClose: "SET_PLATE_CLOSE",
      setResultId: "SET_RESULT_ID"
    }),
    ...mapActions(["endCountDown", "refreshUserInfo"])
  },
  computed: {
    ...mapGetters(["indexGame", "plateValue"])
  },
  watch: {
    activeGame: {
      handler(currentGame) {
        const { currentIndex } = this;
        this.chooseGame(currentGame);
        this.getAwardResult();
        if (currentIndex === 1) {
          this.getHKLotteryInfo(currentGame.game_type_id);
        }
      },
      immediate: true,
      deep: true
    }
  },
  created() {
    this.refreshUserInfo();
    const plate_names = [...PLATE_MAP.keys()];
    const plate_values = [...PLATE_MAP.values()];
    this.plate = plate_names[plate_values.indexOf(this.plateValue)];
    this.getGameTypes().then(res => {
      this.gamesType = res;
      if (this.indexGame) {
        this.fristIndex = this.gamesType.findIndex(
          item => item.game_series_id == this.indexGame.game_series_id
        );
        this.secondIndex = this.gamesType[
          this.fristIndex
        ].game_type_list.findIndex(
          item => item.game_type_id == this.indexGame.game_type_id
        );
        this.setIndexGame(null);
      }
      this.getGameTable();
    });
  },
  beforeDestroy() {
    clearInterval(this.waitTimer);
  }
};
</script>
<style scoped lang='less'>
.box {
  width: 100%;
  display: flex;
  justify-content: center;
  min-width: @main-width;
  min-height: @min-height;
  box-sizing: border-box;
  /*.gameIndex {*/
  /* */
  /*}*/

  .tab_nav {
    .wh(100%, auto);
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 55px;
    margin-top: 31px;

    li {
      .wh(104px, 42px);
      background-color: #d3d0c4;
      .sc(16px, #fff);
      text-align: center;
      line-height: 42px;
      cursor: pointer;

      &:first-child {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
      }

      &:last-child {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
      }
    }

    .active {
      background: -webkit-linear-gradient(
        #ffd194,
        #d1913c
      ); /* Safari 5.1 - 6.0 */
      background: -o-linear-gradient(#ffd194, #d1913c); /* Opera 11.1 - 12.0 */
      background: -moz-linear-gradient(#ffd194, #d1913c); /* Firefox 3.6 - 15 */
      background: linear-gradient(#ffd194, #d1913c); /* 标准的语法 */
    }
  }

  .hk-game {
    .hk-game-header {
      margin: 0 auto;

      .hk-games-tab {
        .wh(920px, auto);
        margin: 0 auto;
        position: relative;
        left: 50%;
        margin-left: -460px;
        display: flex;
        justify-content: space-between;

        .hk-game {
          width: 92px;
          height: 38px;
          background: linear-gradient(
            360deg,
            rgba(209, 145, 60, 1) 0%,
            rgba(255, 209, 148, 1) 100%
          );
          border-radius: 19px;
          font-size: 16px;
          margin-right: 16px;
          font-family: PingFangSC-Regular, PingFang SC;
          font-weight: 400;
          color: rgba(255, 248, 239, 1);
        }

        .gameType {
          margin: 0px auto;
          box-sizing: border-box;
          width: 811px;
          height: 91px;
          margin-bottom: 20px;
          width: 811px;
          height: 91px;
          background: #fff8ef;
          padding: 10px 0 16px 0;
          box-sizing: border-box;
          box-shadow: 0px 2px 16px 0px rgba(209, 145, 60, 1);
          border-radius: 8px;
          overflow: hidden;
          border: 1px solid rgba(209, 145, 60, 1);

          .game_items {
            display: flex;
            flex-wrap: wrap;
            font-size: 16px;
            color: #4a4130;
            line-height: 35px;
            flex: 1;

            .game_name {
              padding: 0 8px;
              position: relative;

              &:after {
                content: "";
                width: 1px;
                height: 12px;
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                background: #4a4130;
              }
            }

            .game_active {
              color: rgba(209, 145, 60, 1);
            }

            .game_name:last-child {
              &:after {
                content: "";
                width: 0px;
                height: 0px;
              }
            }
          }

          .game_items:nth-child(2n + 1) {
            background: #fff8ef;
          }
        }
      }

      .winResult {
        .wh(920px, 92px);
        margin: 0 auto;
        border: 1px solid rgba(209, 145, 60, 1);
        border-radius: 8px;
      }

      .homeTop {
        height: 92px;
        border-radius: 8px;
        display: flex;
        overflow: hidden;
        background: linear-gradient(360deg, #d1913c 0%, #ffd194 100%);

        .tip {
          background: url("~images/bg/bg_game@2x.png") no-repeat;
          background-size: 62%, 100%;
          background-position: 100%, 100%;
          width: 217px;
          padding: 20px;
          box-sizing: border-box;

          p {
            color: #fff;
          }

          p:nth-child(1) {
            font-size: 24px;
            margin-bottom: 3px;
          }

          p:nth-child(2) {
            font-size: 20px;
          }
        }

        .result {
          flex: 1;
          background: #ffffff;
          justify-content: center;
          display: flex;
          align-items: center;

          ul {
            display: flex;

            li {
              position: relative;
              width: 30px;
              text-align: center;
              margin-right: 15px;
              z-index: 1;

              &:last-child {
                margin-left: 20px;
              }

              &:last-child::before {
                content: "+";
                position: absolute;
                top: 12px;
                left: -24px;
                width: 9px;
                height: 20px;
                font-size: 14px;
                font-family: PingFangSC-Medium, PingFang SC;
                font-weight: 500;
                color: rgba(74, 65, 48, 1);
                line-height: 0px;
                z-index: 2;
              }

              .result-number {
                width: 100%;
                height: 30px;
                /*border:1px solid #1F70FF;*/
                text-align: center;
                line-height: 30px;
                font-size: 16px;
                background: url("~images/bg/red_circle.png") no-repeat;
                &.bgr {
                  background: url("~images/bg/red_circle.png") no-repeat;
                }
                &.bgb {
                  background: url("~images/bg/blue_circle.png") no-repeat;
                }
                &.bgg {
                  background: url("~images/bg/green_circle.png") no-repeat;
                }
              }

              p {
                font-size: 14px;
                font-family: PingFangSC-Medium, PingFang SC;
                font-weight: 500;
                color: rgba(74, 65, 48, 1);
                margin-top: 12px;
              }
            }
          }

          .none {
            font-size: 16px;
            font-weight: 500;
          }

          .add {
            margin-right: 15px;
            width: 9px;
            height: 20px;
            font-size: 14px;
            font-family: PingFangSC-Medium, PingFang SC;
            font-weight: 500;
            color: rgba(74, 65, 48, 1);
            line-height: 0px;
          }

          .last-number {
            width: 30px;
            text-align: center;
            margin-right: 15px;

            .result-number {
              background: url("~images/bg/red_circle.png") no-repeat;
              width: 100%;
              height: 30px;
              text-align: center;
              line-height: 30px;
            }

            p {
              font-size: 14px;
              font-family: PingFangSC-Medium, PingFang SC;
              font-weight: 500;
              color: rgba(74, 65, 48, 1);
              margin-top: 12px;
            }
          }
        }
      }

      .more-options {
        margin: 20px auto;
        width: 326px;
        height: 42px;
        background: rgba(255, 255, 255, 1);
        box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        display: flex;
        font-size: 16px;
        font-weight: 400;
        line-height: 22px;
        align-items: center;
        color: #4a4130;

        .game_name {
          padding: 0 8px;
          position: relative;

          &:after {
            content: "";
            width: 1px;
            height: 12px;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: #4a4130;
          }
        }

        .game_active {
          color: rgba(209, 145, 60, 1);
        }

        .game_name:last-child {
          &:after {
            content: "";
            width: 0px;
            height: 0px;
          }
        }
      }

      .game-title {
        width: 100%;
        margin: 0 auto;
        padding: 0 5px;
        box-sizing: border-box;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 40px;
        background: rgba(228, 170, 92, 1);
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
        line-height: 22px;

        .game-title-left {
          display: flex;
          align-items: center;

          div {
            text-align: center;
            width: 53px;
            height: 28px;
            background: rgba(255, 255, 255, 1);
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            color: rgba(224, 32, 32, 1);
            line-height: 28px;
          }
        }

        .game-title-right {
          display: flex;
          align-items: center;

          .game-date {
            display: flex;
            align-items: center;

            .plate {
              width: 53px;
              height: 28px;
              background: rgba(255, 255, 255, 1);
              border-radius: 4px;
              text-align: center;
              line-height: 28px;
              font-size: 16px;
              margin: 0 10px;
            }

            .red-plate {
              color: #e02020;
            }

            .green-plate {
              color: #57e020;
            }
          }

          .area-plate {
            width: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8px;
            box-sizing: border-box;
            height: 28px;
            background: linear-gradient(
              180deg,
              rgba(255, 255, 255, 1) 0%,
              rgba(226, 226, 226, 1) 100%
            );
            box-shadow: 0px -1px 0px 0px rgba(255, 255, 255, 1);
            border-radius: 4px;
            margin-left: 14px;
          }
        }
      }
    }
  }

  .gameType {
    margin: 0px auto;
    padding-top: 30px;
    box-sizing: border-box;
    width: @main-width;
    margin-bottom: 20px;

    .game_line {
      display: flex;
      min-height: 50px;
      box-sizing: border-box;

      .game_type {
        width: 92px;
        height: 38px;
        border-radius: 38px;
        background: linear-gradient(
          360deg,
          rgba(209, 145, 60, 1) 0%,
          rgba(255, 209, 148, 1) 100%
        );
        font-size: 16px;
        color: #fff;
        text-align: center;
        line-height: 38px;
        margin-right: 16px;
        position: relative;
        top: 6px;
      }

      .game_items {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        font-size: 16px;
        color: #4a4130;
        line-height: 35px;
        flex: 1;
        border-left: 1px solid #d1913c;
        border-right: 1px solid #d1913c;

        .game_name {
          padding: 0 8px;
          position: relative;

          &:after {
            content: "";
            width: 1px;
            height: 12px;
            position: absolute;
            right: 0;
            top: 50%;
            margin-top: -6px;
            background: #4a4130;
          }
        }

        .game_active {
          color: #fb3a3a;
        }

        .game_name:last-child {
          &:after {
            content: "";
            width: 0px;
            height: 0px;
          }
        }
      }

      .game_items:nth-child(2n + 1) {
        background: #fff8ef;
      }
    }

    .game_line:nth-child(2n + 1) .game_items {
      background: #fff8ef;
    }

    .game_line:first-child .game_items {
      border-top: 1px solid #d1913c;
      border-top-right-radius: 4px;
      border-top-left-radius: 4px;
    }

    .game_line:last-child .game_items {
      border-bottom: 1px solid #d1913c;
      border-bottom-right-radius: 4px;
      border-bottom-left-radius: 4px;
    }
  }

  .gameTitle {
    height: 42px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0px 2px 6px 0px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;

    .title_item {
      height: 16px;
      padding: 0 8px;
      font-size: 16px;
      color: #4a4130;
      line-height: 16px;
      position: relative;

      &:after {
        content: "";
        width: 1px;
        height: 12px;
        position: absolute;
        right: 0;
        top: 50%;
        margin-top: -6px;
        background: #4a4130;
      }
    }

    .title_item:last-child {
      &:after {
        content: "";
        width: 0px;
        height: 0px;
      }
    }

    .acitve {
      color: #d1913c;
    }
  }
}
</style>
<style lang='less'>
.rich-text {
  width: 920px;
  img {
    width: 100%;
  }
}
</style>
