const gameIndex = r => require.ensure([], () => r(require('@/pages/game/gameIndex')))
const gameHome = r => require.ensure([], () => r(require('@/pages/game/gameHome/gameHome')))
const editMode = r => require.ensure([], () => r(require('@/pages/game/editMode/editMode')))
const autoBet = r => require.ensure([], () => r(require('@/pages/game/autoBet/autoBet')))
const profitStatistics = r => require.ensure([], () => r(require('@/pages/game/profitStatistics/profitStatistics')))
const bettingRecord = r => require.ensure([], () => r(require('@/pages/game/bettingRecord/bettingRecord')))
const gameRule = r => require.ensure([], () => r(require('@/pages/game/gameRule/gameRule')))
const trendChart = r => require.ensure([], () => r(require('@/pages/game/trendChart/trendChart')))
const bet_detail = r => require.ensure([], () => r(require('@/pages/game/bet_detail/bet_detail')))

const gameRouters = [{
  path: '/gameIndex',
  name: 'gameIndex',
  component: gameIndex,
  children: [{
      path: 'gameHome',
      name: 'gameHome',
      component: gameHome
    },
    {
      path: 'editMode',
      name: 'editMode',
      component: editMode
    },
    {
      path: 'autoBet',
      name: 'autoBet',
      component: autoBet
    },
    {
      path: 'profitStatistics',
      name: 'profitStatistics',
      component: profitStatistics
    },
    {
      path: 'bettingRecord',
      name: 'bettingRecord',
      component: bettingRecord
    },
    {
      path: 'trendChart',
      name: 'trendChart',
      component: trendChart
    },
    {
      path: 'gameRule',
      name: 'gameRule',
      component: gameRule
    },
    {
      path: 'bet_detail',
      name: 'bet_detail',
      component: bet_detail
    },
  ]
}]

export const gameRouter = [...gameRouters]
