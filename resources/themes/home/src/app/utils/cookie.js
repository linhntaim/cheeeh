import {crypto} from './crypto'
import {APP_COOKIE} from '../config'
import Vue from 'vue'

export class Cookie {
    set(name, data, expires = null, domain = null) {
        Vue.cookie.set(name, crypto.encryptJson(data, APP_COOKIE.secret), this.buildOptions(expires, domain))
    }

    get(name) {
        let cookie = Vue.cookie.get(name, null)
        if (cookie) {
            return crypto.decryptJson(cookie, APP_COOKIE.secret)
        }
        return null
    }

    remove(names, domain = null) {
        let options = this.buildOptions(null, domain)
        for (let i in names) {
            Vue.cookie.delete(names[i], options)
        }
    }

    buildOptions(expires = null, domain = null) {
        let options = {}
        if (expires) options.expires = expires
        if (domain) options.domain = domain
        return options
    }
}

export const cookie = new Cookie()
