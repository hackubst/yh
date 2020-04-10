<template>
  <div class="bet-list">
    <div class="back">
      <div @click="toBack">
        <img src="~images/icon/icon_jia@2x.png" alt/>返回添加一注
      </div>
    </div>
    <div class="money">
      <input type="text" v-model="money" placeholder="请输入统一金额" @input="handleBetInput"/>
    </div>
    <div class="list" v-for="(item,firstIndex) in list" :key="firstIndex">
      <div class="card" v-for="(child,secIndex) in item.show_bet_json" :key="secIndex" >
        <div class="card-left">
          <img src="~images/icon/icon_x@2x.png" alt @click="toDeleteItem(firstIndex,secIndex)"/>
          <div class="card-left-div">
            <p>{{child.name}}</p>
            <div>赔率:{{child.rate}}</div>
            <div>{{child.current_text}}</div>
          </div>
        </div>
        <div class="card-right">
          <div style="height:30px">
            输入金额
            <input type="text" v-model="child.money"/> 元
          </div>
          <div>1注，单注{{child.money}}元</div>
        </div>
      </div>
    </div>
    <div class="game-footer">
      <div class="game-footer-left">
        <button class="mr10" @click="toClearAll">清空</button>
        <p>已选</p>
        <span>{{total}}</span>个号
        <span>{{totalMoney}}元</span>&nbsp;&nbsp;&nbsp;余额&nbsp;
        <span>{{myTotalMoney}}</span>
      </div>
      <button class="bg" @click="toGameBet">下注</button>
    </div>
  </div>
</template>

<script>
    import {getStore, setStore} from "../../../config/utils";
    import {mapGetters} from "vuex";

    export default {
        name: "betList",
        data() {
            return {
                id: this.$route.query.id, // 当前游戏 game_type_id
                money: "", //同意金额
                list: [], //选择的号码列表
                myTotalMoney: this.$route.query.myTotalMoney //我的金豆
            };
        },
        computed: {
            ...mapGetters(["gameResultId", "plateValue"]),
            total() {
                let num = 0;
                this.list.forEach(result => {
                    num += result.bet_json.length;
                });
                return num;
            },
            totalMoney() {
                let all_bet = 0;
                this.list.forEach(result => {
                  result.total_bet_money = this.getAllBet(result.bet_json);
                  all_bet += +result.total_bet_money;
                });
                return all_bet;
            }
        },
        methods: {
            toBack() {
                this.$router.go(-1);
            },
            // 监听下注金额变化
            handleBetInput() {
                this.list.forEach(item => {
                    item.bet_json.map(child => {
                        if (child.checked) {
                            child.money = this.money;
                        }
                    });
                });
            },
            //删除某一注
            toDeleteItem(firstIndex, index) {
                this.list[firstIndex].show_bet_json.splice(
                    this.list[firstIndex].show_bet_json.findIndex(
                        (item_, index_) => index_ === index
                    ),
                    1
                );
                let lastResultList = this.list.filter(item => item.show_bet_json.length > 0);
                setStore("chooseList", lastResultList);
            },
            //清空
            toClearAll() {
                this.list = [];
                setStore("chooseList", []);
            },
            // 获取投注时选中的json字符串
            getBetJSON() {
                let json_arr = [];
                this.list.forEach(result => {
                    let bet_json = [];
                    let new_bet_json = {}
                    result.bet_json.forEach(item => {
                        console.log(item)
                      if (+result.game_type_id === 66 || +result.game_type_id === 78 || +result.game_type_id === 77 || +result.game_type_id === 76) {
                        // 合肖、连码、连肖连尾
                        bet_json.push({
                          key: item.key,
                          value: item.value,
                          money: item.money
                        });
                      } else {
                        bet_json.push({
                          key: item.key,
                          money: item.money
                        });
                      }
                    });
                    new_bet_json.part =  result.part;
                    new_bet_json.name =  result.name;
                    new_bet_json.bet_json  = bet_json
                    json_arr.push({
                      game_result_id: result.game_result_id,
                      game_type_id: result.game_type_id,
                      pankou: result.pankou,
                      bet_json: new_bet_json,
                      total_bet_money: this.getAllBet(result.bet_json)
                    });
                });
                return JSON.stringify(json_arr);
            },
            // 获取投注总金额
            getAllBet(list) {
                let all_bet = 0;
                console.log(list)
                list.forEach(item => {
                  if (item.current_text.split('-')[0] === '连码') {
                    // 连码
                    all_bet += +item.money * item.value.split('-').length;
                  } else {
                    all_bet += +item.money;
                  }
                });
                return all_bet;
            },
            //下注
            toGameBet() {
                let bet_json = this.getBetJSON();
                let all_bet = this.totalMoney;
                console.log({bet_json}, {all_bet});
                if (isNaN(all_bet) || all_bet <= 0) {
                    //说明没有输入任何金额，提示
                    this.$toast({
                        text: '下注内容不对，请重新下注'
                    });
                    return;
                }
                this.$Api(
                    {
                        api_name: "kkl.game.gameBetLiuhecai",
                        bet_json: bet_json,
                    },
                    (err, data) => {
                        if (!err) {
                            console.log("chenggongla ");
                            this.$toast({
                                text: '投注成功'
                            });
                            setTimeout(() => {
                                this.toClearAll();
                                this.$router.go(-1);
                            }, 1500);
                        } else {
                            this.$toast({
                                text: data.error_msg
                            })
                        }
                    }
                );
                console.log(this.list);
            }
        },
        created() {
            this.id = this.$route.query.id;
            console.log(this.id);
            this.list = JSON.parse(getStore("chooseList"));
            console.log(this.list)
            this.list.map(item=>{
                item.show_bet_json = []
                item.show_bet_json = item.bet_json
                let len = item.bet_json.length
                if(+item.game_type_id === 77){
                    item.show_bet_json = item.show_bet_json.slice(-1)
                }
            })
        },
        beforeCreate() {
            document.querySelector("body").setAttribute("style", "background:#f2f2f2");
        },

        beforeDestroy() {
            document.querySelector("body").setAttribute("style", "background:#ffffff");
        }
    };
