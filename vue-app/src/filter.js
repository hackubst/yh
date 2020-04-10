let filters = {
    formatDate: time => {
      return time ?
        (time * 1000 / (1000 * 60 * 60 * 24 * 365) == new Date().getFullYear()) ?
        new Date(time * 1000).format('yyyy-MM-dd hh:mm') :
        new Date(time * 1000).format('MM-dd hh:mm') : '未知时间'
    },
    formatDateYear: time => {
      return time ? new Date(time * 1000).format('yyyy/MM/dd') : '未知时间'
    },
    formatDateYearStyle: time => {
      return time ? new Date(time * 1000).format('yyyy-MM-dd') : '未知时间'
    },
    formatDateYearTime: time => {
      return time ? new Date(time * 1000).format('yyyy-MM-dd hh:mm:ss') : '未知时间'
    },
    formatDateYearTimeMin: time => {
      return time ? new Date(time * 1000).format('yyyy-MM-dd hh:mm') : '未知时间'
    },
    formatDateYearDot: time => {
      return time ? new Date(time * 1000).format('yyyy.MM.dd') : '未知时间'
    },
    formatDateMonth: time => {
      return time ? new Date(time * 1000).format('MM-dd hh:mm:ss') : '未知时间'
    },
    formatDateMonthDot: time => {
      return time ? new Date(time * 1000).format('MM.dd') : '未知时间'
    },
    formatDateFont: time => {
      return time ? new Date(time * 1000).format('yyyy年MM月dd日 hh:mm:ss') : '未知时间'
    },
    formatTime: time => {
      return time ? new Date(time * 1000).format('MM月dd日 hh:mm') : '未知时间'
    },
    formatTimeDay: time => {
      return time ? new Date(time * 1000).format('hh:mm:ss') : '未知时间'
    },
    // 将数据用逗号分隔    999999999 ->   999,999,999  
    changeBigNum: num => {
      if(!num){
        return 0
      }
      var str = parseInt(num).toString();
      var reg = str.indexOf(".") > -1 ? /(\d)(?=(\d{3})+\.)/g : /(\d)(?=(?:\d{3})+$)/g;
      return str.replace(reg,"$1,"); 
    }
  }
  
  Date.prototype.format = function (fmt) {
    var o = {
      "M+": this.getMonth() + 1, //月份   
      "d+": this.getDate(), //日   
      "h+": this.getHours(), //小时   
      "m+": this.getMinutes(), //分   
      "s+": this.getSeconds(), //秒   
      "q+": Math.floor((this.getMonth() + 3) / 3), //季度   
      "S": this.getMilliseconds() //毫秒   
    };
    if (/(y+)/.test(fmt))
      fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
      if (new RegExp("(" + k + ")").test(fmt))
        fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
  };
  export default filters