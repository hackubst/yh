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
                    <!-- <td colspan="3">
                        <span style="color: #3897FF;">{{first_num}}</span>
                        <span style="color: #F5A623;">{{second_num}}</span>
                        <span style="color: #FF4C20;">{{third_num}}</span>
                    </td>
                    <td colspan="6">
                        <span style="color:#ff0000">10</span>
                        <span style="color:#eca101">12</span>
                        <span style="color:#0167FF">13</span>
                        <span style="color:#ff0000">22</span>
                        <span style="color:#eca101">30</span>
                        <span style="color:#0167FF">31</span>
                        <span style="color:#ff0000">33</span>
                        <span style="color:#eca101">35</span>
                        <span style="color:#0167FF">41</span>
                        <span style="color:#ff0000">42</span>
                        <span style="color:#eca101">47</span>
                        <span style="color:#0167FF">53</span>
                        <span style="color:#ff0000">56</span>
                        <span style="color:#eca101">58</span>
                        <span style="color:#0167FF">60</span>
                        <span style="color:#ff0000">61</span>
                        <span style="color:#eca101">65</span>
                        <span style="color:#0167FF">68</span>
                        <span style="color:#000">76</span>
                        <span style="color:#000">78</span>
                    </td>
                    <td colspan="6">
                        <span style="color:#ff0000">10</span>
                        <span style="color:#eca101">12</span>
                        <span style="color:#0167FF">13</span>
                        <span style="color:#ff0000">22</span>
                        <span style="color:#eca101">30</span>
                        <span style="color:#0167FF">31</span>
                        <span style="color:#ff0000">33</span>
                        <span style="color:#eca101">35</span>
                        <span style="color:#0167FF">41</span>
                    </td> -->
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 8 || item.game_log_info.game_type_id == 11 || item.game_log_info.game_type_id == 14 || item.game_log_info.game_type_id == 15 || item.game_log_info.game_type_id == 23 || item.game_log_info.game_type_id == 24 || item.game_log_info.game_type_id == 25 || item.game_log_info.game_type_id == 26 || item.game_log_info.game_type_id == 30 || item.game_log_info.game_type_id == 31  || item.game_log_info.game_type_id == 33 || item.game_log_info.game_type_id == 35 || item.game_log_info.game_type_id == 36 || item.game_log_info.game_type_id == 40 || item.game_log_info.game_type_id == 44"> 
                    <td>区位</td> 
                    <td>第一区[第2/5/8/11/14/17位数字]</td>
                    <td>第二区[第3/6/9/12/15/18位数字]</td>
                    <td>第三区[第4/7/10/13/16/19位数字]</td>
                </tr>  
                <tr v-if="item.game_log_info.game_type_id == 1 || item.game_log_info.game_type_id == 3 || item.game_log_info.game_type_id == 4 || item.game_log_info.game_type_id == 5 || item.game_log_info.game_type_id == 9 || item.game_log_info.game_type_id == 27">
                    <td>区位</td>
                    <td>第一区[第1/2/3/4/5/6位数字]</td>
                    <td>第二区[第7/8/9/10/11/12位数字]</td>
                    <td>第三区[第13/14/15/16/17/18位数字]</td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 13 || item.game_log_info.game_type_id == 22 || item.game_log_info.game_type_id == 32">
                    <td>区位</td>
                    <td>第一区[第1/4/7/10/13/16位数字]</td>
                    <td>第二区[第2/5/8/11/14/17位数字]</td>
                    <td>第三区[第3/6/9/12/15/18位数字]</td>
                </tr>  
                <tr v-if=" item.game_log_info.game_type_id == 40">
                    <td>区位</td>
                    <td>第一区[第1/4/7位数字]</td>
                    <td>第二区[第2/5/8位数字]</td>
                    <td>第三区[第1/2/3/4/5/6/7/8位数字]</td>
                </tr>  
                <tr v-if=" item.game_log_info.game_type_id == 38">
                    <td>区位</td>
                    <td>第一区[第1/4/7位数字]</td>
                    <td>第二区[第2/5/8位数字]</td>
                    <td>第三区[第3/6/9位数字]</td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 41">
                    <td>区位</td>
                    <td>第一区[第1/4/7位数字]</td>
                    <td>第三区[第3/6/9位数字]</td>
                    <td>第二区[第2/5/8位数字]</td>
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
                <tr v-if="item.game_log_info.game_type_id == 9 || item.game_log_info.game_type_id == 13 || item.game_log_info.game_type_id == 22 || item.game_log_info.game_type_id == 32">
                    <td>计算</td>
                    <td>{{item.game_log_info.part_one_sum}}除以6的余数 + 1</td>
                    <td>{{item.game_log_info.part_two_sum}}除以6的余数 + 1</td>
                    <td>{{item.game_log_info.part_three_sum}}除以6的余数 + 1</td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id != 32">
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
                            <span>+</span>
                            <li><img :src="number_two_img" alt=""></li>
                            <span>+</span>
                            <li><img :src="number_three_img" alt=""></li>
                            <span v-if="item.game_log_info.game_type_id != 4 && item.game_log_info.game_type_id != 25">=</span>
                            <li class="gentle" v-if="item.game_log_info.game_type_id != 4 && item.game_log_info.game_type_id != 25">{{item.game_log_info.result.split(',')[0]}}</li>
                            <li class="gentle" v-if="item.game_log_info.game_type_id == 58 || item.game_log_info.game_type_id == 59 || item.game_log_info.game_type_id == 60 || item.game_log_info.game_type_id == 61"><!--
                                --><template v-if="judgeJunko(item.game_log_info.part_one_result, item.game_log_info.part_two_result, item.game_log_info.part_three_result) === 1">豹</template><!--
                                --><template v-if="judgeJunko(item.game_log_info.part_one_result, item.game_log_info.part_two_result, item.game_log_info.part_three_result) === 2">顺</template><!--
                                --><template v-if="judgeJunko(item.game_log_info.part_one_result, item.game_log_info.part_two_result, item.game_log_info.part_three_result) === 3">对</template><!--
                                --><template v-if="judgeJunko(item.game_log_info.part_one_result, item.game_log_info.part_two_result, item.game_log_info.part_three_result) === 4">半</template><!--
                                --><template v-if="judgeJunko(item.game_log_info.part_one_result, item.game_log_info.part_two_result, item.game_log_info.part_three_result) === 5">杂</template><!--
                            --></li>
                        </ul>
                    </td>
                </tr>	 
            </tbody>
        </table>
    </div>
