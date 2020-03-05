/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

import Vue from 'vue';
import App from "./App";
import router from "./router";

/**
* Create a fresh Vue Application instance
*/
new Vue({
    el: '#app',
    components: { App },
    template: "<App/>",
    router
});
