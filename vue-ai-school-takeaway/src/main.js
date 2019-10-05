import Vue from 'vue'
import { Tabbar, TabbarItem, Swipe, SwipeItem, Sticky, List, NavBar, CouponCell, CouponList, Popup, Icon }  from 'vant';
import 'vant/lib/index.css';
import './plugins/axios'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

Vue.use(Tabbar).use(TabbarItem).use(Swipe).use(SwipeItem).use(Sticky).use(List).use(NavBar).use(CouponCell).use(CouponList).use(Popup).use(Icon);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
