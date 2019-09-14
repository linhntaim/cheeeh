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
                path: '',
                component: () => import('../../../views/master/Blank'),
                meta: {
                    requireNotAuth: true,
                },
                children: [
                    {
                        path: 'login',
                        name: 'login',
                        component: () => import('../../../views/pages/auth/Login'),
                    },
                    {
                        path: 'register',
                        name: 'register',
                        component: () => import('../../../views/pages/auth/Register'),
                    },
                    {
                        path: 'forgot-password',
                        name: 'forgot_password',
                        component: () => import('../../../views/pages/auth/ForgotPassword'),
                    },
                    {
                        path: 'reset-password',
                        name: 'reset_password',
                    },
                    {
                        path: 'reset-password/:email/:token',
                        name: 'reset_password_complete',
                        component: () => import('../../../views/pages/auth/ResetPassword'),
                    },
                ],
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
        children: [
            {
                path: 'logout',
                name: 'logout',
            },
            {
                path: 'verify-email',
                name: 'verify_email',
            },
            {
                path: 'verify-email/:email/:verified_code',
                name: 'verify_email_complete',
            },
        ],
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
                meta: {
                    requireAuth: true,
                },
                component: () => import('../../../views/master/Blank'),
                children: [
                    {
                        path: '',
                        meta: {
                            requireEmailVerified: true,
                        },
                        component: () => import('../../../views/master/Blank'),
                        children: [
                            {
                                path: '',
                                name: 'home',
                                component: () => import('../../../views/pages/Home'),
                            },
                        ],
                    },
                    {
                        path: 'logout',
                        name: 'logout',
                        component: () => import('../../../views/pages/Logout'),
                    },
                    {
                        path: 'verify-email',
                        name: 'verify_email',
                        component: () => import('../../../views/pages/VerifyEmail'),
                    },
                ],
            },
            {
                path: 'verify-email/:email/:verified_code',
                name: 'verify_email_complete',
                component: () => import('../../../views/pages/VerifyEmailComplete'),
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
        ],
    },
    {
        path: '/',
        component: () => import('../../../views/master/Base'),
        meta: {
            middleware: all,
        },
        children: [
            {
                path: 'verify-email/:email/:verified_code',
                name: 'verify_email_complete',
                component: () => import('../../../views/pages/VerifyEmailComplete'),
            },
            {
                path: '*',
                component: () => import('../../../views/error/NotFound'),
            },
        ],
    },
]
