import {
  mapActions,
  mapGetters,
  mapMutations
} from 'vuex'
import SID from '@/sid.js'

import {
  getServerTime
} from "@/config/utils"

export const judgeBar = {
  watch: {
    '$route'() {
      if (this.$route.meta.noBotBar) {
        this.pbPx = 0
      } else {
        this.pbPx = 0
      }
    }
  },

}

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

export const fiveIndex = {
  methods: {
    setPhpsessid: function(sid){
      // console.log('setPhpsessid', sid)
      if(sid && sid !== 'undefined'){
        SID.setSid(sid);
      }
    }
  },
  created () {
    window.setPhpsessid = this.setPhpsessid
  }
}

// 倒计时mixin
export const countDown = {
  computed: {
    ...mapGetters([
      'gameResultList',
      'seconds',
      'otherSeconds',
      'lotteryTime',
      'newestItem',
      'timer',
      'timerOther',
      'newestState'
    ])
  },
  watch: {
    seconds(e) {
      // if (e <= 50) {
      //   this.waitLottery()
      // }
      if (e <= 0) {
        // console.log('进行开奖', this.timerOther)
        this.waitLottery()
      }
    }
  },
  methods: {
    // 开奖中
    waitLottery() {
      let newestItem = this.newestItem // 当前等待开奖的对象
      let gameResultList = JSON.parse(JSON.stringify(this.gameResultList)) // 开奖结果列表
      // console.log(gameResultList)
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
          game_type_id: this.$route.query.game_type_id
        }, (err, data) => {
          if (!err) {
            let list = data.data.game_result_list
            let index = list.findIndex((item) => item.issue == newestItem.issue)
            console.log({list},{index});
            if (list[index].game_log_info.result) {
              clearInterval(this.waitTimer)
              this.setResultList(list)
              this.setNewState(0)
              //  继续下一次开奖倒计时
              this.filterNewstItem(list)
            }
          }
        })
      }, 1000)
    },
    // 选出下一次开奖对象
    filterNewstItem: function (list) {
      if (list.length == 0) return
      let NoLotteryList = list.filter((item) => {
        return item.is_open == 0
      })
      NoLotteryList.sort((a, b) => {
        return parseInt(a.addtime) - parseInt(b.addtime)
      })
      console.log({NoLotteryList});
      if (NoLotteryList.length > 0) {
        this.setNewestItem(NoLotteryList[0])
        this.countDown()
      } else {
        this.setNewestItem(null)
      }
    },
    // 开启一个定时器
    countDown: function () {
      // this.endCountDown()
      // var timestamp = Date.parse(new Date())
      // let second = parseInt(this.newestItem.addtime) - timestamp / 1000
      // this.startCountDown(second)
      this.endCountDown()
      var serverTime = getServerTime();
      console.log({serverTime});
      var timestamp = Date.parse(serverTime)
      let second = parseInt(this.newestItem.addtime) - timestamp / 1000
      let newestItem = this.newestItem // 当前等待开奖的对象
      let gameResultList = JSON.parse(JSON.stringify(this.gameResultList)) // 开奖结果列表
      // console.log(gameResultList)
      let newsetIndex = gameResultList.findIndex((item) => item.issue == newestItem.issue)
      let lotteryTime = this.lotteryTime;
      this.setSeconds(second - lotteryTime);

      if (this.seconds <= 0) {
        // console.log(gameResultList)
        if (gameResultList.length > 0) {
          gameResultList[newsetIndex].is_open = 2
        }
        this.setResultList(gameResultList)
        this.setNewState(1)
      } else {
        this.startCountDown(second)
      }
    },
    ...mapMutations({
      setResultList: 'SET_RESULT_LIST',
      setNewestItem: 'SET_NEWEST_ITEM',
      setNewState: 'SET_NEW_STATE',
      setSeconds: "SET_SECONDS"
    }),
    ...mapActions([
      'endCountDown',
      'startCountDown'
    ])
  }
}
