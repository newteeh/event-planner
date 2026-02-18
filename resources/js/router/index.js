import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/auth/Login.vue';

const routes = [
    // здесь будут маршруты
    {
        path: '/login',
        name: 'login',
        component: Login
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;