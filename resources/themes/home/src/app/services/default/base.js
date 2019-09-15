import DefaultService from '../default_service'

export default class BaseService extends DefaultService {
    index(params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.get(
            '',
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    store(params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.post(
            '',
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    show(id, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.get(
            id,
            {},
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    update(id, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.put(
            id,
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    bulkDestroy(ids, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        params.ids = ids
        return this.delete(
            '',
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    destroy(id, params = {}, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.delete(
            id,
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }
}
