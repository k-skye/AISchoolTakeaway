import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [{
      path: '/',
      // name: 'index',
      component: () => import('./views/index.vue'),
      children: [{
          path: '',
          redirect: '/home'
        },
        {
          path: '/home',
          name: 'home',
          component: () => import('./views/Home.vue')
        },
        {
          path: '/order',
          name: 'order',
          component: () => import('./views/Order.vue')
        },
        {
          path: '/me',
          name: 'me',
          component: () => import('./views/Me.vue')
        }
      ]
    },
    {
      path: '/shop',
      name: 'shop',
      component: () => import('./views/Shops/Shop.vue'),
      //redirect: '/goods',
      children: [{
          path: '/goods',
          name: 'goods',
          component: () => import('./views/Shops/Goods.vue')
        },
        {
          path: '/comments',
          name: 'comments',
          component: () => import('./views/Shops/Comments.vue')
        }
      ]
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('./views/Register.vue')
    },
    {
      path: '/myAddress',
      name: 'myAddress',
      component: () => import('./views/Orders/MyAddress.vue')
    },
    {
      path: '/addAddress',
      name: 'addAddress',
      component: () => import('./views/Orders/AddAddress.vue')
    },
    {
      path: '/settlement',
      name: 'settlement',
      component: () => import('./views/Orders/Settlement.vue')
    },
    {
      path: '/remark',
      name: 'remark',
      component: () => import('./views/Orders/Remark.vue')
    },
    {
      path: '/filter',
      name: 'filter',
      component: () => import('./views/Filter.vue')
    },
    {
      path: '/orderInfo',
      name: 'orderInfo',
      component: () => import('./views/Orders/OrderInfo.vue')
    },
    {
      path: '/help',
      name: 'help',
      component: () => import('./views/Me/Help.vue')
    },
    {
      path: '/protocol',
      name: 'protocol',
      component: () => import('./views/Me/Protocol.vue')
    },
    {
      path: '/support',
      name: 'support',
      component: () => import('./views/Me/Support.vue')
    },
    {
      path: '/comment',
      name: 'comment',
      component: () => import('./views/Orders/Comment.vue')
    },
    {
      path: '/allcomment',
      name: 'allcomment',
      component: () => import('./views/Me/Allcomment.vue')
    },
    {
      path: '/regprotocol',
      name: 'regprotocol',
      component: () => import('./views/RegProtocol.vue')
    },
    {
      path: '/search',
      name: 'search',
      component: () => import('./views/Search.vue')
    }
  ]
})