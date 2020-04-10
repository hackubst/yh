import {
  DEFAULT_GAME_BET,
  getMaxCount
} from '@/config/config.js'

// import {getMaxCount} from '@/config/gameMixin.js'

import {
  getServerTime,
  calcGamePlayResult
} from "@/config/utils"

export const gameMixns = {
  data() {
    return {
      modeNums: [],
      mode_multiply: [0.1, 0.5, 0.8, 1.2, 1.5, 2, 5, 10, 50, 100],
      ModeTypeIndex: -1,
      allBet: 0
    }
  },
  watch: {
    modeNums: {
      handler() {
        this.countAllBet()
      },
      deep: true
    }
  },
  methods: {
    // 通过除数筛选
    filterDivisor(divisor, remainder, index) {
      this.ModeTypeIndex = index
      //  remainderType   0 为数字类型   1： 字符串类型
      let remainderType = typeof remainder == 'number' ? 0 : 1
      this.clearChoose()
      let modeNums = this.modeNums[0]
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
      let modeNums = this.modeNums[0]
      console.log(modeNums)
      if (size == 'mid' || size == 'other') {
        if (modeNums.length > 2) {
          threeNot = modeNums.length % 3 == 0 ? modeNums.length : modeNums.length % 3 == 1 ? modeNums.length - 1 : modeNums.length + 1;
        }
      }
      this.clearChoose()
      for (let i = 0; i < modeNums.length; i++) {
        if (size == 'small' && i < this.modeHalf) {
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
        } else if (size == 'big' && i >= this.modeHalf) {
          if (sizeType == 'all') {
            modeNums[i].chooseed = true
          } else if (sizeType == 'singular' && modeNums[i].key % 2 == 1) {
            modeNums[i].chooseed = true
          } else if (sizeType == 'evennumber' && modeNums[i].key % 2 == 0) {
            modeNums[i].chooseed = true
          }
        } else if (size == 'mid') {
          // let filterNum = modeNums.length - 2 * (threeNot / 3);
          // if (i >= filterNum && i <= filterNum + (threeNot / 3) - 2) {
          //   modeNums[i].chooseed = true
          // }
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
        } else if (size == 'other') {
          // let filterNum = modeNums.length - 2 * (threeNot / 3)
          // if (i < filterNum || i > filterNum + (threeNot / 3) - 2) {
          //   if (sizeType == 'all') {
          //     modeNums[i].chooseed = true
          //   } else if (sizeType == 'big' && i >= this.modeHalf) {
          //     modeNums[i].chooseed = true
          //   } else if (sizeType == 'small' && i < this.modeHalf) {
          //     modeNums[i].chooseed = true
          //   }
          // }
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
      for (let i = 0; i < this.modeNums.length; i++) {
        clearFn(this.modeNums[i])
      }
    },
    // 根据赔率计算某个默认的投注金额
    countDefault(index) {
      // console.log(index)
      // let modeNums = this.modeNums[0]
      // if (!modeNums[index].chooseed) return
      // modeNums[index].bet = Math.ceil(DEFAULT_GAME_BET / modeNums[index].rate)
      // this.countAllBet()

      let modeNums = this.modeNums[0]
      let tempBet = getMaxCount(this.$route.query.game_type_id, modeNums);
      if (!modeNums[index].chooseed) return
      // modeNums[index].bet = Math.round(DEFAULT_GAME_BET / modeNums[index].rate)
      modeNums[index].bet = Math.round(tempBet / modeNums[index].rate);
      this.countAllBet()
    },
    // 计算需要投注总数
    countAllBet() {
      this.allBet = 0
      // 求和累加器
      let addFn = (arr) => {
        this.allBet = arr.reduce((acc, cur) => {
          let accIn = acc
          if (cur.chooseed) {
            accIn = accIn + parseInt(cur.bet || 0)
          }
          return accIn
        }, this.allBet)
      }
      if (this.modeNums.length > 0) {
        for (let i = 0; i < this.modeNums.length; i++) {
          addFn(this.modeNums[i])
        }
      }
    },
    // 选中所有的类型添加倍数
    allDouble(item) {
      let modeNums = this.modeNums[0]
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
      // let modeNums = this.modeNums[0]
      // for (let i = 0; i < modeNums.length; i++) {
      //   modeNums[i].chooseed = true
      //   this.countDefault(i)
      // }
      let modeNums = this.modeNums[0];
      let tempCount = 0;
      let tempBet = getMaxCount(this.$route.query.game_type_id, modeNums);
      let maxBet = getMaxCount(this.$route.query.game_type_id, modeNums)
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
    },
    // 反选
    reverseChoose() {
      let modeNums = this.modeNums[0]
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
      let modeNums = this.modeNums[0]
      let choosedArr = modeNums.filter(item => item.chooseed)
      if (choosedArr.length == 0) return
      choosedArr.sort((a, b) => parseFloat(b.rate) - parseFloat(a.rate));
      let fristBet = parseFloat(choosedArr[0].rate);
      // 如果betNum存在就是定额  否则为我的全部豆子
      let myAllBet = betNum || 1000
      // 计算选中的 所有赔率比例分母
      let denominator = choosedArr.reduce((arr, cur) => {
        let curRate = parseFloat(cur.rate);
        return arr + this.keepTwoDecimal(fristBet / curRate);
      }, 0)
      let average = this.keepTwoDecimal(myAllBet / denominator);
      for (let i = 0; i < choosedArr.length; i++) {
        let rate = parseFloat(choosedArr[i].rate);
        let bet = Math.floor((fristBet / rate) * average)
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
      // result = Math.round(num * 100) / 100;
      return result;
    },
    // 当输入框聚焦时
    inputFocus: function (item) {
      if (!item.chooseed) {
        if (this.rationBet && this.rationBet !== 0) {
          item.chooseed = true
          item.bet = parseInt(this.rationBet)
        } else {
          item.chooseed = false
          // item.bet = 0
        }
      }
    },
    // 当键盘输入内容时
    inputChange: function (item) {
      if (item.bet) {
        item.chooseed = true
      } else {
        item.chooseed = false
      }
    },
    // 上次投注
    lastBet: function () {
      this.$Api({
        api_name: 'kkl.game.getLastBetLog',
        game_type_id: this.$route.query.game_type_id
      }, (err, data) => {
        if (!err) {
          if (data.data.bet_json == 'null') {
            this.$toast({
              text: '您还没有上次投注'
            })
            return
          } else {
            let lastBetObj = JSON.parse(data.data.bet_json)
            this.renderLastBet(lastBetObj)
          }
        }
      })
    },
    // 将上期投注结果赋值给 modeNums
    renderLastBet: function (arr) {
      for (let i = 0; i < arr.length; i++) {
        let lastArr = arr[i].bet_json
        for (let j = 0; j < lastArr.length; j++) {
          let index = this.modeNums[i].findIndex(item => item.key == lastArr[j].key)
          this.modeNums[i][index].bet = lastArr[j].money
          this.modeNums[i][index].chooseed = true
        }
      }
    },
    // 获取投注时选中的json字符串
    getBetJSON() {
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
        recursion(this.modeNums[i], i + 1)
      }
      return JSON.stringify(Json_arr)
    },
    // 获取投注数据
    getModeNums: function (bet_json_str) {
      let len = JSON.parse(bet_json_str).length
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
      let JsonArr = JSON.parse(bet_json_str)
      for (let i = 0; i < len; i++) {
        this.modeNums.push(initArr(JsonArr[i].bet_json))
      }
      this.modeHalf = Math.ceil(this.modeNums[0].length / 2)
      if (this.modeNums[0].length % 2 == 1) {
        if (this.modeNums[0].length == 17 || this.modeNums[0].length == 5) {
          this.modeHalf = Math.ceil(this.modeNums[0].length / 2)
        } else {
          this.modeHalf = Math.floor(this.modeNums[0].length / 2)
        }
      } else if (this.modeNums[0].length % 2 == 0) {
        this.modeHalf = Math.ceil(this.modeNums[0].length / 2)
      }
    },
    // 获取每个数据的 name
    getModeNames: function (bet_json_str) {
      let JsonArr = JSON.parse(bet_json_str)
      let resArr = []
      for (let i = 0; i < JsonArr.length; i++) {
        resArr.push(JsonArr[i].name)
      }
      return resArr
    }
  }
}

export const gameTypeMixins = {
  methods: {
    // 根据数字字符串返回对应的数字图片
    getNumImage: function (numStr) {
      if (!parseInt(numStr) && parseInt(numStr) != 0) return
      let num = parseInt(numStr)
      return require(`images/nums/icon_fangkuai${num}@2x.png`)
    },
    // 判断字符串中是否有某个值
    ifHaveItem: function (item, arr) {
      let itamArr = arr.split(',')
      itamArr.shift()
      return itamArr.indexOf(item) == -1 ? false : true
    },
    // 游戏结果为一个数字的游戏类型
    oneResultGame: function (game_type_id) {
      let game_type_id_arr = [1, 3, 5, 9, 10, 11, 12, 13, 15, 21, 22, 23, 26, 27, 31, 32, 35, 41, 42, 44]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为一串字符串的游戏类型
    oneStrRestultGame: function (game_type_id) {
      let game_type_id_arr = [8, 17, 18, 20, 30, 40, 53, 54, 56]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为一串数字图片的游戏类型
    numsResultGame: function (game_type_id) {
      let game_type_id_arr = [16, 17, 18, 19, 20, 37, 46, 51, 52, 53, 54, 55, 56, 57]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为  豹对 的游戏类型
    strResultGame: function (game_type_id) {
      let game_type_id_arr = [2, 33, 14, 24]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果为  庄 闲等 游戏类型
    pokerResultGame: function (game_type_id) {
      let game_type_id_arr = [7, 29, 39]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 游戏结果没有第三个区的游戏类型
    onlyTwoGame: function (game_type_id) {
      let game_type_id_arr = [10, 12, 16, 18, 19, 20, 21, 42, 52, 54, 55, 56]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 只有一个游戏结果的游戏类型
    onlyOneGame: function (game_type_id) {
      let game_type_id_arr = [34]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? false : true
    },
    // 没有游戏结果的游戏类型
    noResultGame: function (game_type_id) {
      let game_type_id_arr = [4, 6, 25, 28, 34, 36, 37, 38, 40]
      return game_type_id_arr.indexOf(parseInt(game_type_id)) == -1 ? true : false
    },
    // 获取字符串中第 index 个元素
    getStrNum(str, index) {
      let arr = str.split(',')
      return arr[index]
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
