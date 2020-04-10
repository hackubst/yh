<template>
  <div id="exchangeRecord">
    <ul class="nav_list">
      <li
        class="nav_info"
        :class="{active: currentIndex == index}"
        v-for="(item, index) in list"
        :key="index"
        @click="change_index(index)"
      >{{item.title}}</li>
    </ul>
    <div>
      <ul class="head_list" v-if="currentIndex == 0">
        <li
          class="head_title_first"
          v-for="(item, index) in record_list"
          :key="index"
        >{{item.title}}</li>
      </ul>
      <ul class="head_list" v-if="currentIndex == 1">
        <li class="head_title" v-for="(item, index) in head_list" :key="index">{{item.title}}</li>
      </ul>
      <mescroll-vue ref="mescroll" :down="mescrollDown" :up="mescrollUp" @init="mescrollInit">
        <record v-if="currentIndex == 0" :relief_list="relief_list"></record>
        <pointCard
          v-if="currentIndex == 1"
          :relief_list="card_list"
          :check_boolean="check_boolean"
          :copy_list="copy_list"
          @check="check"
          @check_all="check_all"
          @onCopy="onCopy"
        ></pointCard>
      </mescroll-vue>
      <div class="foot" v-if="currentIndex == 1">
        <img
          @click="check_all()"
          v-if="!check_boolean"
          src="../../assets/images/icon/icon_chooseoff@3x.png"
          alt
        />
        <img
          @click="check_all()"
          v-if="check_boolean"
          src="../../assets/images/icon/icon_chooseon@3x.png"
          alt
        />
        <p>全选未选中点卡</p>
        <div
          v-clipboard:copy="copy_list"
          v-clipboard:success="onCopy"
          v-clipboard:error="onError"
        >复制卡密</div>
      </div>
    </div>
  </div>
</template>

