/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import $ from 'jquery';
import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import featherIcon from 'feather-icons';
import SvgVue from 'svg-vue';
import VueRouter from 'vue-router';
import VueBarcodeScanner from 'vue-barcode-scanner';
import VueSweetalert2 from 'vue-sweetalert2';
import vSelect from 'vue-select';
import { longClickDirective } from 'vue-long-click';
import VueToast from 'vue-toast-notification';

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
import 'vue-select/dist/vue-select.css';
// Import one of the available themes
//import 'vue-toast-notification/dist/theme-default.css';
import 'vue-toast-notification/dist/theme-sugar.css';

const swalOptions = {
    confirmButtonColor: '#73c1a9',
    cancelButtonColor: '#f45049',
};

require('./bootstrap');

//integrate
const feather = require('feather-icons');
//call
feather.replace();

window.Vue = require('vue').default;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

let barcodeScannerOption = {
    sound: false, // default is false
    //soundSrc: '', // default is blank
    sensitivity: 300, // default is 100
    requiredAttr: true, // default is false
    controlSequenceKeys: ['NumLock', 'Clear'], // default is null
    callbackAfterTimeout: true // default is false
}

const longClickInstance = longClickDirective({delay: 400, interval: 40})
Vue.directive('longclick', longClickInstance)

Vue.directive('click-outside', {
    bind: function (el, binding, vnode) {
      el.clickOutsideEvent = function (event) {
        // here I check that click was outside the el and his children
        if (!(el == event.target || el.contains(event.target))) {
          // and if it did, call method provided in attribute value
          vnode.context[binding.expression](event);
        }
      };
      document.body.addEventListener('click', el.clickOutsideEvent)
    },
    unbind: function (el) {
      document.body.removeEventListener('click', el.clickOutsideEvent)
    },
});

window.Vuex = Vuex;

Vue.use(Vuex);
Vue.use(featherIcon);
Vue.use(SvgVue);
Vue.use(VueRouter);
Vue.use(VueBarcodeScanner);
Vue.use(VueSweetalert2, swalOptions);
Vue.component('v-select', vSelect);
Vue.use(VueToast);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('navbar-component', require('./components/navbar.vue').default);
Vue.component('deposit-component', require('./components/deposit.vue').default);
Vue.component('app-component', require('./components/App.vue').default);
Vue.component('penjualan-component', require('./components/penjualan.vue').default);

import navbar from './components/navbar';
import deposit from './components/deposit';
import App from './components/App';
import penjualan from './components/penjualan';
import savedCart from './components/savedCart';
import invoices from './components/invoices';
import itemSales from './components/itemSales'

import store from './store.js';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

 const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/app',
            redirect: '/deposit',
        },
        {
            path: '/deposit',
            name: 'deposit',
            component: deposit,
            props: true,

        },
        {
            path: '/penjualan',
            name: 'penjualan',
            component: penjualan,
            props: true,

        },
        {
            path: '/invoices',
            name: 'invoices',
            component: invoices,
            props: true,

        },
        {
            path: '/item-sales',
            name: 'itemSales',
            component: itemSales,
            props: true,

        },
        {
            path: '/saved-cart',
            name: 'saved-cart',
            component: savedCart,
            props: true,

        },
    ],
});


const app = new Vue({
    el: '#app',
    data: {
        //
    },
    components: {
        App,
    },
    store: new Vuex.Store(store),
    router,
});

import JQuery from 'jquery'
window.$ = JQuery
