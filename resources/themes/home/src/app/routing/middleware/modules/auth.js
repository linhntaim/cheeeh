import {authRoutes, notAuthRoutes} from '../../router/routes'
import {log} from '../../../utils/log'
import {APP_PATH} from '../../../../config'
import {Middleware} from '../../../../plugins/middleware'
import passportCookieStore from '../../../utils/cookie_store/passport_cookie_store'

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
                super.redirect($middlewareManager, APP_PATH.bad_request)
            },
        })
    }

    handleAuth($middlewareManager) {
        if (this.replaceRoutesIfNeeded($middlewareManager)) return

        if ($middlewareManager.to.matched.some(record => record.meta.requireNotAuth)) {
            this.redirect($middlewareManager, APP_PATH.redirect_path_if_authenticated)
            return
        }

        $middlewareManager.store.dispatch('account/current', {
            doneCallback: () => {
                super.handle($middlewareManager)
            },
            errorCallback: () => {
                if ($middlewareManager.to.matched.some(record => record.meta.requireAuth)) {
                    this.redirect($middlewareManager, APP_PATH.not_authenticated)
                    return
                }

                super.handle($middlewareManager)
            },
        })
    }

    handleNotAuth($middlewareManager) {
        if (this.replaceRoutesIfNeeded($middlewareManager, false)) return

        if ($middlewareManager.to.matched.some(record => record.meta.requireAuth)) {
            this.redirect($middlewareManager, APP_PATH.redirect_path_if_not_authenticated)
            return
        }

        super.handle($middlewareManager)
    }

    replaceRoutesIfNeeded($middlewareManager, auth = true) {
        const router = $middlewareManager.router
        const routes = auth ? authRoutes : notAuthRoutes
        if (router.options.routes[2].meta.replaced
            || (auth && router.options.routes[2].meta.notAuthReplaced)
            || (!auth && router.options.routes[2].meta.authReplaced)) {
            router.replaceRoutes(routes)
            this.redirect($middlewareManager, $middlewareManager.to.path, $middlewareManager.to.query, $middlewareManager.to.hash)
            return true
        }
        return false
    }
}

export default new AuthMiddleware()
