<template>
    <div id="beanDetail">
        <ul class="head_list">
            <li class="head_title" v-for="(item, index) in list" :key="index">{{item.title}}</li>
        </ul>
        <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
            <ul class="relief_list">
                <li class="relief_info" :class="{dual: index%2 != 0}" v-for="(i, index) in relief_list" :key="index">
                    <!-- <div>{{i.addtime | formatDateYearTime}}</div> -->
                    <div>{{i.addtime_str}}</div>
                    <div>{{i.change_type}}</div>
                    <div class="win_color" :class="{red_color:i.type == 2}">{{i.type == 1?'+':'-'}}{{i.change | changeBigNum}}</div>
                    <div>{{i.amount_after_pay | changeBigNum}}</div>
                    <div>{{i.bank_money_after | changeBigNum}}</div>
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
    name: 'beanDetail',
    data () {
        return {
            list: [{
                title: '时间',
            },{
                title: '类型',
            },{
                title: '数量',
            },{
                title: '余额',
            },{
                title: '银行',
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
                    size: 15 
                },
                htmlNodata: '<p class="upwarp-nodata">-- END --</p>',
			},
        }
    },
    created() {
        
    },
    methods: {
        mescrollInit (mescroll) {
            this.mescroll = mescroll  // 如果this.mescroll对象没有使用到,则mescrollInit可以不用配置
        },
        get_bean_list(firstRow, fetchNum) {
            this.$Api({
                api_name: 'kkl.user.accountList',
                firstRow: firstRow,
                fetchNum: fetchNum
            }, (err, data) => {
                if (!err) {
                    let arr = data.data.account_list
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
            this.get_bean_list((page.num-1)*page.size, page.size)
        },
    }
}
</script>

<style scoped>
    #beanDetail{
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
    .head_list{
        width: 100%;
        height: 38px;
        background-color: #FED093;
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .head_title{
        width: 20%;
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
        /* height: 48px; */
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }
    .dual{
        background-color: #FFF7EC;
    }
    .relief_info div{
        width: 20%;
        /* height: 48px; */
        /* line-height: 48px; */
        line-height: 1.768;
        text-align: center;
        font-size:12px;
        word-wrap: break-word;
        font-weight:400;
        padding: 10px 0px;
        color:rgba(74,65,48,1);
    }
    .relief_info .win_color{
        color: #1979FF
    }
    .relief_info .red_color{
        color: #FF1E1E;
    }
</style>