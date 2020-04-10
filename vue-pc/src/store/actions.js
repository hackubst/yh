import * as types from './mutation-types'
import Apifn from '../api'

export default {
  // 开启一个倒计时
  startCountDown({
    commit,
    state,
    dispatch
  }, seconds) {
    // if (state.timer) return
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
  },

  // 开启另一个倒计时
  startCountDownOther({
    commit,
    state
  }, seconds) {
    // if (state.timerOther) return
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
  },

  // 关闭倒计时
  endCountDown({
    commit,
    state
  }) {
    clearInterval(state.timer)
    commit(types.SET_NEW_STATE, 0)
    commit(types.SET_TIMER, null)
  },

  // 关闭另一个倒计时
  endCountDownOther({
    commit,
    state
  }) {
    clearInterval(state.timerOther)
    commit(types.SET_TIMER_OTHER, null)
  },

  // 重新获取用户信息(刷新用户信息)
  refreshUserInfo({
    commit
  }) {
    Apifn({
      api_name: 'kkl.user.getUserInfo'
    }, (err, data) => {
      if (!err) {
        commit(types.SET_USER, data.data)
      }
    })
  },

  // 获取红包id
  setRedPacket({
    commit
  }, redPacketId) {
    if (redPacketId) {
      commit(types.SET_RED_PACKET, true)
    } else {
      commit(types.SET_RED_PACKET, false)
    }
    commit(types.SET_RED_PACKET_ID, redPacketId)
  }

}
