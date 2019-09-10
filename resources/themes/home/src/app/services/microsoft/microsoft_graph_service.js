import {serviceFactory} from '../service_factory'
import {Service} from '../service'

class MicrosoftGraphService extends Service {
    constructor() {
        super(serviceFactory.factory('msgraph'))
    }

    me(doneCallback, errorCallback) {
        let service = this.service()
        service && service.api('/me').get().then(response => {
            doneCallback(response)
        }).catch(errorCallback)
    }

    mePhoto(doneCallback, errorCallback) {
        let service = this.service()
        service && service.api('/me/photo/$value').get().then(response => {
            doneCallback(response)
        }).catch(errorCallback)
    }

    mePhotoMetadata(doneCallback, errorCallback) {
        let service = this.service()
        service && service.api('/me/photo').get().then(response => {
            doneCallback(response)
        }).catch(errorCallback)
    }

    mePhotosMetadata(doneCallback, errorCallback) {
        let service = this.service()
        service && service.api('/me/photos').get().then(response => {
            doneCallback(response)
        }).catch(errorCallback)
    }

    mePhotosSizeMetadata(size, doneCallback, errorCallback) {
        let service = this.service()
        service && service.api('/me/photos/' + size).get().then(response => {
            doneCallback(response)
        }).catch(errorCallback)
    }
}

export const microsoftGraphService = new MicrosoftGraphService()
