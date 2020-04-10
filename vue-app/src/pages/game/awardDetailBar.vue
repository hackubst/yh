<template>
<div class="awardDetail">
    <div class="title flex-center">
       <span>{{detail_info.game_log_info.game_type_id == 7?'新蛋蛋百家乐':(detail_info.game_log_info.game_type_id == 29?'新加拿大百家乐':'重庆时时彩')}}</span>
       <span>第{{detail_info.issue}}期开奖结果</span>
    </div>
    <div class="info_box">
       <div class="info_item flex">
          <div class="item_left">
             时间
          </div>
          <div class="item_right">
             {{detail_info.open_time | formatDateYearTime}}
          </div>
       </div>
       <div class="info_item">
          <div class="item_left">
            号码
          </div>
          <div class="item_right">
            <p style="color: #1979FF;">{{first_num_result}}</p>
            <p style="color: #FF1E1E;">{{second_num_result}}</p>
            <p style="color: #FF851E;">{{third_num_result}}</p>
          </div>
       </div>
       <div class="info_item">
          <div class="item_left">
            区位
          </div>
          <div class="item_right" v-if="detail_info.game_log_info.game_type_id != 39">
            <p>第一张牌[第1/2/3/19/20位数字]</p>
            <p>第二张牌[第4/5/6位数字]</p>
            <p>第三张牌[第7/8/9位数字]</p>
            <p>第四张牌[第10/11/12位数字]</p>
            <p>第五张牌[第13/14/15位数字]</p>
            <p>第六张牌[第16/17/18位数字]</p>
          </div>
          <div class="item_right" v-if="detail_info.game_log_info.game_type_id == 39">
            <p>第一张牌[第1/4/7位数字]</p>
            <p>第二张牌[第2/5/8位数字]</p>
            <p>第三张牌[第3/6/9位数字]</p>
            <p>第四张牌[第8/7/6位数字]</p>
            <p>第五张牌[第5/4/3位数字]</p>
            <p>第六张牌[第2/1/0位数字]</p>
          </div>
       </div>
       <div class="info_item">
          <div class="item_left">
            数字
          </div>
          <div class="item_right">
            <p>{{first_num}}</p>
            <p>{{second_num}}</p>
            <p>{{third_num}}</p>
            <p>{{fourth_num}}</p>
            <p>{{fifth_num}}</p>
            <p>{{sixth_num}}</p>
          </div>
       </div>
       <div class="info_item flex">
          <div class="item_left">
             求和
          </div>
          <div class="item_right">
            {{detail_info.game_log_info.part_one_sum}}
            {{detail_info.game_log_info.part_two_sum}}
            {{detail_info.game_log_info.part_three_sum}}
            {{detail_info.game_log_info.part_four_sum}}
            {{detail_info.game_log_info.part_five_sum}}
            {{detail_info.game_log_info.part_six_sum}}
          </div>
       </div>
       <div class="info_item flex">
          <div class="item_left">
             计算
          </div>
          <div class="item_right">
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_one_result).num}}</p>
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_two_result).num}}</p>
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_three_result).num}}</p>
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_four_result).num}}</p>
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_five_result).num}}</p>
            <p>除13的余数 + 1={{JSON.parse(this.detail_info.game_log_info.part_six_result).num}}</p>
          </div>
       </div>
    </div>
</div>
</template>
<script>
export default {
  name: "awardDetail",
  data () {
    return {
        first_num_result: '',
        second_num_result: '',
        third_num_result: '',
        first_num: '',
        second_num: '',
        third_num: '',
        fourth_num: '',
        fifth_num: '',
        sixth_num: '',
    };
  },
  created() {
      this.detail_info = this.$route.query.item
      console.log(this.detail_info)
      let number = this.detail_info.result
      this.first_num_result = number.substr(0, 17)
      this.second_num_result = number.substr(18, 17)
      this.third_num_result =  number.substr(36, 17)
      if (this.detail_info.game_log_info.game_type_id == 39) {
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
  }
}
</script>
<style scoped lang='less'>
.awardDetail{
  padding-top: 12px;
  box-sizing: border-box;
  .title{
    span:nth-child(1){
      margin-right: 6px;
    }
    .sc(18px, #333333);
  }
  .info_box{
    width: 351px;
    margin: 12px auto;
    border-radius: 4px;
    border: 1px solid #CCCCCC;
    .info_item{
      padding: 13px 14px;
      box-sizing: border-box;
      .sc(16px, #333333);
      display: flex;
      .item_left{
        margin-right: 12px;
        line-height: 22px;
      }
      .item_right{
        p{
          margin-top: 4px;
        }
        p:nth-child(1){
          margin-top: 0;
        }
      }
    }
    .info_item:nth-child(2n){
      background: #FFF7EC;
    }
  }
}

</style>