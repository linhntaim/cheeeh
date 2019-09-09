import {BeforeMiddlewareManager, AfterMiddlewareManager} from './classes'

const install = (Vue, {router, store, beforeEnable, afterEnable, beforeRouting, afterRouting}) => {
    router.beforeEach((to, from, next) => {
        if (beforeRouting) beforeRouting(to, from)

        if (beforeEnable(to, from)) {
            let middleware = []
            to.matched.forEach((route) => {
                if (route.meta.hasOwnProperty('middleware')) {
                    middleware.push(...route.meta.middleware.before)
                }
            })

            if (middleware.length) {
                (new BeforeMiddlewareManager(middleware, store, router)).run(to, from, next)
            }
            return
        }

        next()
    })

    router.afterEach((to, from) => {
        if (afterEnable(to, from)) {
            let middleware = []
            to.matched.forEach((route) => {
                if (route.meta.hasOwnProperty('middleware')) {
                    middleware.push(...route.meta.middleware.after)
                }
            })
            if (middleware.length) {
                (new AfterMiddlewareManager(middleware, store, router)).run(to, from)
            }
        }

        if (afterRouting) afterRouting(to, from)
    })
}

export default install
