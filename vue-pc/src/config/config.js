// 项目相关参数

// 弹窗弹出时间
export const ALERT_TIME = 1500
// 游戏默认的赔率除数
export const DEFAULT_GAME_BET = 1000

// 游戏开奖中时 不需要跳转回首页的路由
export const NO_SKIP_ROUTE = [
  'gameHome',
  'gameRule',
  'bettingRecord',
  'editMode'
]

// 游戏系列
// 蛋蛋系列
export const EGG_SERIES = [1, 2, 3, 4, 5, 6, 7, 9, 10];
// 北京系列
export const BEIJING_SERIES = [11, 12, 13, 14, 15];
// PK系列
export const PK_SERIES = [16, 17, 18, 19, 20, 51];
// 加拿大系列
export const CANADA_SERIES = [21, 22, 23, 24, 25, 26, 27, 28, 29];
// 韩国系列
export const KOREA_SERIES = [31, 32, 33, 34, 35, 36, 44];
// 腾讯系列
export const TENCENT_SERIES = [37, 38];
// 重庆时时彩系列
export const CHONGQIN_SERIES = [43];
// 比特币系列
export const BITCOIN_SERIES = [45, 46];
// 飞艇系列
export const BOAT_SERIES = [52, 53, 54, 55, 56, 57];
// 群玩法
export const GROUP_PLAY_SERIES = [58, 59, 60, 61];

// 游戏开奖倒计时
export const GAME_LOTTERY_MAP = new Map([
  [EGG_SERIES, 50],
  [BEIJING_SERIES, 50],
  [PK_SERIES, 50],
  [CANADA_SERIES, 30],
  [KOREA_SERIES, 30],
  [TENCENT_SERIES, 50],
  [CHONGQIN_SERIES, 50],
  [BITCOIN_SERIES, 10],
  [BOAT_SERIES, 50],
  [GROUP_PLAY_SERIES, 50]
]);

// 根据 game_type_id 确定游戏系列
function getGameSeries(game_type_id) {
  let id = Number(game_type_id);
  if (EGG_SERIES.indexOf(id) != -1) {
    return EGG_SERIES;
  }
  if (BEIJING_SERIES.indexOf(id) != -1) {
    return BEIJING_SERIES;
  }
  if (PK_SERIES.indexOf(id) != -1) {
    return PK_SERIES
  }
  if (CANADA_SERIES.indexOf(id) != -1) {
    return CANADA_SERIES;
  }
  if (KOREA_SERIES.indexOf(id) != -1) {
    return KOREA_SERIES;
  }
  if (TENCENT_SERIES.indexOf(id) != -1) {
    return TENCENT_SERIES;
  }
  if (CHONGQIN_SERIES.indexOf(id) != -1) {
    return CHONGQIN_SERIES;
  }
  if (BITCOIN_SERIES.indexOf(id) != -1) {
    return BITCOIN_SERIES;
  }
  if (BOAT_SERIES.indexOf(id) != -1) {
    return BOAT_SERIES;
  }
  if (GROUP_PLAY_SERIES.indexOf(id) != -1) {
    return GROUP_PLAY_SERIES;
  }
}

// 根据 game_type_id 确定游戏开奖倒计时时间
export function getLotteryTime(game_type_id) {
  const series = getGameSeries(Number(game_type_id));
  return GAME_LOTTERY_MAP.get(series);
}

// 根据 game_type_id 确定  游戏类型  0： 号码类型    1： 扑克类型     2: 字符串类型
// 游戏类型为号码的游戏id数组
const GameNum_ID_ARR = [1, 2, 3, 4, 5, 6, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 30, 31, 32, 33, 34, 35, 36, 37, 38, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61]

export function getGameType(game_type_id) {
  var parseIntId = parseInt(game_type_id)
  if (parseIntId == 46 || parseIntId == 48 || parseIntId == 50 || parseIntId == 51 || parseIntId == 57) {
    return 2
  }
  if (GameNum_ID_ARR.indexOf(parseIntId) > -1) {
    return 0
  } else {
    return 1
  }
}

