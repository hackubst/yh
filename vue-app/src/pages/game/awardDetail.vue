<template>
  <div class="awardDetail">
    <div class="title flex-center">
      <span>{{game_name}}</span>
      <span>第{{detail_info.issue}}期开奖结果</span>
    </div>
    <!-- 比特币系列（1分28） -->
    <div class="info_box" v-if="detail_info.game_log_info.game_type_id == 45">
      <div class="info_item flex">
        <div class="item_left">时间</div>
        <div class="item_right">{{detail_info.open_time | formatDateYearTime}}</div>
      </div>
      <div class="info_item">
        <div class="item_left">号码</div>
        <div class="item_right">
          <span style="color: #1979FF;">{{detail_info.game_log_info.part_one_result}},</span>
          <span style="color: #FF1E1E;">{{detail_info.game_log_info.part_two_result}},</span>
          <span style="color: #FF851E;">{{detail_info.game_log_info.part_three_result}}</span>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">号源</div>
        <div class="item_right break_all">
          <span>{{detail_info.hash_total}}</span>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">完整号源</div>
        <div class="item_right break_all">
          <p>{{detail_info.hash_one}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_one" target="_blank">检验</a></p>
          <p>{{detail_info.hash_two}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_two" target="_blank">检验</a></p>
          <p>{{detail_info.hash_three}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_three" target="_blank">检验</a></p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">SHA256转化值</div>
        <div class="item_right break_all">
          <p>{{detail_info.hash_new}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">取前16位数字</div>
        <div class="item_right break_all">
          <p>{{detail_info.game_log_info.sixteen_hash}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">取10进位转换</div>
        <div class="item_right break_all">
          <p>{{detail_info.game_log_info.ten_hash}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">除以2的64次方</div>
        <div class="item_right break_all">
          <p>{{detail_info.game_log_info.sub_result}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">结果</div>
        <div class="item_right flex">
          <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>
          <div
            class="ball"
            v-if="detail_info.game_log_info.part_two_result && detail_info.game_log_info.game_type_id != 10 && detail_info.game_log_info.game_type_id != 12 && detail_info.game_log_info.game_type_id != 21 && detail_info.game_log_info.game_type_id != 42"
          >{{detail_info.game_log_info.part_two_result}}</div>
          <div
            class="ball"
            v-if="detail_info.game_log_info.part_three_result"
          >{{detail_info.game_log_info.part_three_result}}</div>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">开奖</div>
        <div class="item_right flex">
          <span style="color: #1979FF;">{{detail_info.game_log_info.part_one_result}} + </span>
          <span style="color: #FF1E1E; margin-left: 5px;">{{detail_info.game_log_info.part_two_result}} + </span>
          <span style="color: #FF851E; margin-left: 5px;">{{detail_info.game_log_info.part_three_result}} = </span>
          <div class="ball">{{detail_info.result}}</div>
        </div>
      </div>
    </div>
    
    <!-- 赛车系列 -->
    <div class="info_box" v-if="detail_info.game_log_info.game_type_id == 46 || detail_info.game_log_info.game_type_id == 51 || detail_info.game_log_info.game_type_id == 57">
      <div class="info_item flex">
        <div class="item_left">时间</div>
        <div class="item_right">{{detail_info.open_time | formatDateYearTime}}</div>
      </div>
      <div class="info_item">
        <div class="item_left">号码</div>
        <div class="item_right">
          <span class="numberl" :class="'n' + (index + 1)" v-for="(num, index) in getStrArr(detail_info.result)" :key="index">{{num}}</span>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">号源</div>
        <div class="item_right break_all">
          <span>{{detail_info.hash_total}}</span>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">完整号源</div>
        <div class="item_right break_all">
          <p>{{detail_info.hash_one}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_one" target="_blank">检验</a></p>
          <p>{{detail_info.hash_two}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_two" target="_blank">检验</a></p>
          <p>{{detail_info.hash_three}} <a class="check-btn" :href="'https://www.blockchain.com/btc/tx/' + detail_info.hash_three" target="_blank">检验</a></p>
        </div>
      </div>
      <div class="info_item ">
        <div class="item_left limit-width">SHA256转化值</div>
        <div class="item_right break_all">
          <p>{{detail_info.hash_new}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left limit-width">取前60位数字分为10份</div>
        <div class="item_right break_all">
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
        </div>
      </div>
      <div class="info_item">
        <div class="item_left limit-width">每份16进制转化为10进制</div>
        <div class="item_right break_all">
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
        </div>
      </div>
      <div class="info_item">
        <div class="item_left limit-width">对10份数值进行大小排序</div>
        <div class="item_right break_all">
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
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">结果</div>
        <div class="item_right flex">
          <span class="numberl" v-for="(num, index) in getStrArr(detail_info.result)" :key="index">{{num}}</span>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">开奖</div>
        <div class="item_right flex">
          <span class="numberl" v-for="(num, index) in getStrArr(detail_info.result)" :key="index">{{num}}</span>
        </div>
      </div>
    </div>
    <!-- 韩国 10 -->
    <div class="info_box" v-else-if="detail_info.game_log_info.game_type_id == 34">
      <div class="info_item flex">
        <div class="item_left">时间</div>
        <div class="item_right">{{detail_info.open_time | formatDateYearTime}}</div>
      </div>
      <div class="info_item">
        <div class="item_left">号码</div>
        <div class="item_right">
          <p style="color: #1979FF;">{{first_num_result}}</p>
          <p style="color: #FF1E1E;">{{second_num_result}}</p>
          <p style="color: #FF851E;">{{third_num_result}}</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">前7个数字</div>
        <div class="item_right">[第1/2/3/4/5/6/7位数字]</div>
      </div>
      <div class="info_item">
        <div class="item_left">数字</div>
        <div class="item_right">
          <p style="color: #1979FF;">{{first_num}}</p>
        </div>
      </div>
      <div class="info_item flex">
        <div class="item_left">求和</div>
        <div class="item_right">{{detail_info.game_log_info.part_one_sum}}</div>
      </div>
      <div class="info_item flex">
        <div class="item_left">计算</div>
        <div class="item_right">取尾数+1</div>
      </div>
      <div class="info_item flex">
        <div class="item_left">开奖</div>
        <div class="item_right flex">
          <div class="ball">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
        </div>
      </div>
    </div>
    <!-- qita -->
    <div class="info_box" v-else>
      <div class="info_item flex">
        <div class="item_left">时间</div>
        <div class="item_right">{{detail_info.open_time | formatDateYearTime}}</div>
      </div>
      <div class="info_item">
        <div class="item_left">号码</div>
        <div class="item_right">
          <p style="color: #1979FF;">{{first_num_result}}</p>
          <p style="color: #FF1E1E;">{{second_num_result}}</p>
          <p style="color: #FF851E;">{{third_num_result}}</p>
        </div>
      </div>
      <div class="info_item"
        v-if="detail_info.game_log_info.game_type_id == 1 || detail_info.game_log_info.game_type_id == 2 || detail_info.game_log_info.game_type_id == 3 || detail_info.game_log_info.game_type_id == 4 || detail_info.game_log_info.game_type_id == 5 || detail_info.game_log_info.game_type_id == 9 || detail_info.game_log_info.game_type_id == 15 || detail_info.game_log_info.game_type_id == 27  || detail_info.game_log_info.game_type_id == 44"
      >
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/2/3/4/5/6位数字]</p>
          <p>第二区位[第7/8/9/10/11/12位数字]</p>
          <p>第三区位[第13/14/15/16/17/18位数字]</p>
        </div>
      </div>
      <div class="info_item"
        v-if="detail_info.game_log_info.game_type_id == 8 || detail_info.game_log_info.game_type_id == 11 || detail_info.game_log_info.game_type_id == 14 || detail_info.game_log_info.game_type_id == 23 || detail_info.game_log_info.game_type_id == 24 || detail_info.game_log_info.game_type_id == 25 || detail_info.game_log_info.game_type_id == 26 || detail_info.game_log_info.game_type_id == 30 || detail_info.game_log_info.game_type_id == 31  || detail_info.game_log_info.game_type_id == 33 || detail_info.game_log_info.game_type_id == 35 || detail_info.game_log_info.game_type_id == 36 || detail_info.game_log_info.game_type_id == 40"
      >
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第2/5/8/11/14/17位数字]</p>
          <p>第二区位[第3/6/9/12/15/18位数字]</p>
          <p>第三区位[第4/7/10/13/16/19位数字]</p>
        </div>
      </div>
      <div class="info_item"
        v-if="detail_info.game_log_info.game_type_id == 13 || detail_info.game_log_info.game_type_id == 22 || detail_info.game_log_info.game_type_id == 32"
      >
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7/10/13/16位数字]</p>
          <p>第二区位[第2/5/8/11/14/17位数字]</p>
          <p>第三区位[第3/6/9/12/15/18位数字]</p>
        </div>
      </div>
      <div class="info_item" v-if="detail_info.game_log_info.game_type_id == 40">
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7位数字]</p>
          <p>第二区位[第2/5/8位数字]</p>
          <p>第三区位[第1/2/3/4/5/6/7/8位数字]</p>
        </div>
      </div>
      <div class="info_item" v-if="detail_info.game_log_info.game_type_id == 38">
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7位数字]</p>
          <p>第二区位[第2/5/8位数字]</p>
          <p>第三区位[第3/6/9位数字]</p>
        </div>
      </div>
      <div class="info_item" v-if="detail_info.game_log_info.game_type_id == 41">
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7位数字]</p>
          <p>第二区位[第3/6/9位数字]</p>
          <p>第三区位[第2/5/8位数字]</p>
        </div>
      </div>
      <div class="info_item" v-if="detail_info.game_log_info.game_type_id == 10">
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/2/3/4/5/6位数字]</p>
          <p>第二区位[第13/14/15/16/17/18位数字]</p>
        </div>
      </div>
      <div class="info_item"
        v-if="detail_info.game_log_info.game_type_id == 12 || detail_info.game_log_info.game_type_id == 21"
      >
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7/10/13/16位数字]</p>
          <p>第二区位[第3/6/9/12/15/18位数字]</p>
        </div>
      </div>
      <div class="info_item" v-if="detail_info.game_log_info.game_type_id == 42">
        <div class="item_left">区位</div>
        <div class="item_right">
          <p>第一区位[第1/4/7位数字]</p>
          <p>第三区位[第2/5/8位数字]</p>
        </div>
      </div>
      <div class="info_item">
        <div class="item_left">数字</div>
        <div class="item_right">
          <p style="color: #1979FF;">{{first_num}}</p>
          <p style="color: #FF1E1E;">{{second_num}}</p>
          <p
            v-if="detail_info.game_log_info.game_type_id != 10 && detail_info.game_log_info.game_type_id != 12 && detail_info.game_log_info.game_type_id != 21 && detail_info.game_log_info.game_type_id != 42"
            style="color: #FF851E;"
          >{{third_num}}</p>
        </div>
      </div>
      <div class="info_item flex" v-if="detail_info.game_log_info.game_type_id != 43">
        <div class="item_left">求和</div>
        <div
          class="item_right"
          v-if="detail_info.game_log_info.game_type_id == 10 || detail_info.game_log_info.game_type_id == 12 || detail_info.game_log_info.game_type_id == 21 || detail_info.game_log_info.game_type_id == 42"
        >{{detail_info.game_log_info.part_one_sum}}; {{detail_info.game_log_info.part_two_sum}}</div>
        <div
          class="item_right"
          v-else
        >{{detail_info.game_log_info.part_one_sum}}; {{detail_info.game_log_info.part_two_sum}}; {{detail_info.game_log_info.part_three_sum}}</div>
      </div>
      <div class="info_item flex" v-if="detail_info.game_log_info.game_type_id != 43">
        <div class="item_left">计算</div>
        <div
          class="item_right"
          v-if="detail_info.game_log_info.game_type_id == 9 || detail_info.game_log_info.game_type_id == 13 || detail_info.game_log_info.game_type_id == 22 || detail_info.game_log_info.game_type_id == 32"
        >
          <p>{{detail_info.game_log_info.part_one_sum}}除以6的余数 + 1</p>
          <p>{{detail_info.game_log_info.part_two_sum}}除以6的余数 + 1</p>
          <p>{{detail_info.game_log_info.part_three_sum}}除以6的余数 + 1</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 10 || detail_info.game_log_info.game_type_id == 12 || detail_info.game_log_info.game_type_id == 21 || detail_info.game_log_info.game_type_id == 42"
        >
          <p>{{detail_info.game_log_info.part_one_sum}}除以6的余数 + 1</p>
          <p>{{detail_info.game_log_info.part_two_sum}}除以6的余数 + 1</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 16 || detail_info.game_log_info.game_type_id == 52"
        >
          <p>取期号尾数，尾数对应第几位数字位开奖号码，如果尾数为0取第10位</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 17 || detail_info.game_log_info.game_type_id == 53"
        >
          <p>取开奖号码前3位之和</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 18 || detail_info.game_log_info.game_type_id == 54"
        >
          <p>开奖取首位</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 19 || detail_info.game_log_info.game_type_id == 55"
        >
          <p>取第一位和最后一位比较，第一位大为龙，最后一位大为虎</p>
        </div>
        <div
          class="item_right"
          v-else-if="detail_info.game_log_info.game_type_id == 20 || detail_info.game_log_info.game_type_id == 56"
        >
          <p>取开奖号码前两位之和</p>
        </div>
        <div class="item_right" v-else>取尾数; 取尾数; 取尾数</div>
      </div>
      <div class="info_item flex" v-if="detail_info.game_log_info.game_type_id != 43">
        <div class="item_left">结果</div>
        <div class="item_right flex" v-if="detail_info.game_log_info.game_type_id == 17 || detail_info.game_log_info.game_type_id == 53">
          <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>+
          <div
            class="ball"
          >{{detail_info.game_log_info.part_two_result}}</div>+
          <div
            class="ball"
          >{{detail_info.game_log_info.part_three_result}}</div>=
          <div class="ball">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
        </div>
        <div class="item_right flex" v-else-if="detail_info.game_log_info.game_type_id == 19 || detail_info.game_log_info.game_type_id == 55">
          <template v-if="(detail_info.game_log_info.part_one_result >>> 0) > (detail_info.game_log_info.part_three_result >>> 0)">
            <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>大于
            <div class="ball">{{detail_info.game_log_info.part_three_result}}</div>
          </template>
          <template v-else>
            <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>小于
            <div class="ball">{{detail_info.game_log_info.part_three_result}}</div>
          </template>
        </div>
        <div class="item_right flex" v-else-if="detail_info.game_log_info.game_type_id == 20 || detail_info.game_log_info.game_type_id == 56">
          <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>+
          <div
            class="ball"
          >{{detail_info.game_log_info.part_three_result}}</div>=
          <div class="ball">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
        </div>
        <div class="item_right flex" v-else>
          <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>
          <div
            class="ball"
            v-if="detail_info.game_log_info.part_two_result && detail_info.game_log_info.game_type_id != 10 && detail_info.game_log_info.game_type_id != 12 && detail_info.game_log_info.game_type_id != 21 && detail_info.game_log_info.game_type_id != 42"
          >{{detail_info.game_log_info.part_two_result}}</div>
          <div
            class="ball"
            v-if="detail_info.game_log_info.part_three_result"
          >{{detail_info.game_log_info.part_three_result}}</div>
        </div>
      </div>
      <div class="info_item flex">
        <div class="item_left">开奖</div>
        <div class="item_right flex">
          <!-- 时时彩 -->
          <div class="flex" v-if="detail_info.game_log_info.game_type_id == 43">
            <div class="ball">{{getStrNum(first_num_result, 0)}}</div>
            <div class="ball">{{getStrNum(first_num_result, 1)}}</div>
            <div class="ball">{{getStrNum(first_num_result, 2)}}</div>
            <div class="ball">{{getStrNum(first_num_result, 3)}}</div>
            <div class="ball">{{getStrNum(first_num_result, 4)}}</div>
            <!-- {{detail_info.game_log_info.result}} 33 2 1 2 4 4 3 -->
            <!-- <div class="ball">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 1) == 1">小</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 1) == 2">大</div>

            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 2) == 1">单</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 2) == 2">双</div>

            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 3) == 1">龙</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 3) == 2">虎</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 3) == 3">和</div>

            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 4) == 1">豹</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 4) == 2">顺</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 4) == 3">对</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 4) == 4">半</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 4) == 5">杂</div>

            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 5) == 1">豹</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 5) == 2">顺</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 5) == 3">对</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 5) == 4">半</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 5) == 5">杂</div>

            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 6) == 1">豹</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 6) == 2">顺</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 6) == 3">对</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 6) == 4">半</div>
            <div class="ball" v-if="getStrNum(detail_info.game_log_info.result, 6) == 5">杂</div> -->
          </div>
          <div class="flex" v-else-if="detail_info.game_log_info.game_type_id == 17 || detail_info.game_log_info.game_type_id == 53 || detail_info.game_log_info.game_type_id == 20 || detail_info.game_log_info.game_type_id == 56">
            <div class="ball">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
          </div>
          <div class="flex" v-else-if="detail_info.game_log_info.game_type_id == 19 || detail_info.game_log_info.game_type_id == 55">
            <div class="ball">{{filterTiger(getStrNum(detail_info.game_log_info.result, 0))}}</div>
          </div>
          <div v-else>
            <div class="ball">{{detail_info.game_log_info.part_one_result}}</div>
            <div
              class="ball"
              v-if="detail_info.game_log_info.part_two_result && detail_info.game_log_info.game_type_id != 10 && detail_info.game_log_info.game_type_id != 12 && detail_info.game_log_info.game_type_id != 21 && detail_info.game_log_info.game_type_id != 42"
            >{{detail_info.game_log_info.part_two_result}}</div>
            <div
              class="ball"
              v-if="detail_info.game_log_info.part_three_result"
            >{{detail_info.game_log_info.part_three_result}}</div>
            <div
              class="ball_result"
              v-if="detail_info.game_log_info.game_type_id != 2 && detail_info.game_log_info.game_type_id != 4 && detail_info.game_log_info.game_type_id != 6 && detail_info.game_log_info.game_type_id != 36 && detail_info.game_log_info.game_type_id != 16 && detail_info.game_log_info.game_type_id != 17 && detail_info.game_log_info.game_type_id != 18 && detail_info.game_log_info.game_type_id != 19 && detail_info.game_log_info.game_type_id != 20 && detail_info.game_log_info.game_type_id != 52 && detail_info.game_log_info.game_type_id != 53 && detail_info.game_log_info.game_type_id != 54 && detail_info.game_log_info.game_type_id != 55 && detail_info.game_log_info.game_type_id != 56"
            >{{detail_info.game_log_info.result}}</div>
            <div class="ball_result" v-if="detail_info.game_log_info.game_type_id == 36">{{getStrNum(detail_info.game_log_info.result, 0)}}</div>
            <template v-if="detail_info.game_log_info.game_type_id == 2 || detail_info.game_log_info.game_type_id == 14 || detail_info.game_log_info.game_type_id == 24 || detail_info.game_log_info.game_type_id == 33">
              <div
                class="ball_result"
                style="background: #66ff33;"
                v-if="detail_info.game_log_info.result == 1"
              >豹</div>
              <div
                class="ball_result"
                style="background: #B822DD;"
                v-else-if="detail_info.game_log_info.result == 2"
              >顺</div>
              <div
                class="ball_result"
                style="background: #3C3CC4;"
                v-else-if="detail_info.game_log_info.result == 3"
              >对</div>
              <div
                class="ball_result"
                style="background: #EE1111;"
                v-else-if="detail_info.game_log_info.result == 4"
              >半</div>
              <div
                class="ball_result"
                style="background: #1AE6E6;"
                v-else-if="detail_info.game_log_info.result == 5"
              >杂</div>
            </template>
            <template v-if="detail_info.game_log_info.game_type_id == 58 || detail_info.game_log_info.game_type_id == 59 || detail_info.game_log_info.game_type_id == 60 || detail_info.game_log_info.game_type_id == 61">
              <div
                class="ball_result"
                style="background: #66ff33;"
                v-if="judgeJunko(detail_info.game_log_info.part_one_result, detail_info.game_log_info.part_two_result, detail_info.game_log_info.part_three_result) == 1"
              >豹</div>
              <div
                class="ball_result"
                style="background: #B822DD;"
                v-else-if="judgeJunko(detail_info.game_log_info.part_one_result, detail_info.game_log_info.part_two_result, detail_info.game_log_info.part_three_result) == 2"
              >顺</div>
              <div
                class="ball_result"
                style="background: #3C3CC4;"
                v-else-if="judgeJunko(detail_info.game_log_info.part_one_result, detail_info.game_log_info.part_two_result, detail_info.game_log_info.part_three_result) == 3"
              >对</div>
              <div
                class="ball_result"
                style="background: #EE1111;"
                v-else-if="judgeJunko(detail_info.game_log_info.part_one_result, detail_info.game_log_info.part_two_result, detail_info.game_log_info.part_three_result) == 4"
              >半</div>
              <div
                class="ball_result"
                style="background: #1AE6E6;"
                v-else-if="judgeJunko(detail_info.game_log_info.part_one_result, detail_info.game_log_info.part_two_result, detail_info.game_log_info.part_three_result) == 5"
              >杂</div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { gameMixns, gameTypeMixins } from "@/config/gameMixin.js";
