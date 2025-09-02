import './bootstrap';
import '../css/app.css';


// Import icon libraries
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'

// Import Quasar css
import 'quasar/src/css/index.sass'


import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from "../../vendor/tightenco/ziggy/dist"
import { Dialog, Loading, Notify, Quasar } from "quasar";


import { Chart, BarController,BarElement,CategoryScale,LinearScale,LineController,PointElement,LineElement, ArcElement, Tooltip,PieController,Legend } from 'chart.js';
Chart.register(PointElement,LineElement,BarController,LineController,CategoryScale,LinearScale,BarElement, ArcElement, Tooltip,PieController,Legend);

const appName = import.meta.env.VITE_APP_NAME || 'EDMS';

createInertiaApp({
    title: (title) => `${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Quasar,{
                plugins:{Notify,Dialog,Loading}
            })
            .mount(el)
    },
    progress: {
        color: '#4B5563',
    },
})
