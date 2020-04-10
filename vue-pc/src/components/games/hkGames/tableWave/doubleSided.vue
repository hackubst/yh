<template>
  <!--  两面-->
  <div class="double-sided">
    <div class="double-sided-top">
      <div class="sided">特双面</div>
      <ul>
        <li :class="{active: item.checked}" v-for="(item,index) in resultListD.bet_json" :key="index" @click.stop="chooseItem(item)">
          <div class="double-sided-name flex-center">{{item.name}}</div>
          <div class="double-sided-rate flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
          <div class="double-sided-input flex-center" @click.stop>
            <el-input v-model="item.money" :disabled="!!isPlateClose" @focus="handleFocus(item)"></el-input>
          </div>
        </li>
      </ul>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="moneyD" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn " @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import {markSixMixin} from "@/config/mixin"
    export default {
        name: "doubleSided",
        data() {
            return {
                gameResult: {},
                moneyD: '',
                resultListD: [],
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
              console.log(this.awardResult)
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
                this.resultListD = this.resultList[0]
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
                            this.resultListD = this.resultList[0]
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
                        total_bet_money:this.moneyD,
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
              if (this.moneyD) {
                if (!item.checked) {
                  item.money = '';
                } else {
                  item.money = this.moneyD;
                }
              }
            },
            // 处理聚焦
            handleFocus(item) {
              if (this.isPlateClose == 1) return;
              item.checked = true;
              if (this.moneyD) {
                item.money = this.moneyD;
              }
            },
            // 监听下注金额变化
            handleBetInput() {
              if (this.isPlateClose == 1) return;
              this.resultList.forEach(item => {
                item.bet_json.map(child => {
                  if (child.checked) {
                    child.money = this.moneyD;
                  }
                });
              });
            },
        },
        mounted() {
          // this.getResult();
        }
    }
</script>

<style scoped lang='less'>
  .double-sided {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 40px;

    .double-sided-top {
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
      overflow: hidden;

      .sided {
        width: 100%;
        height: 40px;
        background: #FFEFD4;
        text-align: center;
        line-height: 40px;
        font-size: 16px;
      }

      ul {
        display: flex;
        flex-wrap: wrap;
        /*border-radius: 5px;*/
        border-top: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
        border-left: 1px solid #cccccc;

        .flex-center {
          display: flex;
          align-items: center;
          justify-content: center;
        }

        li {
          /*width:230px;*/
          flex: 1;
          display: flex;
          height: 32px;
          align-items: center;
          border-bottom: 1px solid #CCCCCC;
          /*border-left: 1px solid #CCCCCC;*/

          &.active > div {
            background: #ffc214 !important;
          }

          &:hover {
            background: #FFEFD4;
            & > div {
              background: #FFEFD4;
            }
          }

          .double-sided-name {
            width: 60px;
            height: 100%;
            background: #FFEFD4;
            font-size: 14px;
            color: #E4AA5C;
            border-right: 1px solid #CCCCCC;
            /*border-left: 1px solid #CCCCCC;*/

          }

          .double-sided-rate {
            width: 70px;
            height: 100%;
            color: red;
            font-size: 12px;
            border-right: 1px solid #CCCCCC;
          }

          .double-sided-input {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 96px;
          }
        }
      }
    }

    .wave-footer {
      display: flex;
      justify-content: center;
      font-size: 14px;
      margin-top: 24px;
      align-items: center;

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
  }
</style>
<style>
  .el-input{
    width: 68px;
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
