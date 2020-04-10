import {
  mapGetters,
  mapMutations,
  mapActions
} from 'vuex'
import {
  DEFAULT_GAME_BET,
  ALERT_TIME,
  NO_SKIP_ROUTE,
  getMaxCount
} from '@/config/config.js'

import {
  getServerTime,
  calcGamePlayResult
} from "@/config/utils"

export const judgeMixin = {
  methods: {
    // 不能编辑投注类型的游戏类型
    judgeEdit: function (game_type_id) {
      const NO_EDIT_ARR = [3, 4, 5, 6, 8, 15, 25, 26, 27, 28, 29, 30, 35, 36, 37, 40, 43, 46, 48, 50, 51, 57, 58, 59, 60, 61]
      if (NO_EDIT_ARR.indexOf(parseInt(game_type_id)) == -1) {
        return true
      } else {
        return false
      }
    }
  }
}

export const defalutImg = {
  computed: {
    getUserGrad() {
      if (this.userInfo.level_name) {
        var level_name = this.userInfo.level_name.toLowerCase()
        return require(`images/icon/icon_${level_name}@2x.png`)
      } else {
        return require(`images/icon/icon_lv0@2x.png`)
      }
    }
  },
  methods: {
    errorImg(e) {
      e.target.src = ''
    },
    getBallIcon(color, num) {
      return require(`images/nums/icon_${color}${num}@2x.png`)
    },
    getPokerImg(resultStr) {
      if (!resultStr) return
      let result
      if (typeof resultStr == 'string') {
        result = JSON.parse(resultStr)
      } else {
        result = resultStr
      }

      let pokerNum = this.getPokerNum(result.num)
      let pokerColor = this.getPokerColor(result.type)
      if (!pokerNum || !pokerColor) return
      return require(`images/poker/icon_poker${pokerNum}_${pokerColor}@2x.png`)
    },
    // 获取扑克的大小
    getPokerNum(num) {
      let result;
      switch (num) {
        case 1:
          result = 'a';
          break;
        case 11:
          result = 'j';
          break;
        case 12:
          result = 'q';
          break;
        case 13:
          result = 'k';
          break;
        default:
          result = num;
      }
      return result
    },
    // 获取扑克花色
    getPokerColor(type) {
      let result;
      switch (type) {
        case 1:
          result = 'heitao';
          break;
        case 2:
          result = 'hongtao';
          break;
        case 3:
          result = 'meihua';
          break;
        case 4:
          result = 'fangpian';
          break;
      }
      return result
    },
    // 获取字符串中第 index 个元素
    getStrNum(str, index) {
      let arr = str.split(',')
      return arr[index]
    },
    // 判断字符串中是否有某个数字
    judgeHaveNum(str, num) {
      return str.indexOf(num) == -1 ? false : true
    }
  }
}

