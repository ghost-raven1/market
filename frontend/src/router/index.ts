import { createRouter, createWebHistory, RouteRecordRaw, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import MessagesList from '@/components/messages/MessagesList.vue'
import Chat from '@/components/messages/Chat.vue'
import AdvertisementList from '@/components/advertisement/AdvertisementList.vue'
import AdvertisementForm from '@/components/advertisement/AdvertisementForm.vue'
import AdvertisementDetails from '@/components/advertisement/AdvertisementDetails.vue'
import CategoryManager from '@/components/category/CategoryManager.vue'
import NotificationList from '@/components/notifications/NotificationList.vue'
import Statistics from '@/components/admin/Statistics.vue'
import ContentModeration from '@/components/admin/ContentModeration.vue'

type RouteMeta = {
  requiresAuth?: boolean;
  guest?: boolean;
  requiresAdmin?: boolean;
  [key: string]: unknown;
}

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/views/HomeView.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/LoginView.vue'),
    meta: { guest: true } as RouteMeta
  },
  {
    path: '/register',
    name: 'register',
    component: () => import('@/views/RegisterView.vue'),
    meta: { guest: true } as RouteMeta
  },
  {
    path: '/advertisements',
    name: 'advertisements',
    component: AdvertisementList,
    meta: { requiresAuth: false }
  },
  {
    path: '/advertisements/create',
    name: 'advertisement-create',
    component: AdvertisementForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/advertisements/:id',
    name: 'advertisement-details',
    component: AdvertisementDetails
  },
  {
    path: '/advertisements/:id/edit',
    name: 'advertisement-edit',
    component: AdvertisementForm,
    meta: { requiresAuth: true }
  },
  {
    path: '/favorites',
    name: 'favorites',
    component: () => import('@/views/FavoritesView.vue'),
    meta: { requiresAuth: true } as RouteMeta
  },
  {
    path: '/messages',
    name: 'messages',
    component: MessagesList,
    meta: { requiresAuth: true }
  },
  {
    path: '/messages/:id',
    name: 'chat',
    component: Chat,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/ProfileView.vue'),
    meta: { requiresAuth: true } as RouteMeta
  },
  {
    path: '/admin',
    name: 'admin',
    component: () => import('@/views/AdminView.vue'),
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    } as RouteMeta
  },
  {
    path: '/categories',
    name: 'categories',
    component: CategoryManager,
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/notifications',
    name: 'notifications',
    component: NotificationList,
    meta: { requiresAuth: true }
  },
  {
    path: '/statistics',
    name: 'statistics',
    component: Statistics,
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/moderation',
    name: 'moderation',
    component: ContentModeration,
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to: RouteLocationNormalized) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some((record: RouteRecordRaw) => (record.meta as RouteMeta)?.requiresAuth)

  if (requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.name === 'login' && authStore.isAuthenticated) {
    return { name: 'home' }
  }

  return true
})

export default router
