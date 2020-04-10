<template>
  <div class="gameMode">
    <div class="choose-box flex-center mode-multiply">
      <div
        class="choose-item multiply-item"
        v-for="(item, index) in mode_multiply"
        :key="index"
        @click="allDouble(item)"
      >
        <span>{{item}}倍</span>
      </div>
    </div>
    <!-- / 选择倍数 -->

    <div class="choose-box flex-center mode-type">
      <div
        class="choose-item type-item"
        :class="{active: ModeTypeIndex == index}"
        v-for="(item, index) in chooseTypes"
        :key="index"
        @click="item.typeFnType == 1 ? filterDivisor(item.divisor, item.remainder, index): filterSize(item.size, item.sizeType, index)"
      >{{item.typeName}}</div>
    </div>
    <!-- / 选择投注筛选类型 -->

    <div class="mode-form">
      <div class="form-cell flex">
        <span class="label">投注模式</span>
        <div class="info">
          <select name="mode" id="bet_mode" v-model="currentMode">
            <option
              :value="mode.bet_mode_id"
              v-for="(mode, index) in modeList"
              :key="index"
            >{{mode.mode_name}}</option>
          </select>
          <div class="img-box flex-center">
            <img src="~@/assets/images/icon/icon_rightarrow@2x.png" alt />
          </div>
        </div>
      </div>
      <div class="form-cell flex">
        <span class="label">模式名称</span>
        <div class="info">
          <input type="text" v-model="modeName" placeholder="填写模式名称" />
        </div>
      </div>
      <div class="form-cell flex">
        <span class="label">投注总数</span>
        <span class="all-bet">{{allBet}}</span>
        <div class="btn-group flex">
          <div class="btn save-btn flex-center" @click="save">保存</div>
          <div class="btn del-btn flex-center" v-if="currentMode !== -1" @click="removeMode">删除</div>
        </div>
      </div>
    </div>
    <!-- / 模式相关表单 -->

    <div class="mode-table">
      <table cellpadding="0" cellspacing="0">
        <tr>
          <th>号码</th>
          <th>赔率</th>
          <th>投注</th>
        </tr>
        <tr v-for="(item, index) in modeNums[0]" :key="index">
          <td style="width: 74px;">
            <div class="ball_box" v-if="oneResultGame(game_type_id)">
              <div class="ball">{{item.key}}</div>
            </div>
            <div class="ball_box" v-else>
              <div class="ball">{{item.name}}</div>
            </div>
          </td>
          <td>{{item.rate}}</td>
          <td>
            <div class="inp_box">
              <input
                type="number"
                v-model="item.bet"
                @focus="inputFocus(item)"
                @keyup="inputChange(item)"
              />
            </div>
          </td>
        </tr>
      </table>
    </div>
    <!-- / 表格 -->
  </div>
</template>

<script>
import { CHOOSE_TYPES, DEFAULT_GAME_BET } from "@/config/config.js";
import { gameMixns, gameTypeMixins } from "@/config/gameMixin";
import { mapGetters } from "vuex";
export default {
  name: "gameMode",
  mixins: [gameMixns, gameTypeMixins],
  data() {
    return {
      game_type_id: this.$route.query.game_type_id,
      chooseTypes: CHOOSE_TYPES, // 投注类型
      currentMode: -1, // 当前投注模式
      modeList: [], // 投注模式列表
      modeName: "", // 模式名称
      bet_mode_id: ""
    };
  },
  computed: {
    ...mapGetters(["awardResult"])
  },
  async created() {
    await this.getModeList();
    this.getModeNums(this.awardResult.game_type_info.bet_json);
  },
  methods: {
    // 获取模式列表
    getModeList() {
      this.$Api(
        {
          api_name: "kkl.game.BetModeList",
          game_type_id: this.$route.query.game_type_id
        },
        (err, res) => {
          if (!err) {
            const { bet_mode_list } = res.data;
            let newMode = {
              bet_mode_id: -1,
              mode_name: "新建模式",
              total_money: 0,
              bet_json: ""
            };
            this.modeList = [newMode, ...bet_mode_list];
          } else {
            this.$toast({
              text: err.error_msg
            });
          }
        }
      );
    },
    // 点击保存
    save() {
      let bet_json = this.getBetJSON();
      let parse_json = JSON.parse(bet_json)
      if (this.modeName == '' || this.allBet == '' || parse_json[0].bet_json.length == 0) {
        this.$toast({
          text: '请填写模式名称'
        });
        return
      }
      // 新建模式
      if (this.currentMode === -1) {
        this.$Api({
          api_name: 'kkl.game.newBetMode',
          bet_json: bet_json,
          game_type_id: this.game_type_id,
          mode_name: this.modeName,
          total_money: this.allBet,
        }, (err, res) => {
          if (!err) {
            this.$toast({
              text: "新建成功"
            });
            this.modeName = ''
            this.bet_mode_id = ''
            this.allBet = ''
            this.clearChoose()
          }
        });
      } else {
        this.$Api({
          api_name: 'kkl.game.newBetMode',
          bet_json: bet_json,
          game_type_id: this.game_type_id,
          mode_name: this.modeName,
          total_money: this.allBet,
          bet_mode_id: this.bet_mode_id,
        }, (err, res) => {
          if (!err) {
            this.$toast({
              text: "修改成功"
            });
            this.getModeList();
          }
        });
      }
    },
    // 点击删除
    removeMode() {
      let id = this.currentMode;
      this.$Api({
        api_name: 'kkl.game.delBetMode',
        bet_mode_id: id,
      }, (err, res) => {
        if (!err) {
          this.$toast({
            text: res.data
          });
          this.currentMode = -1;
          this.getModeList();
          this.modeName = ''
          this.bet_mode_id = ''
          this.allBet = ''
        } else {
          this.$toast({
            text: err.error_msg
          });
        }
      });
    }
  },
  watch: {
    currentMode(val) {
      if (val === -1) {
        this.modeName = ''
        this.bet_mode_id = ''
        this.allBet = ''
        this.clearChoose()
      } else {
        let cur_mode = this.modeList.find(item => item.bet_mode_id === val);
        this.modeName = cur_mode.mode_name;
        this.bet_mode_id = cur_mode.bet_mode_id;
        this.allBet = cur_mode.total_money;
        let lastBetObj = JSON.parse(cur_mode.bet_json);
        this.renderLastBet(lastBetObj);
      }
    }
  }
};
</script>

