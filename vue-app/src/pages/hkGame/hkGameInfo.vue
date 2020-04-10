<template>
  <div class="hk-game-box" >
    <div class="mask" style="background: rgba(0,0,0,0.5);z-index:99999;top:0" v-if="isBack">
      <div class="mask-box">
        <p>离开页面选择的号码将会被清空</p>
        <div class="btn-box">
          <div class="left-box" @click="toClose">我再想想</div>
          <div class="left-box no-border" @click="toClearStore">确定</div>

        </div>
      </div>
    </div>
    <div class="mask" @click="toHide" v-if="showSelected"></div>
    <!--    游戏类型下拉菜单-->
    <div class="selected-box" v-if="showSelected">
      <div class="title">{{current_text}}</div>
      <ul>
        <li
          class="selected-option"
          :class="[currentTab === index + 1  ? 'active':'' ]"
          v-for="(item,index) in list"
          @click="toChoose(item,index)"
          :key="index"
        >{{item.title}}</li>
      </ul>
      <div class="title">{{secondTitle}}</div>
      <ul>
        <li
          class="selected-option"
          :class="[secondTab === childIndex  ? 'active':'' ]"
          v-for="(childItem,childIndex) in childList"
          @click="toChooseSecond(childItem,childIndex)"
          :key="childIndex"
        >{{childItem}}</li>
      </ul>
    </div>
    <!--    盘口下拉-->
    <div class="selected-box" v-if="isShowPlate">
      <div class="title">{{palte}}</div>
      <ul>
        <li
          class="selected-option"
          :class="[currentPlate === index  ? 'active':'' ]"
          v-for="(item,index) in plateList"
          @click="toCheckPlate(index,item)"
          :key="index"
        >{{item}}</li>
      </ul>
    </div>
    <!--    顶部-->
    <div class="topBar flex">
      <div class="left-arrow flex-center">
        <img src="~images/icon/icon_backarrow@2x.png" alt @click="back()" />
      </div>
      <div style="display: flex">
        <div class="hk-title" @click="toShowSelect">
          <span>玩法</span>
          <div class="select-div">
            <div>{{current_text}}-{{secondTitle}}</div>
            <img src="~images/icon/icon_sanjiao@2x.png" alt />
          </div>
        </div>
        <div class="hk-title w62" @click="toShowPlate">
          <div class="plate-div">
            <div>{{plate}}</div>
            <img src="~images/icon/icon_sanjiao@2x.png" alt />
          </div>
        </div>
      </div>
      <div class="right-arrow" @click="toShowMore()">
        <img src="~images/icon/icon_gengduo@2x.png" alt class="more" />
        <ul v-if="showMore">
          <li v-for="(item,index) in moreList" @click.stop="toWhere(index)" :key="index">{{item}}</li>
        </ul>
      </div>
    </div>
    <div class="head">
      <div class="head-left">
        <p>{{lastIssue}}期</p>
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
      <div class="head-left">
        <p>距{{currentIssue}}期截止</p>
        <div class="count-down">
           <yd-countdown
              :time="lotteryTime"
              timetype="timestamp"
              format="{%d}:{%h}:{%m}:{%s}"
              :callback="handleLottery"
            ></yd-countdown>
        </div>
      </div>
    </div>
    <div class="result" @click="toShow" v-if="!isShow">
      <img src="~images/icon/icon_xia@2x.png" alt />展开
    </div>
    <!--    开奖结果-->
    <div class="result-box" v-if="isShow">
      <sevenResult></sevenResult>
    </div>
    <div class="result" @click="toShow" v-if="isShow">
      <img src="~images/icon/icon_shang@2x.png" alt />收起
    </div>
    <div class="content" @click="toHide">
      <div class="game-title">
        {{child_text}}
        <span>赔率</span>
        <span v-if="currentTab===15 ">{{currentItem[0].rate}}</span>
        <span v-if="currentTab === 16">{{totalRate}}</span>
        <template v-if="currentTab === 17 && resultList[0] && resultList[0].bet_json">
          <span v-if="secondTab === 0">中二{{resultList[0].bet_json[secondTab].rate.split(',')[0]}}中三{{resultList[0].bet_json[secondTab].rate.split(',')[1]}}</span>
          <span v-else-if="secondTab === 3">中特{{resultList[0].bet_json[secondTab].rate.split(',')[0]}}中二{{resultList[0].bet_json[secondTab].rate.split(',')[1]}}</span>
          <span v-else>{{resultList[0].bet_json[secondTab].rate}}</span>
        </template>
      </div>
      <!--      特码-->
      <ul v-if="currentTab === 1">
        <template v-if="resultList[secondTab] && resultList[secondTab].bet_json">
        <li
          v-for="(item, index) in resultList[secondTab].bet_json"
          :key="index"
          @click="toCheck(index)"
        >
          <div
            class="result-ball"
            :style="[{'background':item.checked ? item.bg :'none'},{'color':item.checked ? '#fff':item.bg}]"
          >{{item.name}}</div>
          {{item.rate}}
        </li>
        </template>
      </ul>
      <!--      两面-->
      <div class="specical-code" v-if="currentTab === 2">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div
          class="code-box"
          v-for="(item,index) in resultList[0].bet_json"
          :key="index"
          @click="toCheck(index)"
        >
          <div class="code flex-center" :class="{active: item.checked}">{{item.name}}</div>
          <span>{{item.rate}}</span>
        </div>
        </template>
      </div>
      <!--      色波-->
      <div class="color-wave" v-if="currentTab === 3">
        <!--        色波-->
        <template v-if="resultList[secondTab] && resultList[secondTab].bet_json">
        <template v-if="secondTab === 0">
          <div
            class="color-wave-item"
            v-for="(thirdItem,thirdIndex) in resultList[secondTab].bet_json"
            :key="thirdIndex"
            @click="toCheck(thirdIndex)"
          >
            <div class="color-wave-box" :class="{active: thirdItem.checked}">
              <div class="color-wave-box-top">{{thirdItem.name}}</div>
              <div class="color-wave-box-bottom">
                <span
                  v-for="(thirdChild,thirdChildIndex) in thirdItem.child"
                  :key="thirdChildIndex"
                >{{thirdChild < 10 ? '0' + thirdChild : thirdChild}}</span>
              </div>
            </div>
            <span>{{thirdItem.rate}}</span>
          </div>
        </template>
        <template v-else>
          <div
            class="code-box"
            v-for="(item,index) in resultList[secondTab].bet_json"
            :key="index"
            @click="toCheck(index)"
          >
            <div class="code flex-center" :class="{active: item.checked}">{{item.name}}</div>
            <span>{{item.rate}}</span>
          </div>
        </template>
        </template>
      </div>
      <!--      特肖-生肖-->
      <div class="zodiac-sign" v-if="currentTab === 4">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div
          class="color-wave-item"
          v-for="(thirdItem,thirdIndex) in resultList[0].bet_json"
          :key="thirdIndex"
          @click="toCheck(thirdIndex)"
        >
          <div class="color-wave-box" :class="{active: thirdItem.checked}">
            <div class="color-wave-box-top">{{thirdItem.name}}</div>
            <div class="color-wave-box-bottom">
              <span
                v-for="(thirdChild,thirdChildIndex) in thirdItem.child"
                :key="thirdChildIndex"
              >{{thirdChild}}</span>
            </div>
          </div>
          <span>{{thirdItem.rate}}</span>
        </div>
        </template>
      </div>
      <!--      合肖-生肖-->
      <div class="zodiac-sign" v-if="currentTab === 5">
        <template v-if="addShawList && addShawList.length">
        <div
          class="color-wave-item"
          v-for="(thirdItem,thirdIndex) in addShawList"
          :key="thirdIndex"
          @click="toCheckAddShaw(thirdItem)"
        >
          <div class="color-wave-box" :class="{active: thirdItem.checked}">
            <div class="color-wave-box-top">{{thirdItem.name}}</div>
            <div class="color-wave-box-bottom">
              <span
                v-for="(thirdChild,thirdChildIndex) in thirdItem.list"
                :key="thirdChildIndex"
              >{{thirdChild.number}}</span>
            </div>
          </div>
          <!-- <span>{{thirdItem.rate}}</span> -->
        </div>
        </template>
      </div>
      <!--      头尾数-->
      <div class="specical-code" v-if="currentTab === 6">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div
          class="code-box"
          v-for="(item,index) in resultList[0].bet_json"
          :key="index"
          @click="toCheck(index)"
        >
          <div class="code flex-center" :class="{active: item.checked}">{{item.name}}</div>
          <span>{{item.rate}}</span>
        </div>
        </template>
      </div>
      <!--      正码-->
      <ul v-if="currentTab === 7">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <li v-for="(item,index) in resultList[0].bet_json" :key="index">
          <div
            class="result-ball"
            :style="[{'background':item.checked ? item.bg :'none'},{'color':item.checked ? '#fff':item.bg}]"
            @click="toCheck(index)"
          >{{item.name}}</div>
          {{item.rate}}
        </li>
        </template>
      </ul>
      <!--      正码特-->
      <div v-if="currentTab === 8">
        <!--        正1-6特-->
        <template v-if="resultList[secondTab] && resultList[secondTab].bet_json">
        <ul
          v-if="secondTab === 0 || secondTab === 2 || secondTab === 4 || secondTab === 6 || secondTab === 8 ||secondTab === 10 "
        >
          <li v-for="(item,index) in resultList[secondTab].bet_json" :key="index">
            <div
              class="result-ball"
              :style="[{'background':item.checked ? item.bg :'none'},{'color':item.checked ? '#fff':item.bg}]"
              @click="toCheck(index)"
            >{{item.name}}</div>
            {{item.rate}}
          </li>
        </ul>
        <!--        正1-6大小-->
        <div class="specical-code" v-else>
          <div
            class="code-box"
            v-for="(item,index) in resultList[secondTab].bet_json"
            :key="index"
          >
            <div
              class="code flex-center"
              @click="toCheck(index)"
              :class="[item.checked?'active':'']"
            >{{item.name}}</div>
            <span>{{item.rate}}</span>
          </div>
        </div>
        </template>

      </div>
      <!--      正码1-6-->
      <div
        class="specical-code"
        v-if="currentTab === 9 && resultList[secondTab] && resultList[secondTab].bet_json"
      >
        <div
          class="code-box"
          v-for="(item,index) in resultList[secondTab].bet_json"
          :key="index"
          @click="toCheck(index)"
        >
          <div class="code flex-center" :class="[item.checked?'active':'']">{{item.name}}</div>
          <span>{{item.rate}}</span>
        </div>
      </div>
      <!--      五行-->
      <div
        class="color-wave five-element"
        v-if="currentTab === 10 && resultList && resultList[0] && resultList[0].bet_json"
      >
        <div
          class="color-wave-item"
          v-for="(thirdItem,thirdIndex) in resultList[0].bet_json"
          :key="thirdIndex"
        >
          <div
            class="color-wave-box"
            :class="[thirdItem.checked?'active':'']"
            @click="toCheck(thirdIndex)"
          >
            <div class="color-wave-box-top">{{thirdItem.name}}</div>
            <div class="color-wave-box-bottom">
              <span
                v-for="(thirdChild,thirdChildIndex) in thirdItem.list"
                :key="thirdChildIndex"
              >{{thirdChild.number}}</span>
            </div>
          </div>
          <span>{{thirdItem.rate}}</span>
        </div>
      </div>
      <!--      平特一肖尾数-->
      <div v-if="currentTab === 11">
        <template v-if="resultList[secondTab] && resultList[secondTab].bet_json">
        <div class="zodiac-sign" v-if="secondTab === 0">
          <div
            class="color-wave-item"
            v-for="(thirdItem,thirdIndex) in resultList[secondTab].bet_json"
            :key="thirdIndex"
            @click="toCheck(thirdIndex)"
          >
            <div class="color-wave-box" :class="{active: thirdItem.checked}">
              <div class="color-wave-box-top">{{thirdItem.name}}</div>
              <div class="color-wave-box-bottom">
                <span
                  v-for="(thirdChild,thirdChildIndex) in thirdItem.child"
                  :key="thirdChildIndex"
                >{{thirdChild}}</span>
              </div>
            </div>
            <span>{{thirdItem.rate}}</span>
          </div>
        </div>
        <div class="color-wave five-element" v-if="secondTab === 1">
          <div
            class="color-wave-item"
            v-for="(thirdItem,thirdIndex) in resultList[secondTab].bet_json"
            :key="thirdIndex"
            @click="toCheck(thirdIndex)"
          >
            <div class="color-wave-box" :class="{active: thirdItem.checked}">
              <div class="color-wave-box-top">{{thirdItem.name}}</div>
              <div class="color-wave-box-bottom">
                <span
                  v-for="(thirdChild,thirdChildIndex) in thirdItem.child"
                  :key="thirdChildIndex"
                >{{thirdChild}}</span>
              </div>
            </div>
            <span>{{thirdItem.rate}}</span>
          </div>
        </div>
        </template>
      </div>
      <!--      正肖-生肖-->
      <div class="zodiac-sign" v-if="currentTab === 12">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div
          class="color-wave-item"
          v-for="(thirdItem,thirdIndex) in resultList[0].bet_json"
          :key="thirdIndex"
        >
          <div
            class="color-wave-box"
            :class="[thirdItem.checked?'active':'']"
            @click="toCheck(thirdIndex)"
          >
            <div class="color-wave-box-top">{{thirdItem.name}}</div>
            <div class="color-wave-box-bottom">
              <span
                v-for="(thirdChild,thirdChildIndex) in thirdItem.list"
                :key="thirdChildIndex"
              >{{thirdChild.number}}</span>
            </div>
          </div>
          <span>{{thirdItem.rate}}</span>
        </div>
        </template>
      </div>
      <!--      七色波-->
      <div class="specical-code" v-if="currentTab === 13">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div class="code-box" v-for="(item,index) in resultList[0].bet_json" :key="index">
          <div
            class="code flex-center"
            :class="[item.checked?'active':'']"
            @click="toCheck(index)"
          >{{item.name}}</div>
          <span>{{item.rate}}</span>
        </div>
        </template>
      </div>
      <!--      总肖-->
      <div class="specical-code" v-if="currentTab === 14">
        <template v-if="resultList[0] && resultList[0].bet_json">
        <div class="code-box" v-for="(item,index) in resultList[0].bet_json" :key="index">
          <div
            class="code flex-center"
            :class="[item.checked?'active':'']"
            @click="toCheck(index)"
          >{{item.name}}</div>
          <span>{{item.rate}}</span>
        </div>
        </template>
      </div>
      <!--      自选不中-->
      <ul v-if="currentTab === 15">
        <li v-for="(item,index) in numberList_1" :key="index">
          <div
            class="result-ball"
            :style="[{'background':item.checked ? item.bg :'none'},{'color':item.checked ? '#fff':item.bg}]"
            @click="toSelfChoose(index,item)"
          >{{item.number}}</div>
        </li>
      </ul>
      <!--      连肖连尾-->
      <div v-if="currentTab === 16">
        <template v-if="resultList[secondTab] && resultList[secondTab].bet_json">
        <!--        二连肖，三连肖，四连肖，五连肖-->
        <div
          class="zodiac-sign"
          v-if="secondTab === 0 || secondTab === 1 ||secondTab === 2 ||secondTab === 3"
        >
          <div
            class="color-wave-item"
            v-for="(thirdItem,thirdIndex) in resultList[secondTab].bet_json"
            :key="thirdIndex"
          >
            <div
              class="color-wave-box"
              :class="[thirdItem.checked?'active':'']"
              @click="toEvenTail(thirdItem)"
            >
              <div class="color-wave-box-top">{{thirdItem.name}}</div>
              <div class="color-wave-box-bottom">
                <span
                  v-for="(thirdChild,thirdChildIndex) in thirdItem.list"
                  :key="thirdChildIndex"
                >{{thirdChild.number}}</span>
              </div>
            </div>
            <span>{{thirdItem.rate}}</span>
          </div>
        </div>
        <!--      二连尾，三连尾，四连尾，五连尾  -->
        <div class="color-wave five-element" v-else>
          <div
            class="color-wave-item"
            v-for="(thirdItem,thirdIndex) in resultList[secondTab].bet_json"
            :key="thirdIndex"
          >
            <div
              class="color-wave-box"
              :class="[thirdItem.checked?'active':'']"
              @click="toEvenTail(thirdItem)"
            >
              <div class="color-wave-box-top">{{thirdItem.name}}</div>
              <div class="color-wave-box-bottom">
                <span
                  v-for="(thirdChild,thirdChildIndex) in thirdItem.list"
                  :key="thirdChildIndex"
                >{{thirdChild.number}}</span>
              </div>
            </div>
            <span>{{thirdItem.rate}}</span>
          </div>
        </div>
        </template>
      </div>
      <!--      连码-->
      <ul v-if="currentTab === 17">
        <li v-for="(item, index) in numberList" :key="index" @click="toCheckEvenCode(index)">
          <div
            class="result-ball"
            :style="[{'background':item.checked ? item.bg :'none'},{'color':item.checked ? '#fff':item.bg}]"
          >{{item.name}}</div>
        </li>
      </ul>
      <div class="game-footer">
        <div class="game-footer-left">
          <button class="mr10" @click="toClear">清空</button>
          <p>已选</p>
          <span>{{total}}</span>
          个号 &nbsp;&nbsp;&nbsp;余额&nbsp;{{userInfo.left_money}}
          <span></span>
        </div>
        <button class="bg" @click="toBetList">下注</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapMutations, mapGetters } from "vuex";