import { resultMixins } from "@/config/resultMixin.js";
export default {
  name: "awardDetail",
  data () {
    return {
      detail_info: "",
      first_num_result: "",
      second_num_result: "",
      third_num_result: "",
      first_num: "",
      second_num: "",
      third_num: "",
      gameTypes: [],
      game_name: ""
    };
  },
  computed: {
    sixteen_data () {
      let lists = JSON.parse(this.detail_info.game_log_info.sixteen_data)
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
      return sixteen_dataObj
    },
    ten_data () {
      let lists = JSON.parse(this.detail_info.game_log_info.ten_data)
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
      return ten_dataObj
    },
    result_data () {
      let lists = JSON.parse(this.detail_info.game_log_info.result_data)
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
      return result_dataObj
    }
  },
  mixins: [gameMixns, gameTypeMixins, resultMixins],
  created () {
    this.getGameName();
    this.detail_info = this.$route.query.item;
    console.log("detail_info", this.detail_info);
    let number = this.detail_info.result;
    // this.first_num_result = number.substr(0, 17);
    // this.second_num_result = number.substr(18, 17);
    // this.third_num_result = number.substr(36, 17);
    this.first_num_result = number.split(',').slice(0, 7).join(',');
    this.second_num_result = number.split(',').slice(7, 14).join(',');
    this.third_num_result = number.split(',').slice(14, 21).join(',');
    if (
      this.detail_info.game_log_info.game_type_id == 11 ||
      this.detail_info.game_log_info.game_type_id == 8 ||
      this.detail_info.game_log_info.game_type_id == 14 ||
      this.detail_info.game_log_info.game_type_id == 23 ||
      this.detail_info.game_log_info.game_type_id == 24 ||
      this.detail_info.game_log_info.game_type_id == 25 ||
      this.detail_info.game_log_info.game_type_id == 26 ||
      this.detail_info.game_log_info.game_type_id == 30 ||
      this.detail_info.game_log_info.game_type_id == 31 ||
      this.detail_info.game_log_info.game_type_id == 33 ||
      this.detail_info.game_log_info.game_type_id == 35 ||
      this.detail_info.game_log_info.game_type_id == 36
    ) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      let thirdArr = [];
      for (let i = 1; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 2; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      for (let i = 3; i < arr.length; i += 3) {
        thirdArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      this.third_num = thirdArr.join(",");
      return;
    }
    if (
      this.detail_info.game_log_info.game_type_id == 13 ||
      this.detail_info.game_log_info.game_type_id == 22 ||
      this.detail_info.game_log_info.game_type_id == 32
    ) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      let thirdArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      for (let i = 2; i < arr.length; i += 3) {
        thirdArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      this.third_num = thirdArr.join(",");
      return;
    }
    if (this.detail_info.game_log_info.game_type_id == 38) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      let thirdArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      for (let i = 2; i < arr.length; i += 3) {
        thirdArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      this.third_num = thirdArr.join(",");
      return;
    }
    if (this.detail_info.game_log_info.game_type_id == 41) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      let thirdArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 1; i < arr.length; i += 3) {
        thirdArr.push(arr[i]);
      }
      for (let i = 2; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      this.third_num = thirdArr.join(",");
      return;
    }
    if (this.detail_info.game_log_info.game_type_id == 40) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      let thirdArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      this.third_num = number;
      return;
    }
    if (
      this.detail_info.game_log_info.game_type_id == 12 ||
      this.detail_info.game_log_info.game_type_id == 21
    ) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 2; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      return;
    }
    if (this.detail_info.game_log_info.game_type_id == 42) {
      let arr = [];
      arr = number.split(",");
      let firstArr = [];
      let secondArr = [];
      for (let i = 0; i < arr.length; i += 3) {
        firstArr.push(arr[i]);
      }
      for (let i = 1; i < arr.length; i += 3) {
        secondArr.push(arr[i]);
      }
      this.first_num = firstArr.join(",");
      this.second_num = secondArr.join(",");
      return;
    }
    if (this.detail_info.game_log_info.game_type_id == 10) {
      this.first_num = number.substr(0, 17);
      this.second_num = number.substr(36, 17);
      return;
    }
    // this.first_num = number.substr(0, 17);
    // this.second_num = number.substr(18, 17);
    // this.third_num = number.substr(36, 17);
    this.first_num = number.split(',').slice(0, 7).join(',');
    this.second_num = number.split(',').slice(7, 14).join(',');
    this.third_num = number.split(',').slice(14, 21).join(',');
  },
  methods: {
    getGameName () {
      this.$Api(
        {
          api_name: "kkl.game.getGameTypeList"
        },
        (err, data) => {
          if (!err) {
            this.gameTypes = data.data;
            this.gameTypes.map((item, index) => {
              item.game_type_list.map((i, idx) => {
                if (
                  i.game_type_id == this.detail_info.game_log_info.game_type_id
                ) {
                  this.game_name = i.game_type_name;
                }
              });
            });
          }
        }
      );
    }
  }
};
</script>
<style scoped lang='less'>
.awardDetail {
  padding-top: 12px;
  box-sizing: border-box;
  .title {
    span:nth-child(1) {
      margin-right: 6px;
    }
    .sc(18px, #333333);
  }
  .info_box {
    width: 351px;
    margin: 12px auto;
    border-radius: 4px;
    border: 1px solid #cccccc;
    .info_item {
      padding: 13px 14px;
      box-sizing: border-box;
      .sc(16px, #333333);
      display: flex;
      .item_left {
        min-width: 40px;
        margin-right: 12px;
        line-height: 22px;
      }
      .item_right {
        p {
          margin-top: 4px;
        }
        p:nth-child(1) {
          margin-top: 0;
        }
      }
    }
    .info_item:nth-child(2n) {
      background: #fff7ec;
    }
  }
}

.numberl:after{
  content: ',';
  margin-right: 5px;
}
.numberl:last-child:after {
  content: '';
  margin: 0;
}
.limit-width {
  width: 20%;
}
</style>