import { createRouter, createWebHistory } from 'vue-router';
import ContentPage from './pages/ContentPage.vue';
import LoginPage from './pages/LoginPage.vue';
import RegisterPage from './pages/RegisterPage.vue';

const routes = [
    { path: '/content', component: ContentPage, meta: { requiresAuth: true } },
    { path: '/login', component: LoginPage },
    { path: '/register', component: RegisterPage },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !localStorage.getItem('authToken')) {
        next('/login');
    } else {
        next();
    }
});

export default router;
