import {Service} from '../service'
import {serviceFactory} from '../service_factory'
import {Client} from '@microsoft/microsoft-graph-client'

class MicrosoftAuthenticationProvider {
    constructor(accessToken) {
        this.accessToken = accessToken
    }

    getAccessToken() {
        return this.accessToken
    }
}

class MicrosoftAuthenticationService extends Service {
    constructor() {
        super(serviceFactory.factory('msal'))
    }

    login(doneCallback, errorCallback) {
        let service = this.service()
        if (service) {
            let options = {
                    scopes: ['user.read']
                },
                done = (account) => {
                    let done2 = (accessToken) => {
                        serviceFactory.factory('msgraph', Client.initWithMiddleware({
                            authProvider: new MicrosoftAuthenticationProvider(accessToken)
                        }))
                        doneCallback(account)
                    }
                    service.acquireTokenSilent(options)
                        .then(response => {
                            done2(response.accessToken)
                        })
                        .catch(err => {
                            if (err.name === 'InteractionRequiredAuthError') {
                                return service.acquireTokenPopup(options)
                                    .then(response => {
                                        done2(response.accessToken)
                                    })
                                    .catch(errorCallback)
                            }

                            errorCallback(err)
                        })
                },
                currentAccount = service.getAccount()

            if (currentAccount) {
                done(currentAccount)
                return
            }

            service.loginPopup(options)
                .then(response => {
                    done(response.account)
                })
                .catch(errorCallback)
        }
    }

    logout(doneCallback) {
        let service = this.service()
        service && service.logout()
        doneCallback()
    }
}

export const microsoftAuthenticationService = new MicrosoftAuthenticationService()
