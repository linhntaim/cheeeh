import {DeviceService} from './device'

export class AccountDeviceService extends DeviceService {
    constructor() {
        super('account/device')
    }
}

export const accountDeviceService = () => new AccountDeviceService()
