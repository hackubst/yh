// 游戏编辑模式选择方式
// typeFnType  1: 除数余数  divisor: 除数, remainder: 余数
//             2： 筛选大小中边   size  大小，  sizeType:  是否为单或者双
export const CHOOSE_TYPES = [{
    typeName: '单',
    typeFnType: 1,
    divisor: 2,
    remainder: 1
  },
  {
    typeName: '双',
    typeFnType: 1,
    divisor: 2,
    remainder: 0
  },
  {
    typeName: '大',
    typeFnType: 2,
    size: 'big',
    sizeType: 'all'
  },
  {
    typeName: '小',
    typeFnType: 2,
    size: 'small',
    sizeType: 'all'
  },
  {
    typeName: '中',
    typeFnType: 2,
    size: 'mid',
    sizeType: 'all'
  },
  {
    typeName: '边',
    typeFnType: 2,
    size: 'other',
    sizeType: 'all'
  },
  {
    typeName: '大单',
    typeFnType: 2,
    size: 'big',
    sizeType: 'singular'
  },
  {
    typeName: '小单',
    typeFnType: 2,
    size: 'small',
    sizeType: 'singular'
  },
  {
    typeName: '大双',
    typeFnType: 2,
    size: 'big',
    sizeType: 'evennumber'
  },
  {
    typeName: '小双',
    typeFnType: 2,
    size: 'small',
    sizeType: 'evennumber'
  },
  {
    typeName: '大边',
    typeFnType: 2,
    size: 'other',
    sizeType: 'big'
  },
  {
    typeName: '小边',
    typeFnType: 2,
    size: 'other',
    sizeType: 'small'
  }
]



// 根据 table_type 确定ui表格样式
export function getTableType(table_type) {
  var gameTableType;
  switch (parseInt(table_type)) {
    case 1:
      gameTableType = 6
      break;
    case 2:
      gameTableType = 6
      break;
    case 3:
      gameTableType = 3
      break;
    case 4:
      gameTableType = 3
      break;
    case 5:
      gameTableType = 6
      break;
    case 6:
      gameTableType = 6
      break;
    case 7:
      gameTableType = 3
      break;
    case 8:
      gameTableType = 6
      break;
    case 9:
      gameTableType = 3
      break;
    case 10:
      gameTableType = 6
      break;
    case 11:
        gameTableType = 6
        break;
    default:
      gameTableType = 0
  }
  return gameTableType
}
// 弹窗弹出时间
export const ALERT_TIME = 1500
export const DEFAULT_GAME_BET = 1000


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