</script>

<style scoped lang="less">
  .bet-list {
    .wh(100%, auto);
    background: #f2f2f2;
    padding-bottom: 100px;

    .back {
      .wh(100%, 66px);
      padding: 15px;
      box-sizing: border-box;

      div {
        width: 140px;
        height: 36px;
        background: rgba(227, 227, 227, 1);
        border-radius: 4px;
        border: 1px solid rgba(204, 204, 204, 1);
        display: flex;
        padding: 0 12px;
        box-sizing: border-box;
        align-items: center;
        justify-content: space-between;
        font-size: 15px;
        font-weight: 400;
        color: rgba(51, 51, 51, 1);

        img {
          .wh(17px, 17px);
        }
      }
    }

    .money {
      .wh(100%, 52px);
      padding: 8px 12px;
      box-sizing: border-box;
      background: #ffffff;

      input {
        .wh(100%, 100%);
        background: rgba(242, 242, 242, 1);
        border-radius: 4px;
        padding: 8px;
        box-sizing: border-box;
        font-size: 14px;
        font-weight: 400;
        color: #333333;
        border: none;
      }
    }

    .list {
      padding: 0 12px;
      box-sizing: border-box;

      .card {
        padding: 0 12px 0 17px;
        box-sizing: border-box;
        .wh(100%, 83px);
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 400;
        margin-top: 10px;
        color: rgba(102, 102, 102, 1);

        .card-left {
          display: flex;
          align-items: center;

          img {
            .wh(20px, 20px);
            margin-right: 13px;
          }

          .card-left-div {
            p {
              font-size: 15px;
              font-weight: 400;
              color: rgba(255, 30, 30, 1);
            }
          }
        }

        .card-right {
          padding-top: 8px;
          box-sizing: border-box;

          input {
            margin: 8px 0;
            width: 90px;
            height: 21px;
            border-radius: 4px;
            border: 1px solid rgba(204, 204, 204, 1);
            text-align: center;
            font-size: 14px;
            line-height: 21px;
            font-weight: 400;
            color: rgba(51, 51, 51, 1);
          }
        }
      }
    }

    .game-footer {
      .wh(100%, 52px);
      position: fixed;
      bottom: 0;
      left: 0;
      z-index: 1;
      background: rgba(254, 208, 147, 1);
      display: flex;
      align-items: center;
      padding: 0 8px;
      box-sizing: border-box;
      justify-content: space-between;

      button {
        width: 68px;
        height: 36px;
        background: rgba(255, 180, 80, 1);
        border-radius: 4px;
        font-size: 18px;
        font-weight: 500;
        color: rgba(255, 255, 255, 1);
        line-height: 36px;
        text-align: center;
        border: none;
      }

      .game-footer-left {
        display: flex;
        align-items: center;
        font-size: 14px;
        font-weight: 400;
        color: rgba(74, 65, 48, 1);

        span {
          font-size: 14px;
          font-weight: 500;
          color: rgba(255, 30, 30, 1);
        }
      }
    }
  }
</style>
