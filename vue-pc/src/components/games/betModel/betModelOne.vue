<template>
  <div class="betModelOne">
    <table border="1" cellpadding="0" cellspacing="0" class="tabTop">
      <tr>
        <th style="width: 197px;">开始模式</th>
        <th style="width: 190px;">开始期号</th>
        <th style="width: 198px;">期数</th>
        <th style="width: 198px;">乐豆上限</th>
        <th style="width: 130px;">下限</th>
      </tr>
      <tr>
        <td>
          <div class="down_menu">
            <select v-model="bet_mode_id" :disabled="bet_boolean">
              <option v-for="(item, index) in bet_list" :key="index" :value="item.bet_mode_id">{{item.mode_name}}</option>
            </select>
          </div>
        </td>
        <td>
          <div class="inp_box">
            <input type="number" v-model="start_num" :disabled="bet_boolean">
          </div>
        </td>
        <td>
          <div class="inp_box">
            <input type="number" v-model="number" :disabled="bet_boolean">
          </div>
        </td>
        <td>
          <div class="inp_box">
            <input type="number" v-model="max_bean" :disabled="bet_boolean">
          </div>
        </td>
        <td>
          <div class="inp_box">
            <input type="number" v-model="min_bean" :disabled="bet_boolean">
          </div>
        </td>
      </tr>
    </table>
    <div class="tabBot">
      <table border="1" cellpadding="0" cellspacing="0">
        <tr>
          <th style="width: 197px;">投注模式</th>
          <th style="width: 190px;">投注乐豆</th>
          <th style="width: 264px;">赢后使用投注模式</th>
          <th style="width: 264px;">输后使用投注模式</th>
        </tr>
        <tr style="height: 67px; line-height: 67px;" v-for="(item, index) in bet_list" :key="index">
          <td>{{item.mode_name}}</td>
          <td>{{item.total_money}}</td>
          <td>
            <div class="down_menu">
              <select v-model="item.win_mode" :disabled="bet_boolean">
                <option v-for="(item, idx) in bet_list" :key="idx" :value="item.bet_mode_id">{{item.mode_name}}</option>
              </select>
            </div>
          </td>
          <td>
            <div class="down_menu">
              <select v-model="item.loss_mode" :disabled="bet_boolean">
                <option v-for="(item, idx) in bet_list" :key="idx" :value="item.bet_mode_id">{{item.mode_name}}</option>
              </select>
            </div>
          </td>
        </tr>
      </table>
      <div class="start_bet">
        <div class="bet_btn" @click="begin_bet()">{{bet_boolean? '取消投注':'开始投注'}}</div>
      </div>
    </div>
  </div>
</template>
<script>
  import {
    gameCuntDown
  } from '@/config/mixin.js'
  export default {
    name: "betModelOne",
    mixins: [gameCuntDown],
    props: {
      bet_list: {
        type: Array,
        default: () => ''
      },
      bet_boolean: {
        type: Boolean,
        default: () => false
      },
      bet_info: {}
    },
    data() {
      return {
        start_num: '',
        number: '3000000',
        max_bean: '999999999',
        min_bean: '100',
        bet_mode_id:'',
        click: false
      };
    },
    created() {
      this.start_num = Number(this.newestItem.issue) + 4
      setTimeout(()=>{
        if (this.bet_boolean) {
          this.bet_mode_id = this.bet_info.start_mode_id
          this.start_num = this.bet_info.start_issue
          this.number = this.bet_info.issue_number
          this.max_bean = this.bet_info.max_money
          this.min_bean = this.bet_info.min_money
          return
        }
        if (this.bet_list.length > 0) {
          this.bet_mode_id = this.bet_list[0].bet_mode_id
        } 
      },500)
    },
    methods: {
      // 自动投注
      begin_bet() {
        let change_json = []
        this.bet_list.map((item, index) => {
          let obj = {}
          obj.bet_mode_id = item.bet_mode_id
          obj.win_change = item.win_mode
          obj.loss_change = item.loss_mode
          change_json.push(obj)
        })
        if (this.bet_mode_id !='') {
          this.$emit('begin_bet',this.bet_mode_id, this.start_num, this.number, this.max_bean, this.min_bean, change_json)
        } else {
          this.$msg('请选择模式或期号期数', 'error', 1500)
        }
      }
    },
  }
</script>
<style scoped lang='less'>
  .betModelOne {
    margin-bottom: 100px;
    .tabTop {
      margin-bottom: 19px;
    }

    .tabBot {
      height: auto;
      box-sizing: border-box;

      .start_bet {
        height: 137px;
        background: #FFEFD4;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #F5A623;
        border-top: none;

        .bet_btn {
          .commonBtn(56px, 187px);
        }
      }
    }

    table {
      border: 1px solid #F5A623;
      border-top: 2px solid #F5A623;
      width: @main-width;
      font-size: 18px;
      color: #4A4130;
      text-align: center;

      th {
        background: #FFEFD4;
        height: 42px;
        line-height: 42px;
        font-size: 16px;
        color: #4A4130;
        text-align: center;
        border: 1px solid #F5A623;
        border-bottom: none;
      }

      td {
        height: 66px;
        border: 1px solid #F5A623;
        border-top: none;
      }

      .inp_box {
        height: 66px;
        padding: 10px 6px;
        box-sizing: border-box;

        input {
          border: 1px solid #999999;
          border-radius: 4px;
          width: 100%;
          padding: 10px;
          box-sizing: border-box;
          font-size: 18px;
          color: #4A4130;
        }
      }

      .down_menu {
        height: 100%;
        box-sizing: border-box;
        font-size: 18px;
        color: #4A4130;
        display: flex;
        align-items: center;
        justify-content: center;

        img {
          width: 24px;
          height: 20px;
          position: absolute;
          top: 50%;
          margin-top: -10px;
          right: 20px;
        }

        select {
          width: 178px;
          height: 45px;
          border: 1px solid #999999;
          border-radius: 4px;
          padding: 10px;
          box-sizing: border-box;
          background: url("~images/icon/Angle-down@2x.png") no-repeat scroll right center transparent;
          background-size: 24px 20px;
          background-position: 95% 50%;

          option {
            height: 45px;
            padding: 10px;
            box-sizing: border-box;
          }
        }

      }
    }
  }
</style>