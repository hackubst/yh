<template>
<div class="red_packet_box" @click="closeDialog()">
   <div class="red_he" v-if="!ifopen">
      <p>给你发了一个红包</p>
      <div class="red_btn" @click.stop="onpenPacket()">
          点击拆红包
      </div>
   </div>
   <div class="red_kai" v-else>
       <div class="get_info">
           <p class="title">获得金额</p>
           <p class="num">{{money}}</p>
       </div>
       <p class="red_txt">已存入到您的银行中</p>
       <div class="red_btn" @click.stop="sendPacket()">我要发红包</div>
   </div>
</div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { ALERT_TIME } from '@/config/config.js'
export default {
  name: "redPacket",
  data () {
    return {
       ifopen: false,
       money: 0
    };
  },
  computed: {
      ...mapGetters([
          'red_packet_id'
      ])
  },
  methods: {
      onpenPacket: function(){
          this.$Api({
              api_name: 'kkl.index.getRedPacket',
              red_packet_id: this.red_packet_id
          }, (err, data) => {
             if(!err){
                 this.money = data.data.money
                 this.refreshUserInfo()
                 this.ifopen = true
             }else{
                 this.setRedPacket('')
                 this.$msg(err.error_msg, 'error', ALERT_TIME)
             }
          })
      },
      sendPacket: function(){
          this.setRedPacket('')
          this.$router.push({
              path: '/myPacket'
          })
      },
      closeDialog: function(){
          this.refreshUserInfo()
          this.setRedPacket('')
      },
      ...mapActions([
          'setRedPacket',
          'refreshUserInfo'
      ])
  }
}
</script>
<style scoped lang='less'>
.red_packet_box{
    width: 100%;
    height: 100%;
    background:rgba(0,0,0,0.5);
    position: fixed;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
    .red_he{
        width: 460px;
        height: 554px;
        background: url('~images/bg/bg_hongbao1@2x.png') no-repeat;
        background-size: 100% 100%;
        padding-top: 285px;
        box-sizing: border-box;
        text-align: center;
        p{
            font-size: 30px;
            color:rgba(255,248,239,1);
            margin-bottom: 54px;
        }
        .red_btn{
            .commonBtn(78px, 288px);
            margin: 0 auto;
        }

    }
    .red_kai{
        width: 460px;
        height: 554px;
        background: url('~images/bg/bg_hongbao2@2x.png') no-repeat;
        background-size: 100% 100%;
        padding-top: 76px;
        box-sizing: border-box;
        .get_info{
            margin-bottom: 125px;
            color: #D1913C;
            text-align: center;
            .title{
                font-size: 24px; 
                margin-bottom: 3px;  
            }
            .num{
                height: 56px;
                line-height: 56px;
                font-size: 40px;
            }
        }
        .red_txt{
            text-align: center;
            font-size: 30px;
            color:rgba(255,248,239,1);
            margin-bottom: 54px;
        }
        .red_btn{
            .commonBtn(78px, 288px);
            margin: 0 auto;
        }

    }
}
</style>