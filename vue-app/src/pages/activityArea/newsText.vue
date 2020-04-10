<template>
    <div id="newsText">
        <div class="title">{{title}}</div>
        <div class="rich_text" v-html="contents"></div>
    </div>
</template>

<script>
export default {
    components:{
        
    },
    name: 'newsText',
    data () {
        return {
            title: '', // 标题
            type: '', // 详情判断
            contents: '', // 富文本内容
            notice_id: '', // 新闻id
            marketing_rule_id: '', // 详情id
        }
    },
    created() {
        this.type = this.$route.query.type
        if (this.type == 0) {
            this.get_active_detail()
        } else {
            this.get_news_detail()
        }
    },
    methods: {
        // 获取新闻详情
        get_news_detail() {
            this.notice_id = this.$route.query.notice_id
            this.$Api({
                api_name: 'kkl.index.noticeDetail',
                notice_id: this.notice_id
            }, (err, data) => {
                if (!err) {
                    this.title = data.data.info.title
                    this.contents = data.data.info.content
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
        // 获取活动专区详情
        get_active_detail() {
            this.marketing_rule_id = this.$route.query.marketing_rule_id
            this.$Api({
                api_name: 'kkl.index.getActivityInfo',
                marketing_rule_id: this.marketing_rule_id
            }, (err, data) => {
                if (!err) {
                    this.title = data.data.marketing_rule_name
                    this.contents = data.data.contents
                } else {
                    this.$msg(err.error_msg, 'cancel', 'middle', 1500)
                }
            })
        },
    }
}
</script>

<style scoped>
    #newsText{
        width: 100%;
        height: 100%;
    }
</style>