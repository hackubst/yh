<template>
    <div id="dailyRelief">
        <ul class="head_list">
            <li class="head_title" v-for="(item, index) in list" :key="index">{{item.title}}</li>
        </ul>
        <ul class="relief_list">
            <li class="relief_info" :class="{dual: index%2 != 0}" v-for="(i, index) in relief_list" :key="index">
                <div>{{i.level_name}}</div>
                <div>{{i.min_exp}}</div>
                <div>{{i.max_exp}}</div>
                <div>{{i.sign_reward}}</div>
            </li>
        </ul>
        <div class="btn" @click="receive">领取救济</div>
    </div>
</template>

<script>
export default {
    components:{
        
    },
    name: 'dailyRelief',
    data () {
        return {
            list: [{
                title: '等级',
            },{
                title: '经验起步',
            },{
                title: '经验截止',
            },{
                title: '救济乐豆',
            }],
            relief_list: []
        }
    },
    created() {
        this.get_level_list()
    },
    methods: {
        // 获取等级列表
        get_level_list() {
            this.$Api({
                api_name: 'kkl.user.levelList'
            }, (err, data) => {
                if (!err) {
                    this.relief_list = data.data.level_list
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        receive() {
            this.$Api({
                api_name: 'kkl.user.getRelief'
            }, (err, data) => {
                if (!err) {
                    this.$msg(data.data, 'success', 'middle', 1500)
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
    }
}
</script>

<style scoped>
    #dailyRelief{
        width: 100%;
        /* height: 100%; */
        background-color: #fff;
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .head_list{
        width: 100%;
        height: 38px;
        background-color: #FED093;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .head_title{
        width: 93.75px;
        height: 38px;
        font-size:14px;
        font-weight:400;
        color:rgba(74,65,48,1);
        text-align: center;
        line-height: 38px;
    }
    .relief_list{
        width: 100%;
        height: auto;
        background-color: #fff;
    }
    .relief_info {
        width: 100%;
        height: 48px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .dual{
        background-color: #FFF7EC;
    }
    .relief_info div{
        width: 93.75px;
        height: 48px;
        line-height: 48px;
        text-align: center;
        font-size:14px;
        font-weight:400;
        color:rgba(74,65,48,1);
    }
    .btn{
        width:351px;
        height:48px;
        background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
        border-radius:4px;
        font-size:18px;
        font-weight:500;
        color:rgba(255,255,255,1);
        line-height:48px;
        margin: 24px auto 0 auto;
        text-align: center;
    }
</style>