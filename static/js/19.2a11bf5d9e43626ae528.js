webpackJsonp([19],{Z4It:function(t,e){},hJin:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var r=n("Dd8w"),s=n.n(r),i=n("sz1i"),a=n("NYxO"),o={name:"loginTest",components:{headBar:i.a},computed:s()({},Object(a.c)(["haveLogin","userInfo"])),data:function(){return{list:[{title:"不开启"},{title:"开启"}],currentIndex:0}},created:function(){this.currentIndex=this.userInfo.open_chenck_login},methods:s()({change_index:function(t){this.currentIndex=t},confirm:function(){var t=this;this.$Api({api_name:"kkl.user.loginSwitch",switch:this.currentIndex},function(e,n){e?t.$msg(e.error_msg,"error",1500):(t.$msg(n.data,"success",1500),t.$Api({api_name:"kkl.user.getUserInfo"},function(e,n){e?t.$msg(e.error_msg,"error",1500):t.setUser(n.data)}))})}},Object(a.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},c={render:function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{attrs:{id:"loginTest"}},[r("headBar",{attrs:{head_title:"登录验证",head_pro:"设置登陆短信或邮箱验证!"}}),t._v(" "),r("div",{staticClass:"form_list"},[r("div",{staticClass:"boolear"},[r("p",{staticClass:"title_boolear"},[t._v("是否开启登录验证功能？")]),t._v(" "),r("ul",{staticClass:"open_boolear"},t._l(t.list,function(e,s){return r("li",{key:s,on:{click:function(e){return t.change_index(s)}}},[t.currentIndex!=s?r("img",{attrs:{src:n("7BWj"),alt:""}}):t._e(),t._v(" "),t.currentIndex==s?r("img",{attrs:{src:n("+QoG"),alt:""}}):t._e(),t._v(" "),r("p",[t._v(t._s(e.title))])])}),0)]),t._v(" "),t.userInfo.mobile?r("div",{staticClass:"boolear"},[r("p",{staticClass:"title_boolear"},[t._v("手机号码：")]),t._v(" "),r("div",{staticClass:"content_boolear"},[t._v(t._s(t.userInfo.mobile))])]):t._e(),t._v(" "),r("div",{staticClass:"confirm",on:{click:function(e){return t.confirm()}}},[t._v("确定")])])],1)},staticRenderFns:[]};var l=n("VU/8")(o,c,!1,function(t){n("Z4It")},"data-v-666637f9",null);e.default=l.exports}});