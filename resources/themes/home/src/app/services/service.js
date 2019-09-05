export class ServiceError {
    constructor(err) {
        this.err = err
    }

    getMessages() {
        return []
    }
}

export class Service {
    constructor(service, basePath = null) {
        this.s = service
        this.basePath = basePath
    }

    done(response, doneCallback = null) {
        if (doneCallback) doneCallback(response)
    }

    error(error, errorCallback = null) {
        if (errorCallback) errorCallback(error)
    }

    always(alwaysCallback = null) {
        if (alwaysCallback) alwaysCallback()
    }

    path(relativePath = null) {
        let split = this.basePath && relativePath ? '/' : ''
        return this.basePath ? this.basePath + split + relativePath : relativePath
    }

    service() {
        return this.s.instanceCallback ? this.s.instanceCallback(this.s.instance) : this.s.instance
    }

    params(params) {
        return this.s.paramsCallback ? this.paramsCollect(params) : params
    }

    appendParam(params, name, value) {
        if (params instanceof FormData) {
            params.append(name, value)
        } else {
            params[name] = value
        }
        return params
    }

    paramsCollect(params) {
        let isFormDataParams = params instanceof FormData
        let p = isFormDataParams ? {} : params
        for (let index in this.s.paramsCallback) {
            p = this.s.paramsCallback[index](p)
        }
        if (isFormDataParams) {
            for (let index in p) {
                params.append(index, typeof p[index] === 'object' ? JSON.stringify(p[index]) : p[index])
            }
        } else params = p
        return params
    }

    wait() {
        return null
    }

    get() {
        return null
    }

    post() {
        return null
    }

    put() {
        return null
    }

    delete() {
        return null
    }
}
