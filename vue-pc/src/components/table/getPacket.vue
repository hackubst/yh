<template>
    <div id="getPacket">
        <div class="table">
            <div class="head_top">
                <p>红包标题</p>
                <p>抢得时间</p>
                <p>金额</p>
                <p>红包类型</p>
                <p>发起人</p>
            </div>
            <ul class="list">
                <li class="list_info" v-for="(item, index) in list" :key="index">
                    <p class="title ellipsis">{{item.title}}</p>
                    <p class="time">{{item.addtime | formatDateYearTime}}</p>
                    <p class="number">{{item.money}}</p>
                    <p class="type ellipsis">{{item.type==1?'普通红包':'手气红包'}}</p>
                    <p class="username ellipsis">{{item.nickname}}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
  name: "getPacket",
  props: {
    list: {
        type: Array,
        default: ''
    }
  },
  data () {
    return {

    }
  }
}
</script>

<style scoped lang='less'>
    #getPacket{
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
                        margin-left: 41px;
                    }
                    &:nth-of-type(2) {
                        margin-left: 85px;
                    }
                    &:nth-of-type(3) {
                        margin-left: 52px;
                    }
                    &:nth-of-type(4) {
                        margin-left: 39px;
                    }
                    &:last-child{
                        margin-left: 38px;
                    }
                }
            }
            .list{
                .wh(100%, 600px);
                background-color: #f5f5f5;
                .list_info{
                    .wh(100%, 40px);
                    border-bottom: 1px solid #E8E8E8;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    position: relative;
                    background-color: #ffffff;
                    .title{
                        .wh(140px, auto);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 7px;
                    }
                    .time{
                        .wh(140px, auto);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 20px;
                    }
                    .number{
                        .wh(25px, auto);
                        text-align: center;
                        margin-left: 19px;
                        .sc(14px, #4A4130);
                    }
                    .type{
                        .wh(60px, auto);
                        text-align: center;
                        margin-left: 50px;
                        .sc(14px, #4A4130);
                    }
                    .username{
                        .wh(70px, auto);
                        text-align: center;
                        margin-left: 38px;
                        .sc(14px, #4A4130);
                    }  
                }
            }
        }
    }
</style>