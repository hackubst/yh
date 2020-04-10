<template>
    <div id="egg_baccarat">
        <div class="head_top">
            <div class="title">{{game_type_name}} 第{{item.issue}}期开奖结果</div>
            <div class="close" @click="close()">关闭</div>
        </div>
        <table class="game_table game_egg" cellspacing="0px" style="border-collapse:collapse;">
            <tbody>
                <tr>
                    <td colspan="7">{{game_type_name}} 第{{item.issue}} 期开奖结果</td>
                </tr>
                <tr>
                    <td width="100">开奖时间</td>
                    <td colspan="6">{{item.addtime | formatDateYearTime}}</td>
                </tr>
                <tr>
                    <td width="100">开奖号码</td>
                    <td colspan="6">
                        <span style="color: #4A4130;">{{item.result}}</span>
                    </td>
                </tr>
                <tr v-if="item.game_log_info.game_type_id != 39">
                    <td>
                        区位
                    </td>
                    <td>第一张牌[第1/2/3/19/20位数字]</td>
                    <td>第二张牌[第4/5/6位数字]</td>
                    <td>第三张牌[第7/8/9位数字]</td>
                    <td>第四张牌[第10/11/12位数字]</td>
                    <td>第五张牌[第13/14/15位数字]</td>
                    <td>第六张牌[第16/17/18位数字]</td>
                </tr>  
                <tr v-if="item.game_log_info.game_type_id == 39">
                    <td>
                        区位
                    </td>
                    <td>第一张牌[第1/4/7位数字]</td>
                    <td>第二张牌[第2/5/8位数字]</td>
                    <td>第三张牌[第3/6/9位数字]</td>
                    <td>第四张牌[第8/7/6位数字]</td>
                    <td>第五张牌[第5/4/3位数字]</td>
                    <td>第六张牌[第2/1/0位数字]</td>
                </tr>  
                <tr>
                    <td>数字</td>
                    <td>{{first_num}}</td>
                    <td>{{second_num}}</td>
                    <td>{{third_num}}</td>
                    <td>{{fourth_num}}</td>
                    <td>{{fifth_num}}</td>
                    <td>{{sixth_num}}</td>
                </tr>
                <tr>
                    <td>求和</td>
                    <td>{{item.game_log_info.part_one_sum}}</td>
                    <td>{{item.game_log_info.part_two_sum}}</td>
                    <td>{{item.game_log_info.part_three_sum}}</td>
                    <td>{{item.game_log_info.part_four_sum}}</td>
                    <td>{{item.game_log_info.part_five_sum}}</td>
                    <td>{{item.game_log_info.part_six_sum}}</td>
                </tr>
                <tr>
                    <td>计算</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_one_result).num}}</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_two_result).num}}</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_three_result).num}}</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_four_result).num}}</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_five_result).num}}</td>
                    <td>除13的余数 + 1={{JSON.parse(this.item.game_log_info.part_six_result).num}}</td>
                </tr>    
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {   
        name: "egg_baccarat",
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
                first_num: '',
                second_num: '',
                third_num: '',
                fourth_num: '',
                fifth_num: '',
                sixth_num: '',
            };
        },
        created() {
            let number = this.item.result
            if (this.item.game_log_info.game_type_id == 39) {
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
                this.fourth_num = number.substr(12, 12)
                this.fifth_num = number.substr(4, 5)
                this.sixth_num =  number.substr(0, 5)
            } else {
                let arr = []
                arr = number.split(',')
                let firstArr = []
                let secondArr = []
                let thirdArr = []
                let fourthArr = []
                let fifthArr = []
                let sixthArr = []
                for(let i=0; i<arr.length; i++) {  
                    if (i == 0 || i == 1 || i == 2 || i == 18 || i == 19) {
                        firstArr.push(arr[i])
                    } else if (i == 3 || i == 4 || i == 5) {
                        secondArr.push(arr[i])
                    } else if (i == 6 || i == 7 || i == 8) {
                        thirdArr.push(arr[i])
                    } else if (i == 9 || i == 10 || i == 11) {
                        fourthArr.push(arr[i])
                    } else if (i == 12 || i == 13 || i == 14) {
                        fifthArr.push(arr[i])
                    } else if (i == 15 || i == 16 || i == 17) {
                        sixthArr.push(arr[i])
                    }
                }
                this.first_num = firstArr.join(',')
                this.second_num = secondArr.join(',')
                this.third_num = thirdArr.join(',')
                this.fourth_num = fourthArr.join(',')
                this.fifth_num = fifthArr.join(',')
                this.sixth_num = sixthArr.join(',')  
            }
            
        },
        methods: {
            close() {
                this.$emit('close')
            }
        }
    }
</script>

<style scoped lang='less'>
    #egg_baccarat{
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
            td{
                min-height: 44px;
                vertical-align: middle;
            }
        }
    }
</style>