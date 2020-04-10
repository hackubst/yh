<template>
    <div id="headBar">
        <!-- 安全密码验证 -->
        <div class="top_title">
            <div class="sidebar"></div>
            <div class="pro">
                <p class="head_title">{{head_title}}</p>
                <p class="head_pro">{{head_pro}}</p>
                <p class="head_info">{{head_info}}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "headBar",
  props: {
    head_title: {
        type: String,
        default: '需要确认登录'
    },
    head_pro: {
        type: String,
        default: ''
    },
    head_info: {
        type: String,
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
    #headBar{
        .wh(100%,auto);
        .top_title{
            .wh(100%, 45px);
            background-color: #F5F5F5;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 20px;
            .sidebar{
                .wh(5px, 19px);
                background:linear-gradient(360deg,rgba(209,145,60,1) 0%,rgba(255,209,148,1) 100%);
                border-radius:3px;
                margin-left: 13px;
                margin-right: 6px;
            }
            .pro{
                display: flex;
                justify-content: flex-start;
                align-items: center;
                .head_title{
                    .sc(18px, rgba(74,65,48,1));
                    line-height:25px;
                }
                .head_pro{
                    font-size:18px;
                    color:rgba(204,204,204,1);
                    line-height:25px;
                    margin-left: 10px;
                }
                .head_info{
                    .sc(18px, #D1913C);
                    line-height: 25px;
                }
            }
        }
    }
</style>