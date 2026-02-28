import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './components/App.vue';
import router from './router';

import '../css/app.css';
import '@fontsource/tajawal'; // Defaults to weight 400
import '@fontsource/tajawal/700.css'; // Bold
import '@fontsource/tajawal/500.css'; // Medium

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount('#app');
