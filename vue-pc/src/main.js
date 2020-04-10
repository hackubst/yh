// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import store from './store'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import VueClipboard from 'vue-clipboard2'
import Apifn from './api'
import Validate from './config/validator.js'
import MD5 from './config/md5.js'
import filters from './filter'

Vue.use(VueClipboard)

Vue.use(ElementUI);

for (var key in filters) {
  Vue.filter(key, filters[key])
}
Vue.config.productionTip = false

Vue.prototype.$MD5 = MD5;
// 弹框
Vue.prototype.$Alert = function (c, t) {
  return this.$alert(c, t, {
    confirmButtonText: '确定'
  });
}
// 消息提示
Vue.prototype.$msg = function (content, type, time) {
  return this.$message({
    message: content,
    type: type,
    duration: time
  });
}


Vue.prototype.validate = function (type, value) {
  return Validate.validate(type, value)
}

Vue.prototype.$Api = Apifn

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: {
    App
  },
  template: '<App/>'
})

router.beforeEach((to, from, next) => {
  if (!store.getters.haveLogin && to.path != '/login' && to.path != '/register' && to.path != '/stepOne' && to.path != '/stepTwo' && to.path != '/stepThree' && to.path != '/richText' && to.path != '/index') {
    // next({
    //   path: "/login"
    // });
  } else {
    next()
  }
})
