webpackJsonp([9],{ImNN:function(t,e){},NcVn:function(t,e){},UAxg:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var i=n("mvHQ"),s=n.n(i),a=n("Dd8w"),_=n.n(a),o=n("NYxO"),d=n("d2gY"),r=n("vzSq"),l=n("bcYq"),m={name:"betModelOne",mixins:[l.d],props:{bet_list:{type:Array,default:function(){return""}},bet_boolean:{type:Boolean,default:function(){return!1}},bet_info:{}},data:function(){return{start_num:"",number:"3000000",max_bean:"999999999",min_bean:"100",bet_mode_id:"",click:!1}},created:function(){var t=this;this.start_num=Number(this.newestItem.issue)+4,setTimeout(function(){if(t.bet_boolean)return t.bet_mode_id=t.bet_info.start_mode_id,t.start_num=t.bet_info.start_issue,t.number=t.bet_info.issue_number,t.max_bean=t.bet_info.max_money,void(t.min_bean=t.bet_info.min_money);t.bet_list.length>0&&(t.bet_mode_id=t.bet_list[0].bet_mode_id)},500)},methods:{begin_bet:function(){var t=[];this.bet_list.map(function(e,n){var i={};i.bet_mode_id=e.bet_mode_id,i.win_change=e.win_mode,i.loss_change=e.loss_mode,t.push(i)}),""!=this.bet_mode_id?this.$emit("begin_bet",this.bet_mode_id,this.start_num,this.number,this.max_bean,this.min_bean,t):this.$msg("请选择模式或期号期数","error",1500)}}},c={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"betModelOne"},[n("table",{staticClass:"tabTop",attrs:{border:"1",cellpadding:"0",cellspacing:"0"}},[t._m(0),t._v(" "),n("tr",[n("td",[n("div",{staticClass:"down_menu"},[n("select",{directives:[{name:"model",rawName:"v-model",value:t.bet_mode_id,expression:"bet_mode_id"}],attrs:{disabled:t.bet_boolean},on:{change:function(e){var n=Array.prototype.filter.call(e.target.options,function(t){return t.selected}).map(function(t){return"_value"in t?t._value:t.value});t.bet_mode_id=e.target.multiple?n:n[0]}}},t._l(t.bet_list,function(e,i){return n("option",{key:i,domProps:{value:e.bet_mode_id}},[t._v(t._s(e.mode_name))])}),0)])]),t._v(" "),n("td",[n("div",{staticClass:"inp_box"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.start_num,expression:"start_num"}],attrs:{type:"number",disabled:t.bet_boolean},domProps:{value:t.start_num},on:{input:function(e){e.target.composing||(t.start_num=e.target.value)}}})])]),t._v(" "),n("td",[n("div",{staticClass:"inp_box"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.number,expression:"number"}],attrs:{type:"number",disabled:t.bet_boolean},domProps:{value:t.number},on:{input:function(e){e.target.composing||(t.number=e.target.value)}}})])]),t._v(" "),n("td",[n("div",{staticClass:"inp_box"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.max_bean,expression:"max_bean"}],attrs:{type:"number",disabled:t.bet_boolean},domProps:{value:t.max_bean},on:{input:function(e){e.target.composing||(t.max_bean=e.target.value)}}})])]),t._v(" "),n("td",[n("div",{staticClass:"inp_box"},[n("input",{directives:[{name:"model",rawName:"v-model",value:t.min_bean,expression:"min_bean"}],attrs:{type:"number",disabled:t.bet_boolean},domProps:{value:t.min_bean},on:{input:function(e){e.target.composing||(t.min_bean=e.target.value)}}})])])])]),t._v(" "),n("div",{staticClass:"tabBot"},[n("table",{attrs:{border:"1",cellpadding:"0",cellspacing:"0"}},[t._m(1),t._v(" "),t._l(t.bet_list,function(e,i){return n("tr",{key:i,staticStyle:{height:"67px","line-height":"67px"}},[n("td",[t._v(t._s(e.mode_name))]),t._v(" "),n("td",[t._v(t._s(e.total_money))]),t._v(" "),n("td",[n("div",{staticClass:"down_menu"},[n("select",{directives:[{name:"model",rawName:"v-model",value:e.win_mode,expression:"item.win_mode"}],attrs:{disabled:t.bet_boolean},on:{change:function(n){var i=Array.prototype.filter.call(n.target.options,function(t){return t.selected}).map(function(t){return"_value"in t?t._value:t.value});t.$set(e,"win_mode",n.target.multiple?i:i[0])}}},t._l(t.bet_list,function(e,i){return n("option",{key:i,domProps:{value:e.bet_mode_id}},[t._v(t._s(e.mode_name))])}),0)])]),t._v(" "),n("td",[n("div",{staticClass:"down_menu"},[n("select",{directives:[{name:"model",rawName:"v-model",value:e.loss_mode,expression:"item.loss_mode"}],attrs:{disabled:t.bet_boolean},on:{change:function(n){var i=Array.prototype.filter.call(n.target.options,function(t){return t.selected}).map(function(t){return"_value"in t?t._value:t.value});t.$set(e,"loss_mode",n.target.multiple?i:i[0])}}},t._l(t.bet_list,function(e,i){return n("option",{key:i,domProps:{value:e.bet_mode_id}},[t._v(t._s(e.mode_name))])}),0)])])])})],2),t._v(" "),n("div",{staticClass:"start_bet"},[n("div",{staticClass:"bet_btn",on:{click:function(e){return t.begin_bet()}}},[t._v(t._s(t.bet_boolean?"取消投注":"开始投注"))])])])])},staticRenderFns:[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("tr",[n("th",{staticStyle:{width:"197px"}},[t._v("开始模式")]),t._v(" "),n("th",{staticStyle:{width:"190px"}},[t._v("开始期号")]),t._v(" "),n("th",{staticStyle:{width:"198px"}},[t._v("期数")]),t._v(" "),n("th",{staticStyle:{width:"198px"}},[t._v("乐豆上限")]),t._v(" "),n("th",{staticStyle:{width:"130px"}},[t._v("下限")])])},function(){var t=this.$createElement,e=this._self._c||t;return e("tr",[e("th",{staticStyle:{width:"197px"}},[this._v("投注模式")]),this._v(" "),e("th",{staticStyle:{width:"190px"}},[this._v("投注乐豆")]),this._v(" "),e("th",{staticStyle:{width:"264px"}},[this._v("赢后使用投注模式")]),this._v(" "),e("th",{staticStyle:{width:"264px"}},[this._v("输后使用投注模式")])])}]};var u=n("VU/8")(m,c,!1,function(t){n("NcVn")},"data-v-e2974572",null).exports,b={name:"autoBet",mixins:[l.d],components:{winResult:r.a,betModelOne:u},data:function(){return{gameType:0,bet_list:[],click:!0,bet_boolean:!1,bet_info:""}},created:function(){this.get_bet_detail(),this.gameType=Object(d.i)(this.choosedGame.game_type_id)},methods:_()({get_bet_list:function(){var t=this;this.$Api({api_name:"kkl.game.BetModeList",game_type_id:this.choosedGame.game_type_id},function(e,n){e?t.$msg(e.error_msg,"error",1500):(1==t.bet_boolean?n.data.bet_mode_list.map(function(e){t.$set(e,"win_mode",e.win_change),t.$set(e,"loss_mode",e.loss_change)}):n.data.bet_mode_list.map(function(e){t.$set(e,"win_mode",e.bet_mode_id),t.$set(e,"loss_mode",e.bet_mode_id)}),t.bet_list=n.data.bet_mode_list)})},get_bet_detail:function(){var t=this;this.choosedGame.game_type_id&&this.$Api({api_name:"kkl.game.getAutoBetInfo",type:"",game_type_id:this.choosedGame.game_type_id},function(e,n){e?t.$msg(e.error_msg,"error",1500):(t.bet_info=n.data.bet_auto_info,1==n.data.bet_auto_info.is_open?t.bet_boolean=!0:t.bet_boolean=!1,t.get_bet_list())})},begin_bet:function(t,e,n,i,a,_){var o=this;1!=this.bet_boolean?(_.map(function(t){""==t.win_change||""==t.loss_change?o.click=!1:o.click=!0}),0==this.click?this.$msg("请选择模式或期号期数","error",1500):this.$Api({api_name:"kkl.game.setAutoBet",game_type_id:this.choosedGame.game_type_id,start_issue:e,start_mode_id:t,issue_number:n,max_money:i,min_money:a,change_json:s()(_)},function(t,e){t?o.$msg(t.error_msg,"error",1500):(o.$msg(e.data,"success",1500),o.get_bet_detail())})):this.$Api({api_name:"kkl.game.stopAutoBet",game_type_id:this.choosedGame.game_type_id},function(t,e){t?o.$msg(t.error_msg,"error",1500):(o.$msg(e.data,"success",1500),o.get_bet_detail())})}},Object(o.d)({chooseGame:"CHOOSE_GAME"}))},v={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"autoBet"},[n("win-result",{attrs:{gameType:t.gameType}}),t._v(" "),n("div",{staticClass:"count_down"},[n("div",{staticClass:"count_down_left"},[n("div",{staticClass:"expect"},[t._v("\n        第\n        "),n("span",[t._v(t._s(t.newestItem.issue))]),t._v("\n        期\n      ")]),t._v(" "),0==t.newestState?n("div",{staticClass:"second"},[t.seconds<=10?n("span",{staticClass:"normal"},[t._v("停止下注，")]):t._e(),t._v("\n        还有\n        "),t.seconds>0?n("span",[t._v(t._s(t.seconds))]):n("span",[t._v("0")]),t._v(" "),t.seconds>10?n("span",{staticClass:"normal"},[t._v("秒停止下注!")]):n("span",{staticClass:"normal"},[t._v("秒开奖!")])]):1==t.newestState?n("div",{staticClass:"wait"},[n("span",[t._v("正在开奖，请稍后！")])]):t._e()]),t._v(" "),t.awardResult.bet_log_info?n("div",{staticClass:"count_down_mid"},[n("div",{staticClass:"mid_top"},[n("div",{staticClass:"top_item"},[t._v("\n          今日亏盈:\n          "),n("span",[t._v(t._s(t._f("changeBigNum")(t.awardResult.bet_log_info.win_loss)))])]),t._v(" "),n("div",{staticClass:"top_item"},[t._v("\n          参与:\n          "),n("span",[t._v(t._s(t.awardResult.bet_log_info.total_issue))]),t._v("\n          期\n        ")]),t._v(" "),n("div",{staticClass:"top_item"},[t._v("\n          胜率:\n          "),n("span",[t._v(t._s(t.awardResult.bet_log_info.rate))]),t._v("\n          %\n        ")])]),t._v(" "),n("div",{staticClass:"mid_bottom"},[t._v("\n        最高下注\n        "),n("span",[t._v(t._s(t._f("changeBigNum")(t.awardResult.game_type_info.max_bet_money)))]),t._v("\n        万豆,最高中奖\n        "),n("span",[t._v(t._s(t._f("changeBigNum")(t.awardResult.game_type_info.max_win_money)))]),t._v("\n        万豆\n      ")])]):t._e(),t._v(" "),t._m(0),t._v(" "),t.openAudio?n("img",{staticClass:"audio-icon",attrs:{src:"http://www.yunshenghuo88.com/images/S_Open.gif",alt:""},on:{click:function(e){return t.setAudioFn()}}}):n("img",{staticClass:"audio-icon",attrs:{src:"http://www.yunshenghuo88.com/images/S_Close.gif",alt:""},on:{click:function(e){return t.setAudioFn()}}}),t._v(" "),n("audio",{staticStyle:{display:"none"},attrs:{src:"http://www.yunshenghuo88.com/image/security.mp3",id:"myaudio"}})]),t._v(" "),n("bet-model-one",{attrs:{bet_list:t.bet_list,bet_boolean:t.bet_boolean,bet_info:t.bet_info},on:{begin_bet:t.begin_bet}})],1)},staticRenderFns:[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"count_down_right"},[e("div",{staticClass:"right_btn"},[this._v("自动投注")])])}]};var p=n("VU/8")(b,v,!1,function(t){n("ImNN")},"data-v-79e879ff",null);e.default=p.exports}});