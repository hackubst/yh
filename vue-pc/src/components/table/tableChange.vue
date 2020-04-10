<template>
    <div id="tableChange">
        <div class="table">
            <div class="head_top">
                <p>兑换时间</p>
                <p>卡类型</p>
                <p>兑换数量</p>
                <p>操作</p>
            </div>
            <ul class="list">
                <li class="list_info" v-for="(item, index) in table_list" :key="index">
                    <p class="time">{{item.addtime | formatDateYearTime}}</p>
                    <p class="type">{{item.card_name}}</p>
                    <p class="count">{{item.num | changeBigNum}}</p>
                    <p class="operation" @click="view_secret(item)">查看卡密</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
  name: "tableChange",
  props: {
    table_list: {
        type: Array,
        default: ''
    }
  },
  data () {
    return {

    }
  },
  methods: {
    view_secret(item) {
        this.$emit('view_secret', item)
    }
  }
}
</script>

<style scoped lang='less'>
    #tableChange{
        .wh(100%, auto);
        .table{
            .wh(600px, auto);
            background-color: #fff;
            margin: 0 auto;
            .head_top{
                .wh(100%, 50px);
                background-color: #E8E8E8;
                display: flex;
                justify-content: flex-start;
                align-items: center;
                p{
                    font-size:18px;
                    color:rgba(74,65,48,1);
                    &:first-child{
                        margin-left: 64px;
                    }
                    &:nth-of-type(2) {
                        margin-left: 95px;
                    }
                    &:nth-of-type(3) {
                        margin-left: 51px;
                    }
                    &:last-child{
                        margin-left: 89px;
                    }
                }
            }
            .list{
                .wh(100%, 200px);
                background-color: #f5f5f5;
                .list_info{
                    .wh(100%, 40px);
                    border-bottom: 1px solid #E8E8E8;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    background-color: #fff;
                    position: relative;
                    .time{
                        .sc(14px, #4A4130);
                        margin-left: 40px;
                    }
                    .type{
                        .wh(68px, auto);
                        text-align: center;
                        margin-left: 56px;
                        .sc(14px, #D1913C);
                    }
                    .count{
                        .wh(42px, auto);
                        text-align: center;
                        margin-left: 57px;
                        .sc(14px, #4A4130);
                    }
                    .operation{
                        .wh(56px, auto);
                        text-align: center;
                        margin-left: 93px;
                        .sc(14px, #D1913C);
                        cursor: pointer;
                    }   
                }
            }
        }
    }
</style>