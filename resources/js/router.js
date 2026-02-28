import { createRouter, createWebHistory } from 'vue-router';
import PosPage from './pages/PosPage.vue';
import LoginPage from './pages/LoginPage.vue';
import AdminLayout from './components/AdminLayout.vue';
import AdminDashboard from './pages/Admin/AdminDashboard.vue';
import ProductIndex from './pages/Admin/Products/Index.vue';

import CategoryIndex from './pages/Admin/Categories/Index.vue';

const routes = [
    {
        path: '/login',
        component: LoginPage
    },
    {
        path: '/pos',
        alias: '/',
        component: PosPage,
        meta: { requiresAuth: true }
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            // { path: '', name: 'Dashboard', component: AdminDashboard },
            // { path: 'products', name: 'Products', component: ProductIndex },
            // { path: 'categories', name: 'Categories', component: CategoryIndex },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guard للتحقق من تسجيل الدخول
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    
    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else if (to.path === '/login' && token) {
        next('/pos');
    } else {
        next();
    }
});

export default router;
