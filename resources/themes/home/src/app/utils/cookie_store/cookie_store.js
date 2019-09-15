import {cookie} from '../cookie'
import {APP_COOKIE} from '../../config'

export default class CookieStore {
    constructor(cookieName) {
        this.cookieName = cookieName
    }

    retrieve() {
        return cookie.get(this.cookieName)
    }

    store(data, expires = '1Y') {
        cookie.set(this.cookieName, data, expires, APP_COOKIE.domain)
        return data
    }

    remove() {
        cookie.remove([this.cookieName], APP_COOKIE.domain)
    }
}