<script>
import MescrollVue from 'mescroll.js/mescroll.vue'
import record from '../../components/records/index'
import pointCard from '../../components/pointCard/index'
export default {
  components: {
    record: record,
    pointCard: pointCard,
    MescrollVue
  },
  name: 'exchangeRecord',
  data () {
    return {
      list: [{
        title: '记录'
      }, {
        title: '点卡'
      }],
      currentIndex: 0,
      record_list: [{
        title: '兑换时间',
      }, {
        title: '卡类型',
      }, {
        title: '兑换数量',
      }],
      head_list: [{
        title: '选择',
      }, {
        title: '卡密',
      }, {
        title: '卡类型',
      }, {
        title: '状态',
      }],
      relief_list: [],
      card_list: [],
      mescroll: null, // mescroll实例对象
      mescrollDown: {
        use: false
      },
      mescrollUp: { // 上拉加载的配置.
        callback: this.upCallback, // 上拉回调,
        page: {
          num: 0,
          size: 15
        },
        htmlNodata: '<p class="upwarp-nodata">-- END --</p>',
      },
      check_boolean: false,
      copy_list: [],
    }
  },
  created () {

  },
  methods: {
    change_index (index) {
      this.currentIndex = index
      if (this.currentIndex == 0) {
        this.relief_list = []
      } else if (this.currentIndex == 1) {
        this.card_list = []
      }
      this.mescroll.resetUpScroll();
    },
    mescrollInit (mescroll) {
      this.mescroll = mescroll  // 如果this.mescroll对象没有使用到,则mescrollInit可以不用配置
    },
    // 获取兑换记录
    get_list (firstRow, fetchNum) {
      this.$Api({
        api_name: 'kkl.user.userGiftList',
        type: '4',
        firstRow: firstRow,
        fetchNum: fetchNum
      }, (err, data) => {
        if (!err) {
          let arr = data.data.user_gift_list
          // 如果是第一页需手动制空列表
          if (firstRow === 1) this.relief_list = []
          // 把请求到的数据添加到列表
          this.relief_list = this.relief_list.concat(arr)
          this.$nextTick(() => {
            this.mescroll.endSuccess(arr.length)
            this.mescroll.endBySize(arr.length, data.data.total)
          })
        } else {
          this.$msg(err.error_msg, 'cancel', 'middle', 1500)
        }
      })
    },
    // 获取点卡列表
    get_pointer_list (firstRow, fetchNum) {
      this.$Api({
        api_name: 'kkl.user.myGiftList',
        type: '4',
        firstRow: firstRow,
        fetchNum: fetchNum
      }, (err, data) => {
        if (!err) {
          let arr = data.data.user_gift_list
          data.data.user_gift_list.map((item, index) => {
            this.$set(arr[index], 'check', false)
          })
          // 如果是第一页需手动制空列表
          if (firstRow === 1) this.card_list = []
          // 把请求到的数据添加到列表
          this.card_list = this.card_list.concat(arr)
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
    upCallback (page, mescroll) {
      if (this.currentIndex == 0) {
        this.get_list((page.num - 1) * page.size, page.size)
      } else {
        this.get_pointer_list((page.num - 1) * page.size, page.size)
      }
    },
    // 单选
    check (index) {
      let boolean = this.card_list[index].check
      let pwd = this.card_list[index].card_password
      if (boolean == true) {
        this.card_list[index].check = false
        this.copy_list.splice(index, 1)
      } else {
        this.card_list[index].check = true
        this.copy_list.push(pwd)
      }
    },
    // 全选
    check_all () {
      this.check_boolean = !this.check_boolean
      this.copy_list = []
      if (this.check_boolean == true) {
        this.card_list.map((item, index) => {
          item.check = true
          this.copy_list.push(item.card_password)
        })
      } else {
        this.card_list.map((item, index) => {
          item.check = false
        })
      }
    },
    onCopy () {
      this.$msg('复制成功', 'success', 'middle', 1500)
    }
  }
}
</script>

<style scoped>
.mescroll {
  position: fixed;
  top: 130px;
  bottom: 0;
  height: auto;
}
#exchangeRecord {
  width: 100%;
  /* height: 100%; */
  position: absolute;
  top: 44px;
  left: 0;
  right: 0;
  bottom: 0;
}
.nav_list {
  width: 100%;
  height: 48px;
  background-color: #4a4130;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
.nav_info {
  width: 50%;
  height: 48px;
  line-height: 48px;
  text-align: center;
  font-size: 17px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(255, 237, 212, 1);
  position: relative;
}
.active {
  color: #fed093;
}
.active::after {
  content: '';
  width: 48px;
  height: 4px;
  background-color: #fed093;
  position: absolute;
  left: 50%;
  margin-left: -24px;
  bottom: 4px;
}
.head_list {
  width: 100%;
  height: 38px;
  background-color: #fed093;
  display: flex;
  justify-content: flex-start;
  align-items: center;
}
.head_title {
  width: 93.75px;
  height: 38px;
  font-size: 14px;
  font-weight: 400;
  color: rgba(74, 65, 48, 1);
  text-align: center;
  line-height: 38px;
}
.head_title_first {
  width: 125px;
  height: 38px;
  font-size: 14px;
  font-weight: 400;
  color: rgba(74, 65, 48, 1);
  text-align: center;
  line-height: 38px;
}
.foot {
  width: 100%;
  height: 60px;
  background-color: #fed093;
  position: fixed;
  bottom: 0;
  left: 0;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  z-index: 9999;
}
.foot img {
  width: 14px;
  height: 14px;
  margin: 0 8px 0 24px;
}
.foot p {
  font-size: 16px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(74, 65, 48, 1);
  line-height: 22px;
}
.foot div {
  width: 108px;
  height: 36px;
  background: rgba(255, 133, 30, 1);
  border-radius: 4px;
  font-size: 16px;
  font-family: PingFangSC-Medium;
  font-weight: 500;
  color: rgba(255, 255, 255, 1);
  line-height: 36px;
  text-align: center;
  position: absolute;
  right: 10px;
}
</style>