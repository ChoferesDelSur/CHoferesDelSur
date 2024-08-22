import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import VueSelect from 'vue-select';
import DataTablesLib from 'datatables.net'; //Se agrego esto
import DataTable from 'datatables.net-vue3';//Se agrego esto
import axios from 'axios';

 
DataTable.use(DataTablesLib);//Se agrego esto

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
// Configura la base URL para Axios
axios.defaults.baseURL = import.meta.env.VITE_APP_URL;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy) //Le agregué el Ziggy
            .component("v-select",VueSelect)
            .component('DataTable', DataTable) //Se agregó esto
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});