import {log} from '../../../utils/log'
import {Middleware} from '../../../../plugins/middleware'
import {APP_ROUTE} from '../../../config'

class EmailVerifiedMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('email verified', 'middleware')

        if ($middlewareManager.store.getters['account/isLoggedIn']) {
            const user = $middlewareManager.store.getters['account/user']

            if (user.email && user.email.verified
                && $middlewareManager.to.matched.some(record => record.name === APP_ROUTE.verify_email)) {
                this.redirect($middlewareManager, $middlewareManager.router.getPathByName(APP_ROUTE.redirect_path_if_authenticated))
                return
            }

            if ($middlewareManager.to.matched.some(record => record.meta.requireEmailVerified)
                && user.email && !user.email.verified
                && !$middlewareManager.to.matched.some(record => record.name === APP_ROUTE.verify_email)) {
                this.redirect($middlewareManager, $middlewareManager.router.getPathByName(APP_ROUTE.verify_email))
                return
            }
        }

        super.handle($middlewareManager)
    }
}

export default new EmailVerifiedMiddleware()
