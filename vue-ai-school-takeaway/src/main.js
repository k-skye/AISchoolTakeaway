import Vue from 'vue'
import { Tabbar, TabbarItem, Swipe, SwipeItem, Sticky, List, NavBar, CouponCell, CouponList, Popup, Icon, Picker, Cell, CellGroup, Collapse, CollapseItem, Field, Button , Uploader, Rate, Search , RadioGroup, Radio , PullRefresh   }  from 'vant';
import 'vant/lib/index.css';
import './plugins/axios'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

Vue.use(Tabbar).use(TabbarItem).use(Swipe).use(SwipeItem).use(Sticky).use(List).use(NavBar).use(CouponCell).use(CouponList).use(Popup).use(Icon).use(Picker).use(Cell).use(CellGroup).use(Collapse).use(CollapseItem).use(Field).use(Button).use(Uploader).use(Rate).use(Search).use(RadioGroup).use(Radio).use(PullRefresh);


new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
