import {serviceFactory} from './service_factory'
import {Service, ServiceError} from './service'

class DefaultServiceError extends ServiceError {
    constructor(err, extra) {
        super(err)

        this.extra = extra
    }

    getMessages(defaults = {}) {
        if (this.err.e) {
            if (this.err.e.response) {
                if (this.err.e.response.statusText) return [this.err.e.response.statusText]
                switch (this.err.e.response.status) {
                    case 401:
                        return [defaults['401'] ? defaults['401'] : 'Unauthenticated!']
                    case 403:
                        return [defaults['403'] ? defaults['403'] : 'Forbidden error!']
                    case 404:
                        return [defaults['403'] ? defaults['404'] : 'Not found!']
                    case 500:
                        return [defaults['500'] ? defaults['500'] : 'Application error!']
                }
            }
            if (this.err.e.message) return [this.err.e.message]
            return [defaults['0'] ? defaults['0'] : 'Something went wrong!']
        }
        return this.err.m
    }

    getExtra() {
        return this.extra
    }
}

export default class DefaultService extends Service {
    constructor(basePath = null) {
        super(serviceFactory.factory(), basePath)
    }

    e() {
        this.s.paramsCallback.e = params => {
            params._e = 1
            return params
        }
    }

    es() {
        this.s.paramsCallback.es = params => {
            params._es = 1
            return params
        }
    }

    d() {
        if (this.s.paramsCallback.hasOwnProperty('e')) delete this.s.paramsCallback.e
        if (this.s.paramsCallback.hasOwnProperty('es')) delete this.s.paramsCallback.es
    }

    done(response, doneCallback = null) {
        this.d()
        if (doneCallback) doneCallback(response.data._data, response.data._extra)
    }

    error(error, errorCallback = null) {
        this.d()
        if (errorCallback) {
            if (error.response && error.response.data) {
                errorCallback(new DefaultServiceError({
                    d: error.response.data._data,
                    m: error.response.data._messages,
                }, error.response.data._extra))
            } else {
                errorCallback(new DefaultServiceError({e: error}))
            }
        }
    }

    wait(requests = [], doneCallback = null, errorCallback = null, alwaysCallback = null) {
        let request = this.service().all(requests).then((response) => {
            this.done(response, doneCallback)
        })
        if (errorCallback) {
            request.catch((error) => {
                this.error(error, errorCallback)
            })
        }
        if (alwaysCallback) {
            request.then(() => {
                this.always(alwaysCallback)
            })
        }
        return request
    }

    get(path, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null, cancelToken = null) {
        return this.service().get(this.path(path), {
            params: this.params(params),
            cancelToken: cancelToken,
        })
            .then((response) => {
                this.done(response, doneCallback)
            })
            .catch((error) => {
                this.error(error, errorCallback)
            })
            .then(() => {
                this.always(alwaysCallback)
            })
    }

    post(path, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null, cancelToken = null) {
        return this.service().post(this.path(path), this.params(params), {
            cancelToken: cancelToken,
        })
            .then(response => {
                this.done(response, doneCallback)
            })
            .catch(error => {
                this.error(error, errorCallback)
            })
            .then(() => {
                this.always(alwaysCallback)
            })
    }

    put(path, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null, cancelToken = null) {
        return this.service().put(this.path(path), this.params(params), {
            cancelToken: cancelToken,
        })
            .then((response) => {
                this.done(response, doneCallback)
            })
            .catch((error) => {
                this.error(error, errorCallback)
            })
            .then(() => {
                this.always(alwaysCallback)
            })
    }

    delete(path, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null, cancelToken = null) {
        return this.service().delete(this.path(path), {
            params: this.params(params),
            cancelToken: cancelToken,
        })
            .then((response) => {
                this.done(response, doneCallback)
            })
            .catch((error) => {
                this.error(error, errorCallback)
            })
            .then(() => {
                this.always(alwaysCallback)
            })
    }
}
