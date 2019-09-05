import {Middleware} from '../../../../plugins/middleware'
import {localizer} from '../../../utils/localizer'
import {log} from '../../../utils/log'

class LocaleMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('locale', 'middleware')

        let query = $middlewareManager.to.query
        let locale = query.locale ? query.locale : (query.lang ? query.lang : null)
        if (locale) {
            localizer.set(locale).then(() => {
                $middlewareManager.store.commit('account/setLocale', {
                    locale: locale,
                })
                super.handle($middlewareManager)
            })
        } else {
            super.handle($middlewareManager)
        }
    }
}

export default new LocaleMiddleware()
