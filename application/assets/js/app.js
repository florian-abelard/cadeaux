/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import axios from 'axios';
import Vue from 'vue';
import Notifications from 'vue-notification';

import App from "./App";
import NotificationMixin from './mixins/notificationMixin';
import vuetify from './plugins/vuetify';
import router from "./router";

// any CSS you import will output into a single css file (app.css in this case)
import '@mdi/font/css/materialdesignicons.min.css';
import '../css/app.scss';

Vue.use(Notifications);
Vue.mixin(NotificationMixin);

new Vue({
    el: '#app',
    components: { App },
    template: "<App/>",
    router,
    vuetify,
});

axios.interceptors.response.use(
    response => response,
    error => {
        const currentRoute = router.history.current.name;
        if (error.response.status === 401 && currentRoute !== 'login') {
            router.push({ name: 'login' });
        }

        return Promise.reject(error);
    }
);
