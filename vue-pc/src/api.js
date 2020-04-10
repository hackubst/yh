import axios from 'axios'
import qs from 'qs';
import MD5 from "@/config/md5";
import {
  removeStore,
  JsonSort
} from './config/utils'
//  默认请求地址
// axios.defaults.baseURL = "/api"
// axios.defaults.baseURL = 'http://app.jinlong28.com/api/api'
// axios.defaults.baseURL = 'http://www.jinlong28.com/api/api' // 正式服
// axios.defaults.baseURL = 'http://kkl.121.easysoft168.com/api/api' // 正式体验服
axios.defaults.baseURL = 'http://kkl.121.soft1024.com/api/api' // 测试服
// axios.defaults.baseURL = 'http://kklty.121.soft1024.com/api/api' // 测试体验服


var FormDataRemark = function (data) {
    // 数据拷贝，备份
    // var data = Object.assign({
    //   appid: 1,
    //   token: 'eb86fa064482989312e2a1557ddb4032',
    // }, data)
    var data = Object.assign({
      appid: 1,
      // PHPSESSID: 'h4cg6n944gjesspbs4p080k9j0'
    }, data)
    let encrypt_data = {};
    let timestamp = Math.floor(+new Date() / 1000);
    encrypt_data = {
      appid: 1,
      api_name: data.api_name,
      timestamp: timestamp
    };
    console.log({encrypt_data});
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

function Apifn(data, cb){
    axios.post('', FormDataRemark(data)).then(res => {
        if(res.data.code == 0){
           cb(null, res.data)
        }else if(res.data.code == 40011){
           removeStore('userInfo')
           removeStore('haveLogin')
           this.$router.replace({
             path: '/login'
           })
        } else if (res.data.code == 40022) {
          removeStore('userInfo')
          removeStore('haveLogin')
          this.$router.replace({
            path: '/login'
          })
          this.$message.error('您的账号已在其他地方登录，你将被踢下线！');
        } else{
          cb(res.data, res.data)
        }
    }).catch(err =>{
        cb(err, err)
    })
}

export default Apifn
