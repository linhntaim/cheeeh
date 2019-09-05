import DefaultService from '../default_service'

export class AccountService extends DefaultService {
    constructor() {
        super('account')
    }

    current(login = false, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.get(
            '',
            login ? {_login: 1} : {},
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }

    update(params, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.put(
            '',
            params,
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }

    updateLocalization(params, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        params._localization = 1
        this.update(
            params,
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }

    updateLocale(locale, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.update(
            {
                _locale: 1,
                locale: locale,
            },
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }
}

export const accountService = () => new AccountService()
