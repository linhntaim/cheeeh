import commonMiddleware from './modules/common'
import authMiddleware from './modules/auth'
import deviceMiddleware from './modules/device'
import serverMiddleware from './modules/server'
import localeMiddleware from './modules/locale'

export const common = {
    before: [
        commonMiddleware,
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
        serverMiddleware,
    ],
    after: [
        commonMiddleware,
    ],
}
