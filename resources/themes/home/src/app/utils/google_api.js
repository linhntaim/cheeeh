import {externalJs} from './external_js'

export class GoogleApi {
    constructor() {
        this.loaded = false
        this.scriptPointer = null
    }

    load(loadedCallback = null) {
        if (!this.loaded) {
            this.scriptPointer = externalJs.add('https://apis.google.com/js/api:client.js')
            this.ready(loadedCallback)
        }
    }

    ready(callback = null) {
        if (window.gapi) {
            this.loaded = true
            if (callback) callback()
            return
        }

        setTimeout(() => {
            this.ready(callback)
        }, 200)
    }

    remove() {
        if (this.loaded) {
            externalJs.remove(this.scriptPointer)
        }
    }
}

export const googleApi = new GoogleApi()
