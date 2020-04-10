<template>
  <div class="gameAuto">
    <div class="title">自动投注设置</div>
    <div class="auto-form">
      <div class="form-cell flex">
        <span class="label">开始模式</span>
        <div class="info">
          <select v-model="bet_mode_id" :disabled="bet_boolean">
            <option
              v-for="(item, index) in bet_list"
              :key="index"
              :value="item.bet_mode_id"
            >{{item.mode_name}}</option>
          </select>
          <div class="img-box flex-center">
            <img src="~@/assets/images/icon/icon_rightarrow@2x.png" alt />
          </div>
        </div>
      </div>
      <div class="form-cell flex">
        <div class="form-inline flex">
          <span class="label">开始期号</span>
          <div class="info">
            <input type="number" v-model="start_num" :disabled="bet_boolean" />
          </div>
        </div>
        <div class="form-inline flex">
          <span class="label">期数</span>
          <div class="info">
            <input type="number" v-model="number" :disabled="bet_boolean" />
          </div>
        </div>
      </div>
      <div class="form-cell flex">
        <div class="form-inline flex">
          <span class="label">乐豆上限</span>
          <div class="info">
            <input type="number" v-model="max_bean" :disabled="bet_boolean" />
          </div>
        </div>
        <div class="form-inline flex">
          <span class="label">下限</span>
          <div class="info">
            <input type="number" v-model="min_bean" :disabled="bet_boolean" />
          </div>
        </div>
      </div>
    </div>
    <div class="auto-table">
      <table>
        <tr>
          <th>投注模式</th>
          <th>投注乐豆</th>
          <th>赢后使用</th>
          <th>输后使用</th>
        </tr>
        <tr v-for="(item, index) in bet_list" :key="index">
          <td>{{item.mode_name}}</td>
          <td>{{item.total_money}}</td>
          <td>
            <div class="select-box flex-center">
              <div class="down-menu flex">
                <select v-model="item.win_mode" :disabled="bet_boolean">
                  <option
                    v-for="(item, idx) in bet_list"
                    :key="idx"
                    :value="item.bet_mode_id"
                  >{{item.mode_name}}</option>
                </select>
                <div class="img-box flex-center">
                  <img src="~@/assets/images/icon/icon_rightarrow@2x.png" alt />
                </div>
              </div>
            </div>
          </td>
          <td>
            <div class="select-box flex-center">
              <div class="down-menu flex">
                <select v-model="item.loss_mode" :disabled="bet_boolean">
                  <option
                    v-for="(item, idx) in bet_list"
                    :key="idx"
                    :value="item.bet_mode_id"
                  >{{item.mode_name}}</option>
                </select>
                <div class="img-box flex-center">
                  <img src="~@/assets/images/icon/icon_rightarrow@2x.png" alt />
                </div>
              </div>
            </div>
          </td>
        </tr>
      </table>
      <div class="start-bet flex-center">
        <div class="bet-btn" @click="begin_bet()">{{bet_boolean? '取消投注':'开始投注'}}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { countDown } from "@/config/mixin";
