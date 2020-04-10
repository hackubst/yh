import Vue from 'vue'
import Router from 'vue-router'
import index from '@/pages/index/index'
import activity from '@/pages/activityArea/activity'
import newsNotice from '@/pages/activityArea/newsNotice'
import newsText from '@/pages/activityArea/newsText'
import forgetPwd from '@/pages/forgetPwd/forgetPwd'
import retrievePwd from '@/pages/forgetPwd/retrievePwd'
import personalCenter from '@/pages/personalCenter/index'
import myInfo from '@/pages/personalCenter/myInfo'
import userInfo from '@/pages/personalCenter/userInfo'
import bindTencent from '@/pages/personalCenter/bindTencent'
import beanDetail from '@/pages/personalCenter/beanDetail'
import rankReward from '@/pages/personalCenter/rankReward'
import bankPwd from '@/pages/personalCenter/bankPwd'
import bindWeChat from '@/pages/personalCenter/bindWeChat'
import confirmLogin from '@/pages/personalCenter/confirmLogin'
import dailyRelief from '@/pages/personalCenter/dailyRelief'
import information from '@/pages/personalCenter/information'
import modifyPwd from '@/pages/personalCenter/modifyPwd'
import myBank from '@/pages/personalCenter/myBank'
import safePwd from '@/pages/personalCenter/safePwd'
import exchangeRecord from '@/pages/personalCenter/exchangeRecord'
import sendPacket from '@/pages/personalCenter/sendPacket'
import receiveRecord from '@/pages/personalCenter/receiveRecord'
import mySend from '@/pages/personalCenter/mySend'
import richText from '@/pages/richText/index'
import cooperativeBusiness from '@/pages/cooperativeBusiness/index'
import exeBean from '@/pages/personalCenter/exeBean'
import rankList from '@/pages/rankList/rankList'
import exchange from '@/pages/exchange/exchange'
import exchangeGoods from '@/pages/exchange/exchangeGoods'
import gameIndex from '@/pages/game/gameIndex'
import gameInfo from '@/pages/game/gameInfo'
import awardDetail from '@/pages/game/awardDetail'
import awardDetailBar from '@/pages/game/awardDetailBar'
import gameRecordInfo from '@/pages/game/gameRecordInfo'
import gameBet from '@/pages/game/gameBet'
import login from '@/pages/login/login'
import register from '@/pages/register/register'
import extensionIncome from '@/pages/extensionIncome/index'
import TrialRankList from '@/pages/rankList/TrialRankList'
import hkGameInfo from '@/pages/hkGame/hkGameInfo'
import hkGameResult from '@/pages/hkGame/gameBet/index'
import hkBetList from '@/pages/hkGame/gameBet/betList'
import hkAllOrder from '@/pages/hkGame/gameBet/allOrder'
import hkOrderDetails from '@/pages/hkGame/gameBet/orderDetails'
import hkResult from '@/pages/hkGame/gameBet/hkResult'
import hkGameRule from '@/pages/hkGame/gameBet/hkGameRule'

Vue.use(Router)

