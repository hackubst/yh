webpackJsonp([15],{c74h:function(t,e){},hz4r:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=s("Dd8w"),i=s.n(a),r=s("sz1i"),n=s("NYxO"),_={name:"rebate",components:{headBar:r.a},computed:i()({},Object(n.c)(["haveLogin","userInfo"]),{title:function(){return"剩余工资("+this.win_loss_left+")"}}),data:function(){return{list:[],firstRow:0,fetchNum:6,total:0,page:1,win_loss_left:""}},created:function(){this.get_rebate_list(),this.get_user_info()},methods:i()({changePage:function(t){this.page=t,this.firstRow=this.fetchNum*(t-1),this.get_rebate_list()},exchange:function(){var t=this;this.$Api({api_name:"kkl.user.exChangeExp"},function(e,s){e?t.$msg(e.error_msg,"error",1500):(t.$msg(s.data,"success",1500),t.$Api({api_name:"kkl.user.getUserInfo"},function(s,a){s?t.$msg(e.error_msg,"error",1500):t.setUser(a.data)}))})},get_rebate_list:function(){var t=this;this.$Api({api_name:"kkl.user.returnLogList",firstRow:this.firstRow,fetchNum:this.fetchNum},function(e,s){e?t.$msg(e.error_msg,"error",1500):(t.list=s.data.return_log_list,t.total=Number(s.data.total))})},get_user_info:function(){var t=this;this.$Api({api_name:"kkl.user.getUserInfo"},function(e,s){e?t.$msg(e.error_msg,"error",1500):(console.log(s.data),t.win_loss_left=s.data.win_loss_left)})}},Object(n.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},l={render:function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{attrs:{id:"rebate"}},[s("headBar",{attrs:{head_title:t.title,head_pro:"亏损返利"}}),t._v(" "),s("div",{staticClass:"form_list"},[s("p",{staticClass:"title"},[t._v("经验换分")]),t._v(" "),s("ul",{staticClass:"exper_list"},[s("li",[s("p",{staticClass:"label"},[t._v("当前经验：")]),t._v(" "),s("p",{staticClass:"count"},[t._v(t._s(t.userInfo.exp))])]),t._v(" "),s("li",[s("p",{staticClass:"label"},[t._v("可兑换经验：")]),t._v(" "),s("p",{staticClass:"count"},[t._v(t._s(t.userInfo.more_exp))])])]),t._v(" "),s("div",{staticClass:"exchange_btn",on:{click:function(e){return t.exchange()}}},[t._v("兑换")]),t._v(" "),s("p",{staticClass:"title"},[t._v("领取记录")]),t._v(" "),s("div",{staticClass:"table"},[t._m(0),t._v(" "),s("ul",{staticClass:"list"},t._l(t.list,function(e,a){return s("li",{key:a,staticClass:"list_info"},[s("p",{staticClass:"time"},[t._v(t._s(e.addtime_str))]),t._v(" "),s("p",{staticClass:"type"},[t._v(t._s(e.return_type))]),t._v(" "),s("p",{staticClass:"number"},[t._v(t._s(t._f("changeBigNum")(e.money)))])])}),0)]),t._v(" "),0!=t.total?s("div",{staticClass:"paging_box"},[s("el-pagination",{attrs:{"page-size":6,"current-page":t.page,layout:"prev, pager, next",total:t.total,"prev-text":"上一页","next-text":"下一页"},on:{"current-change":t.changePage}})],1):t._e()])],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"head_top"},[e("p",[this._v("领取时间")]),this._v(" "),e("p",[this._v("返利类型")]),this._v(" "),e("p",[this._v("返利金额")])])}]};var o=s("VU/8")(_,l,!1,function(t){s("c74h")},"data-v-920a13c0",null);e.default=o.exports}});