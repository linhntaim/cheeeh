import {APP_DEFAULT_SERVICE, APP_ROUTE} from '../../config'
import {crypto} from '../../utils/crypto'
import {serverClock} from '../../utils/server_clock'
import DefaultService from '../default_service'

class AuthService extends DefaultService {
    logout(doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'auth/logout',
            {},
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    refreshToken(refreshToken, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'oauth/token',
            {
                'grant_type': 'refresh_token',
                'client_id': APP_DEFAULT_SERVICE.client_id,
                'client_secret': APP_DEFAULT_SERVICE.client_secret,
                'refresh_token': refreshToken,
                'scope': '*',
            },
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    login(email, password, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.post(
            'auth/login',
            {
                grant_type: 'password',
                client_id: APP_DEFAULT_SERVICE.client_id,
                client_secret: APP_DEFAULT_SERVICE.client_secret,
                username: email,
                password: password,
                scope: '*',
            },
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    loginWithToken(email, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.e()
        this.login(
            crypto.encrypt(email, serverClock.blockKey()),
            crypto.encryptJson({source: 'token'}, serverClock.blockKey()),
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    loginSocially(provider, providerId, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.e()
        this.login(
            crypto.encryptJson({provider: provider, provider_id: providerId}, serverClock.blockKey()),
            crypto.encryptJson({source: 'social'}, serverClock.blockKey()),
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    loginWithFacebook(providerId, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.loginSocially(
            'facebook',
            providerId,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    loginWithGoogle(providerId, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.loginSocially(
            'google',
            providerId,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    loginWithMicrosoft(providerId, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        this.loginSocially(
            'microsoft',
            providerId,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }

    register(params, doneCallback = null, errorCallback = null, alwaysCallback = null) {
        params.app_verify_email_path = APP_ROUTE.verify_email
        this.post(
            'auth/register',
            params,
            doneCallback,
            errorCallback,
            alwaysCallback,
        )
    }
}

export const authService = () => new AuthService()
