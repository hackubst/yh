import { getStore } from '../config/utils'


const state = {
    userInfo: JSON.parse(getStore('userInfo')),
    haveLogin: getStore('haveLogin'),       // 用户是否已经登录
    choosedGame: {},                        // 选择的游戏类型
    awardResult: {},                         // 游戏开奖结果
    newestItem: {},                         // 下一次开奖对象
    seconds: 0,                             //  倒计时秒数
    otherSeconds: 0,                        // 另一个倒计时秒数
    lotteryTime: 10,                        // 开奖倒计时，用于还有xx秒开奖显示
    timer: null,                            //  游戏计时器
    timerOther: null,                       // 另一个游戏计时器
    newestState: 0,                         // 最新游戏开奖状态  0： 倒计时中，  1 开奖中
    gameResultList: [],                     //  游戏开奖结果列表
    guessingGame: null,                     //  竞猜的游戏
    indexGame: null,                        // 首页选择的游戏类型
    redPacketDialog: false,                 //  红包弹窗
    red_packet_id: '',                       // 红包id
    trendData: null,                         // 走势图数据
    chekLogin: false,                        //  首页登录校验弹窗
    checkState: false,                         //  是否已经校验过了
    openAudio: true,                           // 是否开启游戏音效
    isExperience: false,                       // 是否是体验服
    plateValue: 1,                             // 选择的盘1->A,2->B,3->C,4->D
    hkUserInfo: {},                             // 香港六合彩的用户信息
    isPlateClose: 0, // 是否封盘，0否1是
    gameResultId: '', // 当前投注的id
}

export default state
