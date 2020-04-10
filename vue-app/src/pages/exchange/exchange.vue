<template>
  <div id="exchange">
    <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
        <ul class="list">
          <li class="list_info" v-for="(item, index) in list" :key="index" >
            <p class="title">金龙体验卡</p>
            <p class="number">￥{{item.cash}}</p>
            <p class="prompt">七天未回收，此卡作废</p>
            <div class="btn" @click="turn_next(item.gift_card_id, item.cash)">点击兑换</div>
          </li>
        </ul>
    </mescroll-vue>
    <Confirm v-model="show" :title="title" @on-confirm="confirm()"></Confirm>
  </div>
</template>

<script>
  import MescrollVue from 'mescroll.js/mescroll.vue'
  import { Confirm } from 'vux'
  import { fiveIndex } from '@/config/mixin.js'
  export default {
    name: 'exchange',
    mixins: [fiveIndex],
    data() {
      return {
        list: [],
        show: false,
        title: '',
        id: '',
        mescroll: null, // mescroll实例对象
        mescrollDown:{
          use: false
        }, 
        mescrollUp: { // 上拉加载的配置.
          callback: this.upCallback, // 上拉回调,
          page: {
              num: 0, 
              size: 10 
          },
          noMoreSize: 5,
          htmlNodata: '<p class="upwarp-nodata">-- END --</p>',
			  },
      }
    },
    components: {
      Confirm,
      MescrollVue
    },
    methods: {
      mescrollInit (mescroll) {
        this.mescroll = mescroll  // 如果this.mescroll对象没有使用到,则mescrollInit可以不用配置
      },
      turn_next(id, cash) {
        this.title = '确认兑换' + cash + '元金龙28体验卡' 
        this.id = id
        this.show = true
      },
      confirm() {
        this.$router.push({
            path: '/exchangeGoods',
            query: {
              id: this.id
            }
        })
      },
      // 获取兑换列表
      get_card_list(firstRow, fetchNum) {
        this.$Api({
            api_name: 'kkl.index.giftCardList',
            firstRow: firstRow,
            fetchNum: fetchNum
        }, (err, data) => {
            if (!err) {
                let arr = data.data.gift_card_list
                // 如果是第一页需手动制空列表
                if (firstRow === 1) this.list = []
                // 把请求到的数据添加到列表
                this.list = this.list.concat(arr)
                this.$nextTick(() => {
                    this.mescroll.endSuccess(arr.length)
                    this.mescroll.endBySize(arr.length, data.data.total)
                })
            } else {
                this.$msg(err.error_msg, 'cancel', 'middle', 1500)
            }
        })
      },
      // 上拉加载
      upCallback(page, mescroll) {
        this.get_card_list((page.num-1)*page.size, page.size)
      },
    },
    created() {

    }
  }
</script>

<style scoped lang='less'>
  #exchange {
    width: 100%;
    // min-height: 100%;
    background-color: #4A4130;
    position: absolute;
    top: 44px;
    left: 0;
    bottom: 0;
    right: 0;
    .mescroll {
        height: auto;
    }
    .list{
      display: flex;
      align-items: flex-start;
      justify-content: flex-start;
      flex-wrap: wrap;
      margin-top: 12px;
      .list_info{
        .wh(169px, 140px);
        background:linear-gradient(180deg,rgba(255,209,148,1) 0%,rgba(209,145,60,1) 100%);
        border-radius:8px;
        margin-left: 12px;
        margin-bottom: 12px;
        .title{
          width: 145px;
          font-size:18px;
          font-family:PingFangSC-Medium;
          font-weight:500;
          color:rgba(255,255,255,1);
          line-height:25px;
          margin-top: 12px;
          margin-bottom: 12px;
          margin-left: 12px;
          overflow: hidden;
          text-overflow:ellipsis;
          white-space:nowrap;
        }
        .number{
          font-size:24px;
          font-family:PingFangSC-Medium;
          font-weight:500;
          color:rgba(255,255,255,1);
          line-height:33px;
          margin-left: 12px;
        }
        .prompt{
            font-size:12px;
            font-family:PingFangSC-Regular;
            font-weight:400;
            color:rgba(255,255,255,1);
            line-height:17px;
            margin-left: 12px;
            margin-bottom: 5px;
        }
        .btn{
          width:72px;
          height:24px;
          background:rgba(255,255,255,1);
          border-radius:12px;
          text-align: center;
          line-height: 24px;
          font-size:12px;
          font-family:PingFangSC-Regular;
          font-weight:400;
          color:rgba(74,65,48,1);
          margin-left: 12px;
        }
      }
    }
  }
</style>