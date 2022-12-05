import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    filters: {},
    filtersVisible: false,
    editing: false,
    validating: false,
    canceling: false,
  },
  mutations: {
    updateFilters: function(state, filters) {
      state.filters = filters;
    },
    updateFiltersVisibility: function(state, filtersVisible) {
      state.filtersVisible = filtersVisible;
    },
    toggleFiltersVisibility: function (state) {
      state.filtersVisible = !state.filtersVisible;
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
