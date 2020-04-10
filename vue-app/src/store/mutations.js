import * as types from './mutation-types'
import {
  setStore,
  getStore,
  removeStore
} from '../config/utils'

const mutations = {
  [types.SET_USER](state, userInfo) {
    setStore('userInfo', userInfo)
    setStore('haveLogin', true)
    state.haveLogin = true
    state.userInfo = userInfo
  },
  [types.DEL_USER](state) {
    removeStore('userInfo')
    removeStore('haveLogin')
    state.haveLogin = false
    state.userInfo = {}
  },
  [types.SET_AWAED_RESULT](state, awardResult) {
    state.awardResult = awardResult
  },
  [types.SET_NEWEST_ITEM](state, newestItem) {
    state.newestItem = newestItem
  },
  [types.SET_SECONDS](state, seconds) {
    state.seconds = seconds
  },
  [types.SET_OTHER_SECONDS](state, seconds) {
    state.otherSeconds = seconds
  },
  [types.SET_TIMER](state, timer) {
    state.timer = timer
  },
  [types.SET_TIMER_OTHER](state, timerOther) {
    state.timerOther = timerOther
  },
  [types.SET_LOTTERY_TIME](state, time) {
    state.lotteryTime = time;
  },
  [types.SET_NEW_STATE](state, newestState) {
    state.newestState = newestState
  },
  [types.SET_RESULT_LIST](state, gameResultList) {
    state.gameResultList = gameResultList
  },
  [types.SET_CHOOSE_LIST](state, item) {
    state.gameChooseList.push(item);
    let chooseList = state.gameChooseList;
    setStore('gameChooseList', chooseList)
  },
  [types.SET_PLATE_VALUE](state, value) {
    state.plateValue = value
  },
  [types.SET_PLATE_CLOSE](state, value) {
    state.isPlateClose = value
  },
  [types.SET_RESULT_ID](state, id) {
    state.gameResultId = id
  }
}

export default mutations
