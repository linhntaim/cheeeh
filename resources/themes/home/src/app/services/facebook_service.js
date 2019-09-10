import {Service} from './service'
import {serviceFactory} from './service_factory'

class FacebookService extends Service {
    constructor() {
        super(serviceFactory.factory('facebook'))
    }

    logout(doneCallback) {
        let service = this.service()
        service && service.logout(() => {
            doneCallback()
        })
    }

    login(doneCallback, errorCallback) {
        let service = this.service()
        service && service.login(response => {
            if (response.authResponse) {
                doneCallback(response.authResponse)
            } else {
                errorCallback(response)
            }
        }, {
            scope: 'email',
            return_scopes: true,
        })
    }

    getLoginStatus(connectedCallback, notAuthorizedCallback, notLoginCallback) {
        let service = this.service()
        service && service.getLoginStatus(response => {
            if (response.status === 'connected') {
                connectedCallback(response.authResponse)
            } else if (response.status === 'not_authorized') {
                notAuthorizedCallback(response)
            } else {
                notLoginCallback(response)
            }
        })
    }

    me(doneCallback) {
        let service = this.service()
        service && service.api('/me', {fields: 'id,name,email'}, meResponse => {
            service.api('/me/picture', {redirect: false, width: 1024, height: 1024, type: 'square'}, response => {
                meResponse.picture = response.data.url
                doneCallback(meResponse)
            })
        })
    }
}

export const facebookService = new FacebookService()
