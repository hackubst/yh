import axios from 'axios'
import SID from './sid'
import qs from 'qs';
import MD5 from "@/config/md5";
import {
  removeStore,
  JsonSort
} from './config/utils'

//  默认请求地址
// axios.defaults.baseURL = "/api"
// axios.defaults.baseURL = 'http://www.jinlong28.com/api/api' // 正式服
// axios.defaults.baseURL = 'http://kkl.121.easysoft168.com/api/api' // 正式体验服
axios.defaults.baseURL = 'http://kkl.121.soft1024.com/api/api' // 测试服
// axios.defaults.baseURL = 'http://kklty.121.soft1024.com/api/api' // 测试体验服

//在main.js设置全局的请求次数，请求的间隙
axios.defaults.retry = 5;
axios.defaults.retryDelay = 1000;

axios.interceptors.response.use(undefined, function axiosRetryInterceptor(err) {
    var config = err.config;
    // If config does not exist or the retry option is not set, reject
    if(!config || !config.retry) return Promise.reject(err);

    // Set the variable for keeping track of the retry count
    config.__retryCount = config.__retryCount || 0;

    // Check if we've maxed out the total number of retries
    if(config.__retryCount >= config.retry) {
        // Reject with the error

        alert('网络延缓，请刷新重试');
        return Promise.reject(err);
    }

    // Increase the retry count
    config.__retryCount += 1;

    // Create new promise to handle exponential backoff
    var backoff = new Promise(function(resolve) {
        setTimeout(function() {
            resolve();
        }, config.retryDelay || 1);
    });

    // Return the promise in which recalls axios to retry the request
    return backoff.then(function() {
        return axios(config);
    });
});

var FormDataRemark = function (data) {
  // var data = Object.assign({
  //   appid: 1,
  //   token: 'eb86fa064482989312e2a1557ddb4032',
  //   // PHPSESSID: '05o1049sbe5enjbc1r34k2lhc0'
  //   PHPSESSID: SID.sid
  // }, data)
  var data = Object.assign({
    appid: 1,
    // PHPSESSID: '05o1049sbe5enjbc1r34k2lhc0'
    // PHPSESSID: 'h4cg6n944gjesspbs4p080k9j0',
    PHPSESSID: '1sgn0pu5ke4le6oddgg8fpnot7'
    // PHPSESSID: SID.sid
  }, data)
  let encrypt_data = {};
  let timestamp = Math.floor(+new Date() / 1000);
  encrypt_data = {
    appid: 1,
    api_name: data.api_name,
    timestamp: timestamp
  };
  // console.log({encrypt_data});
  const my_data = JsonSort(encrypt_data);
  let token = qs.stringify(my_data) + "N7&7WY8m6%q@J4*AjvB^A96s9_p+z-h=";
  let encrypt_token = MD5.hex_md5(token);
  data.token = encrypt_token;
  data.timestamp = timestamp;
  var data1 = new FormData()
  for (var key in data) {
    if (typeof data[key] == 'object') {
      data1.append(key, JSON.stringify(data[key]))
    } else {
      data1.append(key, data[key])
    }
  }
  return data1
}

function Apifn(data, cb) {
  axios.post('', FormDataRemark(data)).then(res => {
    if (res.data.code == 0) {
      cb(null, res.data)
    } else {
      if (res.data.code == 40011) {
        removeStore('userInfo')
        removeStore('haveLogin')
        if (this.$route.path == '/index') {
          cb(null, res.data)
        } else {
          cb(res.data, res.data)
          // this.$native.native_login()
          this.$router.replace({
            path: '/login'
          })
        }
      } else if (res.data.code == 40022) {
        removeStore('userInfo')
        removeStore('haveLogin')
        this.$router.replace({
          path: '/login'
        })
        // this.$message.error('您的账号已在其他地方登录，你将被踢下线！');
      } else {
        cb(res.data, res.data)
      }
    }
  })
}

// function Apifn(data, cb) {
//   axios.post('', FormDataRemark(data)).then(res => {
//     if (res.data.code == 0) {
//       cb(null, res.data)
//     } else if (res.data.code == 40011) {
//       removeStore('userInfo')
//       removeStore('haveLogin')
//       this.$router.replace({
//         path: '/login'
//       })
//     } else if (res.data.code == 40022) {
//       removeStore('userInfo')
//       removeStore('haveLogin')
//       this.$router.replace({
//         path: '/login'
//       })
//       this.$message.error('您的账号已在其他地方登录，你将被踢下线！');
//     } else {
//       cb(res.data, res.data)
//     }
//   }).catch(err => {
//     cb(err, err)
//   })
// }

export default Apifn
