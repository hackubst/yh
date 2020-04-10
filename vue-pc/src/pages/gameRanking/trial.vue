<template>
    <div id="gameRanking">
        <ul class="rank_list">
            <li class="list_type" v-for="(item, index) in rank_list" :key="index">
                <img class="head_img" :src="item.img" alt="头部图片">
                <ul class="list_info" :class="index == 0?blue:white">
                    <li class="user_info" :class="{even: idx%2 == 1}" v-for="(i, idx) in item.rank_list_first" :key="idx">
                        <div class="index">
                            <img v-if="idx == 0" src="../../assets/images/icon/icon_first@2x.png" alt="第一名">
                            <img v-if="idx == 1" src="../../assets/images/icon/icon_second@2x.png" alt="第二名">
                            <img v-if="idx == 2" src="../../assets/images/icon/icon_last@2x.png" alt="第三名">
                            <p v-if="idx>2">{{idx + 1}}</p>
                        </div>
                        <div class="realname">
                            <div class="username ellipsis">{{i.nickname}}</div>
                        </div>
                        <div class="count ellipsis">{{i.total | changeBigNum}}</div>
                    </li>
                    <li class="user_info" :class="{even: idx%2 == 1}" v-for="(i, idx) in item.rank_list_second" :key="idx">
                        <div class="index">
                            <img v-if="idx == 0" src="../../assets/images/icon/icon_first@2x.png" alt="第一名">
                            <img v-if="idx == 1" src="../../assets/images/icon/icon_second@2x.png" alt="第二名">
                            <img v-if="idx == 2" src="../../assets/images/icon/icon_last@2x.png" alt="第三名">
                            <p v-if="idx>2">{{idx + 1}}</p>
                        </div>
                        <div class="realname">
                            <div class="username ellipsis">{{i.nickname}}</div>
                            <div class="reward" v-if="i.reward">(奖:{{i.reward | changeBigNum}})</div>
                        </div>
                        <div class="count ellipsis">{{i.total| changeBigNum}}</div>
                    </li>
                    <li class="user_info" :class="{even: idx%2 == 1}" v-for="(i, idx) in item.rank_list_third" :key="idx">
                        <div class="index">
                            <img v-if="idx == 0" src="../../assets/images/icon/icon_first@2x.png" alt="第一名">
                            <img v-if="idx == 1" src="../../assets/images/icon/icon_second@2x.png" alt="第二名">
                            <img v-if="idx == 2" src="../../assets/images/icon/icon_last@2x.png" alt="第三名">
                            <p v-if="idx>2">{{idx + 1}}</p>
                        </div>
                        <div class="realname">
                            <div class="username ellipsis">{{i.nickname}}</div>
                        </div>
                        <div class="count ellipsis">{{i.total | changeBigNum}}</div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapActions } from "vuex";
export default {
  name: "gameRanking",
  data () {
    return {
        rank_list:[{
            img: require('../../assets/images/img/pic_jinri@2x.png'),
            rank_list_first: [],
        },{
            img: require('../../assets/images/img/pic_qiri@2x.png'),
            rank_list_third: [],
        },],
        blue: 'blue',
        red: 'red',
        white: 'white'
    }
  },
  created() {
      this.refreshUserInfo();
      this.get_rank_list()
  },
  methods: {
    // 获取排行榜
    get_rank_list() {
        this.$Api({
            api_name: 'kkl.index.rankList'
        }, (err, data) => {
            if (!err) {
                let res = data.data
                this.rank_list[0].rank_list_first = res.today_list
                this.rank_list[1].rank_list_third = res.sevenday_list
            } else {
                this.$msg(err.error_msg, 'error', 1500)
            }
        })
    },
    ...mapActions([
      "refreshUserInfo"
    ])
  }
}
</script>

<style scoped lang='less'>
    #gameRanking{
        .wh(600px, 1197px);
        margin: 0 auto;
        overflow: hidden;
        .rank_list{
            .wh(100%, auto);
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            margin-top: 4px;
            margin-bottom: 50px;
            .list_type{
                .wh(280px, auto);
                margin-right: 40px;
                &:last-child{
                    margin-right: 0;
                }
                .head_img{
                    .wh(100%, 123px);
                    margin-bottom: 20px;
                }
                .list_info{
                    .wh(100%, auto);
                    box-sizing: border-box;
                    border-radius: 8px;
                    border: 1px solid;
                    .user_info{
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        .wh(100%, 50px);
                        background-color: #FFF8EF;
                        .index{
                            .wh(28px, 30px);
                            margin-left: 10px;
                            margin-right: 10px;
                            img{
                                .wh(100%, 100%);
                            }
                            p{
                                .wh(24px, 24px);
                                background-color: #F5F5F5;
                                border-radius: 50%;
                                text-align: center;
                                line-height: 24px;
                                font-size:14px;
                                color:rgba(74,65,48,1);
                            }
                        }
                        .realname{
                            .sc(18px, rgba(74,65,48,1));
                            width: 102px;
                            margin-right: 25px;
                            display: flex;
                            flex-direction: column;
                            align-items: flex-start;
                            justify-content: flex-start;
                            .reward{
                                .sc(14px, #FF4C20);
                            }
                        }
                        .count{
                            width: 95px;
                            text-align: right;
                            .sc(14px, #4A4130);
                        }
                    }
                    .even{
                        background-color: #ffffff;
                    }
                }
                .blue{
                    border-image:linear-gradient(360deg, rgba(69,207,187,1), rgba(131,79,245,1)) 1 1;   
                }
                .red{
                    border-image:linear-gradient(360deg, rgba(220,36,48,1), rgba(124,66,149,1)) 1 1;
                }
                .white{
                    border-image:linear-gradient(360deg, rgba(249,213,41,1), rgba(227,55,54,1)) 1 1;
                }
            }
        }
    }
</style>