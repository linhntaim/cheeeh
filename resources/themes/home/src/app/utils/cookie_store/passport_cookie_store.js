import CookieStore from './cookie_store'
import {APP_COOKIE} from '../../../config'

class PassportCookieStore extends CookieStore {
    constructor() {
        super(APP_COOKIE.names.default)
    }

    convert(rawPassport) {
        return {
            accessToken: rawPassport.access_token,
            tokenType: rawPassport.token_type,
            refreshToken: rawPassport.refresh_token,
            tokenEndTime: (new Date).getTime() + rawPassport.expires_in * 1000,
        }
    }

    retrieve() {
        let passport = super.retrieve()
        if (passport) {
            return {
                accessToken: passport.access_token,
                tokenType: passport.token_type,
                refreshToken: passport.refresh_token,
                tokenEndTime: parseInt(passport.token_end_time),
            }
        }

        return {
            accessToken: null,
            tokenType: null,
            refreshToken: null,
            tokenEndTime: 0,
        }
    }

    store(passport) {
        return super.store({
            access_token: passport.accessToken,
            token_type: passport.tokenType,
            refresh_token: passport.refreshToken,
            token_end_time: passport.tokenEndTime,
        })
    }
}

export default new PassportCookieStore()
