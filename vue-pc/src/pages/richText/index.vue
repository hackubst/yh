<template>
    <div id="rich_text">
        <div class="mid_content">
            <div class="rich_text" v-html="content"></div>
        </div>
    </div>
</template>

<script>
export default {
  name: "richText",
  components: {
      
  },
  data () {
    return {
        content: ''
    }
  },
  created() {
      this.$Api({
          api_name: 'kkl.index.getArticleInfo',
          article_tag: this.$route.query.type
        }, (err, data) => {
          if (!err) {
            this.content = data.data.description
          } else {
            this.$msg(err.error_msg, 'error', 1500)
          }
        })
  },
  methods: {
     
  }
}
</script>

<style scoped lang='less'>
    #rich_text{
        .wh(100%, 100%);
        position: relative;
        overflow: hidden;
        .mid_content{
            .wh(880px, auto);
            margin: 50px auto;
            padding: 40px 20px;
            background-color: #F6F6F6;
        }
    }
</style>