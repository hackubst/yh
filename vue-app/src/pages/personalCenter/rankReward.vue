<template>
    <div id="rankReward">
        <div class="head_box">
            <div class="head_info">
                <p>今日排行榜</p>
                <span>奖励 {{reward | changeBigNum}}</span>
                <div :class="{active: is_receive == 1}" @click="receive()">{{is_receive == 0?'领取奖励':'已领取'}}</div>
            </div>
        </div>
        <ul class="head_list">
            <li class="head_title" v-for="(item, index) in list" :key="index">{{item.title}}</li>
        </ul>
        <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
            <ul class="relief_list">
                <li class="relief_info" :class="{dual: index%2 != 0}" v-for="(i, index) in relief_list" :key="index">
                    <!-- <div>{{i.addtime | formatDateYearTime}}</div> -->
                    <div>{{i.addtime_str}}</div>
                    <div>{{i.reward | changeBigNum}}</div>
                </li>
            </ul>
        </mescroll-vue>
    </div>
</template>

<script>
import MescrollVue from 'mescroll.js/mescroll.vue'
export default {
    components:{
        MescrollVue
    },
    name: 'rankReward',
    data () {
        return {
            list: [{
                title: '领奖时间',
            },{
                title: '领奖金额',
            }],
            relief_list: [],
            mescroll: null, // mescroll实例对象
            mescrollDown:{
                use: false
            }, 
            mescrollUp: { // 上拉加载的配置.
                callback: this.upCallback, // 上拉回调,
                page: {
                    num: 0, 
                    size: 10
                },
                htmlNodata: '<p class="upwarp-nodata">-- END --</p>',
            },
            reward:'',
            is_receive: ''
        }
    },
    created() {
        this.get_reward()
    },
    methods: {
        // 获取排行榜奖励
        get_reward() {
            this.$Api({
                api_name: 'kkl.user.getRankReward',
            }, (err, data) => {
                if (!err) {
                    this.reward = data.data.reward
                    this.is_receive = data.data.is_receive
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        mescrollInit (mescroll) {
            this.mescroll = mescroll  // 如果this.mescroll对象没有使用到,则mescrollInit可以不用配置
        },
        // 获取排行榜奖列表
        get_list(firstRow, fetchNum) {
            this.$Api({
                api_name: 'kkl.user.rewardList',
                firstRow: firstRow,
                fetchNum: fetchNum
            }, (err, data) => {
                if (!err) {
                    let arr = data.data.rank_list_list
			        // 如果是第一页需手动制空列表
			        if (firstRow === 1) this.relief_list = []
			        // 把请求到的数据添加到列表
			        this.relief_list = this.relief_list.concat(arr)
                    this.$nextTick(() => {
                        this.mescroll.endSuccess(arr.length)
                        this.mescroll.endBySize(arr.length, data.data.total)
                    })
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        // 上拉加载
        upCallback(page, mescroll) {
            this.get_list((page.num-1)*page.size, page.size)
        },
        // 领取奖励
        receive() {
            if (this.is_receive == 1) {
                return
            }
            this.$Api({
                api_name: 'kkl.user.getReward',
            }, (err, data) => {
                if (!err) {
                    this.$msg(data.data, 'success', 'middle', 1500)
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        }
    }
}
</script>

<style scoped>
    #rankReward{
        width: 100%;
        /* height: 100%; */
        background-color: #fff;
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
    }
    .mescroll {
        height: auto;
    }
    .head_box{
        width: 100%;
        height: 104px;
        background-color: #F2F2F2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .head_info{
        width: 356px;
        height: 80px;
        background-color: #fff;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .head_info p{
        font-size:16px;
        font-family:PingFangSC-Regular;
        font-weight:400;
        color:rgba(254,208,147,1);
        line-height:22px;
        margin: 0 22px 0 12px;
    }   
    .head_info span {
        display: inline-block;
        font-size:14px;
        font-family:PingFangSC-Regular;
        font-weight:400;
        color:rgba(255,30,30,1);
        line-height:20px;
        margin-right: 38px;
    }
    .head_info div{
        width:96px;
        height:32px;
        background-color:rgba(254,208,147,1);
        border-radius:4px;
        font-size:14px;
        font-family:PingFangSC-Medium;
        font-weight:500;
        color:rgba(74,65,48,1);
        text-align: center;
        line-height: 32px;
    }
    .head_info .active{
        background-color: #F2F2F2 !important;
        color: #999999 !important;
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
        width: 187.5px;
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
        width: 187.5px;
        height: 48px;
        line-height: 48px;
        text-align: center;
        font-size:14px;
        font-weight:400;
        color:rgba(74,65,48,1);
    }
</style>