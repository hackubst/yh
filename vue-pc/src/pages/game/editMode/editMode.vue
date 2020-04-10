<template>
  <div class="editMode">
    <div class="edit_choose">
      <div :class="currentIndex == index ? 'choose_item active': 'choose_item'" v-for="(item, index) in chooseItem"
        @click="chooseMode(index)" :key="index">{{item}}</div>
    </div>
    <div class="mode_name" v-if="currentIndex == 0">
      <input type="text" v-model="modeName" placeholder="填写模式名称">
    </div>
    <div class="mode_title">
      <span v-if="currentIndex == 0">标准投注选择</span>
      <span v-if="currentIndex == 1">选择模式进行编辑或者<span style="color: #D1913C;" @click="remove_model()">删除已选择模式</span></span>
    </div>
    <!-- 已有模式选择 -->
    <div class="haveModes" v-if="currentIndex == 1">
      <div class="mode_item" v-for="(item, index) in haveModesItem" :key="index"
        :class="chooseModeIndex == index ? 'active':''" @click="choose_model(index, item.mode_name, item.bet_mode_id, item.total_money, item.bet_json)">
        {{item.mode_name}}
      </div>
    </div>
    <!--投注筛选类型  -->
    <div class="mode_types">
      <div class="type_item" v-for="(item, index) in chooseTypes" :key="index"
        @click="item.typeFnType == 1 ? filterDivisor(item.divisor, item.remainder, index): filterSize(item.size, item.sizeType, index)"
        :class="ModeTypeIndex == index ? 'active':''">
        {{item.typeName}}
      </div>
    </div>
    <!-- 选择倍数总投注等 -->
    <div class="mode_operate">
      <div class="mode_multiply">
        <div class="multiply_item" v-for="(item, index) in mode_multiply" :key="index" @click="allDouble(item)">
          <span>{{item}}倍</span>
        </div>
      </div>
      <div class="operate_btn">
        <div class="btn_item" @click="betMyAll()">梭哈</div>
        <div class="btn_item" @click="allChoose()">全包</div>
        <div class="btn_item" @click="reverseChoose()">反选</div>
        <div class="btn_item" @click="clearChoose()">清除</div>
      </div>
      <div class="mode_bet">
        <span>总投注:</span>
        <input type="number" disabled v-model="allBet">
        <img src="~images/icon/icon_douzi@2x.png" alt="">
      </div>
      <div class="save_mode" @click="save()">
        保存
      </div>
    </div>
    <!-- 编辑模式表格组件 -->
    <edit-tablea :modeHalf="modeHalf" :modeTable="modeNumsMore[0]" @countDefault="countDefault"></edit-tablea>
  </div>
