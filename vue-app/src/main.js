// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import Apifn from './api'
import store from './store'
import  filters from './filter'
import  { ToastPlugin } from 'vux'
import MD5 from './config/md5.js'
import Validate from './config/validator.js'
import VueClipboard from 'vue-clipboard2'
import native from './config/index'
// import Vconsole from 'vconsole'
// const vConsole = new Vconsole()
// Vue.use(vConsole)
Vue.config.productionTip = false

Vue.use(ToastPlugin)
Vue.use(VueClipboard)

Vue.prototype.$MD5 = MD5

Vue.prototype.$native = native

Vue.prototype.$toast = function({text: text, type='text', position='middle', time=1500}){
  this.$vux.toast.show({
    text: text,
    type: type,
    position: position,
    time: time
  })
}

Vue.prototype.$msg = function(text, type, position, time){
  this.$vux.toast.show({
    text: text,
    type: type,
    position: position,
    time: time
  })
}

Vue.prototype.validate = function (type, value) {
  return Validate.validate(type, value)
}

for (var key in filters){
  Vue.filter(key, filters[key])
}
Vue.prototype.$Api = Apifn
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
