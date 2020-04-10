import {RED_WAVE, BLUE_WAVE, GREEN_WAVE} from '@/config/config'
/**
 * 存储localStorage
 */
export const setStore = (name, content) => {
  if (!name) return;
  if (typeof content !== 'string') {
    content = JSON.stringify(content);
  }
  window.localStorage.setItem(name, content);
}

/**
 * 获取localStorage
 */
export const getStore = name => {
  if (!name) return;
  return window.localStorage.getItem(name);
}

/**
 * 删除localStorage
 */
export const removeStore = name => {
  if (!name) return;
  window.localStorage.removeItem(name);
}

/*
 * @Author: zwb
 * @Date: 2019-07-10
 * @Last Modified by: zwb
 * @Last Modified time: 2019-07-10
 */
export function JsonSort(jsonData) {
  try {
    let tempJsonObj = {};
    let sdic = Object.keys(jsonData).sort();
    sdic.map((item, index) => {
      tempJsonObj[item] = jsonData[sdic[index]]
    })
    return tempJsonObj;
  } catch (e) {
    return jsonData;
  }
}

/**
 * @todo: 获取服务端时间
 * @param: 
 * @return: <Promise>
 * @author: zcl
 * @date: 2019-12-10 17:56:20
 * @version: V1.0.0
 */
export function getServerTime() {
  // return new Promise((resolve, reject) => {
  //   var xhr = new XMLHttpRequest();
  //   if (!xhr) {
  //     xhr = new ActiveXObject("Microsoft.XMLHTTP");
  //   }
  //   // xhr.open("HEAD", location.href, true);
  //   xhr.open("GET","/",false);
  //   xhr.onreadystatechange = function () {
  //     if (xhr.readyState == 4 && xhr.status == 200) {
  //       resolve(xhr.getResponseHeader("Date"))
  //     }
  //   }
  //   xhr.send(null);
  // })
  var xhr = null;
  if (window.XMLHttpRequest) {
    xhr = new window.XMLHttpRequest();
  } else { // ie
    xhr = new ActiveObject("Microsoft")
  }

  xhr.open("GET", "/", false) //false不可变
  xhr.send(null);
  var date = xhr.getResponseHeader("Date");
  return new Date(date);
}

/**
 * @todo: 计算豹顺对半杂
 * @param: {Array} 数组
 * @return: {Number} result
 * @author: zcl
 * @date: 2019-12-11 11:27:16
 * @version: V1.0.0
 */
export function calcGamePlayResult(array) {
  var resultArray = new Array(2);
  //计算豹子、对子
  resultArray[0] = array[2] - array[1] == 0 ? 1 : 0;
  resultArray[0] = array[1] - array[0] == 0 ? ++resultArray[0] : resultArray[0];

  //计算顺子、半顺、杂六
  resultArray[1] = array[2] - array[1] == 1 ? 1 : 0;
  resultArray[1] = array[1] - array[0] == 1 ? ++resultArray[1] : resultArray[1];

  if (array[0] === 0 && array[2] === 9) {
    if (array[1] === 1) {
      resultArray[1] = 2;
    } else {
      resultArray[1] = 1;
    }
  }

  return resultArray;
}

/**
 * @todo: 根据年龄计算生肖
 * @param: {Number} age
 * @return: {String} 生肖
 * @author: zcl
 * @date: 2020-03-09 17:24:28
 * @version: V1.0.0
 */
export function getShengxiao(age) {
  let year = new Date().getFullYear() - (age - 1);
  const animal_arr = ['猴', '鸡', '狗', '猪', '鼠', '牛', '虎', '兔', '龙', '蛇', '马', '羊'];
  let index = year % 12;
  return animal_arr[index];
}

// 辅助函数，获取背景色
export function getBg(num) {
  let bg;
  if (RED_WAVE.indexOf(+num) !== -1) {
    bg = 1;
  }
  if (BLUE_WAVE.indexOf(+num) !== -1) {
    bg = 2;
  }
  if (GREEN_WAVE.indexOf(+num) !== -1) {
    bg = 3;
  }
  return bg;
}

// 辅助函数：获取尾数
export function getTail(num) {
  let tailStr = '';
  let tail = +num / 10;
  tailStr = tail + '尾';
  return tailStr;
}

/**
 * @todo: 输出给定数组m中组合限制数为n的所有排列组合
 * @param: {Array} m 数组
 * @param: {Number} n 限制数
 * @return: {String}
 * @author: zcl
 * @date: 2020-03-16 15:51:31
 * @version: V1.0.0
 */
export function getCombination(m, n) {
  if (!n || n < 1) {
    return [];
  }

  if (m.length === n) {
    return m.join(',');
  }

  var resultStr = '',
    resultArrs = [],
    flagArr = [],
    isEnd = false,
    i, j, leftCnt;

  for (i = 0; i < m.length; i++) {
    flagArr[i] = i < n ? 1 : 0;
  }

  resultArrs.push(flagArr.concat());

  while (!isEnd) {
    leftCnt = 0;
    for (i = 0; i < m.length - 1; i++) {
      if (flagArr[i] == 1 && flagArr[i + 1] == 0) {
        for (j = 0; j < i; j++) {
          flagArr[j] = j < leftCnt ? 1 : 0;
        }
        flagArr[i] = 0;
        flagArr[i + 1] = 1;
        var aTmp = flagArr.concat();
        resultArrs.push(aTmp);
        if (aTmp.slice(-n).join("").indexOf('0') == -1) {
          isEnd = true;
        }
        break;
      }
      flagArr[i] == 1 && leftCnt++;
    }
  }

  for (let i = 0; i < resultArrs.length; i++) {
    let result = resultArrs[i];
    for (let j = 0; j < result.length; j++) {
      if (+result[j]) {
        result[j] = m[j];
      }
    }
  }

  for (let i = 0; i < resultArrs.length; i++) {
    let result = resultArrs[i];
    resultArrs[i] = result.filter(item => +item !== 0)
  }
  
  resultStr = resultArrs.join('-').replace(/[\[\]]/g, '');
  
  return resultStr;
}
