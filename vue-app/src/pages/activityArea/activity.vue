<template>
    <div id="activity">
        <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
            <ul class="activity_list" v-if="list.length > 0">
                <li class="activity_info" v-for="(item, index) in list" :key="index" @click="view_detail(item.marketing_rule_id)">
                    <img class="activity_img" :src="item.imgurl" alt="">
                    <p class="title">{{item.marketing_rule_name}}</p>
                    <span class="time">活动时间{{item.start_time | formatDateYearStyle}}到{{item.end_time | formatDateYearStyle}}</span>
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
    name: 'activity',
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
                    size: 5 
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
        get_activity_list(firstRow, fetchNum) {
            this.$Api({
                api_name: 'kkl.index.activityList',
                type: '',
                firstRow: firstRow,
                fetchNum: fetchNum
            }, (err, data) => {
                if (!err) {
                    let arr = data.data.marketing_rule_list
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
            this.get_activity_list((page.num-1)*page.size, page.size)
        },
        view_detail(id) {
            this.$router.push({
                path: '/newsText',
                query: {
                    marketing_rule_id: id,
                    type: 0
                }
            })
        }
    }
}
</script>

<style scoped>
    #activity{
        width: 100%;
        /* height: 100%; */
        position: absolute;
        top: 44px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #F2F2F2;
    }
    .activity_list{
        width: 100%;
        height: 100%;
    }
    .activity_info{
        width: 100%;
        min-height: 206px;
        margin-bottom: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: #fff;
    }
    .activity_img{
        width: 351px;
        height: 124px;
        margin-bottom: 8px;
        margin-top: 12px;
    }
    .title{
        width: 351px;
        margin-bottom: 8px;
        line-height: 22px;
        font-size: 16px;
        font-weight: bold;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    .time{
        line-height: 20px;
        font-size: 14px;
        color: #666;
        width: 351px;
        margin-bottom: 12px;
    }
</style>