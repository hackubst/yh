<template>
    <div id="egg_28">
        <div class="head_top">
            <div class="title">{{game_type_name}} 第{{item.issue}}期开奖结果</div>
            <div class="close" @click="close()">关闭</div>
        </div>
        <table class="game_table game_egg" cellspacing="0px" style="border-collapse:collapse;">
            <tbody>
                <tr>
                    <td colspan="4">{{game_type_name}} 第{{item.issue}}期开奖结果</td>
                </tr>
                <tr>
                    <td width="100">开奖时间</td>
                    <td colspan="3">{{item.addtime | formatDateYearTime}}</td>
                </tr>
                <tr>
                    <td width="100">开奖号码</td>
                    <td colspan="3">
                        <span style="color: #4A4130;">{{item.result}}</span>
                    </td>
                </tr>
                <tr>
                    <td>区位</td>
                    <td>第一区[第1/2/3/4/5/6位数字]</td>
                    <td>第二区[第7/8/9/10/11/12位数字]</td>
                    <td>第三区[第13/14/15/16/17/18位数字]</td>
                </tr>  
                <tr>
                    <td>数字</td>
                    <td>{{first_num}}</td>
                    <td>{{second_num}}</td>
                    <td>{{third_num}}</td>
                </tr>
                <tr>
                    <td>求和</td>
                    <td>{{item.game_log_info.part_one_sum}}</td>
                    <td>{{item.game_log_info.part_two_sum}}</td>
                    <td>{{item.game_log_info.part_three_sum}}</td>
                </tr>    
                <!-- <tr>
                    <td>计算</td>
                    <td>59除以6的余数 + 1</td>
                    <td>204除以6的余数 + 1</td>
                    <td>316除以6的余数 + 1</td>
                </tr> -->
                <tr>
                    <td>计算</td>
                    <td>取尾数</td>
                    <td>取尾数</td>
                    <td>取尾数</td>
                </tr>
                <tr class="tr">
                    <td>结果</td>
                    <td> 
                        <div class="num_img">
                            <img :src="number_one_img" alt="">
                        </div>
                    </td>
                    <td>
                        <div class="num_img">
                            <img :src="number_two_img" alt="">
                        </div>
                    </td>
                    <td>
                        <div class="num_img">
                            <img :src="number_three_img" alt="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>开奖</td>
                    <td colspan="3">
                        <ul>
                            <li><img :src="number_one_img" alt=""></li>
                            +
                            <li><img :src="number_two_img" alt=""></li>
                            +
                            <li><img :src="number_three_img" alt=""></li>
                            =
                            <div class="gentle" style="background: #66ff33;" v-if="item.game_log_info.result == 1">豹</div>
                            <div class="gentle" style="background: #B822DD;" v-else-if="item.game_log_info.result == 2">顺</div>
                            <div class="gentle" style="background: #3C3CC4;" v-else-if="item.game_log_info.result == 3">对</div>
                            <div class="gentle" style="background: #EE1111;" v-else-if="item.game_log_info.result == 4">半</div>
                            <div class="gentle" style="background: #1AE6E6;" v-else>杂</div>
                        </ul>
                    </td>
                </tr>	 
                <tr>
                    <td colspan="4" style="color:#F90; font-weight:bold">游戏结果说明</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align:left;">
                        <div style="margin-left: 20px; color= #4A4130">
                            结果优先顺序：豹 &gt; 顺 &gt; 对 &gt; 半 &gt; 杂
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width: 100%; height= 100%; display: flex; justify-content: flex-start; align-items: center;">
                            <span class="gentle">豹</span>
                            <span>3个结果号码相同，如222,333,999</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width: 100%; height= 100%; display: flex; justify-content: flex-start; align-items: center;">
                            <span class="gentle">顺</span>
                            <span>3个结果号码从小到大排序后，号码都相连，如231,765,645.特例:排序后019算顺子</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width: 100%; height= 100%; display: flex; justify-content: flex-start; align-items: center;">
                            <span class="gentle">对</span>
                            <span>3个结果号码只有两个相同，如535,337,899</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width: 100%; height= 100%; display: flex; justify-content: flex-start; align-items: center;">
                            <span class="gentle">半</span>
                            <span>3个结果号码只有任意两个是相连的,不包含顺、对，如635,367,874.特例:包含0和9也算顺子</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div style="width: 100%; height= 100%; display: flex; justify-content: flex-start; align-items: center;">
                            <span class="gentle">杂</span>
                            <span>3个结果号码没有任何关联，如638,942,185</span>
                        </div>
                    </td>
                </tr>   
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {   
        name: "egg_28",
        props: {
            item: {
                type: Object,
                default: {} 
            },
            game_type_name: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                number_one_img: '',
                number_two_img: '',
                number_three_img: '',
                first_num: '',
                second_num: '',
                third_num: ''
            };
        },
        created() {
            this.number_one_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_one_result+'@2x.png')
            this.number_two_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_two_result+'@2x.png')
            this.number_three_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_three_result+'@2x.png')
            let number = this.item.result
            this.first_num = number.substr(0,17)
            this.second_num = number.substr(18,17)
            this.third_num = number.substr(36,17)
        },
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>

<style scoped lang='less'>
    #egg_28{
        .wh(100%, auto);
        .head_top{
            .wh(100%, 24px);
            padding: 7px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #FFEFD4;
            .title{
                margin-left: 15px;
                .sc(16px, #4A4130);
                font-weight: 600;
            }
            .close{
                margin-right: 15px;
                font-size: 16px;
                .sc(16px, #4A4130);
                font-weight: 600;
            }
        }
        .game_egg{
            width: 100%;
            .num_img{
                .wh(100%, 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            ul{
                .wh(100%, 100%);
                display: flex;
                justify-content: center;
                align-items: center;
                li{
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    img{
                        margin: 10px;
                    }
                }
                .gentle{
                    width:35px;
                    height:35px;
                    background:linear-gradient(180deg,rgba(249,212,35,1) 0%,rgba(255,78,80,1) 100%);
                    font-size:25px;
                    font-family:Futura-Bold;
                    font-weight:bold;
                    color:rgba(255,255,255,1);
                    line-height:35px;
                    border-radius: 50%;
                    margin-left: 10px;
                }
            }
            .gentle{
                width:35px;
                height:35px;
                background:linear-gradient(180deg,rgba(249,212,35,1) 0%,rgba(255,78,80,1) 100%);
                font-size:20px;
                font-family:Futura-Bold;
                color:rgba(255,255,255,1);
                line-height:35px;
                border-radius: 50%;
                display: inline-block;
                text-align: center;
                margin-left: 25px;
                margin-right: 10px;
            }
        }
    }
</style>