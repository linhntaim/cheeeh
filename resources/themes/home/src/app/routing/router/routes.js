import {all} from '../middleware'

export const routes = [
    {
        path: '/error',
        component: () => import('../../../views/master/Error'),
        meta: {
            middleware: all,
        },
        children: [
            {
                path: '',
                component: () => import('../../../views/error/NotFound'),
            },
            {
                path: '400',
                name: 'bad_request',
                component: () => import('../../../views/error/BadRequest'),
            },
            {
                path: '401',
                name: 'unauthenticated',
                component: () => import('../../../views/error/Unauthenticated'),
            },
            {
                path: '403',
                name: 'unauthorized',
                component: () => import('../../../views/error/Unauthorized'),
            },
            {
                path: '404',
                name: 'not_found',
                component: () => import('../../../views/error/NotFound'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
    {
        path: '/auth',
        component: () => import('../../../views/master/Auth'),
        meta: {
            middleware: all,
        },
        children: [
            {
                path: 'login',
                name: 'login',
                meta: {
                    requireNotAuth: true,
                },
                component: () => import('../../../views/pages/auth/Login'),
            },
            {
                path: 'logout',
                name: 'logout',
                meta: {
                    requireAuth: true,
                },
                component: () => import('../../../views/pages/auth/Logout'),
            },
            {
                path: 'register',
                name: 'register',
                meta: {
                    requireNotAuth: true,
                },
                component: () => import('../../../views/pages/auth/Register'),
            },
            {
                path: 'forgot-password',
                name: 'forgot_password',
                meta: {
                    requireNotAuth: false,
                },
                component: () => import('../../../views/pages/auth/ForgotPassword'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
    {
        path: '/',
        name: 'home',
        meta: {
            middleware: all,
            replaced: true,
        },
    },
]

export const authRoutes = [
    ...routes.slice(0, 2),
    {
        path: '/',
        component: () => import('../../../views/master/Base'),
        meta: {
            middleware: all,
            authReplaced: true,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../../../views/pages/Home'),
            },
            {
                path: 'account',
                name: 'account',
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
]

export const notAuthRoutes = [
    ...routes.slice(0, 2),
    {
        path: '/',
        component: () => import('../../../views/master/Auth'),
        meta: {
            middleware: all,
            notAuthReplaced: true,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../../../views/pages/auth/Register'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
]
