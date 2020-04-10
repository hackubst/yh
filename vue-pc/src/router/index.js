import Vue from 'vue'
import Router from 'vue-router'
import index from '@/pages/index/index';
import login from '@/pages/login/index'
import register from '@/pages/register/index'
import richText from '@/pages/richText/index'
import stepOne from '@/pages/retrievePwd/stepOne'
import stepTwo from '@/pages/retrievePwd/stepTwo'
import stepThree from '@/pages/retrievePwd/stepThree'
import moneyChanger from '@/pages/moneyChanger/index'
import cashExchange from '@/pages/cashExchange/index'
import businessCooperation from '@/pages/businessCooperation/index'
import extensionIncome from '@/pages/extensionIncome/index'
import activityArea from '@/pages/activityArea/index'
import detail from '@/pages/activityArea/detail'
import gameRanking from '@/pages/gameRanking/index'

import trialGameRanking from '@/pages/gameRanking/trial'

import { gameRouter } from './gameRouter.js'
import { userRouter } from './userRouter.js'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/', 
      redirect: { name: 'index' }
    },
    // 首頁
    {
      path: '/index',
      name: 'index',
      component: index
    },
    // 登录页面
    {
      path: '/login',
      name: 'login',
      component: login
    },
    // 注册页面
    {
      path: '/register',
      name: 'register',
      component: register
    },
    // 特殊文章页面
    {
      path: '/richText',
      name: 'richText',
      component: richText
    },
    // 找回密码
    {
      path: '/stepOne',
      name: 'stepOne',
      component: stepOne
    },{
      path: '/stepTwo',
      name: 'stepTwo',
      component: stepTwo
    },{
      path: '/stepThree',
      name: 'stepThree',
      component: stepThree
    },
    // 兑换中心
    {
      path: '/moneyChanger',
      name: 'moneyChanger',
      component: moneyChanger
    },
    //卡密兑换
    {
      path: '/cashExchange',
      name: 'cashExchange',
      component: cashExchange
    },
    //商务合作
    {
      path: '/businessCooperation',
      name: 'businessCooperation',
      component: businessCooperation
    },
    //推广收益
    {
      path: '/extensionIncome',
      name: 'extensionIncome',
      component: extensionIncome
    },
    //活动专区(活动，新闻)
    {
      path: '/activityArea',
      name: 'activityArea',
      component: activityArea
    },
    //活动详情，新闻详情
    {
      path: '/detail',
      name: 'detail',
      component: detail
    },
    // 游戏排行
    {
      path: '/gameRanking',
      name: 'gameRanking',
      component: gameRanking
    },
    // 体验版游戏排行
    {
      path: '/trialGameRanking',
      name: 'trialGameRanking',
      component: trialGameRanking
    },
    ...gameRouter,
    ...userRouter
  ]
})



