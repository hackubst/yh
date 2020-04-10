<template>
  <!-- 开奖结果 -->
  <div class="winResult">
    <div class="homeTop" v-if="awardResult && awardResult.game_log_info && awardResult.game_result_info && awardResult.game_type_info">
      <div class="tip">
        <p>第{{awardResult.game_result_info.issue}}期</p>
        <p>开奖结果</p>
      </div>
      <div class="result">
        <div class="result_query">
          <!-- (1,2,3,4,5,6 | 7,8,9,10,11,12 | 13,14,15,16,17,18) -->
          <div class="num" v-if="awardResult.game_type_info.part_type == 1">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 6)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 6, 12)}}</span>
            <span style="color: #FF4C20;">{{filterResult(awardResult.game_result_info.result, 12, 18)}}</span>
          </div>
          <!-- (1,2,3,19,20 | 4,5,6 | 7,8,9 | 10,11,12 | 13,14,15 | 16,17,18) -->
          <div class="num" v-else-if="awardResult.game_type_info.part_type == 2">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 3)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 3, 6)}}</span>
            <span style="color: #FF4C20;">{{filterResult(awardResult.game_result_info.result, 6, 9)}}</span>
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 9, 12)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 12, 15)}}</span>
            <span style="color: #FF4C20;">{{filterResult(awardResult.game_result_info.result, 15, 18)}}</span>
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 18, 20)}}</span>
          </div>
          <!-- 每三个循环一次 -->
          <div class="num threeLoop" v-else-if="awardResult.game_type_info.part_type == 3 || awardResult.game_type_info.part_type == 4">
             <span v-for="(item, index) in threeLoop(awardResult.game_result_info.result)" :key="index">
                {{item}}
             </span>
          </div>
          <!-- (1,2,3 | 4,5,6,7,8,9,10) -->
          <div class="num" v-else-if="awardResult.game_type_info.part_type == 5">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 3)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 3, 10)}}</span>
          </div>
          <!-- (1 | 2-10) -->
          <div class="num" v-else-if="awardResult.game_type_info.part_type == 6">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 1)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 1, 10)}}</span>
          </div>
          <!-- (1 | 2-9  | 10) -->
          <div class="num" v-else-if="awardResult.game_type_info.part_type == 7">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 1)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 1, 9)}}</span>
            <span style="color: #FF4C20;">{{filterResult(awardResult.game_result_info.result, 9, 10)}}</span>
          </div>
          <!-- (1 |  2  | 3- 10) -->
          <div class="num" v-else-if="awardResult.game_type_info.part_type == 8">
            <span style="color: #3897FF;">{{filterResult(awardResult.game_result_info.result, 0, 1)}}</span>
            <span style="color: #F5A623;">{{filterResult(awardResult.game_result_info.result, 1, 2)}}</span>
            <span style="color: #FF4C20;">{{filterResult(awardResult.game_result_info.result, 2, 10)}}</span>
          </div>
          <!-- 只有一种颜色 -->
          <div class="num" v-else>
            <span style="color: #3897FF;">{{awardResult.game_result_info.result}}</span>
          </div>
          
          <div class="third_query" @click="thirdQuery()">
            第三方查询
          </div>
        </div>
        <div class="result_num_more" v-if="choosedGame.game_type_id == 43">
           <div v-if="awardResult.game_log_info.part_one_result">{{awardResult.game_log_info.part_one_result}}</div>
           <div v-if="awardResult.game_log_info.part_two_result">{{awardResult.game_log_info.part_two_result}}</div>
           <div v-if="awardResult.game_log_info.part_three_result">{{awardResult.game_log_info.part_three_result}}</div>
           <div v-if="awardResult.game_log_info.part_four_result">{{awardResult.game_log_info.part_four_result}}</div>
           <div v-if="awardResult.game_log_info.part_five_result">{{awardResult.game_log_info.part_five_result}}</div>
           <div v-if="awardResult.game_log_info.part_six_result">{{awardResult.game_log_info.part_six_result}}</div>
        </div>
        <div class="result_num" v-else-if="gameType == 0 && awardResult">
          <div class="result_num_item">
            <span class="item_title" v-if="choosedGame.game_type_id == 37">前三号码：</span>
            <span class="item_title" v-else>一区号码：</span>
            <span class="item_num" v-if="choosedGame.game_type_id == 37">{{filterPard(awardResult.game_log_info.part_one_result)}}</span>
            <span class="item_num" v-else>{{awardResult.game_log_info.part_one_result}}</span>
          </div>
          <div class="result_num_item" v-if="!onlyOneGame(choosedGame.game_type_id)">
            <span class="item_title" v-if="choosedGame.game_type_id == 37">中三号码：</span>
            <span class="item_title" v-else>二区号码：</span>
            <span class="item_num" v-if="choosedGame.game_type_id == 37">{{filterPard(awardResult.game_log_info.part_two_result)}}</span>
            <span class="item_num" v-else-if="onlyTwoGame(choosedGame.game_type_id)">{{awardResult.game_log_info.part_three_result}}</span>
            <span class="item_num" v-else>{{awardResult.game_log_info.part_two_result}}</span>
          </div>
          <div class="result_num_item" v-if="!onlyTwoGame(choosedGame.game_type_id)">
             <span class="item_title" v-if="choosedGame.game_type_id == 37">后三号码：</span>
            <span class="item_title" v-else>三区号码：</span>
            <span class="item_num" v-if="choosedGame.game_type_id == 37">{{filterPard(awardResult.game_log_info.part_three_result)}}</span>
            <span class="item_num" v-else>{{awardResult.game_log_info.part_three_result}}</span>
          </div>
          <div class="item_result" v-if="noResultGame(choosedGame.game_type_id)">
            <div class="result_title">
              结果：
            </div>
            <div class="last_result" v-if="oneResultGame(choosedGame.game_type_id)">
              <span>{{awardResult.game_log_info.result}}</span>
            </div>
            <div class="last_result" v-if="oneStrRestultGame(choosedGame.game_type_id)">
              <span>{{getStrNum(awardResult.game_log_info.result, 0)}}</span>
            </div>
            <div class="last_result_str" v-if="strResultGame(choosedGame.game_type_id)">
              <div class="result_str" style="background: #B822DD;" v-if="awardResult.game_log_info.result == 1">豹</div>
              <div class="result_str" style="background: #B822DD;" v-else-if="awardResult.game_log_info.result == 2">顺
              </div>
              <div class="result_str" style="background: #3C3CC4;" v-else-if="awardResult.game_log_info.result == 3">对
              </div>
              <div class="result_str" style="background: #EE1111;" v-else-if="awardResult.game_log_info.result == 4">半
              </div>
              <div class="result_str" style="background: #1AE6E6;" v-else>杂</div>
            </div>
            <div class="last_result" v-if="choosedGame.game_type_id == 19">
              <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 1">龙</span>
              <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 2">虎</span>
              <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 3">和</span>
            </div>
          </div>
        </div>
        <div class="result_poker" v-else-if="gameType == 1 && awardResult">
          <div class="poker_item_box">
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_one_result)" alt="">
              <div class="poker_area">一区</div>
            </div>
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_two_result)" alt="">
              <div class="poker_area">二区</div>
            </div>
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_three_result)" alt="">
              <div class="poker_area">三区</div>
            </div>
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_four_result)" alt="">
              <div class="poker_area">四区</div>
            </div>
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_five_result)" alt="">
              <div class="poker_area">五区</div>
            </div>
            <div class="poker_item">
              <img :src="getPokerImg(awardResult.game_log_info.part_six_result)" alt="">
              <div class="poker_area">六区</div>
            </div>
          </div>
          <div class="result_txt">
             <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 1">庄</span>
             <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 2">闲</span>
             <span v-if="getStrNum(awardResult.game_log_info.result, 0) == 3">和</span>
          </div>
        </div>
        <div class="result_string" v-else-if="gameType == 2 && awardResult">
          <div class="fl">
            <span class="title">冠亚和：</span>
            <span class="result_number">{{getStrNum(awardResult.game_log_info.result, 0)}}</span>
            <span v-if="awardResult.game_log_info.result">
              <span class="result_size_small" v-if="getStrNum(awardResult.game_log_info.result, 1) == 1">小</span>
              <span class="result_size_big" v-if="getStrNum(awardResult.game_log_info.result, 1) == 2">大</span>
            </span>
            <span v-if="awardResult.game_log_info.result">
              <span class="result_even_dan" v-if="getStrNum(awardResult.game_log_info.result, 2) == 1">单</span>
              <span class="result_even_shuang" v-if="getStrNum(awardResult.game_log_info.result, 2) == 2">双</span>
            </span>
          </div>
          <div class="fr">
            <span class="title">龙虎：</span>
            <span>
              1：
              <span class="long" v-if="getStrNum(awardResult.game_log_info.result, 3) == 1">龙</span>
              <span class="hu" v-if="getStrNum(awardResult.game_log_info.result, 3) == 2">虎</span>
            </span>
            <span>
              2：
              <span class="long" v-if="getStrNum(awardResult.game_log_info.result, 4) == 1">龙</span>
              <span class="hu" v-if="getStrNum(awardResult.game_log_info.result, 4) == 2">虎</span>
            </span>
            <span>
              3：
              <span class="long" v-if="getStrNum(awardResult.game_log_info.result, 5) == 1">龙</span>
              <span class="hu" v-if="getStrNum(awardResult.game_log_info.result, 5) == 2">虎</span>
            </span>
            <span>
              4：
              <span class="long" v-if="getStrNum(awardResult.game_log_info.result, 6) == 1">龙</span>
              <span class="hu" v-if="getStrNum(awardResult.game_log_info.result, 6) == 2">虎</span>
            </span>
            <span>
              5：
              <span class="long" v-if="getStrNum(awardResult.game_log_info.result, 7) == 1">龙</span>
              <span class="hu" v-if="getStrNum(awardResult.game_log_info.result, 7) == 2">虎</span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import {
    mapGetters
  } from 'vuex'
  import {
    filterNum, defalutImg
  } from '@/config/mixin.js'
  import { trendChart } from '@/config/trendChart.js'
  export default {
    name: "winResult",
    props: {
      gameType: {
        type: Number,
        default: 0 // 游戏类型  0： 号码类型   1：扑克类型
      },
      id: {
        type: String,
        default: ''
      }
    },
    mixins: [filterNum, defalutImg, trendChart],
    data() {
      return {};
    },
    methods: {
      thirdQuery() {
        if (Number(this.id) >=21 && Number(this.id) <= 29) {
          window.open("http://lotto.bclc.com/winning-numbers/keno-and-keno-bonus.html");
        } else if (Number(this.id) >= 45 && Number(this.id) <= 50) {
          window.open("https://www.blockchain.com/btc/unconfirmed-transactions");
        } else if (Number(this.id) >= 31 && Number(this.id) <= 36) {
          window.open("http://www.knlotto.kr/keno.aspx?method=kenoWinNoList");
        } else if (Number(this.id) >= 52 && Number(this.id) <= 57) {
          window.open("http://www.luckyairship.com/history.html");
        } else {
          window.open("http://www.bwlc.net/bulletin/keno.html");
        }
      }
    },
    computed: {
      ...mapGetters([
        'awardResult',
        'choosedGame'
      ])
    }
  }
