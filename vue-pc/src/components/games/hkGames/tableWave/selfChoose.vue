<template>
  <!--  自选不中-->
  <div class="self-choose">
    <table class="game_table bb" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th>种类</th>
        <th>自选不中</th>
      </tr>
      <tr>
        <td class="rate">赔率</td>
        <td class="f14 fcr" v-if=" currentItem[0] &&  currentItem[0].rate">{{ currentItem[0].rate}}</td>
      </tr>
    </table>
    <div class="content">
      <div class="first-table" v-for="(firstItem,firstIndex) in 5">
        <div class="first-table-title">
          <div class="item-center bg flex-center">
            号码
          </div>
          <div class="item-right flex-center bg">
            勾选
          </div>
        </div>
        <div class="first-table-content" >
          <div class="item"   @click.stop="toChoose(index,firstIndex)"
               v-for="(item,index) in list.slice(firstIndex*10,firstIndex*10 + 10)">
            <div class="item-left br1 flex-center">
              <div class='result-number' :class="[item.bg == 1 ? 'bgr':item.bg == 2 ? 'bgb':'bgg']"
                   v-if="firstIndex === 0">{{index > 8 ?item.number:`0${item.number}`}}
              </div>
              <div class='result-number' :class="[item.bg == 1 ? 'bgr':item.bg == 2 ? 'bgb':'bgg']"
                   v-if="firstIndex > 0">{{item.number}}
              </div>
            </div>
            <div class="item-right flex-center" >
              <img src="~images/icon/icon_xuanzhong@2x.png" alt="" v-if="item.checked">
              <img src="~images/icon/icon_weixuanzhong@2x.png" alt="" v-else>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="number" v-model="selfChooseMoney" class="input">
      <button class="sure-btn mr10" @click="getGameBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import { RED_WAVE, BLUE_WAVE, GREEN_WAVE,ALERT_TIME} from '@/config/config'
    import {mapGetters, mapMutations, mapActions} from "vuex";
    import {markSixMixin} from "@/config/mixin"
    export default {
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
                currentItem:[{
                   key:'',
                    name:'',
                    rate:'--'
                }],
                list: [
                    {number: '1'},
                    {number: '2'},
                    {number: '3'},
                    {number: '4'},
                    {number: '5'},
                    {number: '6'},
                    {number: '7'},
                    {number: '8'},
                    {number: '9'},
                    {number: '10'},
                    {number: '11'},
                    {number: '12'},
                    {number: '13'},
                    {number: '14'},
                    {number: '15'},
                    {number: '16'},
                    {number: '17'},
                    {number: '18'},
                    {number: '19'},
                    {number: '20'},
                    {number: '21'},
                    {number: '22'},
                    {number: '23'},
                    {number: '24'},
                    {number: '25'},
                    {number: '26'},
                    {number: '27'},
                    {number: '28'},
                    {number: '29'},
                    {number: '30'},
                    {number: '31'},
                    {number: '32'},
                    {number: '33'},
                    {number: '34'},
                    {number: '35'},
                    {number: '36'},
                    {number: '37'},
                    {number: '38'},
                    {number: '39'},
                    {number: '40'},
                    {number: '41'},
                    {number: '42'},
                    {number: '43'},
                    {number: '44'},
                    {number: '45'},
                    {number: '46'},
                    {number: '47'},
                    {number: '48'},
                    {number: '49'}
                ],
                totalRate:0,//赔率
                selfChooseMoney: '',//金钱
                selfChooseList: [],//数组
                rate: '',//赔率
                checkList:[],
            }
        },
        ...mapMutations({
            chooseGame: 'CHOOSE_GAME',
        }),
        methods: {
            clearChoose() {
                const clearFn = arr => {
                    for (let i = 0; i < arr.length; i++) {
                        arr[i].checked = false;
                    }
                };
                clearFn(this.list);
            },
            // 监听下注金额变化
            handleBetInput() {},
            //选择下注
            toChoose(index_, secIndex) {
                if (this.isPlateClose == 1) return;
                let index = secIndex === 0 ? index_ : 10 * secIndex + index_
                let checkList = []
                if (this.checkList.length < 12) {
                    //选择少于12个号码
                    this.list[index].checked = ! this.list[index].checked
                    this.list.map(item => {
                        if (item.checked) {
                            checkList.push(item)
                        } else {
                        }
                    })
                    this.checkList = checkList
                }
                else if(this.checkList.length === 12){
                    //已经选择了12个号，则取消已选择的
                    if(this.list[index].checked){
                        this.list[index].checked = !this.list[index].checked
                        this.checkList.map((item,childIndex)=>{
                            if(item.key === this.list[index].key){
                                this.checkList.splice(childIndex, 1)
                            }
                        })
                    }
                }
                if(checkList.length && checkList.length >4){
                    this.currentItem = this.resultList.bet_json.filter(item=>item.key == checkList.length)
                }else{
                    this.currentItem = [{
                        key:'',
                        name:'',
                        rate:'--'
                    }]
                }
            },
            //初始化数据
            initList(){
                this.list.map(child => {
                    if (RED_WAVE.indexOf(+child.number) !== -1) {
                        this.$set(child,'bg',1)
                    }
                    if(BLUE_WAVE.indexOf(+child.number) !== -1) {
                        this.$set(child,'bg',2)
                    }
                    if(GREEN_WAVE.indexOf(+child.number) !== -1) {
                        this.$set(child,'bg',3)
                    }
                    this.$set(child,'checked',false)
                })
            },
            // 初始化表格
            initTable() {
                console.log('自选不中')
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
                    this.resultList = jsonArr[0];
                    this.initList()
                    // this.sevenWaveList = this.resultList[0]
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
                            this.resultList = JSON.parse(data.data.game_type_info.bet_json)
                            console.log(this.resultList)
                        }
                    }
                );
            },
            //投注bet_joson
            getGameBet() {
                let list = []
                list = this.list.filter((item, index) => item.checked > 0)
                if(!this.selfChooseMoney || list.length < 5){
                    //说明没有输入任何金额，提示
                    this.$alert('下注内容不对，请重新下注', '提示', {
                        confirmButtonText: '确定',
                    });
                    return;
                }
                let value = ''
                var newObject = {};
                let bet_json = []
                for (var i = 0; i < list.length; i++) {
                    value = value ? value + ',' + list[i].number : list[i].number
                }
                newObject.key = this.currentItem[0].key;
                newObject.value = value
                newObject.money = parseInt(this.selfChooseMoney * list.length);
                bet_json.push(newObject)
                console.log(bet_json)
                let obj = []
                obj.push({
                    part: this.resultList.part,
                    bet_json:bet_json ,
                    name: this.resultList.name
                })
                if (!obj.length ||  parseInt(this.selfChooseMoney * list.length) <= 0 || !parseInt(this.selfChooseMoney * list.length)) {
                    //说明没有输入任何金额，提示
                    this.$alert("下注内容不对，请重新下注", "提示", {
                        confirmButtonText: "确定"
                    });
                    return;
                }
                this.$Api(
                    {
                        api_name: "kkl.game.gameBet",
                        bet_json: obj,
                        game_result_id: this.gameResultId,
                        total_bet_money: parseInt(this.selfChooseMoney * list.length),
                        game_type_id: this.choosedGame.game_type_id,
                        pankou: this.plateValue
                    },
                    (err, data) => {
                        if (!err) {
                            this.refreshUserInfo();
                            this.$msg("投注成功", "success", ALERT_TIME);
                        } else {
                            this.$msg(err.error_msg, "error", ALERT_TIME);
                        }
                    }
                );
            },
        },
        mounted() {
            // this.getResult()

        }
        }
