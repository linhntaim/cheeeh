import {externalJs} from './external_js'
import {FACEBOOK_SERVICE} from '../config'

export class FacebookScript {
    constructor() {
        this.loaded = false
        this.localeCode = null
        this.countryCode = null
        this.scriptPointer = null
    }

    load(localeCode, countryCode, loadedCallback = null) {
        if (!this.loaded || localeCode !== this.localeCode || countryCode !== this.countryCode) {
            this.remove()

            this.loaded = false
            this.localeCode = localeCode
            this.countryCode = countryCode

            window.fbAsyncInit = () => {
                this.loaded = true

                window.FB.init({
                    appId: FACEBOOK_SERVICE.app_id,
                    cookie: false,
                    status: true,
                    xfbml: false,
                    version: FACEBOOK_SERVICE.api_version,
                })
                window.FB.AppEvents.logPageView()

                if (loadedCallback) loadedCallback()
            }

            this.scriptPointer = externalJs.add('https://connect.facebook.net/' + localeCode + '_' + countryCode + '/sdk.js')
        }
    }

    remove() {
        if (this.loaded) {
            delete window.FB
            document.getElementById('fb-root').remove()
            externalJs.remove(this.scriptPointer)
        }
    }
}

export const facebookSdk = new FacebookScript()
