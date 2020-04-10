<template>
  <div class="outermost-layer">
    <div class="right" ref="box">
      <div class="right-head">
        <ul>
          <li style="width:90px" class="issue">期数</li>
          <li>星期</li>
          <li style="width:100px" class="date">时间</li>
          <li class="number-li number" style="width:238px;line-height: 0;flex-direction: column;padding-top: 6px;text-align: center">
            <div style="width: 100%;height: 30px;margin-top: 6px">彩球号码</div>
            <div style="display: flex;justify-content: space-between;">
              <div style="flex:1" v-for="item in numberList">{{item}}</div>
            </div>
          </li>
          <li style="width:60px">特码</li>
          <li style="width:98px">总分</li>
          <li>生肖</li>
          <li>合数</li>
          <li class="number-li" style="width:238px;line-height: 0;flex-direction: column;padding-top: 6px;text-align: center">
            <div style="width: 100%;height: 30px;margin-top: 6px">一肖</div>
            <div style="display: flex;justify-content: space-between;">
              <div style="flex:1" v-for="(item,index) in numberList" v-if="index +1 !== numberList.length ">{{item}}</div>
            </div>
          </li>
          <li style="width: 60px">色波</li>
        </ul>
      </div>
      <div  class="rightBody" id="rightBodyId" style="overflow: scroll;" :style="[{'paddingTop':isScroll ? '30px':'0'}]">
        <div v-for="item in list" >
          <ul>
            <li style="width:90px">{{item.issue}}</li>
            <li>{{item.week}}</li>
            <li style="width:100px">{{item.time}}</li>
            <li class="number-ball" style="width:238px;" >
              <div class="ball" v-for="child in item.result" :style="{background:[child.bg === 1 ?'red':child.bg === 2 ? 'blue':'green']}">{{child.number}}</div>
            </li>
            <li style="display: flex;align-items: center;justify-content: space-between;width:61px;padding: 0 10px;box-sizing: border-box">
              <div >{{item.danshuang}}</div>
              <div >{{item.te_daxiao}}</div>
            </li>
            <li style="display: flex;align-items: center;justify-content: space-between;width:99px;padding: 0 10px;box-sizing: border-box">
              <div >{{item.sum}}</div>
              <div >{{item.zong_daxiao}}</div>
              <div >{{item.zong_danshuang}}</div>
            </li>
            <li>{{item.te_shengxiao}}</li>
            <li>{{item.danshuang}}</li>
            <li class="number-ball" style="width:238px;display: flex;align-items: center;justify-content: space-between;" >
              <div style="flex:1">{{item.xiao_1}}</div>
              <div style="flex:1">{{item.xiao_2}}</div>
              <div style="flex:1">{{item.xiao_3}}</div>
              <div style="flex:1">{{item.xiao_4}}</div>
              <div style="flex:1">{{item.xiao_5}}</div>
              <div style="flex:1">{{item.xiao_6}}</div>
            </li>
            <li style="width: 60px;display: flex;align-items: center;justify-content: center">
                <div class='btn' :class="[item.heshu_sebo === '红波' ? 'btnr':item.heshu_sebo  === '蓝波' ? 'btnb':'btng']">{{item.heshu_sebo}}</div></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
    import {
        RED_WAVE,
        BLUE_WAVE,
        GREEN_WAVE
    } from "@/config/config";
    export default {
        name: "home",
        data() {
            return {
                numberList:['一','二','三','四','五','六','特码'],
                list:[],//列表
                isScroll:false,
                headHeight: 30,
                bodyHeight: window.innerHeight - 30,
            }
        },
        methods: {
            //获取最近7天的开奖结果
            hkGameResult() {
                this.$Api(
                    {
                        api_name: "kkl.user.getLastSevenListResult",
                        is_app: 1
                    },
                    (err, data) => {
                        if (!err) {
                            this.list = data.data
                            let resultArr = [];
                            this.list.forEach(item=> {
                                item.result.split(",").forEach(item => {
                                    let bg;
                                    if (RED_WAVE.indexOf(+item) !== -1) {
                                        bg = 1;
                                    }
                                    if (BLUE_WAVE.indexOf(+item) !== -1) {
                                        bg = 2;
                                    }
                                    if (GREEN_WAVE.indexOf(+item) !== -1) {
                                        bg = 3;
                                    }
                                    resultArr.push({
                                        number: item,
                                        bg
                                    });
                                })
                                item.result = resultArr
                            })
                            console.log(this.list)
                        }
                    })
            },
            handleScroll(){
                this.$nextTick(() => {
                    let scrollLeft = this.$refs.box.scrollLeft
                    let scrollTop =  window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
                    console.log(scrollTop)
                    if(scrollLeft > 0){
                        //滑动了
                        this.isScroll = true
                    }else{
                        this.isScroll = false
                    }
                });

            }
        },
        mounted() {
            // window.addEventListener('scroll', this.handleScroll, true)
            this.hkGameResult()
        }

    }
