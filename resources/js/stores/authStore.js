import { useAuthStore } from './authStore';
import { useRouter } from 'vue-router';

export function useAuthMiddleware() {
    const authStore = useAuthStore();
    const router = useRouter();

    // Check if the user is authenticated on route change
    router.beforeEach((to, from, next) => {
        if (to.meta.requiresAuth && !authStore.isAuthenticated) {
            // Redirect to login page if authentication is required and user is not authenticated
            next('/login');
        } else {
            // Continue to the requested route
            next();
        }
    });
}
