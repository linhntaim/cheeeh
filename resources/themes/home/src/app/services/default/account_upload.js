import BaseService from './base'

export class AccountUploadService extends BaseService {
    constructor() {
        super('account/upload')
    }

    chunkUploadInit(doneCallback = null, errorCallback = null, alwaysCallback = null) {
        return this.store(
            {_chunk_init: 1},
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    chunkUpload(fileId, chunkIndex, chunkTotal, chunkData, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        const params = new FormData
        params.append('_chunk', '1')
        if (fileId) {
            params.append('file_id', fileId)
        }
        params.append('chunk_index', chunkIndex)
        params.append('chunk_total', chunkTotal)
        params.append('chunk_file', chunkData)
        return this.store(
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }
}

export const accountUploadService = () => new AccountUploadService()
