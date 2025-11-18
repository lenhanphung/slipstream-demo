import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import CustomerIndex from './views/CustomerIndex.vue';

document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('app');
    if (element) {
        const app = createApp(CustomerIndex);
        app.mount('#app');
    }
});