import {
    PLATE_MAP,
  SHENGXIAO_ARRAY,
  RED_WAVE,
  BLUE_WAVE,
  GREEN_WAVE
} from "@/config/config";
import countdown from '@/components/countdown/countdown'
import sevenResult from '@/components/hkGame/selected'
import { setStore, getStore, getShengxiao, getBg, getCombination} from "@/config/utils";

export default {
  name: "hkGameInfo",
  components: {
    "yd-countdown": countdown,
      sevenResult
  },
  computed: {
    ...mapGetters(["gameChooseList", "plateValue", "gameResultId"])
  },
  watch: {
    $route() {
      this.resultList[this.secondTab].bet_json[index].checked = false;
    }
  },
  data() {
    return {
        isBack:false,//是否退出
        currentItem:[{
            key:'',
            name:'',
            rate:'--'
        }],//自选不中的赔率
      currentPlate: 0, //当前盘口下标
      plateList: ["A盘", "B盘", "C盘", "D盘"],
      isShowPlate: false, //选择盘口的下拉
      plate: "A盘", //盘口显示
      evenTailList: [
        {
          name: "鼠",
          otherName: "0尾",
          otherList: [
            { number: "10", bg: 2 },
            { number: "20", bg: 2 },
            { number: "30", bg: 1 },
            { number: "40", bg: 1 }
          ],
          numberList: [
            { number: "01", bg: 1 },
            { number: "13", bg: 1 },
            { number: "25", bg: 2 },
            { number: "27", bg: 2 },
            { number: "49", bg: 3 }
          ]
        },
        {
          name: "马",
          otherName: "5尾",
          otherList: [
            { number: "05", bg: 3 },
            { number: "15", bg: 2 },
            { number: "25", bg: 2 },
            { number: "35", bg: 1 },
            { number: "45", bg: 1 }
          ],
          numberList: [
            { number: "07", bg: 1 },
            { number: "19", bg: 1 },
            { number: "31", bg: 2 },
            { number: "43", bg: 3 }
          ]
        },
        {
          name: "牛",
          otherName: "1尾",
          otherList: [
            { number: "01", bg: 1 },
            { number: "11", bg: 3 },
            { number: "21", bg: 3 },
            { number: "31", bg: 2 },
            { number: "41", bg: 2 }
          ],
          numberList: [
            { number: "12", bg: 1 },
            { number: "24", bg: 1 },
            { number: "36", bg: 2 },
            { number: "48", bg: 2 }
          ]
        },
        {
          name: "羊",
          otherName: "6尾",
          otherList: [
            { number: "06", bg: 3 },
            { number: "16", bg: 3 },
            { number: "26", bg: 2 },
            { number: "36", bg: 2 },
            { number: "46", bg: 1 }
          ],
          numberList: [
            { number: "06", bg: 3 },
            { number: "18", bg: 1 },
            { number: "30", bg: 1 },
            { number: "42", bg: 2 }
          ]
        },
        {
          name: "虎",
          otherName: "2尾",
          otherList: [
            { number: "02", bg: 1 },
            { number: "12", bg: 1 },
            { number: "22", bg: 3 },
            { number: "32", bg: 3 },
            { number: "42", bg: 2 }
          ],
          numberList: [
            { number: "11", bg: 3 },
            { number: "23", bg: 1 },
            { number: "35", bg: 1 },
            { number: "47", bg: 2 }
          ]
        },
        {
          name: "猴",
          otherName: "7尾",
          otherList: [
            { number: "07", bg: 1 },
            { number: "17", bg: 3 },
            { number: "27", bg: 3 },
            { number: "37", bg: 2 },
            { number: "47", bg: 2 }
          ],
          numberList: [
            { number: "05", bg: 3 },
            { number: "17", bg: 3 },
            { number: "29", bg: 1 },
            { number: "41", bg: 2 }
          ]
        },
        {
          name: "兔",
          otherName: "3尾",
          otherList: [
            { number: "03", bg: 2 },
            { number: "13", bg: 1 },
            { number: "23", bg: 1 },
            { number: "33", bg: 3 },
            { number: "43", bg: 3 }
          ],
          numberList: [
            { number: "10", bg: 2 },
            { number: "22", bg: 3 },
            { number: "34", bg: 1 },
            { number: "46", bg: 1 }
          ]
        },
        {
          name: "鸡",
          otherName: "8尾",
          otherList: [
            { number: "08", bg: 1 },
            { number: "18", bg: 1 },
            { number: "28", bg: 3 },
            { number: "38", bg: 3 },
            { number: "48", bg: 2 }
          ],
          numberList: [
            { number: "04", bg: 2 },
            { number: "16", bg: 3 },
            { number: "28", bg: 3 },
            { number: "40", bg: 1 }
          ]
        },
        {
          name: "龙",
          otherName: "4尾",
          otherList: [
            { number: "04", bg: 2 },
            { number: "14", bg: 2 },
            { number: "24", bg: 1 },
            { number: "34", bg: 1 },
            { number: "44", bg: 3 }
          ],
          numberList: [
            { number: "09", bg: 2 },
            { number: "21", bg: 3 },
            { number: "33", bg: 3 },
            { number: "45", bg: 1 }
          ]
        },
        {
          name: "蛇",
          otherName: "9尾",
          otherList: [
            { number: "09", bg: 2 },
            { number: "19", bg: 1 },
            { number: "29", bg: 1 },
            { number: "39", bg: 3 },
            { number: "49", bg: 3 }
          ],
          numberList: [
            { number: "08", bg: 1 },
            { number: "20", bg: 2 },
            { number: "32", bg: 3 },
            { number: "44", bg: 3 }
          ]
        },
        {
          name: "狗",
          numberList: [
            { number: "03", bg: 2 },
            { number: "15", bg: 2 },
            { number: "27", bg: 3 },
            { number: "39", bg: 3 }
          ]
        },
        {
          name: "猪",
          numberList: [
            { number: "02", bg: 1 },
            { number: "14", bg: 2 },
            { number: "26", bg: 2 },
            { number: "38", bg: 3 }
          ]
        }
      ], //连肖连尾的数字JSON
      positiveShawList: [
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
      ], //正肖数字列表
      resultList: [], //获取赔率列表
      fiveList: [
        {
          name: "金",
          numberList: [
            { number: "06", bg: 3 },
            { number: "07", bg: 1 },
            { number: "20", bg: 2 },
            {
              number: "21",
              bg: 3
            },
            { number: "28", bg: 3 },
            { number: "29", bg: 1 },
            { number: "36", bg: 2 },
            { number: "37", bg: 2 }
          ]
        },
        {
          name: "木",
          numberList: [
            { number: "02", bg: 1 },
            { number: "03", bg: 2 },
            { number: "10", bg: 2 },
            {
              number: "11",
              bg: 3
            },
            { number: "18", bg: 1 },
            { number: "19", bg: 1 },
            { number: "32", bg: 3 },
            {
              number: "33",
              bg: 3
            },
            { number: "40", bg: 1 },
            { number: "41", bg: 2 },
            { number: "48", bg: 2 },
            { number: "49", bg: 3 }
          ]
        },
        {
          name: "水",
          numberList: [
            { number: "08", bg: 1 },
            { number: "09", bg: 2 },
            { number: "16", bg: 3 },
            {
              number: "17",
              bg: 3
            },
            { number: "24", bg: 1 },
            { number: "25", bg: 2 },
            { number: "38", bg: 3 },
            {
              number: "39",
              bg: 3
            },
            { number: "46", bg: 1 },
            { number: "47", bg: 2 }
          ]
        },
        {
          name: "火",
          numberList: [
            { number: "04", bg: 2 },
            { number: "05", bg: 3 },
            { number: "12", bg: 1 },
            {
              number: "13",
              bg: 1
            },
            { number: "26", bg: 2 },
            { number: "27", bg: 3 },
            { number: "34", bg: 1 },
            {
              number: "35",
              bg: 1
            },
            { number: "42", bg: 2 },
            { number: "43", bg: 3 }
          ]
        },
        {
          name: "土",
          numberList: [
            { number: "01", bg: 1 },
            { number: "14", bg: 2 },
            { number: "15", bg: 2 },
            {
              number: "22",
              bg: 3
            },
            { number: "23", bg: 1 },
            { number: "30", bg: 1 },
            { number: "31", bg: 2 },
            {
              number: "44",
              bg: 3
            },
            { number: "45", bg: 1 }
          ]
        }
      ], //五行的号码
      id: this.$route.query.game_type_id, //游戏类型id
      numberList: [
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
      numberList_1: [
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
      showMore: false,
      moreList: ["投注记录", "走势图", "开奖结果", "玩法说明"],
      secondTab: 0, //二级菜单下标
      currentTab: 0, //一级菜单下边
      secondTitle: "选码", //当前二级名称
      tailList: [
        { title: "0尾", child: ["10", "20", "30", "40"] },
        { title: "1尾", child: ["01", "11", "21", "31", "41"] },
        { title: "2尾", child: ["02", "12", "22", "32", "42"] },
        { title: "3尾", child: ["03", "13", "23", "33", "43"] },
        { title: "4尾", child: ["04", "14", "24", "34", "44"] },
        { title: "5尾", child: ["05", "15", "25", "35", "45"] },
        { title: "6尾", child: ["06", "16", "26", "36", "46"] },
        { title: "7尾", child: ["07", "17", "27", "37", "47"] },
        { title: "8尾", child: ["08", "18", "28", "38", "48"] },
        { title: "9尾", child: ["09", "19", "29", "39", "49"] }
      ],
      fiveElement: [
        {
          title: "金",
          child: ["06", "07", "20", "21", "28", "29", "36", "37"]
        },
        {
          title: "木",
          child: [
            "02",
            "03",
            "10",
            "11",
            "18",
            "19",
            "32",
            "33",
            "40",
            "41",
            "48",
            "49"
          ]
        },
        {
          title: "水",
          child: ["08", "09", "16", "17", "24", "25", "38", "39", "46", "47"]
        },
        {
          title: "火",
          child: ["04", "05", "12", "13", "26", "27", "34", "35", "42", "43"]
        },
        {
          title: "土",
          child: ["01", "14", "15", "22", "23", "30", "31", "44", "45"]
        }
      ],
      addShawList: [],
      zodiacSign: [
        { title: "鼠", child: ["01", "13", "25", "37", "49"] },
        {
          title: "牛",
          child: ["12", "24", "36", "48"]
        },
        { title: "虎", child: ["11", "23", "35", "47"] },
        { title: "兔", child: ["10", "22", "34", "46"] },
        {
          title: "龙",
          child: ["09", "21", "33", "45"]
        },
        { title: "蛇", child: ["08", "20", "44", "45"] },
        { title: "马", child: ["07", "19", "31", "43"] },
        {
          title: "羊",
          child: ["06", "18", "30", "42"]
        },
        { title: "猴", child: ["05", "17", "29", "41"] },
        { title: "鸡", child: ["04", "16", "28", "40"] },
        {
          title: "狗",
          child: ["03", "15", "27", "29"]
        },
        { title: "猪", child: ["02", "14", "26", "38"] }
      ],
      list: [
        { title: "特码", child: ["特码A", "特码B"], secondTitle: "选码" },
        { title: "两面", child: ["两面"], secondTitle: "两面" },
        {
          title: "色波",
          child: ["色波", "半波", "半半波"],
          secondTitle: "色波"
        },
        { title: "特肖", child: ["生肖"], secondTitle: "生肖" },
        { title: "合肖", child: ["合肖"], secondTitle: "合肖" },
        { title: "头尾数", child: ["头尾数"], secondTitle: "头尾数" },
        { title: "正码", child: ["选码"], secondTitle: "选码" },
        {
          title: "正码特",
          child: [
            "正一特",
            "正一大小",
            "正二特",
            "正二大小",
            "正三特",
            "正三大小",
            "正四特",
            "正四大小",
            "正五特",
            "正五大小",
            "正六特",
            "正六大小"
          ],
          secondTitle: "正一特"
        },
        {
          title: "正码1-6",
          child: ["正一码", "正二码", "正三码", "正四码", "正五码", "正六码"],
          secondTitle: "正一码"
        },
        { title: "五行", child: ["种类"], secondTitle: "种类" },
        { title: "平特一肖尾数", child: ["一肖", "尾数"], secondTitle: "一肖" },
        { title: "正肖", child: ["生肖"], secondTitle: "生肖" },
        { title: "七色波", child: ["种类"], secondTitle: "种类" },
        { title: "总肖", child: ["种类"], secondTitle: "种类" },
        { title: "自选不中", child: ["自选不中"], secondTitle: "自选不中" },
        {
          title: "连肖连尾",
          child: [
            "二连肖",
            "三连肖",
            "四连肖",
            "五连肖",
            "二连尾",
            "三连尾",
            "四连尾",
            "五连尾"
          ],
          secondTitle: "二连肖"
        },
        {
          title: "连码",
          child: ["中二", "三全中", "二全中", "中特", "特串", "四全中"],
          secondTitle: "中二"
        }
      ],
      showSelected: false,
      isShow: false,
      current_text: "",
      childList: [],
      total: 0, //一共选中多少个
      userInfo: {}, ///用户信息
      totalRate: 0, //总赔率
      child_text: "", //赔率旁边的文字显示
      lastIssue: "99999", // 上期期号
      resultNumber: [], // 上期开奖结果
      currentIssue: "200000", // 当前游戏期号
      closeTime: "", // 封盘时间
      lotteryTime: "" ,// 开奖时间
      checkList:[],//自选不中类型选中的数组
    };
  },

  methods: {

    ...mapMutations({
      setChooseList: "SET_CHOOSE_LIST",
      setPlateVaule: "SET_PLATE_VALUE",
      setPlateClose: "SET_PLATE_CLOSE",
      setResultId: "SET_RESULT_ID"
    }),
    toShowPlate() {
      this.isShowPlate = true;
    },
    toCheckPlate(index, item) {
      // 选择盘口
      this.plate = item;
      this.currentPlate = index;
      let value = PLATE_MAP.get(item);
      this.setPlateVaule(value);
      this.isShowPlate = false;
    },
      //连肖连尾
      toEvenTail(item){
        item.checked = !item.checked
          let checkList = this.resultList[this.secondTab].bet_json.filter(
              item => item.checked === true
          );
          this.total = checkList.length;
      },
      //自选不中
      toSelfChoose(index,item) {
          let checkList = []
          if (this.checkList.length < 12) {
              //选择少于12个号码
              item.checked = !item.checked
              // this.resultList[this.secondTab].bet_json[index].checked = !this.resultList[this.secondTab].bet_json[index].checked
              this.numberList_1.map(item => {
                  if (item.checked) {
                      checkList.push(item)
                  } else {
                  }
              })
              this.checkList = checkList
          }
          else if(this.checkList.length === 12){
              //已经选择了12个号，则取消已选择的
              if(item.checked){
                  // this.list[index].checked = !this.list[index].checked
                  item.checked = !item.checked
                  this.checkList.map((item_,childIndex)=>{
                      if(item_.key === item.key){
                          this.checkList.splice(childIndex, 1)
                      }else{
                          this.currentItem = this.resultList[this.secondTab].bet_json.filter(item=>item.key == this.checkList.length)
                      }
                  })
              }
          }
          if(this.checkList.length && this.checkList.length > 4){
              this.currentItem = this.resultList[this.secondTab].bet_json.filter(item=>item.key == this.checkList.length)
          }else{
              this.currentItem = [{
                  key:'',
                  name:'',
                  rate:'--'
              }]
          }

          this.total = this.checkList.length
      },
    //选择下注
    toCheck(index) {
      this.resultList[this.secondTab].bet_json[index].checked = !this
        .resultList[this.secondTab].bet_json[index].checked;
      let checkList = this.resultList[this.secondTab].bet_json.filter(
        item => item.checked === true
      );
      this.total = checkList.length;

    },
    // 合肖下注
    toCheckAddShaw(item) {
      item.checked = !item.checked;
      let tempArr = this.addShawList.filter(bet => bet.checked);
      if (tempArr.length > 11) {
        item.checked = false;
      }
      this.total = this.addShawList.filter(bet => bet.checked).length;
    },
    // 连码下注
    toCheckEvenCode(index) {
      const checkList = this.numberList.filter(item => item.checked);
      let limit = 6;
      if (this.secondTab === 5) {
        limit = 4;
      }
      if (checkList.length < limit) {
        this.numberList[index].checked = !this.numberList[index].checked;
      }
      this.total = this.numberList.filter(item => item.checked).length;
    },
    //获取全部游戏类型
    hkGameInfo() {
      this.$Api(
        {
          api_name: "kkl.game.getGameTypeListLIUHECAI",
          is_app: 1
        },
        (err, data) => {
          if (!err) {
            this.hkGameList = data.data;
            for (let i = 0; i < this.list.length; i++) {
              if (
                (this.list[i].title = this.hkGameList[0].game_type_list[
                  i
                ].game_type_name)
              ) {
                this.list[i].title = this.hkGameList[0].game_type_list[
                  i
                ].game_type_name;
                this.$set(
                  this.list[i],
                  "id",
                  this.hkGameList[0].game_type_list[i].game_type_id
                );
              }
            }
            this.current_text =
              this.$route.query.game_type_name ||
              this.hkGameList[0].game_type_list[0].game_type_name;
            this.currentTab = Number(this.$route.query.currentTab) || 0;
            this.secondTitle = this.list[this.currentTab - 1].secondTitle;
            this.child_text =
              this.list[this.currentTab - 1].child.length > 1
                ? this.list[this.currentTab - 1].child[0]
                : this.current_text;
            this.getHKLotteryInfo(this.id);
          }
        }
      );
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
            const {
              issue,
              result,
              netx_isuse,
              netx_addtime,
              game_result_id
            } = res.data;
            let resultArr = [];
            this.lastIssue = issue;
            if(result){
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
            }
            this.resultNumber = resultArr;
            this.currentIssue = netx_isuse;
            this.closeTime = +netx_addtime - 30;
            this.lotteryTime = +netx_addtime;
            this.setResultId(game_result_id);
            this.getResult();
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
          game_type_id: this.id
        },
        (err, data) => {
          if (!err) {
            this.userInfo = data.data;
          }
        }
      );
    },
    // 开奖倒计时结束的回调
    handleLottery() {
      console.log("开奖了");
      const tempIssue = this.currentIssue;
      let tryCount = 0;
      // 轮询获取最新开奖结果，直到下一期有数据
      clearInterval(this.waitTimer);
      this.waitTimer = setInterval(() => {
        if (tryCount < 60) {
          this.$Api(
            {
              api_name: "kkl.user.newLiuhecai",
              game_type_id: this.id
            },
            (err, res) => {
              tryCount++;
              if (!err) {
                const {
                  issue,
                  result,
                  netx_isuse,
                  netx_addtime,
                  game_result_id
                } = res.data;
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
                  this.setResultId(game_result_id);
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
    //获取结果后的共同操作
    toCommon(index, gameList) {
      let list =
        index === 10
          ? this.fiveList
          : index === 12
          ? this.positiveShawList
          : [];
      list.map((secItem, secIndex) => {
        gameList.forEach((item, index) => {
          item.bet_json.map(child => {
            if (secItem.name === child.name) {
              this.$set(child, "list", secItem.numberList);
            }
            this.$set(child, "money", 2);
            this.$set(child, "checked", false);
            this.$set(
              child,
              "current_text",
              `${this.current_text}-${this.secondTitle}`
            );
          });
        });
      });
      return gameList;
    },
    //获取赔率
    getResult() {
      this.$Api(
        {
          api_name: "kkl.game.getLastBetInfo",
          game_result_id: this.gameResultId,
          game_type_id: this.id,
          pan_type: this.plateValue
        },
        (err, data) => {
          if (!err) {
            const { new_bet_rate } = data.data;
            let jsonArr = JSON.parse(new_bet_rate);
            this.gameResult = data.data;
            this.resultList = jsonArr;
            let gameList = jsonArr;
            if (this.currentTab === 10 || this.currentTab === 12) {
              //需要带上自己组成的数字操作
              this.resultList = this.toCommon(this.currentTab, gameList);
            } else if (this.currentTab === 3) {
              this.resultList.forEach((item, index) => {
                item.bet_json.map((bitem, bindex) => {
                  if (index === 0) {
                    if (bindex === 0) {
                      this.$set(bitem, "child", RED_WAVE);
                    } else if (bindex === 1) {
                      this.$set(bitem, "child", BLUE_WAVE);
                    } else {
                      this.$set(bitem, "child", GREEN_WAVE);
                    }
                  }
                  this.$set(bitem, "money", 2);
                  this.$set(bitem, "checked", false);
                  this.$set(
                    bitem,
                    "current_text",
                    `${this.current_text}-${this.secondTitle}`
                  );
                });
              });
            } else if (this.currentTab === 4) {
              this.resultList.map(item => {
                item.bet_json.map((child, index) => {
                  for (let i = 0; i < this.zodiacSign.length; i++) {
                    if (child.name === this.zodiacSign[i].title) {
                      this.$set(child, "child", this.zodiacSign[i].child);
                    }
                  }
                  this.$set(child, "money", 2);
                  this.$set(child, "checked", false);
                  this.$set(
                    child,
                    "current_text",
                    `${this.current_text}-${this.secondTitle}`
                  );
                });
              });
            } else if (this.currentTab === 5) {
              this.resultList = jsonArr;
              let tempArr = [];
              for (let i = 0; i < SHENGXIAO_ARRAY.length; i++) {
                tempArr.push({
                  key: i + 1,
                  name: SHENGXIAO_ARRAY[i],
                  checked: false,
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
            } else if (
              this.currentTab === 1 ||
              this.currentTab === 7 ||
              this.currentTab === 8 ||
              this.currentTab === 15
            ) {
              //需要对号码进行红，蓝，绿展示的操作
              this.resultList.forEach((item, index) => {
                item.bet_json.map(child => {
                  if (
                    child.name == "1" ||
                    child.name == "2" ||
                    child.name == "7" ||
                    child.name == "8" ||
                    child.name == "12" ||
                    child.name == "13" ||
                    child.name == "18" ||
                    child.name == "19" ||
                    child.name == "23" ||
                    child.name == "24" ||
                    child.name == "29" ||
                    child.name == "30" ||
                    child.name == "34" ||
                    child.name == "35" ||
                    child.name == "40" ||
                    child.name == "45" ||
                    child.name == "46"
                  ) {
                    //红波球
                    this.$set(child, "bg", "#ff1e1e");
                  } else if (
                    child.name == "3" ||
                    child.name == "4" ||
                    child.name == "9" ||
                    child.name == "10" ||
                    child.name == "14" ||
                    child.name == "15" ||
                    child.name == "20" ||
                    child.name == "25" ||
                    child.name == "26" ||
                    child.name == "31" ||
                    child.name == "36" ||
                    child.name == "37" ||
                    child.name == "41" ||
                    child.name == "42" ||
                    child.name == "47" ||
                    child.name == "48"
                  ) {
                    //蓝波
                    this.$set(child, "bg", "#0091ff");
                  } else {
                    //绿波
                    this.$set(child, "bg", "#6dd400");
                  }
                  this.$set(child, "checked", false);
                  this.$set(child, "money", 2);
                  this.$set(
                    child,
                    "current_text",
                    `${this.current_text}-${this.secondTitle}`
                  );
                });
              });
            } else if (this.currentTab === 11) {
              this.resultList.forEach((item, index) => {
                item.bet_json.map((bitem, bindex) => {
                  if (index === 0) {
                    for (let i = 0; i < this.zodiacSign.length; i++) {
                      if (bitem.name === this.zodiacSign[i].title) {
                        this.$set(bitem, "child", this.zodiacSign[i].child);
                      }
                    }
                  } else {
                    for (let i = 0; i < this.tailList.length; i++) {
                      if (bitem.name === this.tailList[i].title) {
                        this.$set(bitem, "child", this.tailList[i].child);
                      }
                    }
                  }
                  this.$set(bitem, "money", 2);
                  this.$set(bitem, "checked", false);
                  this.$set(
                    bitem,
                    "current_text",
                    `${this.current_text}-${this.secondTitle}`
                  );
                });
              });
            } else if (this.currentTab === 16) {
              this.evenTailList.map((firstItem, firstIndex) => {
                this.resultList.forEach((item, index) => {
                  item.bet_json.map((child, childIndex) => {
                    if (
                      index === 0 ||
                      index === 1 ||
                      index === 2 ||
                      index === 3
                    ) {
                      if (firstItem.name === child.name) {
                        this.$set(child, "list", firstItem.numberList);
                      }
                    } else {
                      if (firstItem.otherName === child.name) {
                        this.$set(child, "list", firstItem.otherList);
                      }
                    }

                    this.$set(child, "checked", false);
                    this.$set(child, "money", 2);
                    this.$set(
                      child,
                      "current_text",
                      `${this.current_text}-${this.secondTitle}`
                    );
                  });
                });
              });

            } else if (this.currentTab === 17 ) {
              this.numberList.map((item, index) => {
                let key = index + 1;
                if (RED_WAVE.indexOf(key) !== -1) {
                  //红波球
                  this.$set(item, "bg", "#ff1e1e");
                }
                if (BLUE_WAVE.indexOf(key) !== -1) {
                  //蓝波
                  this.$set(item, "bg", "#0091ff");
                }
                if (GREEN_WAVE.indexOf(key) !== -1) {
                  //绿波
                  this.$set(item, "bg", "#6dd400");
                }
                this.$set(item, 'key', key);
                this.$set(item, "name", item.number);
                this.$set(item, "checked", false);
                this.$set(
                  item,
                  "current_text",
                  `${this.current_text}-${this.secondTitle}`
                );
              });
            } else {
              this.resultList.map(item => {
                item.bet_json.map((child, index) => {
                  this.$set(child, "money", 2);
                  this.$set(child, "checked", false);
                  this.$set(
                    child,
                    "current_text",
                    `${this.current_text}-${this.secondTitle}`
                  );
                });
              });
            }
          }
        }
      );
    },
    // 获取用户信息
    get_user_info() {
      this.$Api(
        {
          api_name: "kkl.user.getUserHomeInfo"
        },
        (err, data) => {
          let res = data.data;
          this.userInfo = res;
        }
      );
    },
    toWhere(index) {
      let index_ = Number(index) + 1;
      switch (index_) {
        case 1:
          this.$router.push({
            path: "/allOrder"
          });
          break;
        case 2:
          this.$router.push({
            path: "/hkResult"
          });
          break;
        case 3:
          this.$router.push({
            path: "/hkResult"
          });
          break;
        case 4:
          this.$router.push({
            path: "/hkGameRule",
            query: {
              id: this.id
            }
          });
          break;
        default:
          break;
      }
    },
    //清空
    toClear() {
      if (this.total > 0) {
        this.numberList.map(item => {
          item.checked = false;
        });
        this.resultList.map(item => {
          item.bet_json.map(child => {
            child.checked = false;
          });
        });
        this.total = 0;
      }
    },
    toShowMore() {
      this.showMore = !this.showMore;
    },
    //判断是否存在相同游戏
    isCheck(list, checkList) {
      let isTrue = false;
      for (let i = 0; i < list.length; i++) {
        if (list[i].name === this.resultList[this.secondTab].name) {
          isTrue = true;
          break;
        } else {
          isTrue = false;
        }
      }
      return isTrue;
    },
    // 辅助函数
    getStr(arr, key) {
      let str = '';
      let tempArr = []
      for(let i = 0; i < arr.length; i++) {
        tempArr.push(arr[i][key]);
      }
      str = tempArr.join(',');
      return str;
    },
    // 辅助函数： 获取选中号码的key值集合
    getCheckedNumArr() {
      const checkedArr = this.numberList.filter(item => item.checked);
      let resultArr = [];
      for(let i = 0, len = checkedArr.length; i < len; i++) {
        resultArr.push(checkedArr[i].number>>>0);
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
    toBetList() {
      let list = JSON.parse(getStore("chooseList")) || [];
      let obj = {};
      let evenList = []
      let checkList = this.resultList[this.secondTab].bet_json.filter(
        item => item.checked === true
      );

      if (!this.total) {
        this.$msg("下注内容不对，请重新下注", "cancel", "middle", 1500);
        //说明没有选择任何下注
        return;
      }

      if (+this.currentTab === 5) {
        // 合肖
        const chosedList = this.addShawList.filter(item => item.checked);
        if (chosedList.length < 2) {
          this.$msg("下注内容不对，请重新下注", "cancel", "middle", 1500);
          return;
        }
        let betJson = this.resultList[0].bet_json;
        let currentRate;
        let tempArr = [];
        for(let i = 0; i < betJson.length; i++) {
          if (+betJson[i].name === chosedList.length) {
            tempArr.push({
              checked: true,
              key: betJson[i].key,
              money: 2,
              value: this.getStr(chosedList, 'key'),
              name: this.getStr(chosedList, 'name'),
              rate: betJson[i].rate,
              current_text: '合肖-合肖'
            });
          }
        }

        obj = {
          pankou:this.plateValue,
          game_result_id:this.gameResultId,
          game_type_id:this.id,
          total_bet_money: 2,
          part: this.resultList[this.secondTab].part,
          name: this.resultList[this.secondTab].name,
          bet_json: tempArr
        };
      } else if (this.currentTab === 17) {
        // 连码
        const chosedList = this.numberList.filter(item => item.checked);
        let check_arr = this.getCheckedNumArr();
        let limit_num = this.getKeyToNum(this.resultList[0].bet_json[this.secondTab].key);

        if (check_arr.length < limit_num) {
          this.$msg("下注内容不对，请重新下注", "cancel", "middle", 1500);
          return;
        }

        let betJson = this.resultList[0].bet_json;
        let currentRate;
        let tempArr = [];
        const combinationStr = getCombination(check_arr, limit_num);
        tempArr.push({
          checked: true,
          key: betJson[this.secondTab].key,
          money: 2,
          value: combinationStr,
          name: this.getStr(chosedList, 'key'),
          rate: this.secondTab === 0 || this.secondTab === 3 ? betJson[this.secondTab].rate.split(',')[0] : betJson[this.secondTab].rate,
          current_text: `${this.current_text}-${this.secondTitle}`
        });

        obj = {
          pankou:this.plateValue,
          game_result_id:this.gameResultId,
          game_type_id:this.id,
          total_bet_money: 2 * combinationStr.split('-').length,
          part: this.resultList[this.secondTab].part,
          name: this.resultList[this.secondTab].name,
          bet_json: tempArr
        };
      } else if (this.currentTab === 15){
          if(this.total <= 4){
                  this.$msg("下注内容不对，请重新下注", "cancel", "middle", 1500);
                  return;
          }
          //自选不中
          let self_name = ''
              this.checkList.map((item,index)=>{
                  self_name = index !== 0 ? `${self_name},${item.number}`:`${item.number}`

          })

          checkList = this.currentItem
          checkList[0].name = self_name
          checkList[0].value = self_name
          obj = {
              pankou:this.plateValue,
              game_result_id:this.gameResultId,
              game_type_id:this.id,
              total_bet_money:Number(checkList.length * 2),
              part: this.resultList[this.secondTab].part,
              name: this.resultList[this.secondTab].name,
              bet_json: checkList
          };
          console.log(obj)
      } else if (this.currentTab === 16){
          //连肖连尾
          let evenList = []
          checkList.map((item,index)=>{
              evenList.push(item.key)

          })
          let currentIndex;
          if(this.secondTab === 0 || this.secondTab === 4){
              currentIndex = 2
          }else if(this.secondTab === 1 || this.secondTab === 5){
              currentIndex = 3
          }else if(this.secondTab === 2 || this.secondTab === 6){
              currentIndex = 4
          }
          else if(this.secondTab === 3 || this.secondTab === 7){
              currentIndex = 5
          }
          const combinationStr = getCombination(evenList, currentIndex);
          // 连肖连尾
          let choosedList = this.resultList[this.secondTab].bet_json.filter(item => item.checked);
          let betJson = this.resultList[this.secondTab].bet_json;
          let tempArr = [];
          for(let i = 0; i < choosedList.length; i++) {
              // if (+betJson[i].key === +choosedList[i].key) {
                  tempArr.push({
                      checked: true,
                      key: choosedList[i].key,
                      money: 2,
                      value: combinationStr,
                      name: this.getStr(choosedList, 'name'),
                      rate: choosedList[i].rate,
                      current_text: `连肖连尾-${this.secondTitle}`
                  });
              // }
          }
          obj = {
              pankou:this.plateValue,
              game_result_id:this.gameResultId,
              game_type_id:this.id,
              total_bet_money: 2,
              part: this.resultList[this.secondTab].part,
              name: this.resultList[this.secondTab].name,
              bet_json: tempArr,
          };
      } else {
        obj = {
          pankou:this.plateValue,
          game_result_id:this.gameResultId,
          game_type_id:this.id,
          total_bet_money:Number(checkList.length * 2),
          part: this.resultList[this.secondTab].part,
          name: this.resultList[this.secondTab].name,
          bet_json: checkList
        };
      }

      if (list && list.length) {
        //不是第一次xuanz
        let isExistSame = false;
        for(let i = 0, len = list.length; i < len; i++) {
          if (list[i].pankou === obj.pankou && list[i].game_type_id === obj.game_type_id) {
            if(+this.currentTab === 16){
                isExistSame = false;
            }else{
                list[i].bet_json = [...list[i].bet_json, ...obj.bet_json];
                isExistSame = true;
            }

          }
        }
        if (!isExistSame) {
          list.push(obj);
        }
      } else {
        //第一次选择
        list.push(obj);
      }
      setStore("chooseList", list);
      this.gameChooseList.map(item => {
        if (item.name === obj.name) {
          item.bet_json.push(checkList);
        } else {
          this.setChooseList(obj);
        }
      });
      this.$router.push({
        path: "/betList",
        query: {
          id: this.id,
          myTotalMoney: this.userInfo.left_money
        }
      });
    },
    toShow() {
      this.isShow = !this.isShow;
    },
    //显示下拉框
    toShowSelect() {
      this.showSelected = true;
      this.list.forEach((item_, index_) => {
        if (index_ === this.currentTab - 1) {
          this.childList = item_.child;
        }
      });
    },
    //一级选择
    toChoose(item, index) {
      this.totalRate = 0;
      this.current_text = item.title;
      this.child_text = item.child.length > 1 ? item.child[0] : item.title;
      this.currentTab = index + 1;
      this.list.forEach((item_, index_) => {
        if (index_ === index) {
          this.secondTitle = item_.secondTitle;
        }
      });
      this.id = item.id;
      this.getResult();
      this.showSelected = false;
    },
    //二级选择
    toChooseSecond(item, index) {
      this.totalRate = 0;
      this.secondTab = index;
      this.secondTitle = item;
      this.showSelected = false;
      this.child_text =
        this.list[this.currentTab - 1].child.length > 1
          ? item
          : this.current_text;
      this.getResult();
    },
    //隐藏下拉
    toHide() {
      this.showSelected = false;
    },
    //顶部返回
    back() {
      if (this.$route.meta.appBack) {

      } else {
          if(getStore("chooseList")){
              this.isBack = true
          }else{
              this.$router.go(-1);
          }
      }
    },
      toClose(){
          this.isBack = false
      },
    toClearStore(){
        setStore("chooseList", []);
        this.$router.go(-1);

    },
  },
  created() {
    this.hkGameInfo();
  },
  mounted() {
    this.get_user_info();
    // this.hkGameInfo();
    this.numberList_1.forEach((item, index) => {
      this.$set(item, "rate", index);
      this.$set(item, "checked", false);
      this.$set(item, "bg", "");
      if (
        index === 0 ||
        index === 1 ||
        index === 6 ||
        index === 7 ||
        index === 11 ||
        index === 12 ||
        index === 17 ||
        index === 18 ||
        index === 22 ||
        index === 23 ||
        index === 28 ||
        index === 29 ||
        index === 33 ||
        index === 34 ||
        index === 39 ||
        index === 44 ||
        index === 45
      ) {
        item.bg = "#ff1e1e";
      } else if (
        index === 2 ||
        index === 3 ||
        index === 8 ||
        index === 9 ||
        index === 13 ||
        index === 14 ||
        index === 19 ||
        index === 24 ||
        index === 25 ||
        index === 30 ||
        index === 35 ||
        index === 36 ||
        index === 40 ||
        index === 41 ||
        index === 46 ||
        index === 47
      ) {
        item.bg = "#0091ff";
      } else {
        item.bg = "#6dd400";
      }
    });
  },
  beforeRouteEnter: (to, from, next) => {
    if (to.meta.title) {
      to.meta.title = to.query.game_type_name;
    }
    next();
  },
  beforeRouteLeave(to, from, next) {
    if (to.name !== "gameIndex") {
    }
    next();
  }
};
</script>

<style scoped lang="less">
.hk-game-box {
  width: 100%;
  height:100%;
  .mask{
    width: 100%;
    height:100%;
    background: rgba(0,0,0,0);
    position: fixed;
    z-index:1;
    .mask-box{
      text-align: center;
      font-size: 18px;
      width: 80%;
      height:150px;
      color:#333333;
      background: #ffffff;
      border-radius: 10px;
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%,-50%);
      padding-top: 40px;
      box-sizing: border-box;
      p{
        font-size: 18px;
      }
      .btn-box{
        margin-top: 25px;
        width: 100%;
        height: 60px;
        display: flex;
        border-top: 1px solid #cccccc;
        .left-box{
          flex: 1;
          text-align: center;
          line-height: 60px;
          border-right: 1px solid #cccccc;
        }
        .no-border{
          border: none;
        }
      }
    }
  }
  .topBar {
    height: 44px;
    background: rgba(74, 65, 48, 1);
    justify-content: space-between;
    font-size: 17px;
    color: #fed093;
    position: fixed;
    top: 0;
    width: 100%;
    padding: 0 12px;
    box-sizing: border-box;
    z-index: 999;

    .right-arrow {
      width: 24px;
      height: 44px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      position: relative;

      .more {
        .wh(4px, 15px);
      }

      ul {
        position: absolute;
        top: 44px;
        right: -11px;
        padding: 0 12px;
        width: 100px;
        background: #fff;
        z-index: 9999;
        box-sizing: border-box;
        box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, 0.1);

        li {
          .wh(100%, 37px);
          font-size: 14px;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          line-height: 37px;
          text-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
          border-bottom: 1px solid #e8e8e8;
        }

        li:last-child {
          border-bottom: none;
        }
      }
    }

    .left-arrow {
      width: 24px;
      height: 44px;

      img {
        height: 24px;
        width: auto;
      }
    }

    .hk-title {
      display: flex;
      width: 150px;
      align-items: center;
      height: 28px;
      font-size: 12px;
      font-weight: 400;
      color: rgba(254, 208, 147, 1);
      line-height: 14px;
      .plate-div {
        margin-left: 12px;
        width: 62px;
        height: 32px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 1);
        display: flex;
        align-items: center;
        padding: 0 5px;
        box-sizing: border-box;
        font-size: 14px;
        font-weight: 400;
        color: rgba(254, 208, 147, 1);
        justify-content: space-between;
        img {
          .wh(9px, 6px);
        }
      }
      .select-div {
        width: 130px;
        height: 32px;
        border-radius: 4px;
        border: 1px solid rgba(255, 255, 255, 1);
        display: flex;
        align-items: center;
        padding: 0 5px;
        box-sizing: border-box;
        font-size: 14px;
        font-weight: 400;
        color: rgba(254, 208, 147, 1);
        justify-content: space-between;

        div {
          .wh(100px, 100%);
          text-align: center;
          line-height: 32px;
          white-space: nowrap;
          overflow: hidden;
        }

        img {
          .wh(9px, 6px);
        }
      }
    }
    .w62 {
      width: 62px;
    }
  }

  .selected-box {
    .wh(100%, auto);
    background: #cccccc;
    position: fixed;
    top: 44px;
    width: 100%;
    padding: 12px;
    box-sizing: border-box;
    z-index: 999;
    left: 0;

    .title {
      text-align: center;
      font-size: 17px;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
    }

    ul {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;

      .selected-option {
        margin-top: 10px;

        width: 104px;
        height: 36px;
        background: rgba(255, 255, 255, 1);
        border-radius: 4px;
        border: 1px solid rgba(151, 151, 151, 1);
        font-size: 14px;
        font-weight: 400;
        color: rgba(51, 51, 51, 1);
        text-align: center;
        line-height: 36px;
      }

      .active {
        background: rgba(74, 65, 48, 1);
        border-radius: 4px;
        border: 1px solid rgba(151, 151, 151, 1);
        font-size: 14px;
        font-weight: 400;
        color: rgba(255, 255, 255, 1);
      }
    }
  }

  .mr10 {
    margin-right: 10px;
  }

  .bg {
    background: rgba(255, 133, 30, 1) !important;
  }

  .flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .head {
    width: 100%;
    height: 79px;
    background: rgba(255, 255, 255, 1);
    display: flex;
    justify-content: space-between;

    .head-left {
      text-align: center;
      padding: 9px 0;
      box-sizing: border-box;
      flex: 1;

      p {
        font-size: 14px;
        font-weight: 400;
        color: rgba(51, 51, 51, 1);
        margin-bottom: 10px;
      }

      ul {
        display: flex;
        justify-content: space-between;
        width: 100%;
        li {
          text-align: center;
          flex: 1;
          font-size: 14px;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);

          .result-number {
            &.bgr {
              color: #ff1e1e;
            }

            &.bgb {
              color: #0091ff;
            }

            &.bgg {
              color: #6dd400;
            }
          }

          p {
            font-size: 17px;
            font-weight: 500;
            color: rgba(255, 30, 30, 1);
            margin-bottom: 0;
          }
        }
      }

      .count-down {
        display: flex;
        justify-content: flex-end;
        padding-right: 14px;
        box-sizing: border-box;

        .count-number {
          text-align: center;
          line-height: 28px;
          font-size: 24px;
          font-weight: bold;
          color: rgba(255, 30, 30, 1);
          width: 20px;
          height: 28px;
          background: rgba(255, 255, 255, 1);
          box-shadow: 0px 1px 3px 0px rgba(0, 0, 0, 0.1);
          border-radius: 4px;
          border: 1px solid rgba(237, 237, 237, 1);
        }
      }

      .count-dot {
        width: 10px;
        height: 100%;
        line-height: 28px;
        color: rgba(218, 218, 218, 1);
      }
    }
  }

  .result {
    display: flex;
    justify-content: center;
    align-items: center;
    /*height: 25px;*/
    width: 100%;
    font-size: 12px;
    font-weight: 400;
    color: rgba(102, 102, 102, 1);

    img {
      width: 12px;
      height: 12px;
      margin-right: 5px;
    }
  }

  .result-box {
    .wh(100%, auto);
    overflow-x: auto;
    .table-title {
      width: 100%;
      height: 30px;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0px 1px 0px 0px rgba(242, 242, 242, 1);
      display: flex;
      align-items: center;
      font-size: 12px;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
      justify-content: space-between;
    }

    .table-content {
      .wh(100%, auto);
      font-size: 10px;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);

      li {
        margin-top: 5px;
        display: flex;
        justify-content: space-between;

        .number-ball {
          width: 130px;
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        div {
          text-align: center;
        }

        .common-div {
          width: 24px;
        }

        .ball {
          .wh(17px, 17px);
          border-radius: 50%;
          line-height: 17px;
          text-align: center;
          font-size: 10px;
          font-weight: 400;
          color: rgba(255, 255, 255, 1);
        }
      }
    }
  }

  .content {
    .wh(100%, auto);
    padding-bottom: 52px;

    .game-title {
      .wh(100%, 55px);
      font-size: 17px;
      font-weight: 500;
      color: rgba(255, 30, 30, 1);
      display: flex;
      align-items: center;
      justify-content: center;

      span {
        font-size: 14px;
        font-weight: 400;
        color: rgba(153, 153, 153, 1);
        display: inline-block;
        margin-left: 30px;
      }
    }

    ul {
      padding: 0 15px;
      box-sizing: border-box;
      display: flex;
      flex-wrap: wrap;

      li {
        width: 69px;
        height: 76px;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 15px;
        font-weight: 400;
        color: rgba(102, 102, 102, 1);

        .result-ball {
          width: 52px;
          height: 52px;
          background: rgba(255, 255, 255, 1);
          border: 1px solid rgba(204, 204, 204, 1);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
        }
      }

      /*li:nth-child(3n + 1 ) {*/
      /*  .result-ball {*/
      /*    font-size: 24px;*/
      /*    font-weight: 500;*/
      /*    color: rgba(255, 30, 30, 1);*/
      /*  }*/
      /*}*/

      /*li:nth-child(3n + 2) {*/
      /*  .result-ball {*/
      /*    font-size: 24px;*/
      /*    font-weight: 500;*/
      /*    color: #0091FF;*/
      /*  }*/
      /*}*/

      /*li:nth-child(3n + 3) {*/
      /*  .result-ball {*/
      /*    font-size: 24px;*/
      /*    font-weight: 500;*/
      /*    color: #6DD400;*/
      /*  }*/
      /*}*/
    }

    .specical-code,
    .color-wave {
      padding-left: 15px;
      box-sizing: border-box;
      display: flex;
      flex-wrap: wrap;

      .code-box {
        width: 108px;
        margin-bottom: 10px;
        margin-right: 10px;
        font-size: 14px;
        font-weight: 400;
        color: rgba(102, 102, 102, 1);
        text-align: center;

        .code {
          font-size: 24px;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          line-height: 32px;
          width: 108px;
          height: 52px;
          background: rgba(255, 255, 255, 1);
          border-radius: 4px;
          border: 1px solid rgba(204, 204, 204, 1);
        }

        .active {
          border: 1px solid #ff851e;
          color: #ff851e;
        }
      }

      .code-box:nth-child(3n) {
        margin-right: 0;
      }
    }

    .color-wave,
    .zodiac-sign {
      display: flex;
      padding: 0 15px;
      box-sizing: border-box;
      justify-content: space-between;

      .color-wave-item {
        width: 32%;
        height: auto;
        box-sizing: border-box;
        font-size: 14px;
        font-weight: 400;
        color: rgba(51, 51, 51, 1);
        text-align: center;

        .color-wave-box {
          width: 100%;
          border-radius: 8px;
          height: 150px;
          border: 1px solid #e8e8e8;
          padding: 8px;
          box-sizing: border-box;
          font-size: 14px;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          line-height: 14px;
          text-align: center;
          margin-bottom: 5px;

          &.active {
            border: 1px solid #ff851e;
            color: #ff851e;
          }

          .color-wave-box-top {
            font-size: 32px;
            font-weight: 400;
            margin-bottom: 20px;
            margin-top: 20px;
          }

          .color-wave-box-bottom {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;

            span {
              display: inline-block;
              margin-right: 5px;
            }

            span:nth-child(4n) {
              margin-right: 0;
            }
          }
        }
        .active {
          border: 1px solid #ff851e;
          color: #ff851e;
        }
      }
    }

    .zodiac-sign {
      flex-wrap: wrap;

      .color-wave-item {
        .color-wave-box {
          height: 100px;
        }
      }
    }

    .five-element {
      justify-content: flex-start;

      .color-wave-item {
        margin-right: 5px;

        .color-wave-box {
          height: 120px;
        }
      }

      .color-wave-item:nth-child(3n) {
        margin-right: 0;
      }
    }
  }

  .game-footer {
    .wh(100%, 52px);
    position: fixed;
    bottom: 0;
    left: 0;
    z-index: 1;
    background: rgba(254, 208, 147, 1);
    display: flex;
    align-items: center;
    padding: 0 8px;
    box-sizing: border-box;
    justify-content: space-between;

    button {
      width: 68px;
      height: 36px;
      background: rgba(255, 180, 80, 1);
      border-radius: 4px;
      font-size: 18px;
      font-weight: 500;
      color: rgba(255, 255, 255, 1);
      line-height: 36px;
      text-align: center;
      border: none;
    }

    .game-footer-left {
      display: flex;
      align-items: center;
      font-size: 14px;
      font-weight: 400;
      color: rgba(74, 65, 48, 1);

      span {
        font-size: 14px;
        font-weight: 500;
        color: rgba(255, 30, 30, 1);
      }
    }
  }
}
</style>
