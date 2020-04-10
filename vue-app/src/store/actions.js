import * as types from './mutation-types'
import Apifn from '../api'
// 开启一个倒计时
export const startCountDown = function ({
  commit,
  state,
  dispatch
}, seconds) {
  // if (state.timer) return
  // var seconds = seconds
  // var timer = setInterval(() => {
  //   seconds--
  //   commit(types.SET_SECONDS, seconds)
  //   if (seconds <= 0) {
  //     clearInterval(timer)
  //     commit(types.SET_NEW_STATE, 1)
  //   }
  // }, 1000)
  // commit(types.SET_TIMER, timer)
  var seconds = seconds - state.lotteryTime
  var timer = setInterval(() => {
    if (seconds > 0) {
      seconds--
    }
    commit(types.SET_SECONDS, seconds)
    if (seconds <= 0) {
      clearInterval(timer)
      if (state.newestState != 1) {
        commit(types.SET_OTHER_SECONDS, state.lotteryTime)
        dispatch("startCountDownOther", state.lotteryTime);
      }
    }
  }, 1000)
  commit(types.SET_TIMER, timer)
}

export const startCountDownOther = function ({
  commit,
  state
}, seconds) {
  var seconds = seconds
  var timerOther = setInterval(() => {
    seconds--
    commit(types.SET_OTHER_SECONDS, seconds)
    if (seconds <= 0) {
      commit(types.SET_NEW_STATE, 1)
      clearInterval(timerOther)
    }
  }, 1000)
  commit(types.SET_TIMER_OTHER, timerOther)
}

// 关闭倒计时
export const endCountDown = function ({
  commit,
  state
}) {
  clearInterval(state.timer)
  commit(types.SET_NEW_STATE, 0)
  commit(types.SET_TIMER, null)
}
// 重新获取用户信息(刷新用户信息)
export const refreshUserInfo = function ({
  commit
}) {
  Apifn({
    api_name: 'kkl.user.getUserInfo'
  }, (err, data) => {
    if (!err) {
      commit(types.SET_USER, data.data)
    }
  })
}