export const filterNum = {
  methods: {
    // 筛选游戏数字类型三段不同颜色数字
    filterResult: function (numStr, startIndex, endIndex) {
      let numArr = numStr.split(',')
      return numArr.slice(startIndex, endIndex).join(',')
    },
    // 数字颜色三个循环一次
    threeLoop: function (numStr) {
      return numStr.split(',')
    },
    // 根据数字返回对应数字图片
    getResultNum: function (num) {
      if (!parseInt(num) && num != 0) return
      return require(`images/nums/icon_number${num}@2x.png`)
    },
    // 根据数字字符串返回对应的数字图片
    getNumImage: function (numStr) {
      if (!parseInt(numStr) && parseInt(numStr) != 0) return
      let num = parseInt(numStr)
      return require(`images/nums/icon_fangkuai${num}@2x.png`)
    },
    // 游戏结果为一个数字的游戏类型
    oneResultGame: function (game_type_id) {
      let game_type_id_arr = [1, 3, 5, 9, 10, 11, 12, 13, 15, 21, 22, 23, 26, 27, 31, 32, 35, 38, 41, 42, 44, 45, 47, 49, 58, 59, 60, 61]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为一串字符串的游戏类型
    oneStrRestultGame: function (game_type_id) {
      let game_type_id_arr = [8, 16, 17, 18, 20, 30, 52, 53, 54, 56]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为汉字的游戏类型
    strResultGame: function (game_type_id) {
      let game_type_id_arr = [2, 14, 24, 33]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果只有一个区的游戏类型
    onlyOneGame: function (game_type_id) {
      let game_type_id_arr = [16, 18, 34, 52, 54]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果没有第三个区的游戏类型
    onlyTwoGame: function (game_type_id) {
      let game_type_id_arr = [10, 12, 16, 18, 19, 20, 21, 34, 42, 52, 54, 55, 56]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 没有游戏结果的游戏类型
    noResultGame: function (game_type_id) {
      let game_type_id_arr = [4, 6, 25, 28, 34, 36, 37, 38, 40]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? true : false
    },
    // 开奖结果为一串数字图片的游戏类型
    numsResultGame: function (game_type_id) {
      let game_type_id_arr = [16, 17, 18, 19, 20, 52, 53, 54, 55, 56]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 将一个字符串转换成数组
    getStrArr: function (numStr) {
      return numStr.split(',')
    },
    ifHaveItem: function (item, arr) {
      let itamArr = arr.split(',')
      itamArr.shift()
      return itamArr.indexOf(item) == -1 ? false : true
    },
    // 判断大小
    judgeSize(num) {
      let result = '';
      if (+num >= 0 && +num <= 13) {
        result = '小';
      } else {
        result = '大';
      }
      return result;
    },
    // 判断单双
    judgeEven(num) {
      let result = '';
      if (+num % 2 === 0) {
        result = '双';
      } else {
        result = '单';
      }
      return result;
    },
    // 判断豹顺对半杂
    judgeJunko(num1, num2, num3) {
      let arr = [+num1, +num2, +num3];
      let resultArr, result;
      arr.sort((a, b) => a - b);
      resultArr = calcGamePlayResult(arr);
      if (resultArr[0] === 2) {
        result = 1;
      } else if (resultArr[1] === 2) {
        result = 2;
      } else if (resultArr[0] === 1) {
        result = 3;
      } else if (resultArr[1] === 1) {
        result = 4;
      } else {
        result = 5;
      }
      return result;
    }
  }
}

export const gameCuntDown = {
  computed: {
    ...mapGetters([
      'choosedGame',
      'seconds',
      'otherSeconds',
      'lotteryTime',
      'newestItem',
      'newestState',
      'timerOther',
      'gameResultList',
      'awardResult',
      'openAudio'
    ])
  },
  watch: {
    seconds(e) {
      // console.log(e)
      // console.log('xxx', this.timerOther)
      // if (e <= 10) {
      //   this.waitLottery()
      // }
      if (e<=0) {
        console.log('进行开奖', this.timerOther)
        this.waitLottery()
      }
    }
  },
  methods: {
    // 开奖中
    waitLottery() {
      let newestItem = this.newestItem // 当前等待开奖的对象
      let gameResultList = JSON.parse(JSON.stringify(this.gameResultList)) // 开奖结果列表
      let choosedGame = this.choosedGame // 当前选择的游戏类型
      let newsetIndex = gameResultList.findIndex((item) => item.issue == newestItem.issue)
      // 改变状态为开奖中
      if (newsetIndex == -1) return
      gameResultList[newsetIndex].is_open = 2
      this.setResultList(gameResultList)
      if (this.waitTimer) {
        clearInterval(this.waitTimer)
      }
      this.waitTimer = setInterval(() => {
        this.$Api({
          api_name: 'kkl.game.getResultLogList',
          firstRow: 0,
          pageSize: 20,
          game_type_id: choosedGame.game_type_id
        }, (err, data) => {
          if (!err) {
            let list = data.data.game_result_list
            let index = list.findIndex((item) => item.issue == newestItem.issue)
            if (list[index].is_open == 1 && this.$store.state.otherSeconds <= 0) {
              clearInterval(this.waitTimer)
              this.setResultList(list)
              this.setNewState(0)
              console.log("new state: ", this.$store.state.newestState);
              // this.setOtherTimer(null);
              // 重新获取游戏开奖结果
              this.getAwardResult()
              // 开启提示音
              this.playAudio();
              // 开奖后刷新用户信息
              this.refreshUserInfo();
              //  继续下一次开奖倒计时
              this.filterNewstItem(list)
            } else if (list[index].is_open == 0 && list[index - 1] && +list[index - 1].addtime <= (+new Date() / 1000)) {
              clearInterval(this.waitTimer);
              list[index].is_open = 2;
              this.setResultList(list)
              this.setNewState(0)
              //  继续下一次开奖倒计时
              this.filterNewstItem(list)
            }
          }
        })
      }, 1000)
    },
    // 播放音效
    playAudio: function () {
      if (this.openAudio) {
        var myAuto = document.getElementById('myaudio');
        myAuto.src = 'http://www.yunshenghuo88.com/image/security.mp3'; //MP3路径
        myAuto.play();
      }
    },
    // 获取游戏开奖结果
    getAwardResult: function () {
      this.$Api({
        api_name: 'kkl.game.nowResult',
        game_type_id: this.choosedGame.game_type_id
      }, (err, data) => {
        if (!err) {
          this.setAwardResult(data.data)
        }
      })
    },
    // 选出下一次开奖对象
    filterNewstItem: function (list) {
      if (list.length == 0) {
        this.setNewestItem(null)
        return
      }
      let NoLotteryList = list.filter((item) => {
        return item.is_open == 0
      })
      NoLotteryList.sort((a, b) => {
        return parseInt(a.addtime) - parseInt(b.addtime)
      })
      if (NoLotteryList.length > 0) {
        this.setNewestItem(NoLotteryList[0])
        this.countDown()
      } else {
        this.setNewestItem(null)
      }
    },
    setAudioFn: function () {
      this.setAudio(!this.openAudio)
    },
    // 开启一个定时器
    countDown: async function () {
      this.endCountDown()
      var serverTime = getServerTime();
      console.log({serverTime});
      var timestamp = Date.parse(serverTime);
      let lotteryTime = this.lotteryTime;
      let second = parseInt(this.newestItem.addtime) - timestamp / 1000 // 开奖前的总倒计时时间
      let otherSecond = parseInt(this.newestItem.addtime) - lotteryTime - timestamp / 1000 // 开奖前 xx s倒计时的总倒计时时间
      let newestItem = this.newestItem // 当前等待开奖的对象
      let gameResultList = JSON.parse(JSON.stringify(this.gameResultList)) // 开奖结果列表
      let newsetIndex = gameResultList.findIndex((item) => item.issue == newestItem.issue)

      this.setSeconds(second - lotteryTime);

      if (this.seconds <= 0) {
        gameResultList[newsetIndex].is_open = 2
        this.setResultList(gameResultList)
        if (this.newestState != 1 && second > 0) {
          this.endCountDownOther();
          this.setOtherSeconds(second);
          this.startCountDownOther(second);
        } else {
          this.setNewState(1);
        }
      } else {
        this.startCountDown(second)
      }
    },
    ...mapMutations({
      setResultList: 'SET_RESULT_LIST',
      setNewState: 'SET_NEW_STATE',
      setNewestItem: 'SET_NEWEST_ITEM',
      setAwardResult: 'SET_AWAED_RESULT',
      setAudio: 'SET_AUDIO',
      setSeconds: "SET_SECONDS",
      setOtherSeconds: "SET_OTHER_SECONDS"
    }),
    ...mapActions([
      'endCountDown',
      "refreshUserInfo",
      'endCountDownOther',
    ])
  },
  destroyed() {
    clearInterval(this.waitTimer)
  }
}

export const betMixin = {
  data() {
    return {
      modeNums: [],
      modeNumsMore: [], // 二维数组
      modeHalf: 0,
      modeHalfBak: 0,
      mode_multiply: [0.1, 0.5, 0.8, 1.2, 2, 5, 10, 50, 100],
      ModeTypeIndex: -1,
      allBet: '', // 投注总额
      part: 1 // 游戏投注 部分数
    }
  },
  methods: {
    // 通过除数筛选
    filterDivisor(divisor, remainder, index) {
      this.ModeTypeIndex = index
      //  remainderType   0 为数字类型   1： 字符串类型
      let remainderType = typeof remainder == 'number' ? 0 : 1
      this.clearChoose()
      let modeNums = this.modeNumsMore[0]
      for (let i = 0; i < modeNums.length; i++) {
        let intI = parseInt(modeNums[i].key) ? parseInt(modeNums[i].key) : i
        if (remainderType == 0 && intI % divisor == remainder) {
          modeNums[i].chooseed = true
        } else {
          if (remainder == '<5' && intI % divisor < 5) {
            modeNums[i].chooseed = true
          } else if (remainder == '>=5' && intI % divisor >= 5) {
            modeNums[i].chooseed = true
          }
        }
        this.countDefault(i)
      }
    },
    // 通过大小类型筛选
    filterSize(size, sizeType, index) {
      this.ModeTypeIndex = index
      let threeNot = -1
      let modeNums = this.modeNumsMore[0]
      if (size == 'mid' || size == 'other') {
        if (modeNums.length > 2) {
          threeNot = modeNums.length % 3 == 0 ? modeNums.length : modeNums.length % 3 == 1 ? modeNums.length - 1 : modeNums.length + 1;
        }
      }
      this.clearChoose()
      for (let i = 0; i < modeNums.length; i++) {
        if (size == 'small' && i < this.modeHalfBak) {
          if (sizeType == 'all') {
            modeNums[i].chooseed = true
          } else if (sizeType == 'singular' && modeNums[i].key % 2 == 1) {
            if (modeNums.length == 5) {
              console.log(modeNums[i])
              modeNums[0].chooseed = true
            } else {
              modeNums[i].chooseed = true
            }
          } else if (sizeType == 'evennumber' && modeNums[i].key % 2 == 0) {
            modeNums[i].chooseed = true
          }
        } else if (size == 'big' && i >= this.modeHalfBak) {
          if (sizeType == 'all') {
            modeNums[i].chooseed = true
          } else if (sizeType == 'singular' && modeNums[i].key % 2 == 1) {
            modeNums[i].chooseed = true
          } else if (sizeType == 'evennumber' && modeNums[i].key % 2 == 0) {
            modeNums[i].chooseed = true
          }
        } else if (size == 'mid') {
          let filterNum = null
          if (modeNums.length % 2 == 0) {
            if (modeNums.length > 20 && modeNums.length < 30) {
              filterNum = (modeNums.length - 8) / 2
            } else if (modeNums.length > 10 && modeNums.length < 20) {
              filterNum = (modeNums.length - 6) / 2
            } else if (modeNums.length > 2 && modeNums.length <= 10) {
              filterNum = (modeNums.length - 4) / 2
            } else if (modeNums.length <= 2) {
              filterNum = (modeNums.length - 0) / 2
            }
          } else if (modeNums.length % 2 == 1) {
            if (modeNums.length >= 17 && modeNums.length < 20) {
              filterNum = (modeNums.length - 7) / 2
            } else if (modeNums.length > 10 && modeNums.length < 17) {
              filterNum = (modeNums.length - 5) / 2
            } else if (modeNums.length <= 10) {
              filterNum = (modeNums.length - 1) / 2
            }
          }
          if (i >= filterNum && i <= (modeNums.length - filterNum) - 1) {
            modeNums[i].chooseed = true
          }
          // if (i >= filterNum && i <= filterNum + (threeNot / 3) - 2) {
          //   modeNums[i].chooseed = true
          // }
        } else if (size == 'other') {
          // let filterNum = null
          // let filterNum = modeNums.length - 2 * (threeNot / 3)

          let filterNum = null
          if (modeNums.length % 2 == 0) {
            if (modeNums.length > 20 && modeNums.length < 30) {
              filterNum = (modeNums.length - 8) / 2
            } else if (modeNums.length > 10 && modeNums.length < 20) {
              filterNum = (modeNums.length - 6) / 2
            } else if (modeNums.length > 2 && modeNums.length <= 10) {
              filterNum = (modeNums.length - 4) / 2
            } else if (modeNums.length <= 2) {
              filterNum = (modeNums.length - 0) / 2
            }
          } else if (modeNums.length % 2 == 1) {
            if (modeNums.length >= 17 && modeNums.length < 20) {
              filterNum = (modeNums.length - 7) / 2
            } else if (modeNums.length > 10 && modeNums.length < 17) {
              filterNum = (modeNums.length - 5) / 2
            } else if (modeNums.length <= 10) {
              filterNum = (modeNums.length - 1) / 2
            }
          }
          if (sizeType == 'all') {
            if (i < filterNum || i > (modeNums.length - filterNum) - 1) {
              modeNums[i].chooseed = true
            }
          } else if (sizeType == 'big' && i >= this.modeHalf) {
            if (i > (modeNums.length - filterNum) - 1) {
              modeNums[i].chooseed = true
            }
          } else if (sizeType == 'small' && i < this.modeHalf) {
            if (i < filterNum) {
              modeNums[i].chooseed = true
            }
          }
        }
        this.countDefault(i)
      }
    },
    // 清除选中的类型
    clearChoose() {
      this.allBet = ''
      let clearFn = (arr) => {
        for (let i = 0; i < arr.length; i++) {
          arr[i].chooseed = false
          arr[i].bet = ''
        }
      }
      for (let i = 0; i < this.modeNumsMore.length; i++) {
        clearFn(this.modeNumsMore[i])
      }
    },
    // 根据赔率计算某个默认的投注金额
    countDefault(index) {
      console.log(index)
      let modeNums = this.modeNumsMore[0]
      let tempBet = getMaxCount(this.choosedGame.game_type_id, modeNums);
      // console.log(modeNums[index].rate)
      if (!modeNums[index].chooseed) return
      // modeNums[index].bet = Math.round(DEFAULT_GAME_BET / modeNums[index].rate)
      // 群玩法进这里
      if (this.choosedGame.game_type_id == 58 || this.choosedGame.game_type_id == 59 || this.choosedGame.game_type_id == 60 || this.choosedGame.game_type_id == 61) {
        modeNums[index].bet = 100;
      } else {
        modeNums[index].bet = Math.round(tempBet / modeNums[index].rate);
      }
      this.countAllBet()
    },
    // 计算需要投注总数
    countAllBet() {
      this.allBet = 0
      // 求和累加器
      let addFn = (arr) => {
        // console.log('数量',arr)
        this.allBet = arr.reduce((acc, cur) => {
          let accIn = acc
          if (cur.chooseed) {
            accIn = accIn + parseInt(cur.bet || 0)
          }
          return accIn
        }, this.allBet)
      }
      if (this.modeNumsMore.length > 0) {
        for (let i = 0; i < this.modeNumsMore.length; i++) {
          addFn(this.modeNumsMore[i])
        }
      }
    },
    // 选中所有的类型添加倍数
    allDouble(item) {
      let modeNums = this.modeNumsMore[0]
      for (let i = 0; i < modeNums.length; i++) {
        if (modeNums[i].chooseed) {
          let num = Math.floor(modeNums[i].bet * item)
          if (num == 0) {
            modeNums[i].chooseed = false
            modeNums[i].bet = ''
          } else {
            modeNums[i].bet = num
          }
        }
      }
    },
    // 全包
    allChoose() {
      let modeNums = this.modeNumsMore[0];
      let tempCount = 0;
      let tempBet = getMaxCount(this.choosedGame.game_type_id, modeNums);
      let maxBet = getMaxCount(this.choosedGame.game_type_id, modeNums)
      // 群玩法进这里
      if (this.choosedGame.game_type_id == 58 || this.choosedGame.game_type_id == 59 || this.choosedGame.game_type_id == 60 || this.choosedGame.game_type_id == 61) {
        for (let i = 0; i < modeNums.length; i++) {
          modeNums[i].chooseed = true;
          modeNums[i].bet = 100;
          tempCount += modeNums[i].bet
        }
      } else {
        let fn = () => {
          for (let i = 0; i < modeNums.length; i++) {
            modeNums[i].chooseed = true;
            modeNums[i].bet = Math.round(tempBet / modeNums[i].rate);
            tempCount += modeNums[i].bet
          }
          if (tempCount > maxBet) {
            tempBet = maxBet - (tempCount - maxBet);
            tempCount = 0;
            fn();
          }
        }
        fn();
      }
    },
    // 反选
    reverseChoose() {
      let modeNums = this.modeNumsMore[0]
      for (let i = 0; i < modeNums.length; i++) {
        modeNums[i].chooseed = !modeNums[i].chooseed
        if (modeNums[i].chooseed) {
          this.countDefault(i)
        } else {
          modeNums[i].bet = ''
        }
      }
    },
    // 搜哈  投注我的全部豆子
    betMyAll(betNum) {
      let modeNums = this.modeNumsMore[0]
      let choosedArr = modeNums.filter(item => item.chooseed)
      if (choosedArr.length == 0) return
      choosedArr.sort((a, b) => parseFloat(b.rate) - parseFloat(a.rate));
      let fristBet = parseFloat(choosedArr[0].rate);
      // 如果betNum存在就是定额  否则为我的全部豆子
      let myAllBet = betNum || parseInt(this.userInfo.left_money)
      // 计算选中的 所有赔率比例分母
      let denominator = choosedArr.reduce((arr, cur) => {
        console.log('cur', cur)
        let curRate = parseFloat(cur.rate);
        return arr + this.keepTwoDecimal(fristBet / curRate);
      }, 0)
      let average = this.keepTwoDecimal(myAllBet / denominator);
      for (let i = 0; i < choosedArr.length; i++) {
        let rate = parseFloat(choosedArr[i].rate);
        let bet = Math.floor((fristBet / rate) * average)
        console.log(bet)
        if (bet == 0) {
          choosedArr[i].bet = ''
          choosedArr[i].chooseed = false
        } else {
          choosedArr[i].bet = bet
        }
      }
    },
    // 保留两位小数
    keepTwoDecimal(num) {
      var result = parseFloat(num);
      // result = Math.round(num * 100) / 1000;
      return result;
    },
    // 获取投注时选中的json字符串
    getBetJSON(game_type_id) {
      // 投注一项的游戏类型  part: 1
      let part = this.part
      let Json_arr = []
      // 递归调用
      let recursion = function (arr, part) {
        let choosedArr = arr.filter(item => item.chooseed)
        let bet_json = []
        choosedArr.map((item) => {
          bet_json.push({
            key: item.key,
            money: item.bet
          })
        })
        Json_arr.push({
          part: part,
          bet_json: bet_json
        })
      }
      for (let i = 0; i < part; i++) {
        recursion(this.modeNumsMore[i], i + 1)
      }
      return JSON.stringify(Json_arr)
    },
    // 当输入框聚焦时
    inputFocus: function (item) {
      if (!item.chooseed) {
        if (this.rationBet && this.rationBet !== 0 && !isNaN(this.rationBet)) {
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
      if (bet && !isNaN(bet)) {
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
    // 上次投注
    lastBet: function () {
      this.$Api({
        api_name: 'kkl.game.getLastBetLog',
        game_type_id: this.choosedGame.game_type_id
      }, (err, data) => {
        if (!err) {
          if (!data.data.bet_json) {
            this.$msg('您还没有上次投注', 'error', ALERT_TIME)
            return
          } else {
            let lastBetObj = JSON.parse(data.data.bet_json)
            this.renderLastBet(lastBetObj)
          }
        } else {
          this.$msg(err.error_msg, 'error', ALERT_TIME)
        }
      })
    },
    // 将上期投注结果赋值给 modeNumsMore
    renderLastBet: function (arr) {
      for (let i = 0; i < arr.length; i++) {
        let lastArr = arr[i].bet_json
        for (let j = 0; j < lastArr.length; j++) {
          let index = this.modeNumsMore[i].findIndex(item => item.key == lastArr[j].key)
          this.modeNumsMore[i][index].bet = lastArr[j].money
          this.modeNumsMore[i][index].chooseed = true
        }
      }
    }
  },
  watch: {
    modeNumsMore: {
      handler() {
        this.countAllBet()
      },
      deep: true
    }
  },
  created() {
    let len = JSON.parse(this.awardResult.game_type_info.bet_json).length
    this.part = len
    let initArr = function (arr) {
      arr.map((item) => {
        item.chooseed = false
        item.bet = ''
        item.last_bet_rate = ''
        item.have_bet = ''
        return item
      })
      return arr;
    }
    let JsonArr = JSON.parse(this.awardResult.game_type_info.bet_json)
    for (let i = 0; i < len; i++) {
      this.modeNumsMore.push(initArr(JsonArr[i].bet_json))
    }

    this.modeHalf = Math.ceil(this.modeNumsMore[0].length / 2)
    if (this.modeNumsMore[0].length % 2 == 1) {
      if (this.modeNumsMore[0].length == 17 || this.modeNumsMore[0].length == 5) {
        this.modeHalfBak = Math.ceil(this.modeNumsMore[0].length / 2)
      } else {
        this.modeHalfBak = Math.floor(this.modeNumsMore[0].length / 2)
      }
    } else if (this.modeNumsMore[0].length % 2 == 0) {
      this.modeHalfBak = Math.ceil(this.modeNumsMore[0].length / 2)
    }
    // console.log('modeHalf', Math.ceil(this.modeNumsMore[0].length / 2))
    // 判断是否需要上期赔率和当前投注
    if (this.tableType == 1) {
      //  获取当前投注和上期赔率
      this.getLastBet()
    }
  },
  computed: {
    ...mapGetters([
      'awardResult',
      'userInfo'
    ])
  }
}

export const markSixMixin = {
  data () {
    return {
      resultList: [],
    }
  },
  computed: {
    ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose", "gameResultId"])
  },
  methods: {
    // 清空已选
    clearChoose() {
      const clearFn = arr => {
        for (let i = 0; i < arr.length; i++) {
          arr[i].checked = false
          arr[i].money = ''
        }
      }
      for (let i = 0; i < this.resultList.length; i++) {
        clearFn(this.resultList[i].bet_json)
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
  }
}

