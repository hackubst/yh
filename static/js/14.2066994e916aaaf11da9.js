webpackJsonp([14],{KHMT:function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=n("Dd8w"),s=n.n(r),i=n("sz1i"),c=n("NYxO"),a={name:"loginTest",components:{headBar:i.a},computed:s()({},Object(c.c)(["haveLogin","userInfo"])),data:function(){return{list:[{title:"不开启"},{title:"开启"}],currentIndex:0}},created:function(){this.currentIndex=this.userInfo.open_chenck_personal},methods:s()({change_index:function(e){this.currentIndex=e},confirm:function(){var e=this;this.$Api({api_name:"kkl.user.personSwitch",switch:this.currentIndex},function(t,n){t?e.$msg(t.error_msg,"error",1500):(e.$msg(n.data,"success",1500),e.$Api({api_name:"kkl.user.getUserInfo"},function(t,n){t?e.$msg(t.error_msg,"error",1500):e.setUser(n.data)}))})}},Object(c.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},o={render:function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{attrs:{id:"loginTest"}},[r("headBar",{attrs:{head_title:"用户中心验证",head_pro:"设置进入用户中心安全密码验证!!"}}),e._v(" "),r("div",{staticClass:"form_list"},[r("div",{staticClass:"boolear"},[r("p",{staticClass:"title_boolear"},[e._v("是否开启登录验证功能？")]),e._v(" "),r("ul",{staticClass:"open_boolear"},e._l(e.list,function(t,s){return r("li",{key:s,on:{click:function(t){return e.change_index(s)}}},[e.currentIndex!=s?r("img",{attrs:{src:n("7BWj"),alt:""}}):e._e(),e._v(" "),e.currentIndex==s?r("img",{attrs:{src:n("+QoG"),alt:""}}):e._e(),e._v(" "),r("p",[e._v(e._s(t.title))])])}),0)]),e._v(" "),r("div",{staticClass:"confirm",on:{click:function(t){return e.confirm()}}},[e._v("确定")])])],1)},staticRenderFns:[]};var l=n("VU/8")(a,o,!1,function(e){n("WS9p")},"data-v-c29b7758",null);t.default=l.exports},WS9p:function(e,t){}});