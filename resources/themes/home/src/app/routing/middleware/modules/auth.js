import {Middleware} from '../../../../plugins/middleware'
import {log} from '../../../utils/log'
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
                super.redirect($middlewareManager, '/error/400')
            },
        })
    }

    handleAuth($middlewareManager) {
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
            }
        })
    }

    handleNotAuth($middlewareManager) {
        if ($middlewareManager.to.matched.some(record => record.meta.requireAuth)) {
            this.redirect($middlewareManager, '/auth/login')
            return
        }

        super.handle($middlewareManager)
    }
}

export default new AuthMiddleware()
