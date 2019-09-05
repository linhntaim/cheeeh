import DefaultService from '../default_service'

export class PrerequisiteService extends DefaultService {
    constructor() {
        super('prerequisite')
    }

    require(params = [], doneCallback = null, errorCallback = null, alwaysCallback = null) {
        let builtParams = {}
        params.forEach((param) => {
            builtParams[param] = 1
        })
        this.get(
            '',
            builtParams,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }
}

export const prerequisiteService = () => new PrerequisiteService()