// 根据游戏类型Id返回走势图表格样式
const RUN_CHART_ONE = [1, 2, 3, 5, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 26, 27, 31, 32, 33, 45, 47, 49, 52, 53, 54, 55, 56]
const RUN_CHART_TWO = [4, 25]
const RUN_CHART_THREE = [8, 30, 31]
const RUN_CHART_FOUR = []
const RUN_CHART_FIVE = []
const RUN_CHART_SIX = [6, 28]
const RUN_CHART_SEVEN = [7]
const RUN_CHART_EIGHT = [43]
const RUN_CHART_NINE = [46, 48, 50, 51, 57]

export function getRunchartType(game_type_id){
  var parseIntId = parseInt(game_type_id)
  if(RUN_CHART_ONE.indexOf(parseIntId) > -1){
    return 1
  }else if(RUN_CHART_TWO.indexOf(parseIntId) > -1){
    return 2
  }else if(RUN_CHART_THREE.indexOf(parseIntId) > -1){
    return 3
  }else if(RUN_CHART_FOUR.indexOf(parseIntId) > -1){
    return 4
  }else if(RUN_CHART_FIVE.indexOf(parseIntId) > -1){
    return 5
  }else if(RUN_CHART_SIX.indexOf(parseIntId) > -1){
    return 6
  }else if(RUN_CHART_SEVEN.indexOf(parseIntId) > -1){
    return 7
  }else if(RUN_CHART_EIGHT.indexOf(parseIntId) > -1){
    return 8
  }else if(RUN_CHART_NINE.indexOf(parseIntId) > -1){
    return 9
  } else{
    console.log('没有定义', parseIntId)
    return 0
  }
}

// 根据 table_type 确定ui表格样式
export function getTableType(table_type) {
  
  var gameTableType;
  switch (parseInt(table_type)) {
    case 1:
      gameTableType = 6
      break;
    case 2:
      gameTableType = 2
      break;
    case 3:
      gameTableType = 3
      break;
    case 4:
      gameTableType = 5
      break;
    case 5:
      gameTableType = 1
      break;
    case 6:
      gameTableType = 4
      break;
    case 7:
      gameTableType = 13
      break;
    case 8:
      gameTableType = 6
      break;
    case 9:
      gameTableType = 14
      break;
    case 10:
      gameTableType = 15
      break;
    case 11:
      gameTableType = 17
      break;
    default:
      gameTableType = 0
  }
  return gameTableType
}

