<template>
  <!--    五行-->
  <div class="five-elements">
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 79px;">种类</th>
        <th style="width: 678px;">号码</th>
        <th style="width: 65px;">赔率</th>
        <th style="width: 86px;">金额</th>
      </tr>
      <tr v-if="fiveEList &&  fiveEList.bet_json" v-for="(item,index) in fiveEList.bet_json"
          :key="index" :class="{active: item.checked}">
        <td class="sort" @click.stop="chooseItem(item)" >{{item.name}}</td>
        <td class="number" @click.stop="chooseItem(item)" >
          <ul>
            <li v-for="(child,secindex) in item.list" :key="secindex">
              <div class='result-number' v-if='child && child.bg'
                   :class="[child.bg === 1 ? 'bgr':child.bg === 2 ? 'bgb':'bgg']">{{child.number}}
              </div>
            </li>
          </ul>
        </td>
        <td @click.stop="chooseItem(item)" >{{isPlateClose == 1 ? '--' : item.rate}}</td>
        <td class="input-td">
          <el-input v-model="item.money" style="width: 76px!important;margin-left: 10px"  ref="inputVal" @focus="handleFocus(item)" :disabled="!!isPlateClose"></el-input>
        </td>
      </tr>
    </table>
    <div class="wave-footer">
      <span>金额</span>
      <input type="text" v-model="FiveEmoney" class="input" @input="handleBetInput" >
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import {markSixMixin} from "@/config/mixin"
    import {mapGetters} from "vuex";
    export default {
        name: "fiveElements",
        mixins: [markSixMixin],
        computed: {
            ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose","gameResultId"])
        },
        data() {
            return {
                FiveEmoney: '',
                list: [
                    {
                        name: '金',
                        numberList: [{number: '06', bg: 3}, {number: '07', bg: 1}, {number: '20', bg: 2}, {
                            number: '21', bg: 3
                        }, {number: '28', bg: 3}, {number: '29', bg: 1}, {number: '36', bg: 2}, {number: '37', bg: 2}]
                    },
                    {
                        name: '木',
                        numberList: [{number: '02', bg: 1}, {number: '03', bg: 2}, {number: '10', bg: 2}, {
                            number: '11',
                            bg: 3
                        }, {number: '18', bg: 1}, {number: '19', bg: 1}, {number: '32', bg: 3}, {
                            number: '33',
                            bg: 3
                        }, {number: '40', bg: 1}, {number: '41', bg: 2}, {number: '48', bg: 2}, {number: '49', bg: 3}]
                    },
                    {
                        name: '水',
                        numberList: [{number: '08', bg: 1}, {number: '09', bg: 2}, {number: '16', bg: 3}, {
                            number: '17',
                            bg: 3
                        }, {number: '24', bg: 1}, {number: '25', bg: 2}, {number: '38', bg: 3}, {
                            number: '39',
                            bg: 3
                        }, {number: '46', bg: 1}, {number: '47', bg: 2}]
                    },
                    {
                        name: '火',
                        numberList: [{number: '04', bg: 2}, {number: '05', bg: 3}, {number: '12', bg: 1}, {
                            number: '13',
                            bg: 1
                        }, {number: '26', bg: 2}, {number: '27', bg: 3}, {number: '34', bg: 1}, {
                            number: '35',
                            bg: 1
                        }, {number: '42', bg: 2}, {number: '43', bg: 3}]
                    },
                    {
                        name: '土',
                        numberList: [{number: '01', bg: 1}, {number: '14', bg: 2}, {number: '15', bg: 2}, {
                            number: '22',
                            bg: 3
                        }, {number: '23', bg: 1}, {number: '30', bg: 1}, {number: '31', bg: 2}, {
                            number: '44',
                            bg: 3
                        }, {number: '45', bg: 1}]
                    }
                ],
                fiveEList: [],


            }
        },
        props: {
            id: {
                type: String,
                default: () => ''
            },
        },
        methods: {
            // 初始化表格
            initTable() {
                console.log('是我呀,五行')
                console.log(this.awardResult)
                this.$Api({
                    api_name: 'kkl.game.getLastBetInfo',
                    game_result_id: this.gameResultId,
                    game_type_id: this.choosedGame.game_type_id,
                    pan_type: this.plateValue
                }, (err, data) => {
                    const {new_bet_rate} = data.data;
                    let jsonArr = JSON.parse(new_bet_rate);
                    this.list.map((secItem, secIndex) => {
                      jsonArr.forEach((item, index) => {
                          item.bet_json.map(child => {
                              if (secItem.name === child.name) {
                                  child.list = secItem.numberList
                              }
                              child.checked = false;
                              child.money = '';
                          });
                      });
                  })
                    this.resultList = jsonArr;
                    this.fiveEList = this.resultList[0]
                });
            },
            // 选择某一项
            chooseItem(item) {
                if (this.isPlateClose == 1) return;
                item.checked = !item.checked;
                if (this.FiveEmoney) {
                    if (!item.checked) {
                        item.money = '';
                    } else {
                        item.money = this.FiveEmoney;
                    }
                }
            },
            // 处理聚焦
            handleFocus(item) {
                if (this.isPlateClose == 1) return;
                item.checked = true;
                if (this.FiveEmoney) {
                    item.money = this.FiveEmoney;
                }
            },
            // 监听下注金额变化
            handleBetInput() {
                if (this.isPlateClose == 1) return;
                    this.fiveEList.bet_json.map(child => {
                        if (child.checked) {
                            child.money = this.FiveEmoney;
                        }
                    });
            },
            onFocus(index){
              //获取焦点
                this.fiveEList.bet_json[index].checked = true
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
                            this.list.map((secItem, secIndex) => {
                                this.resultList.forEach((item, index) => {
                                    item.bet_json.map(child => {
                                        if (secItem.name === child.name) {
                                            this.$set(child, 'list', secItem.numberList)
                                        }
                                        this.$set(child, 'money', '')
                                        this.$set(child, 'checked', false)
                                    })
                                })

                            })
                            this.fiveEList = this.resultList[0]
                        }
                    }
                );
            },
            //投注
            getGameBet() {
                let list = []
                let totalMoney = 0
                list = this.fiveEList.bet_json.filter((item, index) => item.money > 0)
                if(!list.length){
                    //说明没有输入任何金额，提示
                    this.$alert('下注内容不对，请重新下注', '提示', {
                        confirmButtonText: '确定',
                    });
                    return;
                }
                //组合bet_json
                var newArray2 = [];
                for (var i = 0; i < list.length; i++) {
                    totalMoney = totalMoney + list[0].money
                    var newObject = {};
                    newObject.key = list[i].key;
                    newObject.money = list[i].money;
                    newArray2.push(newObject);
                }
                let obj = []
                obj.push({
                    part: this.fiveEList.part,
                    bet_json: newArray2,
                    name: this.fiveEList.name
                })
                console.log(obj, 'OBJ')
                this.$Api(
                    {
                        api_name: "kkl.game.gameBet",
                        bet_json: obj,
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

            //选择下注
            toCheck(index){
                this.fiveEList.bet_json[index].checked = !this.fiveEList.bet_json[index].checked
                if(!this.fiveEList.bet_json[index].checked){
                    //取消选择后，金额也取消
                    this.fiveEList.bet_json[index].money = ''
                }else{
                    //选中时，底下的金额有就自动填充
                    if(this.FiveEmoney){
                        this.fiveEList.bet_json[index].money = this.FiveEmoney

                    }
                }
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


<style scoped lang='less'>
  .five-elements {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .game_table {
      width: 100%;
     tr{
       td {
         height: 32px;
         border: 1px solid #e8e8e8;
         text-align: center;
         font-size: 14px;
         color: #4A4130;
         line-height: 32px;
         -webkit-box-sizing: border-box;
         box-sizing: border-box;
         vertical-align: center !important;
       }
       .input-td{
         position: relative;
         z-index:2;
       }
       .sort {
         font-size: 14px;
         font-weight: 600;
         color: rgba(133, 86, 9, 1);
         background: #FFEFD4;
       }

       .number {
         height: 32px;
         padding: 0 20px;
         box-sizing: border-box;
         /*display: flex;*/
         /*align-items: center;*/

         ul {
           display: flex;

           li {
             width: 30px;
             text-align: center;
             margin-right: 15px;

             .result-number {
               width: 100%;
               height: 30px;
               /*border:1px solid #1F70FF;*/
               text-align: center;
               line-height: 30px;
               font-size: 16px;
             }

             .bgr {
               background: url('~images/bg/red_circle.png') no-repeat;
             }

             .bgb {
               background: url('~images/bg/blue_circle.png') no-repeat;
             }

             .bgg {
               background: url('~images/bg/green_circle.png') no-repeat;
             }
           }
         }


       }
       &.active > td {
         background: #ffc214 !important;
       }

       &:hover {
         background: #FFEFD4;
         & > div {
           background: #FFEFD4;
         }
       }
     }


    }

    .wave-footer {
      margin-top: 24px;
      display: flex;
      font-size: 14px;
      justify-content: center;
      /*width: 222px;*/
      /*height: 22px;*/
      /*margin: 0 auto;*/
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
    display: flex !important;
    align-items: center;
    justify-content: center;
    height: 30px;
  }
  .el-input__inner {
    padding:0 5px;
    height: 20px !important;
    line-height: 20px !important;
  }
</style>
