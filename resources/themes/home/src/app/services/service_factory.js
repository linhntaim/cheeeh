import {defaultService, services} from './services'

export class ServiceFactory {
    factory(name = null, instance = null) {
        if (name) {
            if (instance) {
                if (!services.hasOwnProperty(name)) {
                    services[name] = {
                        instance: instance,
                        instanceCallback: null,
                        paramsCallback: {},
                    }
                } else {
                    services[name].instance = instance
                }
            }
        } else {
            name = defaultService
        }
        return services[name]
    }

    modify(callback, name = null) {
        callback(this.factory(name))
    }
}

export const serviceFactory = new ServiceFactory()
