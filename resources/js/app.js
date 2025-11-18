import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import CustomerIndex from './views/CustomerIndex.vue';

const app = createApp({});

app.component('CustomerIndex', CustomerIndex);

document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('app');
    if (element) {
        app.mount('#app');
    }
});
