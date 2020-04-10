<template>
  <div class="left-nav">
    <div class="left-nav-top">
      <div class="left-nav-title">请检视您的账户</div>
      <div class="left-nav-content">
        <ul>
          <li >
            <p>会员账号：</p>
            <p style="font-weight: 500">{{hkUserInfo.mobile}}</p>
          </li>
          <li >
            <p>会员名称：</p>
            <p style="font-weight: 500">{{hkUserInfo.nickname}}</p>
          </li>
          <li >
            <p>盘类：</p>
            <p style="font-weight: 500">{{plate}}</p>
          </li>
          <li >
            <p>信用额度：</p>
            <p style="font-weight: 500">{{hkUserInfo.left_money}}</p>
          </li>
          <li >
            <p>已用金额：</p>
            <p style="font-weight: 500">{{hkUserInfo.use_money}}</p>
          </li>
          <li >
            <p>下注期数：</p>
            <p style="font-weight: 500">{{hkUserInfo.issue}}</p>
          </li>
          <li >
            <p>开奖时间：</p>
            <p style="font-weight: 500">{{hkUserInfo.addtime}}</p>
          </li>
        </ul>
      </div>
    </div>
    <div class="left-nav-top mt10">
      <div class="left-nav-title">最新12笔下注资料</div>
      <div class="left-nav-table">
        <ul>
          <li v-for="item in tableTitle">{{item}}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
    export default {
        name: "leftNav",
        props: {
            hkUserInfo: {
                type: Object,
                default: () => {}
            },
            plate:{
                type: String,
                default: () => ''
            }
        },
        data() {
            return {
                userInfo: {},//六合彩用户信息
                tableTitle: ['时间', '内容', '赔率', '金额'],
                list: [
                    {
                        title: '会员账号',
                        name: 'ming'
                    },
                    {
                        title: '会员名称：',
                        name: 'ming'
                    },
                    {
                        title: '盘类：',
                        name: 'ming'
                    },
                    {
                        title: '信用额度：',
                        name: 'ming'
                    }, {
                        title: '已用金额：',
                        name: 'ming'
                    },
                    {
                        title: '下注期数：',
                        name: 'ming'
                    },
                    {
                        title: '开奖日期：',
                        name: '20202020'
                    }

                ]
            }
        },
        methods: {
            //获取最新12期的下注
            getLastGameBet(){
                this.$Api(
                    {
                        api_name: "kkl.user.getLiuhecaiUser",
                        game_type_id: this.game_result_id
                    },
                    (err, data) => {
                        if (!err) {
                            this.userInfo = data.data
                            console.log(this.userInfo)
                        }
                    }
                );
            },

        },
        mounted() {
        }
    }
</script>

<style scoped lang="less">
  .left-nav {
    width: 100%;
    height: auto;

    .mt10 {
      margin-top: 10px;
    }

    .left-nav-top {
      width: 100%;
      border: 1px solid rgba(228, 170, 92, 1);
      border-radius: 4px 4px 0px 0px;

      .left-nav-title {
        height: 40px;
        background: #E4AA5C;
        font-size: 16px;
        font-weight: 400;
        padding-left: 10px;
        box-sizing: border-box;
        color: rgba(255, 255, 255, 1);
        line-height: 40px;
      }

      .left-nav-content {
        padding: 10px;
        box-sizing: border-box;

        li {
          display: flex;
          justify-content: space-between;
          font-weight: 400;
          color: rgba(51, 51, 51, 1);
          margin-bottom: 12px;
        }
      }

      .left-nav-table {
        width: 100%;
        height: 30px;

        ul {
          display: flex;
          background: rgba(255, 239, 212, 1);

          li {
            flex: 1;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-right: 1px solid #e8e8e8;
            font-weight: 400;
            color: rgba(51, 51, 51, 1);
          }
        }
      }
    }
  }
</style>
