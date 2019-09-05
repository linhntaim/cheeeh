import DefaultService from '../default_service'
import {
    APP_PASSPORT_PW_CLIENT_ID,
    APP_PASSPORT_PW_CLIENT_SECRET,
} from '../../../config'

class AuthService extends DefaultService {
    logout(doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'auth/logout',
            {},
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }

    refreshToken(refreshToken, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'oauth/token',
            {
                'grant_type': 'refresh_token',
                'client_id': APP_PASSPORT_PW_CLIENT_ID,
                'client_secret': APP_PASSPORT_PW_CLIENT_SECRET,
                'refresh_token': refreshToken,
                'scope': '*',
            },
            doneCallback,
            errorCallback,
            alwaysCallback
        )
    }
}

export const authService = () => new AuthService()
