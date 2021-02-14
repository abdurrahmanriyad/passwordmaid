/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('sweetalert');
window.Vue = require('vue');

import VueRouter from 'vue-router'
import ProjectComponent from './components/ProjectsComponent'
import GroupComponent from './components/GroupsComponent'
import CredentialComponent from './components/CredentialsComponent'
import HomeComponent from './components/HomeComponent'
import store from './store'
import Clipboard from 'v-clipboard'
import AuthService from './services/Authentication'

Vue.use(Clipboard);
Vue.use(VueRouter);

// require('./router/routes');
require('./bootstrap/component-registration');

//router
// const projectComponent = {template: require('./components/ProjectsComponent')};

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeComponent
    },
    {
        path: '/projects',
        name: 'projects',
        component: ProjectComponent
    },
    {
        path: '/projects/:projectId',
        name: 'groups',
        component: GroupComponent
    },
    {
        path: '/projects/groups/:groupId',
        name: 'credentials',
        component: CredentialComponent
    },

];

const router = new VueRouter({
    routes // short for `routes: routes`
});

router.beforeEach((to, from, next) => {
    AuthService.isLoggedIn().then(() => {
        next();
    }).catch((error) => {
        window.location.reload(true);
    });
});

const app = new Vue({
    router,
    store
}).$mount('#app');