</script>
<style scoped lang='less'>
  .winResult{
    height: 150px;
    border: 1px solid rgba(209, 145, 60, 1);
    border-radius: 8px;
  }
  .homeTop {
    height: 150px;
    display: flex;
    overflow: hidden;

    .tip {
      background: url('~images/bg/bg_game@2x.png') no-repeat;
      background-size: 100%;
      width: 217px;
      padding: 20px;
      box-sizing: border-box;

      p {
        color: #fff;
      }

      p:nth-child(1) {
        font-size: 25px;
        margin-bottom: 3px;
      }

      p:nth-child(2) {
        font-size: 24px;
      }
    }

    .result {
      flex: 1;

      .result_query {
        height: 75px;
        border-bottom: 1px solid #D1913C;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: space-around;

        .num {
          font-size: 20px;
          line-height: 40px;
          height: 40px;
        }

        .threeLoop{
          span:nth-child(3n){
            color: #3897FF;
          }
          span:nth-child(3n+1){
            color: #FF4C20;
          }
          span:nth-child(3n+2){
            color: #F5A623;
            
          }
        }

        .third_query {
          font-size: 14px;
          color: #D1913C;
          width: 118px;
          height: 32px;
          background: #FFEFD4;
          text-align: center;
          line-height: 32px;
          border-radius: 8px;
        }
      }

      .result_num_more{
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: space-around;
        div {
          .result_ball(40px, 20px);
        }
      }

      .result_num {
        height: 75px;
        display: flex;
        align-items: center;
        justify-content: space-around;

        .result_num_item {
          display: flex;
          align-items: flex-end;

          .item_title {
            font-size: 20px;
            color: #4A4130;
          }

          .item_num {
            font-size: 30px;
            color: #F5A623;
            line-height: 28px;
          }
        }

        .item_result {
          display: flex;
          align-items: center;

          .result_title {
            font-size: 20px;
            color: #4A4130;
          }

          .last_result {
            .result_ball(40px, 20px);
          }

          .last_result_str {
            .result_str {
              margin-left: 6px;
              width: 40px;
              height: 40px;
              border-radius: 40px;
              line-height: 40px;
              text-align: center;
              font-size: 20px;
              color: #fff;
            }
          }
        }
      }

      .result_poker {
        height: 75px;
        padding: 0 108px 0 75px;
        display: flex;
        align-items: center;
        justify-content: space-between;

        .poker_item_box {
          display: flex;

          .poker_item {
            margin-left: 30px;
            height: 75px;
            width: 40px;
            padding-top: 1px;
            box-sizing: border-box;

            img {
              width: 40px;
              height: 53px;
            }

            .poker_area {
              .sc(16px, #4A4130);
            }
          }
        }

        .result_txt {
          font-size: 30px;
          color: #F5A623;
        }
      }

      .result_string {
        height: 75px;
        padding: 0 108px 0 75px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        .title {
          font-size: 18px;
          color: #656565;
        }
        .result_number {
          color: #018796;
        }
        .result_size_small {
          color: #FD00FF;
        }
        .result_size_big {
          color: #14D4D0;
        }
        .result_even_dan {
          color: #1909F7;
        }
        .result_even_shuang {
          color: #C00;
        }
        .long {
          color: #1200FF;
        }
        .hu {
          color: #F00;
        }
        span {
          font-size: 14px;
          font-weight: bold;
        }
      }
    }

  }
</style>