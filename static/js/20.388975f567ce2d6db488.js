webpackJsonp([20],{"9wtb":function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var i=a("Dd8w"),s=a.n(i),n=a("mtWM"),r=a.n(n),o=a("NYxO"),m=a("xzuO"),l={name:"myInfo",components:{headBar:a("sz1i").a},computed:s()({},Object(o.c)(["haveLogin","userInfo"])),data:function(){return{realname:"",user_img:"",mobile:"",email:"",username:"",number:"",disable_boolean:!1,wx_img:"",token:"",domain:""}},created:function(){this.userInfo.email&&(this.disable_boolean=!0),this.realname=this.userInfo.nickname,this.email=this.userInfo.email,this.mobile=this.userInfo.mobile,this.user_img=this.userInfo.headimgurl,this.username=this.userInfo.alipay_account_name,this.number=this.userInfo.qq,this.wx_img=this.userInfo.wx_account,this.get_token()},methods:s()({get_token:function(){var e=this;this.$Api({api_name:"kkl.index.getQiniuToken"},function(t,a){t?e.$msg(t.error_msg,"error",1500):(e.token=a.data.token,e.domain=a.data.image_domain)})},change_img:function(e){if(this.$refs.upload.files[0]){var t=this,a=r.a.create({withCredentials:!1}),i=new FormData;i.append("token",this.token),i.append("file",this.$refs.upload.files[0]),a({method:"POST",url:"http://upload.qiniu.com/",data:i}).then(function(e){var a=e.data,i=(a.hash,a.key);t.user_img=""+t.domain+i+"?imageView2/1/w/114/h/114"}).catch(function(e){console.log("err",e)})}},confirm:function(){var e=this,t=[{reg:"noData",val:this.realname,errMsg:"请输入正确的昵称"},{reg:"mobile",val:this.mobile,errMsg:"请输入正确的手机号"},{reg:"noData",val:this.email,errMsg:"请输入正确的邮箱号"},{reg:"qq",val:this.number,errMsg:"请输入正确的qq号"},{reg:"noData",val:this.username,errMsg:"请输入正确的收款人"}];Object(m.a)(t).result?this.$Api({api_name:"kkl.user.editUserInfo",nickname:this.realname,qq:this.number,wx_account:this.wx_img,headimgurl:this.user_img,alipay_account_name:this.username,email:this.email,mobile:this.mobile},function(t,a){t?e.$msg(t.error_msg,"error",1500):(e.$msg(a.data,"success",1500),e.$Api({api_name:"kkl.user.getUserInfo"},function(a,i){a?e.$msg(t.error_msg,"error",1500):e.setUser(i.data)}))}):this.$msg(Object(m.a)(t),"error",1500)}},Object(o.d)({setUser:"SET_USER",delUser:"DEL_USER"}))},u={render:function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{attrs:{id:"myInfo"}},[a("headBar",{attrs:{head_title:"我的资料"}}),e._v(" "),a("div",{staticClass:"form_list"},[a("div",{staticClass:"top_part"},[a("ul",{staticClass:"info_detail"},[a("li",[e._m(0),e._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:e.realname,expression:"realname"}],class:{blank:""!=e.realname},attrs:{type:"text"},domProps:{value:e.realname},on:{input:function(t){t.target.composing||(e.realname=t.target.value)}}})]),e._v(" "),a("li",[e._m(1),e._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:e.mobile,expression:"mobile"}],class:{disabled:""!=e.mobile},attrs:{type:"number",disabled:""!=e.mobile},domProps:{value:e.mobile},on:{input:function(t){t.target.composing||(e.mobile=t.target.value)}}})])]),e._v(" "),a("div",{staticClass:"user_img"},[a("img",{attrs:{src:e.user_img,alt:""}}),e._v(" "),a("div",[e._v("更换头像")]),e._v(" "),a("input",{ref:"upload",attrs:{type:"file",accept:"image/*"},on:{change:function(t){return e.change_img(t)}}})])]),e._v(" "),a("ul",{staticClass:"center_part"},[a("li",[e._m(2),e._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:e.email,expression:"email"}],class:{disabled:e.disable_boolean},attrs:{type:"text",disabled:e.disable_boolean},domProps:{value:e.email},on:{input:function(t){t.target.composing||(e.email=t.target.value)}}})]),e._v(" "),a("li",[e._m(3),e._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:e.username,expression:"username"}],class:{blank:""!=e.username},attrs:{type:"text"},domProps:{value:e.username},on:{input:function(t){t.target.composing||(e.username=t.target.value)}}})]),e._v(" "),a("li",[e._m(4),e._v(" "),a("input",{directives:[{name:"model",rawName:"v-model",value:e.number,expression:"number"}],class:{blank:""!=e.number},attrs:{type:"text"},domProps:{value:e.number},on:{input:function(t){t.target.composing||(e.number=t.target.value)}}})]),e._v(" "),a("li",[e._m(5),e._v(" "),a("img",{attrs:{src:e.wx_img}})])]),e._v(" "),a("div",{staticClass:"confirm",on:{click:function(t){return e.confirm()}}},[e._v("确定")])])],1)},staticRenderFns:[function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("昵称："),t("span",[this._v("6-15个字符以内")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("手机号："),t("span",[this._v("11位手机号码")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("邮箱："),t("span",[this._v("输入有效的邮箱地址")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("收款人姓名："),t("span",[this._v("必须与收款的支付宝姓名一致，填写后不能再修改")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("QQ号码："),t("span",[this._v("输入有效的QQ号码")])])},function(){var e=this.$createElement,t=this._self._c||e;return t("p",[this._v("微信："),t("span",[this._v("绑定微信后收验证码更方便")])])}]};var c=a("VU/8")(l,u,!1,function(e){a("ekJQ")},"data-v-64788ae6",null);t.default=c.exports},ekJQ:function(e,t){}});