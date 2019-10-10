import authMiddleware from './modules/auth'
import commonMiddleware from './modules/common'
import deviceMiddleware from './modules/device'
import emailVerifiedMiddleware from './modules/email_verified'
import localeMiddleware from './modules/locale'
import serverMiddleware from './modules/server'

export const common = {
    before: [
        commonMiddleware,
        localeMiddleware,
    ],
    after: [
        commonMiddleware,
    ],
}

export const all = {
    before: [
        commonMiddleware,
        localeMiddleware,
        serverMiddleware,
        authMiddleware,
        deviceMiddleware,
        emailVerifiedMiddleware,
    ],
    after: [
        commonMiddleware,
    ],
}
