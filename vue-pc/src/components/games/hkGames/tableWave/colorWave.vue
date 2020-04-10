<template>
  <div class="color-wave">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 79px;">种类</th>
        <th style="width: 678px;">号码</th>
        <th style="width: 65px;">赔率</th>
        <th style="width: 86px;">金额</th>
      </tr>
      <tr :class="{active: item.checked}" v-for="(item,index) in resultListE.bet_json" :key="index" @click.stop="chooseItem(item)">
        <td class="sort">{{item.name}}</td>
        <td class="number">
          <ul>
            <li v-for="(child_,secindex) in (index === 0 ?redList :index === 1?blueList:greenList)" :key="secindex">
              <div class='result-number' :class="[index === 1 ? 'blue-bg':index === 2 ? 'green-bg':'red-bg']">{{child_}}</div>
            </li>
          </ul>
        </td>
        <td>{{isPlateClose == 1 ? '--' : item.rate}}</td>
        <td @click.stop style="padding-left: 6px">
          <el-input v-model="item.money" :disabled="!!isPlateClose" style="width: 76px!important;height:45px" @focus="handleFocus(item)"></el-input>
        </td>
      </tr>
    </table>
    <div class="head-tail-title">
      <!--     头位数-->
      <div>
        <div class="tail-number">
          <div class="tail-number-head" v-for="(secItem,secIndex) in 3" :key="secIndex">
            <div class="left flex-center">半波</div>
            <div class="center flex-center">赔率</div>
            <div class="right flex-center w157">金额</div>
          </div>
        </div>
        <div>
          <ul>
            <li :class="{active: item.checked}" v-for="(item,index) in resultListF.bet_json" :key="index" @click.stop="chooseItem(item)">
              <div class="li-left flex-center">{{item.name}}</div>
              <div class="li-center flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
              <div class="li-right flex-center  w157" :style="{borderRightWidth:(index + 1 )% 3 === 0 ?'0':'1px'}" @click.stop>
                <el-input v-model="item.money" :disabled="!!isPlateClose" style="width:130px" @focus="handleFocus(item)"></el-input>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div style="margin-top: 10px">
        <div class="tail-number">
          <div class="tail-number-head" v-for="(secItem,secIndex) in 3" :key="secIndex">
            <div class="left flex-center">半半波</div>
            <div class="center flex-center">赔率</div>
            <div class="right flex-center w157">金额</div>
          </div>
        </div>
        <div>
          <ul >
            <li :class="{active: thirdItem.checked}" v-for="(thirdItem,thirdIndex) in resultListG.bet_json" :key="thirdIndex" @click.stop="chooseItem(thirdItem)">
              <div class="li-left flex-center">{{thirdItem.name}}</div>
              <div class="li-center flex-center">{{isPlateClose == 1 ? '--' : thirdItem.rate}}</div>
              <div class="li-right flex-center  w157" :style="{borderRightWidth:(thirdIndex + 1 )% 3 === 0 ?'0':'1px'}" @click.stop>
                <el-input v-model="thirdItem.money" :disabled="!!isPlateClose" style="width:130px" @focus="handleFocus(thirdItem)"></el-input>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <!--      尾位数-->
      <div style="display: flex" v-if="false">
        <div class="tail-number" v-for="(parentItem,parentIndex) in 2" :key="parentIndex">
          <div class="tail-number-head">
            <div class="left flex-center">{{firstIndex === 0 ?'半':'半半'}}波</div>
            <div class="center flex-center">赔率</div>
            <div class="right flex-center">金额</div>
          </div>
          <ul>
            <li :class="{active: item.checked}" v-for="(item,index) in titleList" :key="index" @click.stop="chooseItem(item)">
              <div class="li-left flex-center">{{parentIndex === 0 ? '蓝':'绿'}}{{item.title}}</div>
              <div class="li-center flex-center">{{index}}</div>
              <div class="li-right flex-center" @click.stop>
                <el-input v-model="index" style="width:107px" @focus="handleFocus(item)"></el-input>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="moneyThree" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn " @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
