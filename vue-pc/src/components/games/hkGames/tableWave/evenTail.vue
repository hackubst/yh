<template>
  <!--  连肖连尾-->
  <div class="even-tail">
    <ul class="tab_nav_first">
      <li :class="{active: current_index == index}" v-for="(item, index) in list" :key="index"
          @click="changeIndex(index)">{{item.title}}
      </li>
    </ul>
    <table class="game_table" border="1" cellpadding="0" cellspacing="0">
      <tr>
        <th style="width: 53px;">色波</th>
        <th style="width: 283px;">赔率</th>
        <th style="width: 59px;">金额</th>
        <th style="width: 60px;">勾选</th>
        <th style="width: 53px;">色波</th>
        <th style="width: 283px;">赔率</th>
        <th style="width: 59px;">金额</th>
        <th style="width: 60px;">勾选</th>
      </tr>
    </table>
    <div class="even-tail-table">
      <div class="table-item" v-if="evenTailList && evenTailList[current_index] && evenTailList[current_index].bet_json">
        <div v-for="(item,index) in evenTailList[current_index].bet_json" :key="index" class="item" @click="toChoose(index)" >
          <div class="item-left flex-center">{{item.name}}</div>
          <div class="item-center">
            <ul>
              <li v-for="(child_,secindex) in item.list" class="item-li">
                <div class='result-number' v-if='child_ && child_.bg' :class="[child_.bg === 1 ? 'bgr':child_.bg === 2 ? 'bgb':'bgg']">{{child_.number}}</div>
              </li>
            </ul>
          </div>
          <div class="item-rate flex-center">{{isPlateClose == 1 ? '--' : item.rate}}</div>
          <div class="item-right flex-center" >
            <img src="~images/icon/icon_xuanzhong@2x.png" alt="" v-if="item.checked">
            <img src="~images/icon/icon_weixuanzhong@2x.png" alt="" v-else>
          </div>
        </div>
      </div>
    </div>
    <div class="wave-footer">
      <span>金额</span>
      <input type="text" v-model="evenTailMoney" class="input">
      <button class="sure-btn mr10" @click="getGameBet">确定</button>
      <button class="sure-btn" @click="clearChoose">重置</button>
    </div>
  </div>
</template>

