<template>
  <!--  总肖-->
  <div class="chief-shaw">
    <div class="chief-shaw-box">
      <!--    顶部-->
      <div class="chief-shaw-header bt">
        <div v-for="(item,index) in 3" :key="index" class="item">
          <div class="item-left flex-center">种类</div>
          <div class="item-center flex-center bg">赔率
          </div>
          <div class="item-right flex-center bg"
               :style="{width:(index + 1) % 3 === 0 ?'142px':'140px',borderRightWidth:(index + 1) % 3 === 0 ?'0':'1px'}">
            金额
          </div>
        </div>
      </div>
      <!--    内容-->
      <div class="chief-shaw-content" v-if="chiefList  && chiefList.bet_json">
        <div v-for="(item,index) in chiefList.bet_json" :key="index" class="item"
             :style="{borderBottomWidth: (index === 6 ||  index === 7 )?'0':'1px'}" :class="{active: item.checked}">
          <div class="item-left flex-center" @click="chooseItem(item)">{{item.name}}</div>
          <div class="item-center flex-center bg" @click="chooseItem(item)">{{isPlateClose == 1 ? '--' : item.rate}}
          </div>
          <div class="item-right flex-center bg"
               :style="{width:(index + 1)% 3 === 0 ?'142px':'140px',borderRightWidth:(index + 1) % 3 === 0 ?'0':'1px'}">
            <el-input v-model="item.money" style="width: 120px!important;" @focus="handleFocus(item)" :disabled="!!isPlateClose"></el-input>
          </div>
        </div>
      </div>
    </div>
    <!--    底部-->
    <div class="wave-footer">
      <span>金额</span>
      <input type="text" v-model="chiefMoney" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn " @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import {markSixMixin} from "@/config/mixin"
    import {mapGetters, mapMutations, mapActions} from "vuex";
    export default {
        name: "chiefShaw",
        mixins: [markSixMixin],
        computed: {
            ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose"])
        },
        props: {
            id: {
                type: String,
                default: () => ''
            },
        },
        data() {
            return {
                chiefMoney: '',
                chiefList: [],
            }
        },
        watch: {
            chiefMoney(val) {
                    this.chiefList.bet_json.map(item => {
                            if (item.checked) {
                                item.money = val ? val :''
                        }
                    })
            }
        },
        methods: {
            initTable() {
                this.$Api({
                    api_name: 'kkl.game.getLastBetInfo',
                    game_result_id: this.awardResult.game_log_info.game_result_id,
                    game_type_id: this.choosedGame.game_type_id,
                    pan_type: this.plateValue
                }, (err, data) => {
                    const {new_bet_rate} = data.data;
                    let jsonArr = JSON.parse(new_bet_rate);
                    jsonArr.forEach((item, index) => {
                        item.bet_json.map(child => {
                            item.bet_json.map((child, childIndex) => {
                                this.$set(child, 'money', '')
                                this.$set(child, 'checked', '')
                            })
                        });
                    });
                    this.resultList = jsonArr;
                    this.chiefList =  this.resultList[0]
                });
            },
            // 选择某一项
            chooseItem(item) {
                if (this.isPlateClose == 1) return;
                item.checked = !item.checked;
                if (this.chiefMoney) {
                    if (!item.checked) {
                        item.money = '';
                    } else {
                        item.money = this.chiefMoney;
                    }
                }
            },
            // 处理聚焦
            handleFocus(item) {
                if (this.isPlateClose == 1) return;
                item.checked = true;
                if (this.chiefMoney) {
                    item.money = this.chiefMoney;
                }
            },
            // 监听下注金额变化
            handleBetInput() {
                if (this.isPlateClose == 1) return;
                this.chiefList.bet_json.map(child => {
                    if (child.checked) {
                        child.money = this.chiefMoney;
                    }
                });
            },
            toReset(){
                this.chiefList.bet_json.map(item=>{
                    item.checked = false
                    item.money = ''
                })
            },
            onFocus(index) {
                //获取焦点
                this.chiefList.bet_json[index].checked = true
            },
            //选择下注
            toCheck(index){
                this.chiefList.bet_json[index].checked = !this.chiefList.bet_json[index].checked
                if(!this.chiefList.bet_json[index].checked){
                    //取消选择后，金额也取消
                    this.chiefList.bet_json[index].money = ''
                }else{
                    //选中时，底下的金额有就自动填充
                    if(this.chiefMoney){
                        this.chiefList.bet_json[index].money = this.chiefMoney

                    }
                }
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
                            let resultList = JSON.parse(data.data.game_type_info.bet_json)
                            resultList.forEach((item, index) => {
                                item.bet_json.map((child, childIndex) => {
                                    this.$set(child, 'money', '')
                                    this.$set(child, 'checked', '')
                                })
                            })
                            this.chiefList = resultList[0]
                        }
                    }
                );
            },
            //投注
            getGameBet() {
                let list = []
                let totalMoney = 0
                list = this.chiefList.bet_json.filter((item, index) => item.money > 0)
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
                    totalMoney = Number(totalMoney)+ Number(list[i].money)
                    var newObject = {};
                    newObject.key = list[i].key;
                    newObject.money = list[i].money;
                    newArray2.push(newObject);
                }
                let obj = []
                obj.push({
                    part: this.chiefList.part,
                    bet_json: newArray2,
                    name: this.chiefList.name
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
            ...mapActions([
              "refreshUserInfo",
            ])
        },
        mounted() {
            // this.getResult()
        },
    }
</script>

<style scoped lang='less'>
  .chief-shaw {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .flex-center {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .chief-shaw-box {
      border: 1px solid #e8e8e8;
      border-top: none;

      .chief-shaw-header {
        .wh(920px, 24px);
        background: #FFEFD4;
        border-top: 2px solid #F5A623;
        font-size: 12px;
        font-weight: 600;
        color: rgba(74, 65, 48, 1);
        display: flex;

        .item {
          display: flex;
          align-items: center;
          flex-wrap: wrap;
          border-bottom: 1px solid #e8e8e8;

          .item-left {
            .wh(69px, 100%);
            border-right: 1px solid #e8e8e8;
          }

          .item-center {
            .wh(94px, 100%);
            border-right: 1px solid #e8e8e8;

          }

          .item-right {
            .wh(140px, 100%);
            border-right: 1px solid #e8e8e8;
          }
        }

      }

      .chief-shaw-content {
        .wh(920px, auto);
        display: flex;
        flex-wrap: wrap;
        background:  #FFEFD4;
        .item {
          background: #FFFFFF;
          display: flex;
          flex-wrap: wrap;
          height: 32px;
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
          .item-left {
            .wh(69px, 100%);
            background: #FFEFD4;
            border-right: 1px solid #e8e8e8;
            font-size: 14px;
            font-weight: 600;
            color: rgba(133, 86, 9, 1);
          }

          .item-center {
            .wh(94px, 100%);
            border-right: 1px solid #e8e8e8;
            font-size: 12px;
            font-weight: 600;
            color: rgba(224, 32, 32, 1);
          }

          .item-right {
            .wh(140px, 100%);
            border-right: 1px solid #e8e8e8;
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
    height: 20px !important;
    line-height: 20px !important;
    box-sizing: border-box;
  }
</style>
