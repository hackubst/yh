webpackJsonp([10],{"0DDH":function(t,e){},mbNo:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=i("Dd8w"),n=i.n(s),r=i("NYxO"),a={name:"dailyRelief",components:{headBar:i("sz1i").a},data:function(){return{list:[]}},computed:n()({},Object(r.c)(["haveLogin","userInfo"])),created:function(){this.get_level_list()},methods:n()({get_level_list:function(){var t=this;this.$Api({api_name:"kkl.user.levelList"},function(e,s){if(e)t.$msg(e.error_msg,"error",1500);else{t.list=s.data.level_list;for(var n=0;n<s.data.level_list.length;n++)t.$set(t.list[n],"img",i("ptDo")("./icon_lv"+n+"@2x.png"))}})},receive:function(){var t=this;this.$Api({api_name:"kkl.user.getRelief"},function(e,i){e?t.$msg(e.error_msg,"error",1500):(t.$msg(i.data,"success",1500),t.$Api({api_name:"kkl.user.getUserInfo"},function(i,s){i?t.$msg(e.error_msg,"error",1500):t.setUser(s.data)}))})}},Object(r.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},l={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{attrs:{id:"dailyRelief"}},[i("headBar",{attrs:{head_title:"每日救济",head_pro:"每日可领取系统救济!"}}),t._v(" "),i("div",{staticClass:"form_list"},[i("p",{staticClass:"title"},[t._v("救济条件")]),t._v(" "),i("div",{staticClass:"table"},[t._m(0),t._v(" "),i("ul",{staticClass:"list"},t._l(t.list,function(e,s){return i("li",{key:s,staticClass:"list_info"},[i("img",{staticClass:"level_img",attrs:{src:e.img,alt:""}}),t._v(" "),i("div",{staticClass:"ex_info"},[i("span",[t._v(t._s(e.min_exp))]),t._v(" "),i("div"),t._v(" "),i("span",[t._v(t._s(e.max_exp))])]),t._v(" "),i("p",{staticClass:"bean_num"},[t._v(t._s(e.sign_reward))])])}),0),t._v(" "),i("div",{staticClass:"confirm",on:{click:function(e){return t.receive()}}},[t._v("领取救济")])])])],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"head_top"},[e("p",[this._v("等级")]),this._v(" "),e("p",[this._v("经验")]),this._v(" "),e("p",[this._v("救济乐豆")])])}]};var _=i("VU/8")(a,l,!1,function(t){i("0DDH")},"data-v-6f1fa1ae",null);e.default=_.exports},ptDo:function(t,e,i){var s={"./icon_lv0@2x.png":"C2jl","./icon_lv1@2x.png":"rlrL","./icon_lv2@2x.png":"Of2x","./icon_lv3@2x.png":"t+kD","./icon_lv4@2x.png":"JCwz","./icon_lv5@2x.png":"+iZt","./icon_lv6@2x.png":"X0Ui","./icon_lv7@2x.png":"UPT6"};function n(t){return i(r(t))}function r(t){var e=s[t];if(!(e+1))throw new Error("Cannot find module '"+t+"'.");return e}n.keys=function(){return Object.keys(s)},n.resolve=r,t.exports=n,n.id="ptDo"}});