export default new Router({
  routes: [{
    path: '/',
    redirect: {
      name: 'index'
    }
  },
    {//香港六合彩
      path: '/hkGameInfo',
      name: 'hkGameInfo',
      component: hkGameInfo,
      meta: {
        title: '游戏',
        noBotBar: false,
        noHead: true,
      }
    },
    {//香港六合彩-投注列表页
      path: '/betList',
      name: 'betList',
      component: hkBetList,
      meta: {
        title: '投注列表',
        noBotBar: false,
      }
    },
    {//香港六合彩-投注记录
      path: '/allOrder',
      name: 'allOrder',
      component: hkAllOrder,
      meta: {
        title: '投注记录',
        noBotBar: false,
        noHead: true
      }
    },
    {//香港六合彩-订单详情页
      path: '/orderDetails',
      name: 'orderDetails',
      component: hkOrderDetails,
      meta: {
        title: '投注列表',
        noBotBar: false,
      }
    },
    {//香港六合彩-开奖结果
      path: '/hkResult',
      name: 'hkResult',
      component: hkResult,
      meta: {
        title: '结果页',
        noBotBar: false,
      }
    },
    {//香港六合彩-投注记录
      path: '/hkGameRule',
      name: 'hkGameRule',
      component: hkGameRule,
      meta: {
        title: '玩法说明',
        noBotBar: false,
      }
    },
    { // 首页
      path: '/index',
      name: 'index',
      meta: {
        title: '首页',
        noArrow: true, // 顶部不需要箭头
        noBotBar: true, // 是否不需要底部导航栏   不写默认需要
        hasPc: true
      },
      component: index
    },
    { // 活动专区
      path: '/activity',
      name: 'activity',
      meta: {
        title: '最新活动',
        noArrow: false,
        noBotBar: false
      },
      component: activity
    }, { // 新闻资讯
      path: '/newsNotice',
      name: 'newsNotice',
      meta: {
        title: '新闻公告',
        noArrow: false,
        noBotBar: false
      },
      component: newsNotice
    }, { // 公告详情
      path: '/newsText',
      name: 'newsText',
      meta: {
        title: '公告详情',
        noArrow: false,
        noBotBar: false
      },
      component: newsText
    }, { // 忘记密码
      path: '/forgetPwd',
      name: 'forgetPwd',
      meta: {
        title: '忘记密码',
        noArrow: false,
        noBotBar: true,
        appBack: true
      },
      component: forgetPwd
    }, { // 忘记密码
      path: '/retrievePwd',
      name: 'retrievePwd',
      meta: {
        title: '忘记登录密码',
        noArrow: false,
        noBotBar: true,
        appBack: false
      },
      component: retrievePwd
    }, { // 个人中心
      path: '/personalCenter',
      name: 'personalCenter',
      meta: {
        title: '会员',
        noArrow: true,
        noBotBar: true
      },
      component: personalCenter
    }, { // 个人资料
      path: '/myInfo',
      name: 'myInfo',
      meta: {
        title: '修改资料',
        noArrow: false,
        noBotBar: false
      },
      component: myInfo
    }, { // 个人基本信息
      path: '/userInfo',
      name: 'userInfo',
      meta: {
        title: '基本信息',
        noArrow: false,
        noBotBar: false
      },
      component: userInfo
    }, { // 绑定QQ
      path: '/bindTencent',
      name: 'bindTencent',
      meta: {
        title: '绑定QQ',
        noArrow: false,
        noBotBar: true
      },
      component: bindTencent
    }, { // 乐豆明细
      path: '/beanDetail',
      name: 'beanDetail',
      meta: {
        title: '乐豆明细',
        noArrow: false,
        noBotBar: false
      },
      component: beanDetail
    }, { // 排行榜奖励
      path: '/rankReward',
      name: 'rankReward',
      meta: {
        title: '排行榜奖励',
        noArrow: false,
        noBotBar: false
      },
      component: rankReward
    }, { // 兑换记录
      path: '/exchangeRecord',
      name: 'exchangeRecord',
      meta: {
        title: '兑换记录',
        noArrow: false,
        noBotBar: false
      },
      component: exchangeRecord
    }, { // 发红包
      path: '/sendPacket',
      name: 'sendPacket',
      meta: {
        title: '发红包',
        noArrow: false,
        noBotBar: false
      },
      component: sendPacket
    }, { // 收到红包记录
      path: '/receiveRecord',
      name: 'receiveRecord',
      meta: {
        title: '收到红包记录',
        noArrow: false,
        noBotBar: false
      },
      component: receiveRecord
    }, { // 我发的红包
      path: '/mySend',
      name: 'mySend',
      meta: {
        title: '我发的红包',
        noArrow: false,
        noBotBar: false
      },
      component: mySend
    }, { // 银行密码
      path: '/bankPwd',
      name: 'bankPwd',
      meta: {
        title: '修改银行密码',
        noArrow: false,
        noBotBar: false
      },
      component: bankPwd
    }, { // 绑定微信
      path: '/bindWeChat',
      name: 'bindWeChat',
      meta: {
        title: '绑定微信',
        noArrow: false,
        noBotBar: false
      },
      component: bindWeChat
    }, { // 确认安全登录
      path: '/confirmLogin',
      name: 'confirmLogin',
      meta: {
        title: '确认登录',
        noArrow: false,
        noBotBar: false
      },
      component: confirmLogin
    }, { // 每日救济
      path: '/dailyRelief',
      name: 'dailyRelief',
      meta: {
        title: '每日救济',
        noArrow: false,
        noBotBar: false
      },
      component: dailyRelief
    }, { // 站内信息
      path: '/information',
      name: 'information',
      meta: {
        title: '站内信息',
        noArrow: false,
        noBotBar: false
      },
      component: information
    }, { // 密码修改
      path: '/modifyPwd',
      name: 'modifyPwd',
      meta: {
        title: '密码修改',
        noArrow: false,
        noBotBar: false
      },
      component: modifyPwd
    }, { // 我的银行
      path: '/myBank',
      name: 'myBank',
      meta: {
        title: '我的銀行',
        noArrow: false,
        noBotBar: false
      },
      component: myBank
    }, { // 安全密码
      path: '/safePwd',
      name: 'safePwd',
      meta: {
        title: '安全密码修改',
        noArrow: false,
        noBotBar: false
      },
      component: safePwd
    }, { // 经验换豆
      path: '/exeBean',
      name: 'exeBean',
      meta: {
        title: '经验换豆',
        noArrow: false,
        noBotBar: false
      },
      component: exeBean
    }, { // 富文本页面
      path: '/richText',
      name: 'richText',
      meta: {
        title: '用户协议',
        noArrow: false,
        noBotBar: false
      },
      component: richText
    }, { // 合作商家
      path: '/cooperativeBusiness',
      name: 'cooperativeBusiness',
      meta: {
        title: '合作商家',
        noArrow: false,
        noBotBar: false
      },
      component: cooperativeBusiness
    }, { // 牛人榜
      path: '/rankList',
      name: 'rankList',
      meta: {
        title: '牛人榜',
        noArrow: true,
        noBotBar: true
      },
      component: rankList
    }, { // 兑奖
      path: '/exchange',
      name: 'exchange',
      meta: {
        title: '兑奖',
        noArrow: true,
        noBotBar: true
      },
      component: exchange
    }, { // 兑奖奖品
      path: '/exchangeGoods',
      name: 'exchangeGoods',
      meta: {
        title: '兑奖奖品',
        noArrow: false,
        noBotBar: false
      },
      component: exchangeGoods
    },
    {
      // 游戏乐园首页
      path: '/gameIndex',
      name: 'gameIndex',
      component: gameIndex,
      meta: {
        title: '游戏乐园',
        noArrow: true,
        noBotBar: true
      }
    },
    {
      path: '/gameInfo',
      name: 'gameInfo',
      component: gameInfo,
      meta: {
        title: '游戏',
        noBotBar: false
      }
    }, {
      path: '/awardDetail',
      name: 'awardDetail',
      component: awardDetail,
      meta: {
        title: '详情',
        noBotBar: false
      }
    }, {
      path: '/awardDetailBar',
      name: 'awardDetailBar',
      component: awardDetailBar,
      meta: {
        title: '详情',
        noBotBar: false
      }
    }, {
      path: '/gameRecordInfo',
      name: 'gameRecordInfo',
      component: gameRecordInfo,
      meta: {
        title: '详情',
        noArrow: false,
        noBotBar: false
      }
    }, {
      path: '/gameBet',
      name: 'gameBet',
      component: gameBet,
      meta: {
        title: '下注',
        noBotBar: false
      }
    }, {
      path: '/login',
      name: 'login',
      component: login,
      meta: {
        title: '登录',
        noArrow: false,
        noBotBar: false
      }
    }, {
      path: '/register',
      name: 'register',
      component: register,
      meta: {
        title: '注册',
        noArrow: false,
        noBotBar: false
      }
    }, {
      path: '/extensionIncome',
      name: 'extensionIncome',
      component: extensionIncome,
      meta: {
        title: '推广收益',
        noArrow: false,
        noBotBar: false
      }
    }, {
      // 体验版游戏乐园首页
      path: '/gameIndexTrial',
      name: 'gameIndexTrial',
      component: gameIndex,
      meta: {
        title: '体验中心',
        noArrow: false,
        noBotBar: false,
        hasRanking: true
      }
    }, { // 体验版排行榜
      path: '/trialRankList',
      name: 'trialRankList',
      meta: {
        title: '战绩榜',
        noArrow: true,
        noBotBar: true
      },
      component: TrialRankList
    }
  ]
})
