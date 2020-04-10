// 走势图相关处理逻辑
import {
  mapGetters
} from 'vuex'
export const trendChart = {
  data() {
    return {

    }
  },
  methods: {
    // 筛选大小
    filterSize: function (type) {
      if (type == 1) {
        return '小'
      } else {
        return '大'
      }
    },
    // 筛选单双
    filterOddEven: function (type) {
      if (type == 1) {
        return '单'
      } else {
        return '双'
      }
    },
    // 龙虎和
    filterTiger: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '龙'
          break;
        case 2:
          str = '虎'
          break;
        case 3:
          str = '和'
          break;
      }
      return str
    },
    // 豹对
    filterPard: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '豹'
          break;
        case 2:
          str = '顺'
          break;
        case 3:
          str = '对'
          break;
        case 4:
          str = '半'
          break;
        default:
          str = '杂'
      }
      return str
    },
    // 五行
    filterWood: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '金'
          break;
        case 2:
          str = '木'
          break;
        case 3:
          str = '水'
          break;
        case 4:
          str = '火'
          break;
        default:
          str = '土'
      }
      return str
    },
    // 四季
    filterSeasons: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '春'
          break;
        case 2:
          str = '夏'
          break;
        case 3:
          str = '秋'
          break;
        default:
          str = '冬'
      }
      return str
    },
    // 星座
    filterConstellation: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '水瓶'
          break;
        case 2:
          str = '双鱼'
          break;
        case 3:
          str = '白羊'
          break;
        case 4:
          str = '金牛'
          break;
        case 5:
          str = '双子'
          break;
        case 6:
          str = '巨蟹'
          break;
        case 7:
          str = '狮子'
          break;
        case 8:
          str = '处女'
          break;
        case 9:
          str = '天秤'
          break;
        case 10:
          str = '天蝎'
          break;
        case 11:
          str = '射手'
          break;
        default:
          str = '摩羯'
      }
      return str
    },
    // 生肖
    filterZodiac: function (type) {
      let str = ''
      let typeNum = parseInt(type)
      switch (typeNum) {
        case 1:
          str = '鼠'
          break;
        case 2:
          str = '牛'
          break;
        case 3:
          str = '虎'
          break;
        case 4:
          str = '兔'
          break;
        case 5:
          str = '龙'
          break;
        case 6:
          str = '蛇'
          break;
        case 7:
          str = '马'
          break;
        case 8:
          str = '羊'
          break;
        case 9:
          str = '猴'
          break;
        case 10:
          str = '鸡'
          break;
        case 11:
          str = '狗'
          break;
        default:
          str = '猪'
      }
      return str
    }
  },
  computed: {
    ...mapGetters([
      'choosedGame',
      'trendData'
    ])
  }
}