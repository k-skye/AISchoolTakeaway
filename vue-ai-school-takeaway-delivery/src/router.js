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
          redirect: '/me'
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
          component: () => import('./views/Wallet.vue')
        }

      ]
    }
  ]
})
