<template>
  <div class="head-tail">
    <div class="head-tail-title">
      <!--     头位数-->
      <div class="tail-number">
        <div class="tail-number-head">
          <div class="left flex-center">种类</div>
          <div class="center flex-center">赔率</div>
          <div class="right flex-center w157">金额</div>
        </div>
        <ul  v-if="resultListH && resultListH[0] && resultListH[0].bet_json">
          <li :class="{active: item.checked}" v-for="(item,index) in (resultListH[0].bet_json).slice(0,5)" :key="index" @click.stop="chooseItem(item)">
            <div class="li-left flex-center">{{item.name}}</div>
            <div class="li-center flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
            <div class="li-right flex-center  w157" @click.stop>
              <el-input v-model="item.money" :disabled="!!isPlateClose" style="width:120px" @focus="handleFocus(item)"></el-input>
            </div>
          </li>
        </ul>
      </div>
      <!--      尾位数-->
      <div style="display: flex">
        <div class="tail-number" v-for="(parentItem,parentIndex) in 2" :key="parentIndex">
          <div class="tail-number-head">
            <div class="left flex-center">种类</div>
            <div class="center flex-center">赔率</div>
            <div class="right flex-center">金额</div>
          </div>
          <ul v-if="resultListH && resultListH[0] && resultListH[0].bet_json">
            <li :class="{active: secitem.checked}" v-for="(secitem,secindex) in (resultListH[0].bet_json).slice(parentIndex*5 + 5,parentIndex*5 + 10)" :key="secindex" @click.stop="chooseItem(secitem)">
              <div class="li-left flex-center">{{secitem.name}}</div>
              <div class="li-center flex-center">{{isPlateClose == 1 ? '--' : secitem.rate}}</div>
              <div class="li-right flex-center" @click.stop>
                <el-input v-model="secitem.money" :disabled="!!isPlateClose" style="width:97px" @focus="handleFocus(secitem)"></el-input>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="moneyH" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn " @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import {markSixMixin} from "@/config/mixin"
  export default {
    name: "headAndTail",
    data() {
      return {
          resultListH:[],
          moneyH:''
      }
    },
      props: {
          id: {
              type: String,
              default: () => ''
          },
      },
      mixins: [markSixMixin],
      methods: {
        // 初始化表格
          initTable() {
            this.$Api({
              api_name: 'kkl.game.getLastBetInfo',
              game_result_id: this.gameResultId,
              game_type_id: this.choosedGame.game_type_id,
              pan_type: this.plateValue
            }, (err, data) => {
              const {new_bet_rate} = data.data;
              let jsonArr = JSON.parse(new_bet_rate);
              jsonArr.forEach((item, index) => {
                item.bet_json.map(child => {
                    child.checked = false;
                    child.money = '';
                });
              });
              this.resultList = jsonArr;
              this.resultListH = this.resultList
            });
          },
          //获取开奖结果和赔率
          getResult() {
              this.$Api(
                  {
                      api_name: "kkl.game.nowResult",
                      game_type_id: this.id
                  },
                  (err, data) => {
                      if (!err) {
                          this.gameResult = data.data
                          let jsonArr = JSON.parse(this.gameResult.game_type_info.bet_json)
                          jsonArr.forEach(item => {
                            item.bet_json.map(child => {
                              child.checked = false
                              child.money = ''
                            })
                          })
                          this.resultList = jsonArr
                          this.resultListH = this.resultList
                      }
                  }
              );
          },
          //投注
          getGameBet() {
              let list = []
              list = this.resultListD.bet_json.filter((item, index) => item.money > 0)
              var newArray2 = [];
              for (var i = 0; i < list.length; i++) {
                  var newObject = {};
                  newObject.key = list[i].key;
                  newObject.money = list[i].money;
                  newArray2.push(newObject);
              }
              let obj = []
              obj.push({
                  part: this.resultListD.part,
                  bet_json: newArray2,
                  name: this.resultListD.name
              })

              console.log(obj, 'OBJ')
              this.$Api(
                  {
                      api_name: "kkl.game.gameBet",
                      bet_json:obj,
                      game_result_id:this.gameResult.game_type_info.issue_num,
                      total_bet_money:this.moneyH,
                      game_type_id:this.id
                  },
                  (err, data) => {
                      if (!err) {
                          console.log('chenggongla ')
                      }
                  }
              );
          },
          // 选择某一项
          chooseItem(item) {
            if (this.isPlateClose == 1) return;
            item.checked = !item.checked;
            if (this.moneyH) {
              if (!item.checked) {
                item.money = '';
              } else {
                item.money = this.moneyH;
              }
            }
          },
          // 处理聚焦
          handleFocus(item) {
            if (this.isPlateClose == 1) return;
            item.checked = true;
            if (this.moneyH) {
              item.money = this.moneyH;
            }
          },
          // 监听下注金额变化
          handleBetInput() {
            if (this.isPlateClose == 1) return;
            this.resultList.forEach(item => {
              item.bet_json.map(child => {
                if (child.checked) {
                  child.money = this.moneyH;
                }
              });
            });
          },
      },
      mounted() {
          // this.getResult()
      }
  }