</script>

<style scoped lang="less">
  .self-choose {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .content {
      display: flex;
      border: 1px solid #e8e8e8;
      margin-top: 10px;

      .br1 {
        border-right: 1px solid #e8e8e8;
      }

      .first-table {
        flex: 1;

        .first-table-title {
          display: flex;
          height: 25px;
          background: #FFEFD4;
          font-size: 12px;
          font-weight: 600;
          color: rgba(74, 65, 48, 1);
          line-height: 25px;
          border-top: 2px solid #F5A623;
          border-bottom: 1px solid #e8e8e8;

          .item-center {
            .wh(61px, 100%);
            padding: 0 10px;
            box-sizing: border-box;
            border-right: 1px solid #e8e8e8;
            border-left: 1px solid #e8e8e8;

            .result-number {
              width: 30px;
              height: 30px;
              /*border:1px solid #1F70FF;*/
              text-align: center;
              line-height: 30px;
              font-size: 16px;
              background: url('~images/bg/red_circle.png') no-repeat;
            }

            .bg {
              background: #FFEFD4;
              width: auto;
            }
          }

          .item-right {
            .wh(123px, 100%);
            background: #ffffff;

          }
        }

        .first-table-content {
          .wh(100%, 330px);
          background: #FFEFD4;
          .item {
            display: flex;
            .wh(100%, 32px);
            border-bottom: 1px solid #e8e8e8;
            align-items: center;
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
              text-align: center;
              .wh(60px, 100%);
              .result-number {
                width: 30px;
                height: 30px;
                /*border:1px solid #1F70FF;*/
                text-align: center;
                line-height: 30px;
                font-size: 16px;
                background: url('~images/bg/red_circle.png') no-repeat;
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

            .item-center {
              text-align: center;
              .wh(52px, 100%);
              font-size: 12px;
              font-weight: 600;
              color: rgba(224, 32, 32, 1);
              background: #ffffff;
            }

            .item-right {
              .wh(123px, 100%);
              background: #ffffff;
              border-right: 1px solid #e8e8e8;

              img {
                width: 16px;
                height: 16px;
              }
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

    .game_table {
      .wh(920px, auto);

      .rate {
        font-size: 14px;
        font-weight: 600;
        color: rgba(133, 86, 9, 1);
      }
    }

    .bbn {
      border-bottom: none !important;
    }

    .bt {
      border-top: 2px solid #F5A623;
      margin-top: 10px;
    }

    .even-code-table {
      .wh(920px, auto);
      display: flex;
      flex-wrap: wrap;
      background: #FFEFD4;
      /*border-left: 1px solid #e8e8e8;*/
      border-right: 1px solid #e8e8e8;
      border-bottom: 1px solid #e8e8e8;

      .table-item {
        display: flex;
        height: 32px;
        border-bottom: 1px solid #e8e8e8;

        .item-center {
          .wh(61px, 100%);
          padding: 0 10px;
          box-sizing: border-box;
          border-right: 1px solid #e8e8e8;
          border-left: 1px solid #e8e8e8;

          .result-number {
            width: 30px;
            height: 30px;
            /*border:1px solid #1F70FF;*/
            text-align: center;
            line-height: 30px;
            font-size: 16px;
            background: url('~images/bg/red_circle.png') no-repeat;
          }

          .bg {
            background: #FFEFD4;
            width: auto;
          }
        }

        .item-right {
          .wh(123px, 100%);
          background: #ffffff;
          /*border-let: 1px solid #e8e8e8;*/
        }

        .bg {
          background: #FFEFD4;
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
  .el-checkbox__input.is-checked .el-checkbox__inner {
    background: #7ED321;
  }

</style>