<style lang="less" scoped>
.gameMode {
  flex: 1;
  background-color: #fff;
  .choose-box {
    padding: 12px 10px;
    padding-bottom: 4px;
    flex-wrap: wrap;
    .choose-item {
      .sc(14px, #4a4130);
      padding: 4px 7px;
      border: 1px solid #979797;
      border-radius: 4px;
      margin-right: 8px;
      margin-bottom: 8px;
      background: #fff;
      &.active {
        .sc(14px, #fff);
        background: #ff851e;
      }
    }
  }
  .mode-multiply {
    background-color: #fff7ec;
  }
  .mode-form {
    padding: 12px;
    padding-bottom: 22px;
    .form-cell {
      position: relative;
      width: 100%;
      margin-bottom: 12px;
      .label {
        margin-right: 7px;
        .sc(16px, #333);
        line-height: 22px;
        white-space: nowrap;
      }
      .info {
        position: relative;
        height: 32px;
        .sc(16px, #333);
        line-height: 32px;
        background-color: #f2f2f2;
        border-radius: 4px;
        input {
          .wh(100%, 100%);
          padding-left: 8px;
          border: none;
          background-color: transparent;
          box-sizing: border-box;
        }
        .img-box {
          position: absolute;
          top: 0;
          right: 0;
          .wh(32px, 32px);
          img {
            .wh(16px, 16px);
          }
        }
      }
      .all-bet {
        margin-left: 8px;
        .sc(16px, #333);
        line-height: 32px;
      }
      .btn-group {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0;
        .btn {
          .wh(72px, 36px);
          .sc(16px, #fff);
          line-height: normal;
          text-align: center;
          white-space: nowrap;
          font-weight: bold;
          border-radius: 4px;
        }
        .save-btn {
          background-color: #ff851e;
        }
        .del-btn {
          margin-left: 10px;
          background-color: #ff1e1e;
        }
      }
      &:first-child {
        .info {
          width: calc(100% - 73px);
          select {
            .wh(100%, 100%);
            padding-left: 8px;
            border: none;
            background-color: transparent;
            box-sizing: border-box;
          }
        }
      }
      &:nth-child(2) {
        .info {
          width: calc(100% - 73px);
        }
      }
      &:last-child {
        margin-bottom: 0;
      }
    }
  }
  .ball_box {
    display: flex;
    padding: 10px 23px;
    box-sizing: border-box;
    .ball {
      width: 28px;
      height: 28px;
      border-radius: 28px;
      line-height: 28px;
      font-size: 14px;
      white-space: nowrap;
    }
  }

  .inp_box {
    height: 48px;
    display: flex;
    justify-content: center;
    align-items: center;
    input {
      width: 192px;
      // flex: 1;
      height: 32px;
      background: #f2f2f2;
      border-radius: 4px;
      padding-left: 4px;
      box-sizing: border-box;
      border: none;
    }
  }
}
</style>