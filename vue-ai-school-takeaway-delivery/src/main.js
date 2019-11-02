import Vue from 'vue'
import { Tabbar, TabbarItem, Switch, List, Cell, Button, Icon, Popup, Picker, Field, CellGroup, Tag, NavBar, Panel, Divider, PasswordInput, NumberKeyboard, Collapse, CollapseItem, PullRefresh, Image, Uploader } from 'vant';
import 'vant/lib/index.css';
import './plugins/axios'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

Vue.use(Tabbar).use(TabbarItem).use(Switch).use(List).use(Cell).use(Button).use(Icon).use(Popup).use(Picker).use(Field).use(CellGroup).use(Tag).use(NavBar).use(Panel).use(Divider).use(PasswordInput).use(NumberKeyboard).use(Collapse).use(CollapseItem).use(PullRefresh).use(Image).use(Uploader);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
