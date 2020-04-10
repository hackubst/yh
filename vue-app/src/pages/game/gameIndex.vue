<template>
  <div class="gameIndex is_pad">
    <div class="game_type_box" v-for="(typeItem, index) in gameTypes" :key="index">
      <div class="title">{{typeItem.game_series_name}}</div>
      <div class="game_items">
        <div
          class="item"
          v-for="(gameItem,index) in typeItem.game_type_list"
          :key="gameItem.game_type_id"
          @click="gameInfo(gameItem,1,index)"
        >{{gameItem.game_type_name}}
        </div>
      </div>
    </div>
    <div class="game_type_box" v-if="hkGameList[0] && hkGameList[0].game_series_name">
      <div class="title">{{hkGameList[0].game_series_name}}</div>
      <div class="game_items">
        <div
          class="item"
          v-for="(gameItem,index) in hkGameList[0].game_type_list"
          :key="gameItem.game_type_id"
          @click="gameInfo(gameItem,2,index)"
        >{{gameItem.game_type_name}}
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    import {fiveIndex} from '@/config/mixin.js'
    import {
        setStore,
    } from '../../config/utils'
    export default {
        name: "gameIndex",
        mixins: [fiveIndex],
        data() {
            return {
                gameTypes: [],
                hkGameList: []
            };
        },
        methods: {
            gameInfo(gameItem, type, index) {
                console.log(index)
                let url = Number(type) === 1 ? `/gameInfo` : `/hkGameInfo`
                setStore('chooseList', [])
                this.$router.push({
                    path: url,
                    query: {
                        game_type_id: gameItem.game_type_id,
                        game_type_name: gameItem.game_type_name,
                        table_type:gameItem.table_type,
                        currentTab:index + 1,
                    }
                })
            },
            hkGameInfo() {
                this.$Api(
                    {
                        api_name: "kkl.game.getGameTypeListLIUHECAI",
                        is_app: 1
                    },
                    (err, data) => {
                        if (!err) {
                            this.hkGameList = data.data;
                        }
                    }
                );
            }
        },
        created() {
            this.$Api(
                {
                    api_name: "kkl.game.getGameTypeList",
                    is_app: 1
                },
                (err, data) => {
                    if (!err) {
                        this.gameTypes = data.data;
                    }
                }
            );
            this.hkGameInfo()
        }
    };
</script>
<style scoped lang='less'>
  .gameIndex {
    flex: 1;
    background: #f2f2f2;

    .game_type_box {
      width: 355px;
      background: #fff;
      padding: 9px 10px 12px;
      border-radius: 4px;
      box-sizing: border-box;
      margin: 0 auto;
      margin-top: 10px;

      .title {
        font-size: 16px;
        text-align: center;
        margin-bottom: 8px;
      }

      .game_items {
        display: flex;
        flex-wrap: wrap;

        .item {
          width: 79px;
          height: 28px;
          .sc(14px, #fff);
          text-align: center;
          line-height: 28px;
          margin-left: 6px;
          margin-bottom: 6px;
          border-radius: 4px;
          box-sizing: border-box;
          overflow: hidden;
        }

        .item:nth-child(4n + 1) {
          margin-left: 0px;
        }
      }
    }

    .game_type_box:nth-child(4n + 1) {
      .title {
        color: #ff1616;
      }

      .game_items {
        .item {
          background: #ff1616;
        }
      }
    }

    .game_type_box:nth-child(4n + 2) {
      .title {
        color: #ff851e;
      }

      .game_items {
        .item {
          background: #ff851e;
        }
      }
    }

    .game_type_box:nth-child(4n + 3) {
      .title {
        color: #1879ff;
      }

      .game_items {
        .item {
          background: #1879ff;
        }
      }
    }

    .game_type_box:nth-child(4n) {
      .title {
        color: #c612fd;
      }

      .game_items {
        .item {
          background: #c612fd;
        }
      }
    }
  }
</style>
