<template>
    <div id="pointCard">
        <div class="table">
            <div class="head_top">
                <div @click="all_check()">
                    <img v-if="!check_boolear" src="../../assets/images/icon/icon_weixuanzhong@2x.png" alt="">
                    <img v-if="check_boolear" src="../../assets/images/icon/icon_xuanzhong@2x.png" alt="">
                    <span>全选</span>
                </div>
                <p>卡密</p>
                <p>卡类型</p>
                <p>卡状态</p>
                <p>点数</p>
            </div>
            <ul class="list">
                <li class="list_info" v-for="(item, index) in card_list" :key="index">
                    <div class="img_click" @click="check(index)">
                        <img v-if="!item.check" src="../../assets/images/icon/icon_weixuanzhong@2x.png" alt="">
                        <img v-if="item.check" src="../../assets/images/icon/icon_xuanzhong@2x.png" alt="">
                    </div>
                    <p class="secret">{{item.card_password}}</p>
                    <p class="type">{{item.name}}</p>
                    <p class="status">{{item.isuse == 0?'已使用':'正常'}}</p>
                    <p class="count">{{item.cash | changeBigNum}}</p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
  name: "pointCard",
  props: {
    card_list: {
        type: Array,
        default: ''
    },
    check_boolear: {
        type: Boolean,
        default: false
    }
  },
  data () {
    return {

    }
  },
  methods: {
    all_check() {
        this.$emit('all_check')
    },
    check(index) {
        this.$emit('check', index)
    }
  }
}
</script>

<style scoped lang='less'>
    #pointCard{
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
                div{
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                    margin-left: 8px;
                    font-size:18px;
                    color:rgba(74,65,48,1);
                    img{
                        .wh(16px, 16px);
                        margin-right: 5px;
                    }
                }
                p{
                    font-size:18px;
                    color:rgba(74,65,48,1);
                    &:nth-of-type(1){
                        margin-left: 84px;
                    }
                    &:nth-of-type(2) {
                        margin-left: 106px;
                    }
                    &:nth-of-type(3) {
                        margin-left: 54px;
                    }
                    &:last-child{
                        margin-left: 55px;
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
                    position: relative;
                    background-color: #fff;
                    .img_click{
                        .wh(16px, 16px);
                        margin-left: 24px;
                        img{
                            .wh(100%, 100%);
                        }
                    }
                    .secret{
                        .sc(14px, #4A4130);
                        margin-left: 48px;
                    }
                    .type{
                        .wh(73px, auto);
                        text-align: center;
                        margin-left: 37px;
                        .sc(14px, #D1913C);
                    }
                    .status{
                        .wh(28px, auto);
                        text-align: center;
                        margin-left: 58px;
                        .sc(14px, #4A4130);
                    }
                    .count{
                        .wh(85px, auto);
                        text-align: center;
                        margin-left: 43px;
                        .sc(14px, #4A4130);
                    }   
                }
            }
        }
    }
</style>