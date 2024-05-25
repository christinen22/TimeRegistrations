import { createRouter, createWebHistory } from 'vue-router';
import LogIn from '../views/LogIn.vue';
import UserProfile from '../views/UserProfile.vue';
import EditUserProfile from '../views/EditUserProfile.vue';
import TimeTracking from '../views/TimeTracking.vue';


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
    {
        path: '/profile/edit',
        name: 'EditUserProfile',
        component: EditUserProfile,
        meta: { requiresAuth: true },
    },
    {
        path: '/time-tracking',
        name: 'TimeTracking',
        component: TimeTracking,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