</template>

<script>
import { filterNum } from "@/config/mixin.js";
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
        mixins: [filterNum],
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
            if (this.item.game_log_info.game_type_id == 11 || this.item.game_log_info.game_type_id == 8 || this.item.game_log_info.game_type_id == 14 || this.item.game_log_info.game_type_id == 15 || this.item.game_log_info.game_type_id == 23 || this.item.game_log_info.game_type_id == 24 || this.item.game_log_info.game_type_id == 25 || this.item.game_log_info.game_type_id == 26 || this.item.game_log_info.game_type_id == 30 || this.item.game_log_info.game_type_id == 31 || this.item.game_log_info.game_type_id == 33 || this.item.game_log_info.game_type_id == 35 || this.item.game_log_info.game_type_id == 36 || this.item.game_log_info.game_type_id == 44) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=1; i<arr.length-1; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=2; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                for(let i=3; i<arr.length; i+=3) {  
                    thirdArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                return 
            }
            if (this.item.game_log_info.game_type_id == 13 || this.item.game_log_info.game_type_id == 22 || this.item.game_log_info.game_type_id == 32) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=0; i<16; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=1; i<arr.length-1; i+=3) {  
                    secondArr.push(arr[i])
                }
                for(let i=2; i<arr.length; i+=3) {  
                    thirdArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                return 
            }
            if(this.item.game_log_info.game_type_id == 38) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=0; i<arr.length; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=1; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                for(let i=2; i<arr.length; i+=3) {  
                    thirdArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                return 
            }
            if(this.item.game_log_info.game_type_id == 41) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=0; i<arr.length; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=1; i<arr.length; i+=3) {  
                    thirdArr.push(arr[i])
                }
                for(let i=2; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                return 
            }
            if(this.item.game_log_info.game_type_id == 40) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=0; i<arr.length; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=1; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = number
                return 
            }
            if(this.item.game_log_info.game_type_id == 1 || this.item.game_log_info.game_type_id == 3 || this.item.game_log_info.game_type_id == 4 || this.item.game_log_info.game_type_id == 5 || this.item.game_log_info.game_type_id == 9 || this.item.game_log_info.game_type_id == 27) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                for(let i=0; i<6; i++) {  
                    firstArr.push(arr[i])
                }
                for(let i=6; i<12; i++) {  
                    secondArr.push(arr[i])
                }
                for(let i=12; i<18; i++) {  
                    thirdArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                return 
            }
            this.first_num = number.substr(0, 17)
            this.second_num = number.substr(18, 17)
            this.third_num =  number.substr(36, 17)
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