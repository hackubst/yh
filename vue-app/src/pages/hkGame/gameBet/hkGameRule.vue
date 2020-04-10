<template>
    <div>
      <div class="main" v-html="rich_text"></div>
    </div>
</template>

<script>
    export default {
        name: "hkGameRule",
        data(){
            return{
                rich_text:''
            }
        },
        mounted() {
            let id = this.$route.query.id
                this.$Api(
                    {
                        api_name: "kkl.game.getGameRule",
                        game_type_id: id
                    },
                    (err, data) => {
                        if (!err) {
                            this.rich_text = data.data.game_rule
                        }
                    }
                );
        }
    }
</script>

<style scoped>

</style>