<script>
    import {ALERT_TIME} from "@/config/config";
    import {  getCombination } from "@/config/utils";
    import {mapGetters, mapMutations, mapActions} from "vuex";
    import {markSixMixin} from "@/config/mixin"
    export default {
        mixins: [markSixMixin],
        computed: {
            ...mapGetters(["awardResult", "choosedGame", "plateValue", "isPlateClose","gameResultId"])
        },

        data() {
            return {
                list_:[
                    {   name:'鼠',
                        otherName:'0尾',
                        otherList:[{number:'10',bg:2},{number:'20',bg:2},{number:'30',bg:1},{number:'40',bg:1}],
                        numberList:[{number:'01',bg:1},{number:'13',bg:1},{number:'25',bg:2},{number:'27',bg:2},{number:'49',bg:3}],
                    },
                    {  name:'马',
                        otherName:'5尾',
                        otherList:[{number:'05',bg:3},{number:'15',bg:2},{number:'25',bg:2},{number:'35',bg:1},{number:'45',bg:1}],
                        numberList:[{number:'07',bg:1},{number:'19',bg:1},{number:'31',bg:2},{number:'43',bg:3}],
                    },
                    {   name:'牛',
                        otherName:'1尾',
                        otherList:[{number:'01',bg:1},{number:'11',bg:3},{number:'21',bg:3},{number:'31',bg:2},{number:'41',bg:2}],
                        numberList:[{number:'12',bg:1},{number:'24',bg:1},{number:'36',bg:2},{number:'48',bg:2}],
                    },
                    {   name:'羊',
                        otherName:'6尾',
                        otherList:[{number:'06',bg:3},{number:'16',bg:3},{number:'26',bg:2},{number:'36',bg:2},{number:'46',bg:1}],
                        numberList:[{number:'06',bg:3},{number:'18',bg:1},{number:'30',bg:1},{number:'42',bg:2}],
                    },
                    {   name:'虎',
                        otherName:'2尾',
                        otherList:[{number:'02',bg:1},{number:'12',bg:1},{number:'22',bg:3},{number:'32',bg:3},{number:'42',bg:2}],
                        numberList:[{number:'11',bg:3},{number:'23',bg:1},{number:'35',bg:1},{number:'47',bg:2}],
                    },
                    {   name:'猴',
                        otherName:'7尾',
                        otherList:[{number:'07',bg:1},{number:'17',bg:3},{number:'27',bg:3},{number:'37',bg:2},{number:'47',bg:2}],
                        numberList:[{number:'05',bg:3},{number:'17',bg:3},{number:'29',bg:1},{number:'41',bg:2}],
                    },
                    {   name:'兔',
                        otherName:'3尾',
                        otherList:[{number:'03',bg:2},{number:'13',bg:1},{number:'23',bg:1},{number:'33',bg:3},{number:'43',bg:3}],
                        numberList:[{number:'10',bg:2},{number:'22',bg:3},{number:'34',bg:1},{number:'46',bg:1}],
                    },
                    {   name:'鸡',
                        otherName:'8尾',
                        otherList:[{number:'08',bg:1},{number:'18',bg:1},{number:'28',bg:3},{number:'38',bg:3},{number:'48',bg:2}],
                        numberList:[{number:'04',bg:2},{number:'16',bg:3},{number:'28',bg:3},{number:'40',bg:1}],
                    },
                    {   name:'龙',
                        otherName:'4尾',
                        otherList:[{number:'04',bg:2},{number:'14',bg:2},{number:'24',bg:1},{number:'34',bg:1},{number:'44',bg:3}],
                        numberList:[{number:'09',bg:2},{number:'21',bg:3},{number:'33',bg:3},{number:'45',bg:1}],
                    },
                    {   name:'蛇',
                        otherName:'9尾',
                        otherList:[{number:'09',bg:2},{number:'19',bg:1},{number:'29',bg:1},{number:'39',bg:3},{number:'49',bg:3}],
                        numberList:[{number:'08',bg:1},{number:'20',bg:2},{number:'32',bg:3},{number:'44',bg:3}],
                    },
                    {   name:'狗',
                        numberList:[{number:'03',bg:2},{number:'15',bg:2},{number:'27',bg:3},{number:'39',bg:3}],
                    },
                    {   name:'猪',
                        numberList:[{number:'02',bg:1},{number:'14',bg:2},{number:'26',bg:2},{number:'38',bg:3}],
                    },
                ],
                evenTailMoney: '',
                current_index: 0,
                evenTailList:[],
                list: [{title: '二连肖'}, {title: '三连肖'}, {title: '四连肖'}, {title: '五连肖'}, {title: '二连尾'}, {title: '三连尾'}, {title: '四连尾'}, {title: '五连尾'}],
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
                this.$Api({
                        api_name: 'kkl.game.getLastBetInfo',
                        game_result_id: this.gameResultId,
                        game_type_id: this.choosedGame.game_type_id,
                        pan_type: this.plateValue
                    }, (err, data) => {
                        const {new_bet_rate} = data.data;
                        let jsonArr = JSON.parse(new_bet_rate);
                        let tempArr = [];
                        console.log(jsonArr)
                        this.list_.map((firstItem,firstIndex)=> {
                            jsonArr.forEach((item,index) => {
                                item.bet_json.map(child => {
                                    if(index === 0 || index === 1 || index === 2 || index === 3){
                                        if(firstItem.name === child.name){
                                            this.$set(child, 'list', firstItem.numberList)
                                            console.log(child)
                                        }
                                    }else{
                                        if(firstItem.otherName === child.name){
                                            this.$set(child, 'list', firstItem.otherList)
                                        }
                                    }
                                    this.$set(child, 'checked', false)
                                })
                            })
                        })
                        this.resultList = jsonArr
                        this.evenTailList = this.resultList
                    }
                );
            },
            //选择下注
            toChoose(index) {
                if (this.isPlateClose == 1) return;
               this.evenTailList[this.current_index].bet_json[index].checked = !this.evenTailList[this.current_index].bet_json[index].checked
            },
            // 切换
            changeIndex(index) {
                this.current_index = index
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
                            this.list_.map((firstItem,firstIndex)=>{
                                this.resultList.forEach((item, index) => {
                                    item.bet_json.map((child,childIndex) => {
                                        if(index === 0 || index === 1 || index === 2 || index === 3){
                                            if(firstItem.name === child.name){
                                                this.$set(child, 'list', firstItem.numberList)
                                            }
                                        }else{
                                            if(firstItem.otherName === child.name){
                                                this.$set(child, 'list', firstItem.otherList)
                                            }
                                        }

                                        this.$set(child, 'checked', false)
                                    })
                                })
                            })
                            this.evenTailList = this.resultList
                        }
                    }
                );
            },
            //投注
            getGameBet() {
                let list = []
                let value = []
                list = this.evenTailList[this.current_index].bet_json.filter((item, index) => item.checked > 0)
                list.map(item=>{
                    value.push(item.key)
                })
                let currentIndex;
                if(this.current_index === 0 || this.current_index === 4){
                    currentIndex = 2
                }else if(this.current_index === 1 || this.current_index === 5){
                    currentIndex = 3
                }else if(this.current_index === 2 || this.current_index === 6){
                    currentIndex = 4
                }
                else if(this.current_index === 3 || this.current_index === 7){
                    currentIndex = 5
                }
                let len =((getCombination(value,currentIndex)).split('-').length)
                var newArray2 = [];
                for (var i = 0; i < list.length; i++) {
                    var newObject = {};
                    newObject.key = list[i].key;
                    newObject.money =this.evenTailMoney;
                    newObject.value = getCombination(value,currentIndex)
                    newArray2.push(newObject);
                }
                let obj = []
                obj.push({
                    part: this.evenTailList[this.current_index].part,
                    bet_json: newArray2,
                    name: this.evenTailList[this.current_index].name
                })
                if (!obj.length ||  parseInt(this.evenTailMoney * len) <= 0 || !parseInt(this.evenTailMoney * len)) {
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
                        total_bet_money: parseInt(this.evenTailMoney * len),
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
            }
        },
        mounted() {
            // this.getResult()
        }
    }
