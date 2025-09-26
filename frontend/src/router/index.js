import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '@/stores/auth'

import Home from '@/views/Home.vue';
import Login from '@/views/Login.vue';
import Register from '@/views/Register.vue';
import Dashboard from '@/views/Pages/Dashboard.vue';

const routes = [
    //Guest
    { path: '/', component: Home, name: 'home' },
    { path: '/login', component: Login, name: 'login' },
    { path: '/register', component: Register, name: 'register' },
    
    //Auth
    { path: '/dashboard', component: Dashboard, name: 'dashboard', meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore()
    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login')
    } else {
        next()
    }
})

export default router;
