<template>
  <!--  七色波-->
  <div class="seven-color">
    <div class="head-tail-title">
      <div class="tail-number" v-for="(secItem,secIndex) in 2" :key="secIndex">
        <div class="tail-number-head" >
          <div class="left flex-center">色波</div>
          <div class="center flex-center">赔率</div>
          <div class="right flex-center w157">金额</div>
        </div>
        <div>
          <ul v-if="sevenWaveList && sevenWaveList.bet_json">
            <li v-for="(item,index) in sevenWaveList.bet_json.slice(secIndex *2,secIndex*2 + 2)" :key="index"  :class="{active: item.checked}">
              <div class="li-left flex-center" @click="chooseItem(item)">{{item.name}}</div>
              <div class="li-center flex-center" @click="chooseItem(item)">{{isPlateClose == 1 ? '--' : item.rate}}</div>
              <div class="li-right flex-center  w157" >
                <el-input v-model="item.money" style="width:180px" @focus="handleFocus(item)" :disabled="!!isPlateClose" ></el-input>
              </div>
            </li>
          </ul>
        </div>
      </div>

    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="sevenWaveMoney" class="input" @input="handleBetInput">
      <button class="sure-btn mr10" @click="sureBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import {mapGetters, mapMutations, mapActions} from "vuex";
    import {markSixMixin} from "@/config/mixin"
    export default {
        name: "sevenColorWave",
        mixins: [markSixMixin],
        computed: {
            ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose","gameResultId"])
        },
        props: {
            id: {
                type: String,
                default: () => ''
            },
        },
        data() {
            return {
                sevenWaveList:[],//七色波
                sevenWaveMoney: ''//下注金额
            }
        },
        methods: {
            // 初始化表格
            initTable() {
                console.log('是我呀七色波')
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
                    this.sevenWaveList = this.resultList[0]
                });
            },
            // 选择某一项
            chooseItem(item) {
                if (this.isPlateClose == 1) return;
                item.checked = !item.checked;
                if (this.sevenWaveMoney) {
                    if (!item.checked) {
                        item.money = '';
                    } else {
                        item.money = this.sevenWaveMoney;
                    }
                }
            },
            // 处理聚焦
            handleFocus(item) {
                if (this.isPlateClose == 1) return;
                item.checked = true;
                if (this.sevenWaveMoney) {
                    item.money = this.sevenWaveMoney;
                }
            },
            // 监听下注金额变化
            handleBetInput() {
                if (this.isPlateClose == 1) return;
                this.sevenWaveList.bet_json.map(child => {
                    if (child.checked) {
                        child.money = this.sevenWaveMoney;
                    }
                });
            },
            toReset(){
                this.sevenWaveList.bet_json.map(item=>{
                    item.checked = false
                    item.money = ''
                })
            },
            onFocus(index,secIndex){
                //获取焦点
                let index_ = secIndex === 1 ?index + 2:index
                console.log(index_)
                this.sevenWaveList.bet_json[index_].checked = true
            },
            //选择下注
            toCheck(index_,secIndex){
                let index = secIndex === 1 ?index_ + 2:index_
                this.sevenWaveList.bet_json[index].checked = !this.sevenWaveList.bet_json[index].checked
                if(!this.sevenWaveList.bet_json[index].checked){
                    //取消选择后，金额也取消
                    this.sevenWaveList.bet_json[index].money = ''
                }else{
                    //选中时，底下的金额有就自动填充
                    if(this.sevenWaveMoney){
                        this.sevenWaveList.bet_json[index].money = this.sevenWaveMoney

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
                                    this.$set(child, 'checked', false)
                                })
                            })
                            this.sevenWaveList = resultList[0]
                        }
                    }
                );
            },
            //投注
            getGameBet() {
                let list = this.sevenWaveList.bet_json.filter((item, index) => item.money > 0)
                var newArray2 = [];
                let totalMoney = 0;
                for (var i = 0; i < list.length; i++) {
                    var newObject = {};
                    totalMoney = totalMoney + list[i].money
                    newObject.key = list[i].key;
                    newObject.money = list[i].money;
                    newArray2.push(newObject);
                }
                let obj = []
                obj.push({
                    part: this.sevenWaveList.part,
                    bet_json: newArray2,
                    name: this.sevenWaveList.name
                })
                if(!list.length){
                    //说明没有输入任何金额，提示
                    this.$alert('下注内容不对，请重新下注', '提示', {
                        confirmButtonText: '确定',
                    });
                    return;
                }
                this.$Api(
                    {
                        api_name: "kkl.game.gameBet",
                        bet_json: obj,
                        game_result_id: this.gameResult.game_type_info.issue_num,
                        total_bet_money:totalMoney,
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
            }
        },
        mounted() {
            // this.getResult()
        },
    }
</script>

<style scoped lang='less'>
  .seven-color {
    .head-tail-title {
      margin-top: 10px;
      margin-bottom: 20px;
      .wh(920px, auto);
      display: flex;
      .tail-number-head {
        display: flex;
        width: 100%;
        border-top: 2px solid #F5A623;
        background: #FFEFD4;
        font-weight: 600;
        font-size: 14px;
        color: #4A4130;
        height: 32px;
        border-bottom: 1px solid #e8e8e8;

        .left {
          .wh(108px, 32px);
          border-right: 1px solid #e8e8e8;

        }

        .center {
          .wh(150px, 32px);
          border-right: 1px solid #e8e8e8;
        }


        .right {
          .wh(199px, 32px);
          border-right: 1px solid #e8e8e8;
        }
      }

      .tail-number {
        .wh(920px,auto);

        /*display: flex;*/

      }
      ul {
        border-bottom: none;
        display: flex;
        flex-wrap: wrap;
        width: 50%;
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
            .wh(108px, 32px);
            background: rgba(255, 239, 212, 1);
            font-size: 14px;
            font-weight: 600;
            color: rgba(133, 86, 9, 1);
            border-right: 1px solid #e8e8e8;
          }

          .li-center {
            .wh(150px, 32px);
            font-size: 12px;
            font-weight: 600;
            color: rgba(224, 32, 32, 1);
            border-right: 1px solid #e8e8e8;
          }

          .li-right {
            .wh(199px, 32px);
            border-right: 1px solid #e8e8e8;
          }
        }
      }
    }

    .seven-color-table {
      .wh(920px, auto);
      border: 1px solid #e8e8e8;
      .title {
        .wh(920, 32px);
        background:#FFEFD4 ;
        border-top: 2px solid #F5A623;
        display: flex;
        ul{
          display: flex;
          align-items: center;
          li{
            flex:1;

          }
        }

      }
    }

    .seven-color-wave {
      display: flex;
      .wh(920px, auto);
      margin: 0 auto;
      margin-top: 23px;
      margin-bottom: 10px;
    }

    .wave-footer {
      display: flex;
      justify-content: center;
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
        box-sizing:border-box;
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
  }
</style>
