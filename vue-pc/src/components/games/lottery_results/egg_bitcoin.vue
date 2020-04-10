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
            <span style="color: #3897FF;">{{item.game_log_info.part_one_result}}，</span>
            <span style="color: #F5A623;">{{item.game_log_info.part_two_result}}，</span>
            <span style="color: #FF4C20;">{{item.game_log_info.part_three_result}}</span>
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
          <td>取前16位数字：</td>
          <td colspan="3" class="notshow" style="">
            {{item.game_log_info.sixteen_hash}}
          </td>
        </tr>
        <tr>
          <td>10进位转换：</td>
          <td colspan="3" class="notshow" style="">
            {{item.game_log_info.ten_hash}}
          </td>
        </tr>
        <tr>
          <td>除以2的64次方：</td>
          <td colspan="3" class="notshow" style="">
            {{item.game_log_info.sub_result}}
          </td>
        </tr>
        <tr class="tr">
          <td>结果</td>
          <td>
            <div class="num_img">
              <img :src="number_one_img" alt />
            </div>
          </td>
          <td>
            <div class="num_img">
              <img :src="number_two_img" alt />
            </div>
          </td>
          <td>
            <div class="num_img">
              <img :src="number_three_img" alt />
            </div>
          </td>
        </tr>
        <tr>
          <td>开奖</td>
          <td colspan="3">
            <ul>
              <li>
                <img :src="number_one_img" alt />
              </li>
              <span>+</span>
              <li>
                <img :src="number_two_img" alt />
              </li>
              <span>+</span>
              <li>
                <img :src="number_three_img" alt />
              </li>
              <span
                v-if="item.game_log_info.game_type_id != 4 && item.game_log_info.game_type_id != 25"
              >=</span>
              <li
                class="gentle"
                v-if="item.game_log_info.game_type_id != 4 && item.game_log_info.game_type_id != 25"
              >{{item.game_log_info.result.split(',')[0]}}</li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
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
  created () {
    this.number_one_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_one_result + '@2x.png')
    this.number_two_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_two_result + '@2x.png')
    this.number_three_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_three_result + '@2x.png')
    let number = this.item.result
    if (this.item.game_log_info.game_type_id == 11 || this.item.game_log_info.game_type_id == 8 || this.item.game_log_info.game_type_id == 14 || this.item.game_log_info.game_type_id == 23 || this.item.game_log_info.game_type_id == 24 || this.item.game_log_info.game_type_id == 25 || this.item.game_log_info.game_type_id == 26 || this.item.game_log_info.game_type_id == 30 || this.item.game_log_info.game_type_id == 31 || this.item.game_log_info.game_type_id == 33 || this.item.game_log_info.game_type_id == 35 || this.item.game_log_info.game_type_id == 36) {
      let arr = []
      arr = number.split(',')
      let firstArr = []
      let secondArr = []
      let thirdArr = []
      for (let i = 1; i < arr.length; i += 3) {
        firstArr.push(arr[i])
      }
      for (let i = 2; i < arr.length; i += 3) {
        secondArr.push(arr[i])
      }
      for (let i = 3; i < arr.length; i += 3) {
        thirdArr.push(arr[i])
      }
      this.first_num = firstArr.join(',')
      this.second_num = secondArr.join(',')
      this.third_num = thirdArr.join(',')
      return
    }
    if (this.item.game_log_info.game_type_id == 13 || this.item.game_log_info.game_type_id == 22 || this.item.game_log_info.game_type_id == 32) {
      let arr = []
      arr = number.split(',')
      let firstArr = []
      let secondArr = []
      let thirdArr = []
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i])
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i])
      }
      for (let i = 2; i < arr.length; i += 3) {
        thirdArr.push(arr[i])
      }
      this.first_num = firstArr.join(',')
      this.second_num = secondArr.join(',')
      this.third_num = thirdArr.join(',')
      return
    }
    if (this.item.game_log_info.game_type_id == 38) {
      let arr = []
      arr = number.split(',')
      let firstArr = []
      let secondArr = []
      let thirdArr = []
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i])
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i])
      }
      for (let i = 2; i < arr.length; i += 3) {
        thirdArr.push(arr[i])
      }
      this.first_num = firstArr.join(',')
      this.second_num = secondArr.join(',')
      this.third_num = thirdArr.join(',')
      return
    }
    if (this.item.game_log_info.game_type_id == 41) {
      let arr = []
      arr = number.split(',')
      let firstArr = []
      let secondArr = []
      let thirdArr = []
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i])
      }
      for (let i = 1; i < arr.length; i += 3) {
        thirdArr.push(arr[i])
      }
      for (let i = 2; i < arr.length; i += 3) {
        secondArr.push(arr[i])
      }
      this.first_num = firstArr.join(',')
      this.second_num = secondArr.join(',')
      this.third_num = thirdArr.join(',')
      return
    }
    if (this.item.game_log_info.game_type_id == 40) {
      let arr = []
      arr = number.split(',')
      let firstArr = []
      let secondArr = []
      let thirdArr = []
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i])
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i])
      }
      this.first_num = firstArr.join(',')
      this.second_num = secondArr.join(',')
      this.third_num = number
      return
    }
    this.first_num = number.substr(0, 17)
    this.second_num = number.substr(18, 17)
    this.third_num = number.substr(36, 17)
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
}
</style>