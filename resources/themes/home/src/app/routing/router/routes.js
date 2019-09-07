import Base from '../../../views/master/Base'
import Auth from '../../../views/master/Auth'
import Error from '../../../views/master/Error'
import {all} from '../middleware'

export default [
    {
        path: '/error',
        component: Error,
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
        path: '/',
        component: Auth,
        meta: {
            middleware: all,
        },
        children: [
            {
                path: '',
                name: 'home',
                meta: {
                    requireAuth: false,
                },
                component: () => import('../../../views/pages/auth/Register'),
            },
            {
                path: 'auth/login',
                name: 'login',
                meta: {
                    requireAuth: false,
                },
                component: () => import('../../../views/pages/auth/Login'),
            },
            {
                path: 'auth/logout',
                name: 'logout',
                meta: {
                    requireAuth: true,
                },
                component: () => import('../../../views/pages/auth/Logout'),
            },
            {
                path: 'auth/register',
                name: 'register',
                meta: {
                    requireAuth: false,
                },
                component: () => import('../../../views/pages/auth/Register'),
            },
            {
                path: 'auth/forgot-password',
                name: 'forgot_password',
                meta: {
                    requireAuth: false,
                },
                component: () => import('../../../views/pages/auth/Register'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
]
