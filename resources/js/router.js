import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '@/Pages/Dashboard.vue';
import Contrato from '@/Pages/Contrato.vue';
import Profile from '@/Pages/Profile.vue';

const routes = [
    { path: '/dashboard', component: Dashboard },
    { path: '/contrato', component: Contrato },
    { path: '/profile', component: Profile },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