</script>

<style scoped lang="less">
  .head-tail {
    padding-top: 20px;
    .wave-footer {
      display: flex;
      justify-content: center;
      margin-top: 24px;
      align-items: center;
      font-size: 14px;

      input {
        width: 60px;
        height: 20px;
        background: rgba(255, 255, 255, 1);
        box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.2);
        border-radius: 2px;
        border: 1px solid rgba(204, 204, 204, 1);
        margin: 0 10px;
        box-sizing: border-box;
      }

      .sure-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 22px;
        background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
        border-radius: 2px;
        font-size: 12px;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
      }

      .mr10 {
        margin-right: 10px;
      }

    }
    .w157{
      width: 157px !important;
    }
    .game_table {
      th {
        height: 32px;
        line-height: 32px;
        font-size: 14px;
        background: #FFEFD4;
        color: #4A4130;
        border: 1px solid #e8e8e8;
      }
    }

    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .head-tail-title {
      .wh(920px, auto);
      display: flex;

      .tail-number-head {
        display: flex;
        border-top: 2px solid #F5A623;
        background: #FFEFD4;
        font-weight: 600;
        font-size: 14px;
        color: #4A4130;
        height: 32px;
        border-bottom: 1px solid #e8e8e8;

        .left {
          .wh(69px, 32px);
          border-right: 1px solid #e8e8e8;

        }

        .center {
          .wh(95px, 32px);
          border-right: 1px solid #e8e8e8;
        }


        .right {
          .wh(131px, 32px);
          border-right: 1px solid #e8e8e8;
        }
      }

      .tail-number {
        ul {

          border-bottom: none;

          li {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e8e8e8;

            &.active > div {
              background: #ffc214 !important;
            }

            &:hover {
              background: #FFEFD4;
              & > div {
                background: #FFEFD4;
              }
            }

            .li-left {
              .wh(69px, 32px);
              background: rgba(255, 239, 212, 1);
              font-size: 14px;
              font-weight: 600;
              color: rgba(133, 86, 9, 1);
              border-right: 1px solid #e8e8e8;
            }

            .li-center {
              .wh(95px, 32px);
              font-size: 12px;
              font-weight: 600;
              color: rgba(224, 32, 32, 1);
              border-right: 1px solid #e8e8e8;
            }

            .li-right {
              .wh(131px, 32px);
              border-right: 1px solid #e8e8e8;
            }
          }
        }
      }
    }
  }
</style>
<style>
  .el-input{
    display: flex !important;
    align-items: center;
    justify-content: center;
  }
  .el-input__inner {
    height: 20px !important;
    line-height: 20px !important;
    padding: 0 5px !important;
    box-sizing: border-box !important;
  }
</style>

