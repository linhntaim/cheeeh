import {Service} from './service'
import {serviceFactory} from './service_factory'
import {GOOGLE_SERVICE} from '../../config'

class GoogleService extends Service {
    constructor() {
        super(serviceFactory.factory('google'))

        this.authClient = null
    }

    auth(callback, errorCallback) {
        if (this.authClient) {
            callback()
            return
        }

        let service = this.service()
        service && service.load('auth2', () => {
            service.auth2.init({
                clientId: GOOGLE_SERVICE.client_id,
            }).then(() => {
                this.authClient = service.auth2.getAuthInstance()
                callback()
            }, errorCallback)
        })
    }

    login(doneCallback, errorCallback) {
        this.auth(() => {
            let done = () => {
                doneCallback(this.authClient.currentUser.get())
            }
            if (this.authClient.isSignedIn.get()) {
                done()
                return
            }

            this.authClient.signIn().then(done, errorCallback)
        }, errorCallback)
    }

    logout(doneCallback) {
        if (this.authClient) {
            this.authClient.signOut().then(() => {
                doneCallback()
            })
            return
        }

        doneCallback()
    }
}

export const googleService = new GoogleService()
