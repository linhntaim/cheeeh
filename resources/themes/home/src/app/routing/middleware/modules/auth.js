import {log} from '../../../utils/log'
import {authRoutes, notAuthRoutes} from '../../router/routes'
import {Middleware} from '../../../../plugins/middleware'
import passportCookieStore from '../../../utils/cookie_store/passport_cookie_store'
import Router from 'vue-router'
import {ui} from '../../../utils/ui'

class AuthMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('auth', 'middleware')

        this.handlePassport($middlewareManager)
    }

    handlePassport($middlewareManager) {
        const store = $middlewareManager.store
        const storedPassport = passportCookieStore.retrieve()

        if (store.getters['account/isLoggedIn']) {
            if (!storedPassport.accessToken || !storedPassport.tokenType || !storedPassport.refreshToken || !storedPassport.tokenEndTime) {
                store.dispatch('account/storePassport')
            }

            this.handleAuth($middlewareManager)
            return
        }

        if (!storedPassport.accessToken || !storedPassport.tokenType || !storedPassport.refreshToken || !storedPassport.tokenEndTime) {
            this.handleNotAuth($middlewareManager)
            return
        }

        if ((new Date).getTime() <= storedPassport.tokenEndTime) {
            store.commit('account/setAuth', storedPassport)
            this.handleAuth($middlewareManager)
            return
        }

        store.dispatch('account/refreshToken', {
            refreshToken: storedPassport.refreshToken,
            doneCallback: () => {
                this.handleAuth($middlewareManager)
            },
            errorCallback: () => {
                super.redirect($middlewareManager, '/error/400')
            },
        })
    }

    handleAuth($middlewareManager) {
        this.replaceRouterIfNeeded($middlewareManager)

        if ($middlewareManager.to.matched.some(record => record.meta.notAuthReplaced)) {
            ui.reloadPage()
            return
        }

        if ($middlewareManager.to.matched.some(record => record.meta.requireNotAuth)) {
            this.redirect($middlewareManager, '/')
            return
        }

        $middlewareManager.store.dispatch('account/current', {
            doneCallback: () => {
                super.handle($middlewareManager)
            },
            errorCallback: () => {
                if ($middlewareManager.to.matched.some(record => record.meta.requireAuth)) {
                    this.redirect($middlewareManager, '/error/401')
                    return
                }

                super.handle($middlewareManager)
            },
        })
    }

    handleNotAuth($middlewareManager) {
        this.replaceRouterIfNeeded($middlewareManager, false)


        if ($middlewareManager.to.matched.some(record => record.meta.authReplaced)) {
            ui.reloadPage()
            return
        }

        if ($middlewareManager.to.matched.some(record => record.meta.requireAuth)) {
            this.redirect($middlewareManager, '/')
            return
        }

        super.handle($middlewareManager)
    }

    replaceRouterIfNeeded($middlewareManager, auth = true) {
        const router = $middlewareManager.router
        const routes = auth ? authRoutes : notAuthRoutes
        if (router.options.routes[2].meta.replaced
            || (auth && router.options.routes[2].meta.notAuthReplaced)
            || (!auth && router.options.routes[2].meta.authReplaced)) {
            const newRouter = (new Router({
                mode: 'history',
                base: process.env.BASE_URL,
                routes: routes,
            }))
            router.options.routes = routes
            router.matcher = newRouter.matcher
            // this.redirect($middlewareManager, $middlewareManager.to.path, $middlewareManager.to.query, $middlewareManager.to.hash)
            return true
        }
        return false
    }
}

export default new AuthMiddleware()
