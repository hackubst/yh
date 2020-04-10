<template>
  <div id="egg_28">
    <div class="head_top">
      <div class="title">{{game_type_name}} 第{{item.issue}}期开奖结果</div>
      <div class="close" @click="close()">关闭</div>
    </div>
    <table class="game_table game_egg" cellspacing="0px" style="border-collapse:collapse;">
      <tbody>
        <tr>
          <td colspan="4">{{game_type_name}} 第{{item.issue}}期开奖结果</td>
        </tr>
        <tr>
          <td width="100">开奖时间</td>
          <td colspan="3">{{item.addtime | formatDateYearTime}}</td>
        </tr>
        <tr>
          <td width="100">开奖号码</td>
          <td colspan="3">
            <span class="numberl" :class="'n' + (index + 1)" v-for="(num, index) in getStrArr(item.result)" :key="index">{{num}}</span>
          </td>
        </tr>
        <tr>
          <td width="100">号源</td>
          <td colspan="3">
            <div class="MHover">
              {{item.hash_total}}
            </div>
          </td>
        </tr>
        <tr>
          <td width="100">完整号源</td>
          <td colspan="3" class="hash-rank">
            <span>{{item.hash_one}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + item.hash_one" target="_blank">检验</a></span>
            <span>{{item.hash_two}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + item.hash_two" target="_blank">检验</a></span>
            <span>{{item.hash_three}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + item.hash_three" target="_blank">检验</a></span>
          </td>
        </tr>
        <tr>
          <td>SHA256转化值：</td>
          <td colspan="3" class="notshow" style="">
            {{item.hash_new}}
          </td>
        </tr>
        <tr>
          <td>取前60位数字&nbsp;&nbsp;&nbsp;&nbsp;<br>分为10份：</td>
          <td class="notshow" style="border-collapse: collapse;" colspan="3">
            <table style="width: 100%; border-collapse:collapse;" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td v-for="data1 in sixteen_data.sixteen_data1" :key="data1.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data1.key}}</span>
                    <span class="value" width="60%">{{data1.result}}</span>
                  </td>
                </tr>
                <tr>
                  <td v-for="data2 in sixteen_data.sixteen_data2" :key="data2.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data2.key}}</span>
                    <span class="value" width="60%">{{data2.result}}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td>每份16进制&nbsp;&nbsp;&nbsp;&nbsp;<br>转化为10进制：</td>
          <td class="notshow" style="border-collapse: collapse;" colspan="3">
            <table style="width: 100%; border-collapse:collapse;" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td v-for="data1 in ten_data.ten_data1" :key="data1.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data1.key}}</span>
                    <span class="value" width="60%">{{data1.result}}</span>
                  </td>
                </tr>
                <tr>
                  <td v-for="data2 in ten_data.ten_data2" :key="data2.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data2.key}}</span>
                    <span class="value" width="60%">{{data2.result}}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr>
          <td>对十份数值进&nbsp;&nbsp;&nbsp;&nbsp;<br>行大小排序：</td>
          <td class="notshow" style="border-collapse: collapse;" colspan="3">
            <table style="width: 100%; border-collapse:collapse;" cellpadding="0" cellspacing="0">
              <tbody>
                <tr>
                  <td v-for="data1 in result_data.result_data1" :key="data1.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data1.key}}</span>
                    <span class="value" width="60%">{{data1.result}}</span>
                  </td>
                </tr>
                <tr>
                  <td v-for="data2 in result_data.result_data2" :key="data2.result" style="width: 20%; display: block; float: left;">
                    <span class="key" width="40%">{{data2.key}}</span>
                    <span class="value" width="60%">{{data2.result}}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
        <tr class="tr">
          <td>得出赛车号码顺序：</td>
          <td colspan="3">
            <span class="numberl" v-for="(num, index) in getStrArr(item.result)" :key="index">{{num}}</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import { filterNum, defalutImg } from "@/config/mixin.js";
export default {
  name: "egg_28",
  props: {
    item: {
      type: Object,
      default: {}
    },
    game_type_name: {
      type: String,
      default: ''
    }
  },
  mixins: [filterNum, defalutImg],
  data () {
    return {
      number_one_img: '',
      number_two_img: '',
      number_three_img: '',
      first_num: '',
      second_num: '',
      third_num: ''
    };
  },
  computed: {
    sixteen_data () {
      let lists = JSON.parse(this.item.game_log_info.sixteen_data)
      let sixteen_dataObj = {}
      let sixteen_data1 = []
      let sixteen_data2 = []
      for (let i = 0; i < lists.length; i++) {
        if (i < 5) {
          sixteen_data1.push(lists[i])
        } else {
          sixteen_data2.push(lists[i])
        }
      }
      sixteen_dataObj.sixteen_data1 = sixteen_data1
      sixteen_dataObj.sixteen_data2 = sixteen_data2
      console.log()
      return sixteen_dataObj
    },
    ten_data () {
      let lists = JSON.parse(this.item.game_log_info.ten_data)
      let ten_dataObj = {}
      let ten_data1 = []
      let ten_data2 = []
      for (let i = 0; i < lists.length; i++) {
        if (i < 5) {
          ten_data1.push(lists[i])
        } else {
          ten_data2.push(lists[i])
        }
      }
      ten_dataObj.ten_data1 = ten_data1
      ten_dataObj.ten_data2 = ten_data2
      console.log()
      return ten_dataObj
    },
    result_data () {
      let lists = JSON.parse(this.item.game_log_info.result_data)
      let result_dataObj = {}
      let result_data1 = []
      let result_data2 = []
      for (let i = 0; i < lists.length; i++) {
        if (i < 5) {
          result_data1.push(lists[i])
        } else {
          result_data2.push(lists[i])
        }
      }
      result_dataObj.result_data1 = result_data1
      result_dataObj.result_data2 = result_data2
      console.log()
      return result_dataObj
    }
  },
  created () {
  },
  methods: {
    close () {
      this.$emit('close')
    }
  }
}
</script>

<style scoped lang='less'>
#egg_28 {
  .wh(100%, auto);
  .head_top {
    .wh(100%, 24px);
    padding: 7px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffefd4;
    .title {
      margin-left: 15px;
      .sc(16px, #4a4130);
      font-weight: 600;
    }
    .close {
      margin-right: 15px;
      font-size: 16px;
      .sc(16px, #4a4130);
      font-weight: 600;
    }
  }
  .game_egg {
    width: 100%;
    table-layout: fixed;
    td {
      vertical-align: middle;
    }
    .num_img {
      .wh(100%, 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    ul {
      .wh(100%, 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      li {
        display: flex;
        justify-content: center;
        align-items: center;
        img {
          margin: 10px;
        }
      }
      .gentle {
        width: 35px;
        height: 35px;
        background: linear-gradient(
          180deg,
          rgba(249, 212, 35, 1) 0%,
          rgba(255, 78, 80, 1) 100%
        );
        font-size: 25px;
        font-family: Futura-Bold;
        font-weight: bold;
        color: rgba(255, 255, 255, 1);
        line-height: 35px;
        border-radius: 50%;
        margin-left: 10px;
      }
    }
    .gentle {
      width: 35px;
      height: 35px;
      background: linear-gradient(
        180deg,
        rgba(249, 212, 35, 1) 0%,
        rgba(255, 78, 80, 1) 100%
      );
      font-size: 20px;
      font-family: Futura-Bold;
      color: rgba(255, 255, 255, 1);
      line-height: 35px;
      border-radius: 50%;
      display: inline-block;
      text-align: center;
      margin-left: 25px;
      margin-right: 10px;
    }
  }
  .MHover {
    word-break:break-all;
  }
  .numberl:after{
    content: ',';
    margin-right: 5px;
  }
  .numberl:last-child:after {
    content: '';
    margin: 0;
  }
  .MHover {
    word-break:break-all;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
  }
  .hash-rank {
    line-height: 2;
    span {
      .check-btn {
        color: #D1913C;
      }
    }
  }
  .notshow {
    .key, .value {
      display: block;
      float: left
    }
    .key {
      width: 35%;
      border-right: 1px solid #e8e8e8;
    }
    .value {
      width: 64%;
    }
  }
}
</style>