export default {
  name: "gameAuto",
  mixins: [countDown],
  data() {
    return {
      bet_mode_id: "",
      bet_boolean: false,
      bet_list: [],
      start_num: "",
      number: "3000000",
      max_bean: "999999999",
      min_bean: "100",
      click: true,
    };
  },
  created() {
    this.start_num = Number(this.newestItem.issue) + 4;
    setTimeout(() => {
      if (this.bet_boolean) {
        this.bet_mode_id = this.bet_info.start_mode_id;
        this.start_num = this.bet_info.start_issue;
        this.number = this.bet_info.issue_number;
        this.max_bean = this.bet_info.max_money;
        this.min_bean = this.bet_info.min_money;
        return;
      }
      if (this.bet_list.length > 0) {
        this.bet_mode_id = this.bet_list[0].bet_mode_id;
      }
    }, 500);
    this.get_bet_detail();
  },
  methods: {
    // 获取投注模式列表
    get_bet_list() {
      this.$Api(
        {
          api_name: "kkl.game.BetModeList",
          game_type_id: this.$route.query.game_type_id
        },
        (err, data) => {
          if (!err) {
            console.log({data});
            if (this.bet_boolean == true) {
              data.data.bet_mode_list.map(item => {
                this.$set(item, "win_mode", item.win_change);
                this.$set(item, "loss_mode", item.loss_change);
              });
            } else {
              data.data.bet_mode_list.map(item => {
                this.$set(item, "win_mode", item.bet_mode_id);
                this.$set(item, "loss_mode", item.bet_mode_id);
              });
            }
            this.bet_list = data.data.bet_mode_list;
          } else {
            this.$toast({ text: err.error_msg });
          }
        }
      );
    },
    // 获取自动投注详情
    get_bet_detail() {
      if (!this.$route.query.game_type_id) return;
      this.$Api(
        {
          api_name: "kkl.game.getAutoBetInfo",
          type: "",
          game_type_id: this.$route.query.game_type_id
        },
        (err, data) => {
          if (!err) {
            this.bet_info = data.data.bet_auto_info;
            if (data.data.bet_auto_info.is_open == 1) {
              this.bet_boolean = true;
            } else {
              this.bet_boolean = false;
            }
            this.get_bet_list();
          } else {
            this.$toast({ text: err.error_msg });
          }
        }
      );
    },
    // 自动投注
    begin_bet() {
      let change_json = [];
      this.bet_list.map((item, index) => {
        let obj = {};
        obj.bet_mode_id = item.bet_mode_id;
        obj.win_change = item.win_mode;
        obj.loss_change = item.loss_mode;
        change_json.push(obj);
      });
      if (this.bet_mode_id != "") {
        // 取消自动投注
        if (this.bet_boolean == true) {
          this.$Api(
            {
              api_name: "kkl.game.stopAutoBet",
              game_type_id: this.$route.query.game_type_id
            },
            (err, data) => {
              if (!err) {
                this.$toast({text: data.data});
                this.get_bet_detail();
              } else {
                this.$toast({text: err.error_msg});
              }
            }
          );
          return;
        }
        change_json.map(item => {
          if (item.win_change == "" || item.loss_change == "") {
            this.click = false;
          } else {
            this.click = true;
          }
        });
        if (this.click == false) {
          this.$toast({text: "请选择模式或期号期数"});
        } else {
          this.$Api(
            {
              api_name: "kkl.game.setAutoBet",
              game_type_id: this.$route.game_type_id,
              start_issue: this.start_num,
              start_mode_id: this.bet_mode_id,
              issue_number: this.number,
              max_money: this.max_bean,
              min_money: this.min_bean,
              change_json: JSON.stringify(change_json)
            },
            (err, data) => {
              if (!err) {
                this.$toast({text: '设置成功'});
                this.get_bet_detail();
              } else {
                this.$toast({text: err.error_msg});
              }
            }
          );
        }
      } else {
        this.$toast({text: '请选择模式或期号期数'});
      }
    }
  }
};
</script>

<style lang="less" scoped>
.gameAuto {
  padding-top: 38px;
  .title {
    padding-left: 41px;
    .sc(16px, #333);
    line-height: 22px;
  }
  .auto-form {
    padding: 14px 12px 19px;
    .form-cell {
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
      .form-inline:last-of-type {
        margin-left: 12px;
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
      &:last-child {
        margin-bottom: 0;
      }
    }
  }
  .select-box {
    .wh(100%, 100%);
  }
  .down-menu {
    position: relative;
    .wh(72px, 32px);
    background-color: #f2f2f2;
    border-radius: 4px;
    select {
      .wh(100%, 100%);
      padding-left: 4px;
      .sc(12px, #333);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      background-color: transparent;
      border: none;
      box-sizing: border-box;
    }
    .img-box {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      right: 0;
      .wh(16px, 16px);
      img {
        .wh(16px, 16px);
      }
    }
  }
  .start-bet {
    margin-top: 20px;
    .bet-btn {
      .wh(156px, 36px);
      .sc(16px, #fff);
      text-align: center;
      line-height: 36px;
      white-space: nowrap;
      font-weight: bold;
      background-color: #ff851e;
      border-radius: 4px;
    }
  }
}
</style>