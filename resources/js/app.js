import './bootstrap';
import { createApp } from 'vue';
import router from './router';

import App from './components/App.vue';
import NavBar from './components/NavBar.vue';
import ContentPage from './pages/ContentPage.vue';
import LoginPage from './pages/LoginPage.vue';
import RegisterPage from './pages/RegisterPage.vue';

const app = createApp(App);

app.component('nav-bar', NavBar);
app.component('content-page', ContentPage);
app.component('login-page', LoginPage);
app.component('register-page', RegisterPage);

app.use(router);
app.mount('#app');
