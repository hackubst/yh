
import { getStore } from '../config/utils'

const state = {
    userInfo: JSON.parse(getStore('userInfo')),
    haveLogin: getStore('haveLogin'),       // 用户是否已经登录
    awardResult: {},                        // 游戏开奖结果
    newestItem: {},                         // 下一次开奖对象
    seconds: 0,                             //  倒计时秒数
    timer: null,                            //  游戏计时器
    otherSeconds: 0,                        // 另一个倒计时秒数
    timerOther: null,                       // 另一个游戏计时器
    lotteryTime: 10,                        // 开奖倒计时，用于还有xx秒开奖显示
    newestState: 0,                         // 最新游戏开奖状态  0： 倒计时中，  1 开奖中
    gameResultList: [],                    //  游戏开奖结果列表
    gameChooseList: [],                     //  选择六合彩的列表
    plateValue: 1,                             // 选择的盘1->A,2->B,3->C,4->D
    isPlateClose: 0, // 是否封盘，0否1是
    gameResultId: '', // 当前投注的id
}

export default state
