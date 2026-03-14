import { createRouter, createWebHistory } from 'vue-router';
import PosPage from './pages/PosPage.vue';
import LoginPage from './pages/LoginPage.vue';
import AdminLayout from './components/AdminLayout.vue';
import AdminDashboard from './pages/Admin/AdminDashboard.vue';
import ProductIndex from './pages/Admin/Products/Index.vue';
import AdminAuth from './pages/AdminAuth.vue';

import CategoryIndex from './pages/Admin/Categories/Index.vue';
import StaffIndex from './pages/Admin/Staff/Index.vue';
import SettingsIndex from './pages/Admin/Settings/Index.vue';
import PermissionsIndex from './pages/Admin/Permissions/Index.vue';
import EditDesignationPermissions from './pages/Admin/Permissions/EditDesignation.vue';

const routes = [
    {
        path: '/login',
        component: LoginPage
    },
    {
        path: '/admin/auth',
        component: AdminAuth,
        meta: { requiresAuth: false }
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
            { path: '', name: 'Dashboard', component: AdminDashboard },
            { path: 'products', name: 'Products', component: ProductIndex },
            { path: 'categories', name: 'Categories', component: CategoryIndex },
            { path: 'staff', name: 'Staff', component: StaffIndex },
            { path: 'settings', name: 'Settings', component: SettingsIndex },
            { path: 'permissions', name: 'Permissions', component: PermissionsIndex },
            { 
                path: 'permissions/designations/:id/edit', 
                name: 'EditDesignationPermissions', 
                component: EditDesignationPermissions 
            },
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
    
    // صفحة مصادقة المدير لا تتطلب token
    if (to.path === '/admin/auth') {
        if (token) {
            // إذا كان المستخدم مسجل الدخول بالفعل، يمكنه الوصول لمصادقة المدير
            next();
        } else {
            // إذا لم يكن مسجل الدخول، وجهه لتسجيل الدخول العادي
            next('/login');
        }
        return;
    }
    
    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else if (to.path === '/login' && token) {
        next('/pos');
    } else {
        next();
    }
});

export default router;
