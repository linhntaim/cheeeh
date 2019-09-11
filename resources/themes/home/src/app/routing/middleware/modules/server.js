import {app} from '../../../utils/app'
import {log} from '../../../utils/log'
import {serverClock} from '../../../utils/server_clock'
import {serviceFactory} from '../../../services/service_factory'
import {Middleware} from '../../../../plugins/middleware'
import {UserAgentApplication} from 'msal'
import {APP_URL, MICROSOFT_SERVICE} from '../../../config'
import {facebookSdk} from '../../../utils/facebook_sdk'
import {googleApi} from '../../../utils/google_api'

class ServerMiddleware extends Middleware {
    constructor() {
        super()

        this.server = {}
    }

    handle($middlewareManager) {
        log.write('server', 'middleware')

        app.get().then(appInstance => {
            this.server = appInstance.$server
            this.handleClock()
            this.handleOthers($middlewareManager)
            super.handle($middlewareManager)
        })
    }

    handleClock() {
        serverClock.setClock(this.server.c)
    }

    handleOthers($middlewareManager) {
        // TODO: Handle others by server configuration

        if (this.server.microsoft_enabled) {
            serviceFactory.factory('msal', new UserAgentApplication({
                auth: {
                    clientId: MICROSOFT_SERVICE.client_id,
                    redirectUri: APP_URL,
                },
            }))
        }

        if (this.server.google_enabled) {
            googleApi.load(() => {
                log.write('loaded', 'google')

                serviceFactory.factory('google', window.gapi)
            })
        }

        if (this.server.facebook_enabled) {
            const currentUser = $middlewareManager.store.getters['account/user']
            facebookSdk.load(currentUser.localization.locale, currentUser.localization.country, () => {
                log.write('loaded', 'facebook')

                serviceFactory.factory('facebook', window.FB)
            })
        }
    }
}

export default new ServerMiddleware()
