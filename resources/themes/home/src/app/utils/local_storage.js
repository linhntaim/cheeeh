import {log} from './log'

export class LocalStorage {
    get(name, defaultValue = null) {
        let value = window.localStorage.getItem(name)
        return value ? value : defaultValue
    }

    set(name, value) {
        window.localStorage.setItem(name, value)
    }

    setJson(name, value) {
        window.localStorage.setItem(name, JSON.stringify(value))
    }

    getJson(name, defaultValue = null) {
        let value = this.get(name)
        try {
            return value ? JSON.parse(value) : defaultValue
        } catch (e) {
            log.write(e, 'local_storage')

            return defaultValue
        }
    }

    remove(name) {
        window.localStorage.removeItem(name)
    }
}

export const localStorage = new LocalStorage()
