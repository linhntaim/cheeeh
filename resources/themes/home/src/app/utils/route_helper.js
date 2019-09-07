import helpers from './helpers'

export default {
    isLogin($route) {
        return helpers.string.trim($route.path, '/') === 'auth/login'
    },

    isRegister($route) {
        return helpers.string.trim($route.path, '/') === 'auth/register'
    },
}
