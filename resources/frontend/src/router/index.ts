import { createRouter, createWebHistory } from 'vue-router';
import LogIn from '../views/LogIn.vue';
import UserProfile from '../views/UserProfile.vue';


const routes = [
    {
        path: '/login',
        name: 'LogIn',
        component: LogIn
    },
    {
        path: '/profile',
        name: 'Profile',
        component: UserProfile,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
