webpackJsonp([22],{"9E+G":function(t,e){},KjIA:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=s("Dd8w"),a=s.n(i),r=s("NYxO"),n={name:"listAwards",components:{headBar:s("sz1i").a},data:function(){return{list:[],firstRow:0,fetchNum:15,total:0,page:1}},computed:a()({},Object(r.c)(["haveLogin","userInfo"])),created:function(){this.get_list()},methods:a()({changePage:function(t){this.page=t,this.firstRow=this.fetchNum*(t-1),this.get_list()},get_list:function(){var t=this;this.$Api({api_name:"kkl.user.rewardList",firstRow:this.firstRow,fetchNum:this.fetchNum},function(e,s){e?t.$msg(e.error_msg,"error",1500):(t.list=s.data.rank_list_list,t.total=Number(s.data.total))})},receive:function(){var t=this;this.$Api({api_name:"kkl.user.getReward"},function(e,s){e?t.$msg(e.error_msg,"error",1500):(t.$msg(s.data,"success",1500),t.get_list(),t.$Api({api_name:"kkl.user.getUserInfo"},function(s,i){s?t.$msg(e.error_msg,"error",1500):t.setUser(i.data)}))})}},Object(r.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},c={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"listAwards"}},[s("headBar",{attrs:{head_title:"排行榜奖励",head_pro:"排行榜可每日可领排行榜奖励"}}),t._v(" "),s("div",{staticClass:"form_list"},[s("p",{staticClass:"title"},[t._v("领奖")]),t._v(" "),s("div",{staticClass:"table"},[t._m(0),t._v(" "),s("ul",{staticClass:"list"},t._l(t.list,function(e,i){return s("li",{key:i,staticClass:"list_info"},[s("p",{staticClass:"time"},[t._v(t._s(e.addtime_str))]),t._v(" "),s("p",{staticClass:"num"},[t._v(t._s(t._f("changeBigNum")(e.reward)))]),t._v(" "),s("p",{staticClass:"btn",class:{active:0==e.is_received},on:{click:function(e){return t.receive()}}},[t._v(t._s(0==e.is_received?"领取":1==e.is_received?"已领取":"已过期"))])])}),0)]),t._v(" "),0!=t.total?s("div",{staticClass:"paging_box"},[s("el-pagination",{attrs:{"page-size":15,"current-page":t.page,layout:"prev, pager, next",total:t.total,"prev-text":"上一页","next-text":"下一页"},on:{"current-change":t.changePage}})],1):t._e()])],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"head_top"},[e("p",[this._v("日期")]),this._v(" "),e("p",[this._v("奖金额")]),this._v(" "),e("p",[this._v("领取")])])}]};var l=s("VU/8")(n,c,!1,function(t){s("9E+G")},"data-v-2c6bb2b3",null);e.default=l.exports}});