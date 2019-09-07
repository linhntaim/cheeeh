import helpers from './helpers'

export default {
    isHome($route) {
        return helpers.string.trim($route.path, '/') === ''
    },

    isLogin($route) {
        return helpers.string.trim($route.path, '/') === 'auth/login'
    },

    isRegister($route) {
        return helpers.string.trim($route.path, '/') === 'auth/register'
    },
}
