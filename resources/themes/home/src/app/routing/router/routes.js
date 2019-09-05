import Home from '../../../views/master/Home'
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
        path: '/auth',
        component: Auth,
        meta: {
            middleware: all,
        },
        children: [
            {
                path: 'logout',
                name: 'logout',
                meta: {
                    requireAuth: true,
                },
                component: () => import('../../../views/pages/auth/Logout'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
    {
        path: '/',
        component: Home,
        meta: {
            middleware: all,
        },
        children: [
            {
                path: '',
                name: 'home',
                component: () => import('../../../views/pages/Home'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
]
