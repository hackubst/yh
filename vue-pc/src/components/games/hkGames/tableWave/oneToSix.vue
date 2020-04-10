<template>
  <!--  正码1-6-->
  <div class="one-six">
    <!--      1-3-->
    <div class="one-three" v-for="(firstItem,firstIndex) in 2">
      <table class="game_table" border="1" cellpadding="0" cellspacing="0">
        <tr class="title" v-if="firstIndex === 0">
          <th>正码一</th>
          <th>正码二</th>
          <th>正码三</th>
        </tr>
        <tr class="title" v-if="firstIndex === 1">
          <th>正码四</th>
          <th>正码五</th>
          <th>正码六</th>
        </tr>
      </table>
      <div class="one-three-content" v-if="firstIndex === 0">
        <ul v-for="(secondedItem,secondedIndex) in 3"
            v-if="oneSixList && oneSixList[secondedIndex * firstIndex  + secondedIndex] && oneSixList[secondedIndex * firstIndex  + secondedIndex].bet_json ">
          <li v-for="(item,index) in oneSixList[secondedIndex * firstIndex  + secondedIndex].bet_json" :key="index"
              :class="{active: item.checked}">
            <div class="li-left flex-center" @click="toCheck(firstIndex,secondedIndex,index)">{{item.name}}</div>
            <div class="li-center flex-center" @click="toCheck(firstIndex,secondedIndex,index)">{{isPlateClose == 1 ? '--' : item.rate}}</div>
            <div class="li-right flex-center" :style="{borderRightWidth:secondedIndex === 2 ? '0':'1px'}">
              <el-input v-model="item.money" style="width: 150px" type="number"
                        @focus="handleFocus(item)" :disabled="!!isPlateClose"></el-input>
            </div>
          </li>
        </ul>
      </div>
      <div class="one-three-content" v-if="firstIndex === 1">
        <ul v-for="(thirdItem,thirdIndex) in 3"
            v-if="oneSixList && oneSixList[thirdIndex * firstIndex + 3] && oneSixList[thirdIndex * firstIndex + 3].bet_json">
          <li v-for="(item,index) in oneSixList[thirdIndex * firstIndex  + 3].bet_json" :key="index"
              :class="{active: item.checked}">
            <div class="li-left flex-center" @click="toCheck(firstIndex,thirdIndex,index)">{{item.name}}</div>
            <div class="li-center flex-center" @click="toCheck(firstIndex,thirdIndex,index)">{{isPlateClose == 1 ? '--' : item.rate}}</div>
            <div class="li-right flex-center" :style="{borderRightWidth:thirdIndex === 2 ? '0':'1px'}">
              <el-input v-model="item.money" style="width: 150px" type="number"
                        @focus="handleFocus(item)" :disabled="!!isPlateClose"></el-input>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="oneSixMoney" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import {mapGetters, mapMutations, mapActions} from "vuex";
    import {markSixMixin} from "@/config/mixin"
    export default {
        mixins: [markSixMixin],
        ...mapMutations({
            chooseGame: 'CHOOSE_GAME',
        }),
        computed: {
            ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose","gameResultId"])
        },
        name: "oneToSix",
        data() {
            return {
                oneSixList: [],
                oneSixMoney: '',
                totalMoney: 0,
            }
        },
        props: {
            id: {
                type: String,
                default: () => ''
            },
        },
        methods: {
            initTable(){
                console.log('是我呀,正码1-6')
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
                    this.resultList = jsonArr;
                    // this.resultListJ = this.resultList
                    this.oneSixList =  this.resultList

                });
            },
            // 处理聚焦
            handleFocus(item) {
                console.log(this.isPlateClose);
                if (this.isPlateClose == 1) return;
                item.checked = true;
                if (this.oneSixMoney) {
                    item.money = this.oneSixMoney;
                }
            },
            // 监听下注金额变化
            handleBetInput() {
                if (this.isPlateClose == 1) return;
                this.oneSixList.map((item, index) => {
                    item.bet_json.map((child, index) => {
                        if (child.checked) {
                            child.money = this.oneSixMoney;
                        }
                    })
                })
            },
            //重置
            toReset() {
                this.oneSixList.map((item, index) => {
                    item.bet_json.map((child, index) => {
                        if (child.checked) {
                            child.money = ''
                            child.checked = false
                        }
                    })
                })
            },
            //选择下注
            toCheck(firstIndex, secIndex, index) {
                if (this.isPlateClose == 1) return;
                let parentIndex = firstIndex > 0 ? firstIndex * secIndex + 3 : secIndex
                this.oneSixList[parentIndex].bet_json[index].checked = !this.oneSixList[parentIndex].bet_json[index].checked
                console.log(this.oneSixList[parentIndex].bet_json[index])
                if (!this.oneSixList[parentIndex].bet_json[index].checked) {
                    //取消选择后，金额也取消
                    this.oneSixList[parentIndex].bet_json[index].money = ''
                } else {
                    //选中时，底下的金额有就自动填充
                    if (this.oneSixMoney) {
                        this.oneSixList[parentIndex].bet_json[index].money = this.oneSixMoney
                    }
                }
            },
            //获取焦点
            onFocus(firstIndex, secIndex, index) {
                if (this.isPlateClose == 1) return;
                let parentIndex = firstIndex > 0 ? firstIndex * secIndex + 3 : secIndex
                this.oneSixList[parentIndex].bet_json[index].checked = true
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
                            this.resultList = JSON.parse(data.data.game_type_info.bet_json)
                            this.resultList.forEach((item, index) => {
                                item.bet_json.map(child => {
                                    this.$set(child, 'money', '')
                                    this.$set(child, 'checked', false)
                                })
                            })
                            this.oneSixList = this.resultList
                        }
                    }
                );
            },
            //拼成bet_json格式
            toFilter(index) {
                let list = []
                let bet_json = []
                let obj = {}
                let newArray = []
                //筛选出选择的数组
                this.oneSixList[index].bet_json.map((child, index) => {
                    if (child.checked && child.money) {
                        list.push(child)
                    }
                })
                list.forEach((child, index) => {
                    if (child.checked && child.money) {
                        //选择了 并填写了金额
                        var newObject = {};
                        newObject.key = child.key;
                        newObject.money = child.money;
                        newArray.push(newObject);
                        this.totalMoney = Number(this.totalMoney) + Number(child.money)
                    }
                })
                obj = {
                    part: this.oneSixList[index].part,
                    name: this.oneSixList[index].name,
                    bet_json: newArray
                }
                return obj
            },
            //投注
            getGameBet() {
                let bet_json = []
                for (let i = 0; i < 6; i++) {
                    console.log(this.toFilter(i).bet_json)
                    if (this.toFilter(i).bet_json.length) {
                        bet_json.push(this.toFilter(i))
                    }
                }
                if (!bet_json.length) {
                    //说明没有输入任何金额，提示
                    this.$alert('下注内容不对，请重新下注', '提示', {
                        confirmButtonText: '确定',
                    });
                    return;
                }
                 this.$Api(
                     {
                         api_name: "kkl.game.gameBet",
                         bet_json: newArr,
                         game_result_id: this.gameResult.game_type_info.issue_num,
                         total_bet_money: totalMoney,
                         game_type_id: this.id
                     },
                     (err, data) => {
                         if (!err) {
                             console.log('chenggongla ')
                         }else{
                             this.$message.error(data.error_msg);
                         }
                     }
                 );

            },
            // 切换
            changeIndex(index) {
                this.current_index = index
            },
        },
        mounted() {
            // this.getResult()
        }
    }
</script>

<style scoped lang="less">
  .one-six {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .one-three {
      margin-top: 10px;

      .game_table {
        .wh(920px, auto);

        .title {
          th {
            font-size: 12px;
            font-weight: 600;
            color: rgba(74, 65, 48, 1);
            height: 25px;
            line-height: 25px;
          }
        }
      }

      .one-three-content {
        display: flex;
        border-left: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;

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
              .wh(78px, 32px);
              font-size: 12px;
              font-weight: 600;
              color: rgba(224, 32, 32, 1);
              border-right: 1px solid #e8e8e8;
            }

            .li-right {
              .wh(156px, 32px);
              border-right: 1px solid #e8e8e8;
            }
          }
        }

      }
    }

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
        font-size: 14px;
        padding: 0 5px;
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
    display: flex !important;
    align-items: center;
    justify-content: center;
  }
  .el-input__inner {
    padding:0 5px;
    line-height: 20px !important;
    height: 20px !important;
  }
</style>
