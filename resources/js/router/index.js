import { createRouter, createWebHistory } from 'vue-router'
import store from '../../store/store'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/dashboard' },
    {
      path: '/',
      component: () => import('../layouts/default.vue'),
      children: [
        {
          path: 'dashboard',
          component: () => import('../pages/dashboard.vue'),
        },
        {
          path: 'account-settings',
          component: () => import('../pages/account-settings.vue'),
        },
        {
          path: 'create_account',
          component: () => import('../pages/create_account.vue'),
        },
        {
          path: 'add_warehouse',
          component: () => import('../pages/add_warehouse.vue'),
        },
        {
          path: 'view_warehouse',
          component: () => import('../pages/view_warehouse.vue'),
          children: [
            {
              path: 'warehouse/:id',
              name: 'warehouse',
              component: ()=> import('../views/pages/warehouse/viewbyid.vue'),
            },
          ],
        },
        {
          path: 'view_employee',
          component: () => import('../pages/view_employees.vue'),
          children: [
            {
              path: 'employee/:id',
              name: 'employee',
              component: ()=> import('../views/employee/view.vue'),
            },
          ],
        },
        {
          path: 'view_category',
          component: () => import('../pages/view_category.vue'),
        },
        {
          path: 'add_category',
          component: () => import('../pages/add_category.vue'),
        },
        {
          path: 'add_product',
          component: () => import('../pages/add_product.vue'),
        },
        {
          path: 'add_color',
          component: () => import('../pages/add_color.vue'),
        },
        {
          path: 'view_color',
          component: () => import('../pages/view_color.vue'),
        },
        {
          path: 'add_brand',
          component: () => import('../pages/add_brand.vue'),
        },
        {
          path: 'view_brands',
          component: () => import('../pages/view_brands.vue'),
        },
        {
          path: 'typography',
          component: () => import('../pages/typography.vue'),
        },
        {
          path: 'icons',
          component: () => import('../pages/icons.vue'),
        },
        {
          path: 'cards',
          component: () => import('../pages/cards.vue'),
        },
        {
          path: 'tables',
          component: () => import('../pages/tables.vue'),
        },
        {
          path: 'form-layouts',
          component: () => import('../pages/form-layouts.vue'),
        },
      ],
    },
    {
      path: '/',
      component: () => import('../layouts/blank.vue'),
      children: [
        {
          path: 'login',
          component: () => import('../pages/login.vue'),
        },
        {
          path: 'register',
          component: () => import('../pages/register.vue'),
        },
        {
          path: '/:pathMatch(.*)*',
          component: () => import('../pages/[...all].vue'),
        },
      ],
    },
  ],
})

router.beforeEach((to, from, next) => {
  console.log(store.state.isAuthenticated)

  const isAuthenticated = store.getters.isAuthenticated
  if (to.path !== '/login' && !isAuthenticated) {
    next('/login')
  } else {
    next()
  }
})

export default router
