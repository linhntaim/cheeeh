import DefaultService from '../default_service'

export class DeviceService extends DefaultService {
    constructor(basePath = 'device') {
        super(basePath)
    }

    currentSave(provider, secret, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'current',
            {
                provider: provider,
                secret: secret,
            },
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }
}

export const deviceService = () => new DeviceService()
