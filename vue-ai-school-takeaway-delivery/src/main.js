import Vue from 'vue'
import { Tabbar, TabbarItem, Switch, List, Cell, Button, Icon, Popup, Picker, Field, CellGroup, Tag, NavBar, Panel, Divider, PasswordInput, NumberKeyboard } from 'vant';
import 'vant/lib/index.css';
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

Vue.use(Tabbar).use(TabbarItem).use(Switch).use(List).use(Cell).use(Button).use(Icon).use(Popup).use(Picker).use(Field).use(CellGroup).use(Tag).use(NavBar).use(Panel).use(Divider).use(PasswordInput).use(NumberKeyboard);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
