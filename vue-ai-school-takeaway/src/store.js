import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const types = {
  ADDRESS_INFO: 'ADDRESS_INFO',
  ORDER_INFO: 'ORDER_INFO',
  REST_INFO: 'REST_INFO',
  USER_INFO: 'USER_INFO',
  REMARK_INFO: 'REMARK_INFO'
};

export default new Vuex.Store({
  state: {
    addrInfo: null,
    orderInfo: null,
    restInfo: null,
    userInfo: null,
    remarkInfo: {
      tableware: '',
      remark: ''
    }
  },
  getters: {
    addrInfo: state => state.addrInfo,
    orderInfo: state => state.orderInfo,
    restInfo: state => state.restInfo,
    userInfo: state => state.userInfo,
    totalPrice: state => {
      let price = 0;
      if (state.orderInfo) {
        const selectFoods = state.orderInfo.selectFoods;
        selectFoods.forEach(food => {
          price += parseFloat(food.price) * food.count;
        });
        price += parseFloat(state.restInfo.deliveryFee);
      }
      return price;
    },
    remarkInfo: state => state.remarkInfo
  },
  mutations: {
    [types.ADDRESS_INFO](state, addrInfo) {
      if (addrInfo) {
        state.addrInfo = addrInfo;
      } else {
        state.addrInfo = '';
      }
    },
    [types.ORDER_INFO](state, orderInfo) {
      if (orderInfo) {
        state.orderInfo = orderInfo;
      } else {
        state.orderInfo = null;
      }
    },
    [types.REST_INFO](state, restInfo) {
      if (restInfo) {
        state.restInfo = restInfo;
      } else {
        state.restInfo = null;
      }
    },
    [types.USER_INFO](state, userInfo) {
      if (userInfo) {
        state.userInfo = userInfo;
      } else {
        state.userInfo = null;
      }
    },
    [types.REMARK_INFO](state, remarkInfo) {
      if (remarkInfo) {
        state.remarkInfo = remarkInfo;
      } else {
        state.remarkInfo = null;
      }
    }
  },
  actions: {
    setAddrInfo: ({ commit }, addrInfo) => {
      commit(types.ADDRESS_INFO, addrInfo);
    },
    setOrderInfo: ({ commit }, orderInfo) => {
      commit(types.ORDER_INFO, orderInfo);
    },
    setRestInfo: ({ commit }, restInfo) => {
      commit(types.REST_INFO, restInfo);
    },
    setUserInfo: ({ commit }, userInfo) => {
      commit(types.USER_INFO, userInfo);
    },
    setRemarkInfo: ({ commit }, remarkInfo) => {
      commit(types.REMARK_INFO, remarkInfo);
    }
  }
})
