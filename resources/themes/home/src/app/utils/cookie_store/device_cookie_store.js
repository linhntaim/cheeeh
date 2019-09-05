import CookieStore from './cookie_store'
import {APP_COOKIE, DEFAULT_DEVICE} from '../../../config'

class DeviceCookieStore extends CookieStore {
    constructor() {
        super(APP_COOKIE.names.device)
    }

    retrieve() {
        let device = super.retrieve()
        if (device) {
            return {
                provider: device.provider ? device.provider : DEFAULT_DEVICE.provider,
                secret: device.secret ? device.secret : null,
            }
        }

        return DEFAULT_DEVICE
    }

    store(device) {
        return super.store({
            provider: device.provider,
            secret: device.secret,
        })
    }
}

export default new DeviceCookieStore()
