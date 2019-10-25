import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      // name: 'index',
      component: () => import('./views/index.vue'),
      children: [{
          path: '',
          redirect: '/order'
        },
        {
          path: '/order',
          name: 'order',
          component: () => import('./views/Order.vue')
        },
        {
          path: '/deliver',
          name: 'deliver',
          component: () => import('./views/Deliver.vue')
        },
        {
          path: '/me',
          name: 'me',
          component: () => import('./views/Me.vue')
        },
        {
          path: '/wallet',
          name: 'wallet',
          component: () => import('./views/Me/Wallet.vue')
        },
        {
          path: '/help',
          name: 'help',
          component: () => import('./views/Me/Help.vue')
        },
        {
          path: '/support',
          name: 'support',
          component: () => import('./views/Me/Support.vue')
        },
        {
          path: '/protocol',
          name: 'protocol',
          component: () => import('./views/Me/Protocol.vue')
        },
        {
          path: '/allorder',
          name: 'allorder',
          component: () => import('./views/Me/Allorder.vue')
        },
        {
          path: '/comment',
          name: 'comment',
          component: () => import('./views/Me/Comment.vue')
        },
        {
          path: '/billdetail',
          name: 'billdetail',
          component: () => import('./views/Me/Billdetail.vue')
        }
      ]
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('./views/Register.vue')
    },
    {
      path: '/regprotocol',
      name: 'regprotocol',
      component: () => import('./views/RegProtocol.vue')
    }
  ]
})