</script>

<style scoped lang='less'>
  .even-tail {
    .wh(920px, auto);
    margin: 0 auto;
    margin-top: 23px;
    margin-bottom: 10px;

    .flex-center {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .game_table {
      .wh(920px, auto);
    }

    .tab_nav_first {

      display: flex;
      justify-content: flex-start;
      align-items: center;

      li {
        .wh(78px, 30px);
        margin-right: 10px;
        background-color: #E8E8E8;
        text-align: center;
        line-height: 30px;
        border-radius: 4px 4px 0px 0px;
        .sc(14px, #4A4130);
      }

      .active {
        background-color: #D1913C;
        .sc(14px, #FFF8EF);
      }
    }

    .even-tail-table {
      .wh(920px, auto);
      display: flex;
      border-left: 1px solid #e8e8e8;

      .table-item {
        display: flex;
        flex-wrap: wrap;

        .item {
          display: flex;
          align-items: center;
          height: 32px;
          border-bottom: 1px solid #e8e8e8;
          &:hover {
            background: #FFEFD4;
            & > div {
              background: #FFEFD4;
            }
          }
          .item-left {
            width: 53px;
            height: 100%;
            background: #FFEFD4;
            border-right: 1px solid #e8e8e8;
            font-size: 14px;
          }

          .item-center {
            .wh(284px, 100%);
            padding: 0 10px;
            box-sizing: border-box;
            /*background: #ffffff;*/
            border-right: 1px solid #e8e8e8;

            ul {
              display: flex;
              /*background: #ffffff;*/

              li {
                width: 30px;
                text-align: center;
                margin-right: 4px;
                flex: 0;
                /*background: #ffffff;*/

                .result-number {
                  width: 30px;
                  height: 30px;
                  /*border:1px solid #1F70FF;*/
                  text-align: center;
                  line-height: 30px;
                  font-size: 16px;
                }
                .bgr{
                  background: url('~images/bg/red_circle.png') no-repeat;
                }
                .bgb{
                  background: url('~images/bg/blue_circle.png') no-repeat;
                }
                .bgg{
                  background: url('~images/bg/green_circle.png') no-repeat;
                }
              }
            }

          }

          .item-rate {
            border-right: 1px solid #e8e8e8;
            .wh(59px, 100%);
            background: #ffffff;
            color: #E02020;
            font-size: 14px;
            border-right: 1px solid #e8e8e8;
          }

          .item-right {
            .wh(60px, 100%);
            background: #ffffff;
            border-right: 1px solid #e8e8e8;
            img{
              .wh(16px,16px);
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

