import {cookie} from '../cookie'
import {APP_COOKIE} from '../../config'

export default class CookieStore {
    constructor(cookieName) {
        this.cookieName = cookieName
    }

    retrieve() {
        return cookie.get(this.cookieName, null, APP_COOKIE.domain)
    }

    store(data) {
        cookie.set(this.cookieName, data, null, APP_COOKIE.domain)
        return data
    }

    remove() {
        cookie.remove([this.cookieName])
    }
}