</script>
<style>
  #vux_view_box_body {
    padding: 0px;
  }
</style>
<style>
  #vux_view_box_body {
    padding: 0px;
  }
</style>

<style scoped lang="less">
  .result-box {
    .wh(100%, auto);

    .table-title {
      width: 100%;
      height: 30px;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0px 1px 0px 0px rgba(242, 242, 242, 1);
      display: flex;
      align-items: center;
      font-size: 12px;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);
      justify-content: space-between;
    }

    .table-content {
      .wh(100%, auto);
      font-size: 10px;
      font-weight: 400;
      color: rgba(51, 51, 51, 1);

      li {
        margin-top: 5px;
        display: flex;
        justify-content: space-between;

        .number-ball {
          width: 130px;
          display: flex;
          align-items: center;
          justify-content: space-between;
        }

        div {
          text-align: center;
        }

        .common-div {
          width: 24px;
        }

        .ball {
          .wh(17px, 17px);
          border-radius: 50%;
          line-height: 17px;
          text-align: center;
          font-size: 10px;
          font-weight: 400;
          color: rgba(255, 255, 255, 1);

        }
      }
    }
  }
  .outermost-layer {
    background-color: #ffffff;
    padding: 0px;
  }

  .left {
    width: 220px;
    height: 100%;
    /*background-color: orange;*/
    float: left;
    display: inline-block;
  }

  .left-head {
    width: 100%;
    /*position: fixed;*/
    /*height: 30px;*/
    z-index: 1;
    background: #cccccc;
    clear: both;
    ul{
      display: flex;
      li{
        width: 40px;
        text-align: center;
        line-height: 30px;
      }
      .number-li{
        width: 140px;
      }
    }
  }

  .left-body {
    /*background-color: olive;*/
    clear: both;
    /*height: 617px;*/
    /*左边设置滚动条，系统监听左边的滚动条位置，保持高度一致*/
    /*overflow-y: scroll;*/
    /*padding-top: 30px;*/
    ul{
      display: flex;
      height: 50px;
      li{
        width: 40px;
        font-size: 12px;
        color: #333333;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .number-ball{
        width: 140px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;

      }
      .ball{
        .wh(20px,20px);
        background: red;
        border-radius: 50%;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        color: #ffffff;
      }
    }
  }
  .number-ball{
    width: 140px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;

  }
  .ball{
    .wh(20px,20px);
    background: red;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    font-size: 12px;
    color: #ffffff;
  }
  .right {
    /*width: calc(100% - 10px);*/
    height: 100%;
    float: left;
    overflow-x: scroll;
    display: inline-block;
  }

  .right-head {
    width:100%;
    background: #FED093;
    /*position: fixed;*/
    height: 66px;
    z-index:1;
    /*background: #cccccc;*/
    /*background-color: greenyellow;*/
    /*height: 30px;*/
    clear: both;
    overflow: auto;
    ul{
      display: flex;
      li{
        display: flex;
        width: 40px;
        line-height: 66px;
        border-right: 1px solid #cccccc;
        justify-content: center;
      }
    }
  }

  .rightBody {

    .btn {
      width: 45px;
      height: 24px;
      border-radius: 2px;
      line-height: 24px;
    }
    .btnr{
      background: linear-gradient(180deg, rgba(245, 81, 95, 1) 0%, rgba(159, 4, 27, 1) 100%);
    }
    .btnb{
      background:linear-gradient(360deg,rgba(16,38,123,1) 0%,rgba(89,141,244,1) 100%);
    }
    .btng{
      background:linear-gradient(360deg,rgba(16,123,36,1) 0%,rgba(71,219,120,1) 100%);
    }
    /*padding-top: 30px;*/
    width:100%;
    /*height: 617px;*/
    clear: both;
    /*overflow: auto;*/
    ul{
      display: flex;
      border-bottom: 1px solid #cccccc;

      li{
        width: 40px;
        height: 50px;
        border-right: 1px solid #cccccc;
        text-align: center;
        line-height: 50px;
      }
    }
  }

</style>
