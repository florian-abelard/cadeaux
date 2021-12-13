import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    filters: {},
  },
  mutations: {
    saveFilters(state, filters) {
      state.filters = filters;
    }
  }
});
