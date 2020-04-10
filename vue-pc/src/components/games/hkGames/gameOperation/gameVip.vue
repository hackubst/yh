<template>
    <div class="game-vip">
      <table class="game_table" border="1" cellpadding="0" cellspacing="0">
        <tr>
          <th style="width:144px;">
            号码
          </th>
          <th style="width: 100px;">
            A盘退水率%
          </th>
          <th style="width: 100px;">
            B盘退水率%
          </th>
          <th style="width: 100px;">
            C盘退水率%
          </th>
          <th style="width: 100px;">
            D盘退水率%
          </th>
          <th style="width: 122px;">
            单项限额
          </th>
          <th style="width: 122px;">
            最高单注限额
          </th>
          <th style="width: 123px;">
            最低单注限额
          </th>
        </tr>
        <tr v-for="(firstItem,firstIndex) in list" :key="firstIndex">
          <td  >{{firstItem.name}}</td>
          <td  >{{firstItem.a *100}}</td>
          <td  >{{firstItem.b  *100}}</td>
          <td  >{{firstItem.c  *100}}</td>
          <td  >{{firstItem.d  *100}}</td>
          <td  >{{firstItem.limit}}</td>
          <td  >{{firstItem.high_limit}}</td>
          <td  >{{firstItem.low_limit}}</td>
        </tr>
      </table>
    </div>
</template>

<script>
    export default {
        name: "gameVip",
        props: {
            list: {
                type:Array,
                default: () => []
            },
        },
      data(){
          return{
          }
      }
    }
</script>

<style scoped lang="less">
  .game-vip{
    .game_table{
      .wh(920px,auto);
      th{
        height: 24px;
        line-height: 24px;
      }
      td{
        height: 32px;
        text-align: right;
        padding-right: 10px;
        line-height: 32px;
        font-size:14px;
        font-weight:600;
        color:rgba(51,51,51,1);
      }
      tr>td:first-child{
        text-align: center;
      }
    }
  }

</style>
