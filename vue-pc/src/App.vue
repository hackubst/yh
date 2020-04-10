<template>
  <div id="app">
    <top-bar></top-bar>
    <router-view/>
    <bot-bar v-if="!isExperience"></bot-bar>
    <red-packet v-if="redPacketDialog"></red-packet>
    <send-code v-if="chekLogin"></send-code>
  </div>
</template>

<script>
import topBar from '@/components/topBar/topBar'
import botBar from '@/components/botBar/botBar'
import redPacket from '@/components/redPacket/redPacket'
import sendCode from '@/components/sendCode/sendCode'
import { mapGetters } from 'vuex'
export default {
  name: 'App',
  components: {
    topBar, botBar, redPacket, sendCode
  },
  computed: {
    ...mapGetters([
      'redPacketDialog',
      'chekLogin',
      'isExperience'
    ])
  }
}
</script>

<style>
@import "../static/css/base.css";
#app{
  box-sizing: border-box;
  height: 100%;
}
#exchangeRecords .el-dialog{
  border-radius:8px !important;
}
#exchangeRecords .el-dialog__header{
  padding: 0 !important;
}
#exchangeRecords .el-dialog__body{
  padding: 0 !important;
}
.gameHome  .el-dialog__header{
  padding: 0 !important;
}
.gameHome  .el-dialog__body{
  padding: 0 !important;
}
.paging_box .el-pagination button{
    width: 66px !important;
    height: 36px !important;
    border-radius: 4px !important;
    border: 1px solid #D1913C !important;
    font-size: 14px !important;
    color: #D1913C !important;
    margin-right: 10px !important;
}
.paging_box .el-pagination .el-pager .number{
    height: 36px !important;
    width: 32px !important;
    border: 1px solid #D1913C !important;
    font-size: 14px !important;
    line-height: 32px !important;
    color: #D1913C !important;
    margin-right: 10px !important;
    border-radius: 4px !important;
}
.paging_box .el-pagination .el-pager .active{
    background: linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%) !important;
    color: #fff !important;
}
.el-cascader{
    width: 282px !important;
}
.el-input__inner{
  border-radius: 0px !important;
}
.el-input.is-active .el-input__inner, .el-input__inner:focus{
    border-color: #D1913C !important;
}
.el-cascader .el-input.is-focus .el-input__inner{
    border-color: #D1913C !important;
}
.el-cascader-menu__item.is-active{
    color: #D1913C !important;
}
</style>
