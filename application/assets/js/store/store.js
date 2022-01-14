import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    filters: {},
    editing: false,
    validating: false,
    canceling: false,
  },
  mutations: {
    updateFilters: function(state, filters) {
      state.filters = filters;
    },
    validateForm: function (state) {
      state.validating = true;
    },
    formValidated: function (state, hasError = false) {
      if (!hasError) {
        state.editing = false;
      }
      state.validating = false;
    },
    cancelForm: function (state) {
      state.canceling = true;
    },
    formCanceled: function (state, mode) {
      state.canceling = false;
    },
  }
});
