import commonMiddleware from './modules/common'
import authMiddleware from './modules/auth'
import deviceMiddleware from './modules/device'
import emailVerifiedMiddleware from './modules/email_verified'
import serverMiddleware from './modules/server'
import localeMiddleware from './modules/locale'

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
        authMiddleware,
        deviceMiddleware,
        emailVerifiedMiddleware,
        serverMiddleware,
    ],
    after: [
        commonMiddleware,
    ],
}