</template>
<script>
  import {
    mapMutations, mapActions
  } from 'vuex'
  import {
    CHOOSE_TYPES
  } from '@/config/config.js'
  import {
    gameCuntDown, betMixin
  } from '@/config/mixin.js'
  import editTablea from '@/components/games/editTable/editTablea.vue'
  export default {
    name: "editMode",
    mixins: [betMixin, gameCuntDown],
    components: {
      editTablea
    },
    data() {
      return {
        chooseTypes: CHOOSE_TYPES,
        chooseItem: ['新建模式', '已有模式'],
        haveModesItem: [],
        chooseModeIndex: 0,
        currentIndex: 0,
        modeName: '',
        bet_mode_id: '',
        allBet: ''
      };
    },
    methods: {
      // 获取模式列表
      get_model_list() {
        this.$Api({
            api_name: 'kkl.game.BetModeList',
            game_type_id: this.choosedGame.game_type_id
        }, (err, data) => {
            if (!err) {
                this.haveModesItem = data.data.bet_mode_list
                if (this.haveModesItem.length != 0) {
                  this.modeName = this.haveModesItem[this.chooseModeIndex].mode_name
                  this.bet_mode_id = this.haveModesItem[this.chooseModeIndex].bet_mode_id
                  this.allBet = this.haveModesItem[this.chooseModeIndex].total_money
                  let lastBetObj = JSON.parse(this.haveModesItem[this.chooseModeIndex].bet_json)
                  this.renderLastBet(lastBetObj)
                }
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
      },
      // 新建，修改模式
      chooseMode(index) {
        this.currentIndex = index
        if (index == 1) {
          this.get_model_list()
        } else {
          this.modeName = ''
          this.bet_mode_id = ''
          this.allBet = ''
          this.clearChoose()
        }
      },
      // 删除模式
      remove_model() {
        let id = this.haveModesItem[this.chooseModeIndex].bet_mode_id
        this.$Api({
            api_name: 'kkl.game.delBetMode',
            bet_mode_id: id,
        }, (err, data) => {
            if (!err) {
                this.$msg(data.data, 'success', 1500)
                this.chooseModeIndex = 0
                this.get_model_list()
                this.clearChoose()
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
      },
      choose_model(index, mode_name, id, num, bet_json) {
        this.clearChoose()
        this.chooseModeIndex = index
        this.modeName = mode_name
        this.bet_mode_id = id
        this.allBet = num
        let lastBetObj = JSON.parse(bet_json)
        this.renderLastBet(lastBetObj)
      },
      // 保存模式
      save() {
        let bet_json = this.getBetJSON(this.choosedGame.game_type_id)
        let parse_json = JSON.parse(bet_json)
        if (this.modeName == '' || this.allBet == '' || parse_json[0].bet_json.length == 0) {
          this.$msg('请填写模式名称', 'error', 1500)
          return
        }
        // 修改模式
        if (this.currentIndex == 1) {
            this.$Api({
                api_name: 'kkl.game.newBetMode',
                bet_json: bet_json,
                game_type_id: this.choosedGame.game_type_id,
                mode_name: this.modeName,
                total_money: this.allBet,
                bet_mode_id: this.bet_mode_id,
            }, (err, data) => {
                if (!err) {
                    this.$msg('修改成功', 'success', 1500)
                    this.get_model_list()
                } else {
                    this.$msg(err.error_msg, 'error', 1500)
                }
            })
          return
        }
        // 新建模式
        this.$Api({
            api_name: 'kkl.game.newBetMode',
            bet_json: bet_json,
            game_type_id: this.choosedGame.game_type_id,
            mode_name: this.modeName,
            total_money: this.allBet,
        }, (err, data) => {
            if (!err) {
                this.$msg('新建成功', 'success', 1500)
                this.modeName = ''
                this.bet_mode_id = ''
                this.allBet = ''
                this.clearChoose()
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
      },
      ...mapMutations({
        chooseGame: 'CHOOSE_GAME',
      }),
    }
  }
</script>
<style scoped lang='less'>
  .editMode {
    width: @main-width;
    margin: 0 auto;
  }

  .edit_choose {
    width: 208px;
    height: 42px;
    border-radius: 8px;
    overflow: hidden;
    background: #ccc;
    .sc(16px, #fff);
    text-align: center;
    line-height: 42px;
    display: flex;
    margin: 0 auto;
    margin-bottom: 20px;

    .choose_item {
      width: 50%;
    }

    .active {
      background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
    }
  }

  .mode_name {
    width: 208px;
    height: 42px;
    margin: 0 auto;
    margin-bottom: 10px;
    border-bottom: 1px solid #999999;

    input {
      height: 42px;
      font-size: 16px;
      text-align: center;
    }
  }

  .mode_title {
    height: 22px;
    padding-left: 10px;
    box-sizing: border-box;
    position: relative;
    line-height: 22px;
    color: #4A4130;
    font-size: 16px;
    margin-bottom: 10px;

    &:before {
      content: "";
      width: 4px;
      height: 22px;
      background: #D1913C;
      left: 0;
      top: 0;
      position: absolute;
    }
  }

  .haveModes {
    width: @main-width;
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;

    .mode_item {
      padding: 7px 23px;
      box-sizing: border-box;
      background: #FFEFD4;
      color: #D1913C;
      font-size: 16px;
      margin-right: 10px;
      border-radius: 4px;
      margin-bottom: 10px;
    }

    .active {
      background: #D1913C;
      color: #FFF8EF;
    }

  }

  .mode_types {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 10px;

    .type_item {
      width: 48px;
      height: 30px;
      line-height: 30px;
      font-size: 14px;
      text-align: center;
      background: #FFEFD4;
      color: #D1913C;
      margin-right: 2px;
      margin-bottom: 2px;
      &:hover{
        cursor: pointer;
      }
    }

    .active {
      background: #D1913C;
      color: #fff;
    }
  }

  .mode_operate {
    display: flex;
    justify-content: space-between;
    height: 42px;
    margin-bottom: 20px;

    .mode_multiply {
      width: 498px;
      height: 42px;
      display: flex;
      background: #D1913C;
      border-radius: 4px;
      padding: 10px;
      box-sizing: border-box;
      align-items: center;

      .multiply_item {
        flex-grow: 1;
        font-size: 16px;
        color: #FFF8EF;
        position: relative;
        text-align: center;

        &:after {
          content: "";
          width: 1px;
          height: 12px;
          background: #FFF8EF;
          position: absolute;
          right: 0;
          top: 50%;
          margin-top: -6px;
        }
        &:hover {
          cursor: pointer;
        }
      }

      .multiply_item:last-child {
        &:after {
          content: "";
          width: 0px;
          height: 0px;
        }
      }
    }

    .operate_btn {
      display: flex;
      justify-content: space-between;

      .btn_item {
        width: 42px;
        height: 42px;
        border-radius: 4px;
        border: 1px solid rgba(209, 145, 60, 1);
        margin-left: 5px;
        background: rgba(255, 239, 212, 1);
        text-align: center;
        line-height: 42px;
        color: #D1913C;
        font-size: 16px;
        box-sizing: border-box;
        &:hover{
          cursor: pointer;
        }
      }
    }

    .mode_bet {
      width: 171px;
      height: 42px;
      background: #FFEFD4;
      border-radius: 4px;
      margin-left: 5px;
      margin-right: 5px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid rgba(209, 145, 60, 1);
      white-space: nowrap;
      font-size: 16px;
      color: #D1913C;

      input {
        width: 76px;
        color: #FB3A3A;
        margin: 0 5px;
        font-size: 18px;
      }

      img {
        width: 16px;
        height: 16px;
      }
    }

    .save_mode {
      width: 52px;
      height: 42px;
      line-height: 42px;
      text-align: center;
      font-size: 16px;
      color: #FFF8EF;
      background: linear-gradient(360deg, rgba(209, 145, 60, 1) 0%, rgba(255, 209, 148, 1) 100%);
      border-radius: 4px;
    }
  }
</style>