// 游戏编辑模式选择方式
// typeFnType  1: 除数余数  divisor: 除数, remainder: 余数
//             2： 筛选大小中边   size  大小，  sizeType:  是否为单或者双
export const CHOOSE_TYPES = [
        { typeName: '单', typeFnType: 1, divisor: 2, remainder: 1 },
        { typeName: '双', typeFnType: 1, divisor: 2, remainder: 0 },
        { typeName: '大', typeFnType: 2, size: 'big', sizeType: 'all' },
        { typeName: '小', typeFnType: 2, size: 'small', sizeType: 'all' },
        { typeName: '中', typeFnType: 2, size: 'mid', sizeType: 'all' },
        { typeName: '边', typeFnType: 2, size: 'other', sizeType: 'all' },
        { typeName: '大单', typeFnType: 2, size: 'big', sizeType: 'singular' },
        { typeName: '小单', typeFnType: 2, size: 'small', sizeType: 'singular' },
        { typeName: '大双', typeFnType: 2, size: 'big', sizeType: 'evennumber' },
        { typeName: '小双', typeFnType: 2, size: 'small', sizeType: 'evennumber' },
        { typeName: '大边', typeFnType: 2, size: 'other', sizeType: 'big' },
        { typeName: '小边', typeFnType: 2, size: 'other', sizeType: 'small' },
        { typeName: '0尾', typeFnType: 1, divisor: 10, remainder: 0 },
        { typeName: '1尾', typeFnType: 1, divisor: 10, remainder: 1 },
        { typeName: '2尾', typeFnType: 1, divisor: 10, remainder: 2 },
        { typeName: '3尾', typeFnType: 1, divisor: 10, remainder: 3 },
        { typeName: '4尾', typeFnType: 1, divisor: 10, remainder: 4 },
        { typeName: '小尾', typeFnType: 1, divisor: 10, remainder: '<5' },
        { typeName: '5尾', typeFnType: 1, divisor: 10, remainder: 5 },
        { typeName: '6尾', typeFnType: 1, divisor: 10, remainder: 6 },
        { typeName: '7尾', typeFnType: 1, divisor: 10, remainder: 7 },
        { typeName: '8尾', typeFnType: 1, divisor: 10, remainder: 8 },
        { typeName: '9尾', typeFnType: 1, divisor: 10, remainder: 9 },
        { typeName: '大尾', typeFnType: 1, divisor: 10, remainder: '>=5' },
        { typeName: '3余0', typeFnType: 1, divisor: 3, remainder: 0 },
        { typeName: '3余1', typeFnType: 1, divisor: 3, remainder: 1 },
        { typeName: '3余2', typeFnType: 1, divisor: 3, remainder: 2 },
        { typeName: '4余0', typeFnType: 1, divisor: 4, remainder: 0 },
        { typeName: '4余1', typeFnType: 1, divisor: 4, remainder: 1 },
        { typeName: '4余2', typeFnType: 1, divisor: 4, remainder: 2 },
        { typeName: '4余3', typeFnType: 1, divisor: 4, remainder: 3 },
        { typeName: '5余0', typeFnType: 1, divisor: 5, remainder: 0 },
        { typeName: '5余1', typeFnType: 1, divisor: 5, remainder: 1 },
        { typeName: '5余2', typeFnType: 1, divisor: 5, remainder: 2 },
        { typeName: '5余3', typeFnType: 1, divisor: 5, remainder: 3 },
        { typeName: '5余4', typeFnType: 1, divisor: 5, remainder: 4 }
]

// 计算游戏自动投注最多投注数量
export function getMaxCount(type_id, modeNums){
   let game_type_id = parseInt(type_id);
   let type_1 = [9, 13, 22, 32];
   let type_2 = [20, 56];
   let type_10 = [10, 12, 17, 21, 53];
   let type_pk = [16, 18, 19, 52, 54, 55];
   let maxcount = 0;
   if(type_10.indexOf(game_type_id) > -1){
    maxcount = parseInt(modeNums[0].rate) * 10;
   }else if(type_1.indexOf(game_type_id) > -1){
    maxcount = parseInt(modeNums[0].rate);
   }else if(type_2.indexOf(game_type_id) > -1){
    maxcount = parseInt(modeNums[0].rate) * 2;
   } else if(type_pk.indexOf(game_type_id) > -1){
    maxcount = 100;
   } else {
    maxcount = DEFAULT_GAME_BET
   }
   return maxcount;
}


// ******************************************************
// ***************************************************
// ************************************************
// ------------------------------------------------------
// 六合彩定义数据 start
export const PLATE_MAP = new Map([['A盘', 1], ['B盘', 2], ['C盘', 3], ['D盘', 4]]);

export const RED_WAVE = [1, 2, 7, 8, 12, 13, 18, 19, 23, 24, 29, 30, 34, 35, 40, 45, 46];

export const BLUE_WAVE = [3, 4, 9, 10, 14, 15, 20, 25, 26, 31, 36, 37, 41, 42, 47, 48];

export const GREEN_WAVE = [5, 6, 11, 16, 17, 21, 22, 27, 28, 32, 33, 38, 39, 43, 44, 49];

export const SHENGXIAO_ARRAY = ['鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊', '猴', '鸡', '狗', '猪'];
// 六合彩定义数据 end
// ------------------------------------------------------