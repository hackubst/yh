const personalCenter = r => require.ensure([], () => r(require('@/pages/personalCenter/index')))
const userCenter = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/userCenter')))
const myInfo = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/myInfo')))
const myBank = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/myBank')))
const pwdModify = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/pwdModify')))
const loginTest = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/loginTest')))
const userTest = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/userTest')))
const dailyRelief = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/dailyRelief')))
const beanDetail = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/beanDetail')))
const listAwards = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/listAwards')))
const income = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/income')))
const exchangeRecords = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/exchangeRecords')))
const rebate = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/rebate')))
const myPacket = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/myPacket')))
const loginLimit = r => require.ensure([], () => r(require('@/pages/personalCenter/childPage/loginLimit')))

const userRouters = [{
  path: '/personalCenter',
  name: 'personalCenter',
  component: personalCenter,
  children: [
    // 用户中心
    {
      path: '/userCenter',
      component: userCenter,
    },
    // 我的资料
    {
      path: '/myInfo',
      component: myInfo,
    },
    // 我的银行
    {
      path: '/myBank',
      component: myBank,
    },
    // 登录验证
    {
      path: '/pwdModify',
      component: pwdModify,
    },
    // 登录测试
    {
      path: '/loginTest',
      component: loginTest,
    },
    // 用户测试
    {
      path: '/userTest',
      component: userTest,
    },
    // 每日救济
    {
      path: '/dailyRelief',
      component: dailyRelief,
    },
    // 乐豆明细
    {
      path: '/beanDetail',
      component: beanDetail,
    },
    // 排行榜奖励
    {
      path: '/listAwards',
      component: listAwards,
    },
    // 推广收益
    {
      path: '/income',
      component: income,
    },
    // 兑换记录
    {
      path: '/exchangeRecords',
      component: exchangeRecords,
    },
    // 领取返利
    {
      path: '/rebate',
      component: rebate,
    },
    // 我的红包
    {
      path: '/myPacket',
      component: myPacket,
    },
    // 登录地区限制
    {
      path: '/loginLimit',
      component: loginLimit,
    },
  ],
}]

export const userRouter = [...userRouters]
