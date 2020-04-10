<template>
    <div id="news_notice">
        <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
            <ul class="news_list" v-if="list.length > 0">
                <li class="news_info" v-for="(item, index) in list" :key="index" @click="view_detail(item.notice_id)">
                    <p class="title">{{item.title}}</p>
                    <span class="time">{{item.addtime | formatDateYearStyle}}</span>
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
        MescrollVue, empty
    },
    name: 'news_notice',
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
                    size: 10 
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
        get_news_list(firstRow, fetchNum) {
            this.$Api({
                api_name: 'kkl.index.noticeList',
                firstRow: firstRow,
                fetchNum: fetchNum
            }, (err, data) => {
                if (!err) {
                    let arr = data.data.notice_list
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
            this.get_news_list((page.num-1)*page.size, page.size)
        },
        view_detail(id) {
            this.$router.push({
                path: '/newsText',
                query: {
                    notice_id: id,
                    type: 1
                }
            })
        }
    }
}
</script>

<style scoped>
    #news_notice{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #F2F2F2;
    }
    .mescroll {
        position: fixed;
        top: 44px;
        bottom: 0;
        height: auto;
    }
    .news_list{
        width: 100%;
        height: 100%;
    }
    .news_info{
        width: 100%;
        height: 72px;
        border-bottom: 1px solid #D8D8D8;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #fff;
    }
    .news_info:last-child{
        border-bottom: none;
    }
    .title{
        width: 351px;
        margin-bottom: 8px;
        line-height: 22px;
        font-size: 16px;
        color: #333;
        overflow: hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }
    .time{
        display: inline-block;
        width: 351px;
        line-height: 20px;
        font-size: 14px;
        color: #666;
        margin: 0 auto;
    }
</style>