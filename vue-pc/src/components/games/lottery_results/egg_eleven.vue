<template>
    <div id="egg_11">
        <div class="head_top">
            <div class="title">{{game_type_name}} 第{{item.issue}}期开奖结果</div>
            <div class="close" @click="close()">关闭</div>
        </div>
        <table class="game_table game_egg" cellspacing="0px" style="border-collapse:collapse;">
            <tbody>
                <tr>
                    <td colspan="3">{{game_type_name}} 第{{item.issue}} 期开奖结果</td>
                </tr>
                <tr>
                    <td width="100">开奖时间</td>
                    <td colspan="2">{{item.addtime | formatDateYearTime}}</td>
                </tr>
                <tr>
                    <td width="100">开奖号码</td>
                    <td colspan="3">
                        <span style="color: #4A4130;">{{item.result}}</span>
                    </td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 10">
                    <td>区位</td>
                    <td>第一区[第1/2/3/4/5/6位数字]</td>
                    <td>第二区[第13/14/15/16/17/18位数字]</td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 12 || item.game_log_info.game_type_id == 21">
                    <td>区位</td>
                    <td>第一区[第1/4/7/10/13/16位数字]</td>
                    <td>第二区[第3/6/9/12/15/18位数字]</td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id == 42">
                    <td>区位</td>
                    <td>第一区[第1/4/7位数字]</td>
                    <td>第二区[第2/5/8位数字]</td>
                </tr>
                <tr>
                    <td>数字</td>
                    <td>{{first_num}}</td>
                    <td>{{second_num}}</td>
                </tr>
                <tr>
                    <td>求和</td>
                    <td>{{item.game_log_info.part_one_sum}}</td>
                    <td>{{item.game_log_info.part_three_sum}}</td>
                </tr>
                <tr>
                    <td>计算</td>
                    <td>{{item.game_log_info.part_one_sum}}除以6的余数 + 1</td>
                    <td>{{item.game_log_info.part_three_sum}}除以6的余数 + 1</td>
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
                </tr>
                <tr>
                    <td>开奖</td>
                    <td colspan="2">
                        <ul>
                            <li><img :src="number_one_img" alt=""></li>
                            +
                            <li><img :src="number_two_img" alt=""></li>
                            =
                            <li class="gentle">{{item.game_log_info.result}}</li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {   
        name: "egg_11",
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
                first_num: '',
                second_num: '',
            };
        },
        created() {
            this.number_one_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_one_result+'@2x.png')
            this.number_two_img = require('../../../assets/images/nums/icon_number' + this.item.game_log_info.part_three_result+'@2x.png')        
            let number = this.item.result
            if (this.item.game_log_info.game_type_id == 12 || this.item.game_log_info.game_type_id == 21) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                for(let i=0; i<16; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=2; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                return 
            }
            if (this.item.game_log_info.game_type_id == 42) {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                for(let i=0; i<arr.length; i+=3) {  
                    firstArr.push(arr[i])
                }
                for(let i=1; i<arr.length; i+=3) {  
                    secondArr.push(arr[i])
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                return 
            }
            this.first_num = number.substr(0,17)
            this.second_num = number.substr(36,17)
        },    
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>

<style scoped lang='less'>
    #egg_11{
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
        }
    }
</style>