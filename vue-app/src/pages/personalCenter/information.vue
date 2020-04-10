<template>
    <div id="information">
        <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
            <ul class="message_list" v-if="list.length > 0">
                <li class="message_info" v-for="(item, index) in list" :key="index">
                    <p class="title">{{item.content}}</p>
                    <span class="time">{{item.addtime | formatDateYearTimeMin}}</span>
                </li>
            </ul>
            <empty v-else></empty>
        </mescroll-vue>
    </div>
</template>

<script>
import MescrollVue from 'mescroll.js/mescroll.vue'
import empty from "@/components/common/Empty.vue"
export default {
    components:{
        MescrollVue,
        empty
    },
    name: 'information',
    data () {
        return {
            list: [],
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
        get_message_list(firstRow, fetchNum) {
            this.$Api({
                api_name: 'kkl.index.getPushLogList',
                type: '',
                firstRow: firstRow,
                fetchNum: fetchNum
            }, (err, data) => {
                if (!err) {
                    let arr = data.data.push_log_list
			        // 如果是第一页需手动制空列表
			        if (firstRow === 1) this.list = []
			        // 把请求到的数据添加到列表
			        this.list = this.list.concat(arr)
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
            this.get_message_list((page.num-1)*page.size, page.size)
        }
    }
}
</script>

<style scoped>
    #information{
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
    .message_list{
        width: 100%;
        height: 100%;
    }
    .message_info{
        width: 100%;
        height: 72px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        border-bottom: 1px solid #d8d8d8;
        box-sizing: border-box;
    }
    .message_info:last-child{
        border-bottom: none;
    }
    .title{
        width: 351px;
        font-size: 16px;
        color: #333;
        margin-left: 12px;
        margin-right: 12px;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
        margin-bottom: 13px;
        line-height: 22px;
    }
    .time{
        margin-left: 12px;
        font-size: 14px;
        color: #666;
        line-height: 20px;
    }
</style>