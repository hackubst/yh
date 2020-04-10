<template>
  <div class="gameRule">
    <div class="main" v-html="ruleHtml"></div>
  </div>
</template>
<script>
export default {
  name: "gameRule",
  data() {
    return {
      ruleHtml: ""
    };
  },
  created() {
    this.$Api(
      {
        api_name: "kkl.game.getGameRule",
        game_type_id: this.$route.query.game_type_id
      },
      (err, data) => {
        if (!err) {
          this.ruleHtml = data.data.game_rule;
        }
      }
    );
  }
};
</script>
<style scoped lang='less'>
</style>