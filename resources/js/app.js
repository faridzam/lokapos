/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import $ from 'jquery';
import Vue from 'vue';
import featherIcon from 'feather-icons';
import SvgVue from 'svg-vue';
import VueRouter from 'vue-router';

require('./bootstrap');

//integrate
const feather = require('feather-icons');
//call
 feather.replace();

window.Vue = require('vue').default;

Vue.use(featherIcon);
Vue.use(SvgVue);
Vue.use(VueRouter);
window.$ = window.jQuery = $;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('deposit-component', require('./components/deposit.vue').default);
Vue.component('header-component', require('./components/header.vue').default);

import deposit from './components/deposit';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/deposit',
            name: 'deposit',
            component: deposit,
            props: true,
        },
    ],
});

const app = new Vue({
    el: '#app',
});
