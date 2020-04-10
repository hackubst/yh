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
  [types.CHOOSE_GAME](state, choosedGame) {
    state.choosedGame = choosedGame
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
  [types.SET_LOTTERY_TIME](state, time) {
    state.lotteryTime = time;
  },
  [types.SET_TIMER](state, timer) {
    state.timer = timer
  },
  [types.SET_TIMER_OTHER](state, timerOther) {
    state.timerOther = timerOther
  },
  [types.SET_NEW_STATE](state, newestState) {
    // console.log("new state:", newestState);
    state.newestState = newestState
  },
  [types.SET_RESULT_LIST](state, gameResultList) {
    state.gameResultList = gameResultList
  },
  [types.SET_GUSS_GAME](state, guessingGame){
    state.guessingGame = guessingGame
  },
  [types.SET_INDEX_GAME](state, indexGame){
    state.indexGame = indexGame
  },
  [types.SET_RED_PACKET](state, redPacketDialog){
    state.redPacketDialog = redPacketDialog
  },
  [types.SET_RED_PACKET_ID](state, red_packet_id){
    state.red_packet_id = red_packet_id
  },
  [types.SET_TREND_DATA](state, trendData){
    state.trendData = trendData
  },
  [types.SET_LOGIN_CHECK](state, chekLogin){
    state.chekLogin = chekLogin
  },
  [types.SET_CHECK_STATE](state, checkState){
    state.checkState = checkState
  },
  [types.SET_AUDIO](state, openAudio){
    state.openAudio = openAudio
  },
  [types.SET_IS_EXPERIENCE](state, isExperience) {
    state.isExperience = isExperience
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