<template>
    <div id="sendPacket">
        <div class="table">
            <div class="head_top">
                <p>红包标题</p>
                <p>已领额</p>
                <p>已领数</p>
                <p>红包类型</p>
                <p>作废时间</p>
                <p>生成时间</p>
                <p>操作</p>
            </div>
            <ul class="list">
                <li class="list_info" v-for="(item, index) in send_list" :key="index">
                    <p class="title ellipsis_more">{{item.title}}</p>
                    <p class="receiveCollar">{{item.residue_money}}/{{item.total_money}}</p>
                    <p class="receiveNumber">{{item.residue_num}}/{{item.num}}</p>
                    <p class="type">{{item.type == 1?'普通红包':'手气红包'}}</p>
                    <p class="voidTime">{{item.expire_time | formatDateYearTime}}</p>
                    <p class="createTime">{{item.addtime | formatDateYearTime}}</p>
                    <p class="operation">
                        <!-- <span class="cancle" @click="cancle(item.red_packet_id)">{{item.is_cancel==0?'撤销':'已撤销'}}</span> -->
                        <span class="copy" @click="copy(item.jiami_red_packet_id)">复制链接</span>
                    </p>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
  name: "sendPacket",
  props: {
    send_list: {
        type: Array,
        default: ''
    }
  },
  data () {
    return {
        
    }
  },
  methods: {
    // cancle(id) {
    //     this.$emit('cancle', id)
    // },
    copy(jiami_id) {
        this.$emit('copy', jiami_id)
    }
  }
}
</script>

<style scoped lang='less'>
    #sendPacket{
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
                    margin-left: 22px;
                    &:first-child{
                        margin-left: 14px;
                    }
                }
            }
            .list{
                .wh(100%, 600px);
                background-color: #f5f5f5;
                .list_info{
                    .wh(100%, 60px);
                    border-bottom: 1px solid #E8E8E8;
                    box-sizing: border-box;
                    display: flex;
                    justify-content: flex-start;
                    align-items: center;
                    position: relative;
                    background-color: #fff;
                    .title{
                        .wh(72px, 40px);
                        .sc(14px, #4A4130);
                        margin-left: 14px;
                        line-height: 20px;
                    }
                    .receiveCollar{
                        .wh(58px, auto);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 20px;
                        line-height: 20px;
                    }
                    .receiveNumber{
                        .wh(38px, auto);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 28px;
                        line-height: 20px;
                    }
                    .type{
                        .wh(56px, auto);
                        text-align: center;
                        margin-left: 38px;
                        .sc(14px, #4A4130);
                    }
                    .voidTime{
                        .wh(79px, 40px);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 28px;
                        line-height: 20px;
                    }
                    .createTime{
                        .wh(79px, 40px);
                        .sc(14px, #4A4130);
                        text-align: center;
                        margin-left: 15px;
                        line-height: 20px;
                    }
                    .operation{
                        .wh(42px, auto);
                        text-align: center;
                        margin-left: 15px;
                        .sc(14px, #D1913C);
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        .cancle{
                            margin-bottom: 10px;
                            cursor: pointer;
                        }
                        .copy{
                            width: 56px;
                            cursor: pointer;
                        }
                    }  
                }
            }
        }
    }
</style>