import {Middleware} from '../../../../plugins/middleware'
import {session} from '../../../utils/session'
import {log} from '../../../utils/log'
import deviceCookieStore from '../../../utils/cookie_store/device_cookie_store'

class DeviceMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('device', 'middleware')

        const store = $middlewareManager.store
        if (store.getters['device/failed'] && $middlewareManager.to.path === '/error/400') {
            super.handle($middlewareManager)
            return
        }

        const storedDevice = deviceCookieStore.retrieve()

        if (session.isNotFresh() && store.getters['device/existed']) {
            if (!storedDevice.provider || !storedDevice.secret) {
                store.dispatch('device/device')
            }
            super.handle($middlewareManager)
            return
        }

        if (session.isFresh() || !storedDevice.provider || !storedDevice.secret) {
            store.dispatch('device/current', {
                device: storedDevice,
                isLoggedIn: store.getters['account/isLoggedIn'],
                doneCallback: () => {
                    super.handle($middlewareManager)
                },
                errorCallback: () => {
                    store.dispatch('device/fails')
                    super.redirect($middlewareManager, '/error/400')
                },
            })
            return
        }

        super.handle($middlewareManager)
    }
}

export default new DeviceMiddleware()
