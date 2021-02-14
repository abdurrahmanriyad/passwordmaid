import Vuex from 'vuex';
import Vue from 'vue';
import projectModule from './modules/project'

// Load Vuex
Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        projectModule
    }
})