import {markSixMixin} from "@/config/mixin"
    export default {
        name: "colorWave",
        props: {
            id: {
                type: String,
                default: () => ''
            },
        },
        mixins: [markSixMixin],
        data() {
            return {
                gameResult: {},
                moneyThree: '',
                titleList: [{title: '单'}, {title: '双'}, {title: '大'}, {title: '肖'}],
                redList:['01', '02', '07', '08', '12', '13', '18', '19', '23', '24', '29', '30', '34', '35', '40', '45', '46'],
                blueList: ['03', '04', '09', '10', '14', '15', '20', '25', '26', '31', '36', '37', '41', '42', '47', '48'],
                greenList:['05', '06', '11', '16', '17', '21', '22', '27', '28', '32', '33', '38', '39', '43', '44', '49'],
                resultListE:[],
                resultListF:[],
                resultListG:[]
            }
        },
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
                jsonArr.forEach(item => {
                  item.bet_json.map(child => {
                    child.checked = false
                    child.money = ''
                  })
                })
                this.resultList = jsonArr
                this.resultListE = this.resultList[0]
                this.resultListF = this.resultList[1]
                this.resultListG = this.resultList[2]
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
                            this.resultListE = this.resultList[0]
                            this.resultListF = this.resultList[1]
                            this.resultListG = this.resultList[2]
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
                        bet_json: obj,
                        game_result_id: this.gameResult.game_type_info.issue_num,
                        total_bet_money: this.moneyThree,
                        game_type_id: this.id
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
              if (this.moneyThree) {
                if (!item.checked) {
                  item.money = '';
                } else {
                  item.money = this.moneyThree;
                }
              }
            },
            // 处理聚焦
            handleFocus(item) {
              if (this.isPlateClose == 1) return;
              item.checked = true;
              if (this.moneyThree) {
                item.money = this.moneyThree;
              }
            },
            // 监听下注金额变化
            handleBetInput() {
              if (this.isPlateClose == 1) return;
              this.resultList.forEach(item => {
                item.bet_json.map(child => {
                  if (child.checked) {
                    child.money = this.moneyThree;
                  }
                });
              });
            },
        },
        mounted() {
          // this.getResult();
        },
    }

</script>

<style scoped lang="less">
  .color-wave {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .game_table {
      width: 100%;

      tr {
        &.active > td {
          background: #ffc214 !important;
        }

        &:hover {
          background: #FFEFD4;
          & > td {
            background: #FFEFD4;
          }
        }
      }

      td {
        border-bottom: 1px solid #e8e8e8;
        border-bottom: 1px solid #e8e8e8;
      }

      .sort {
        font-size: 14px;
        font-weight: 600;
        color: rgba(133, 86, 9, 1);
        background: #FFEFD4;
      }

      .number {
        height: 45px;
        padding: 0 20px;
        box-sizing: border-box;
        /*display: flex;*/
        /*align-items: center;*/

        ul {
          display: flex;
          li {
            width: 30px;
            text-align: center;
            margin-right: 5px;

            .result-number {
              width: 100%;
              height: 30px;
              text-align: center;
              line-height: 30px;
              font-size: 16px;
            }
            .red-bg {
              background: url('~images/bg/red_circle.png') no-repeat;
            }
            .blue-bg {
              background: url('~images/bg/blue_circle.png') no-repeat;
            }
            .green-bg {
              background: url('~images/bg/green_circle.png') no-repeat;
            }
          }
        }


      }

    }

    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .w157 {
      width: 140px !important;
    }

    .head-tail-title {
      margin-top: 10px;
      .wh(920px, auto);

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
        .wh(920px,auto);
        display: flex;

      }
      ul {

        border-bottom: none;
        display: flex;
        flex-wrap: wrap;
        border-right: 1px solid #e8e8e8;
        width: 920px;
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

    .wave-footer {
      margin-top: 24px;
      display: flex;
      justify-content: center;
      font-size: 14px;
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

