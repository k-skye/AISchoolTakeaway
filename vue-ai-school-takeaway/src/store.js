import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const types = {
  SET_ADDRESS: 'SET_ADDRESS'
};

export default new Vuex.Store({
  state: {
    address: ''
  },
  getters: {
    address: state => state.address
  },
  mutations: {
    [types.SET_ADDRESS](state, address) {
      if (address) {
        state.address = address;
      } else {
        state.address = '';
      }
    }
  },
  actions: {
    setAddress: ({ commit }, address) => {
    commit(types.SET_ADDRESS, address);
  }
  }
})
