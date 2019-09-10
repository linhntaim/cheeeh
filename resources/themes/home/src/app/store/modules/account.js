import {authService} from '../../services/default/auth'
import {accountService} from '../../services/default/account'
import {callbackWaiter} from '../../utils/callback_waiter'
import {localizer} from '../../utils/localizer'
import {log} from '../../utils/log'
import {numberFormatHelper} from '../../utils/number_format_helper'
import {serviceFactory} from '../../services/service_factory'
import {APP_DEFAULT_SERVICE, DEFAULT_LOCALIZATION} from '../../config'
import helpers from '../../utils/helpers'
import localizationCookieStore from '../../utils/cookie_store/localization_cookie_store'
import passportCookieStore from '../../utils/cookie_store/passport_cookie_store'

const setDefaultServiceLocalizationHeader = localization => {
    serviceFactory.modify(defaultService => {
        defaultService.instance.defaults.headers.common[APP_DEFAULT_SERVICE.headers.localization] = JSON.stringify(helpers.object.only(
            [
                '_from_app',
                '_ts',
                'locale',
                'country',
                'timezone',
                'currency',
                'number_format',
                'first_day_of_week',
                'long_date_format',
                'long_time_format',
                'short_date_format',
                'short_time_format',
            ],
            localization,
        ))
    })
}

export default {
    namespaced: true,
    state: {
        isLoggedIn: false,
        passport: {
            accessToken: null,
            tokenType: null,
            refreshToken: null,
            tokenEndTime: 0,
        },
        user: null,
    },
    getters: {
        isLoggedIn: state => state.isLoggedIn,
        user: state => state.user,
        roles: state => state.user ? state.user.role_names : null,
        permissions: state => state.user && state.user.permission_names ? state.user.permission_names : [],
        passport: state => {
            return {
                accessToken: state.passport.accessToken,
                tokenType: state.passport.tokenType,
                refreshToken: state.passport.refreshToken,
                tokenEndTime: state.passport.tokenEndTime,
            }
        },
    },
    mutations: {
        setAuth(state, {accessToken, tokenType, refreshToken, tokenEndTime}) {
            state.isLoggedIn = true
            state.passport = {
                accessToken: accessToken,
                tokenType: tokenType,
                refreshToken: refreshToken,
                tokenEndTime: tokenEndTime,
            }

            let authorizationHeader = tokenType + ' ' + accessToken
            serviceFactory.modify(defaultService => {
                defaultService.instance.defaults.headers.common[APP_DEFAULT_SERVICE.headers.token_authorization] = authorizationHeader
            })

            passportCookieStore.store(state.passport)
        },

        unsetAuth(state) {
            state.isLoggedIn = false
            state.passport = {
                accessToken: null,
                tokenType: null,
                refreshToken: null,
                tokenEndTime: 0,
            }

            serviceFactory.modify(defaultService => {
                defaultService.instance.defaults.headers.common[APP_DEFAULT_SERVICE.headers.token_authorization] = null
            })

            passportCookieStore.remove()
        },

        setUser(state, {user, localeCallback}) {
            state.user = user

            setDefaultServiceLocalizationHeader(state.user.localization)
            localizationCookieStore.store(state.user.localization)
            localizer.localize(state.user.localization).then(() => {
                localeCallback && localeCallback()
            })
            numberFormatHelper.localize(state.user.localization)
        },

        setLocale(state, {locale, callback}) {
            if (locale != state.user.localization.locale) {
                state.user.localization._ts = 0
            }
            state.user.localization.locale = locale

            setDefaultServiceLocalizationHeader(state.user.localization)
            localizationCookieStore.store(state.user.localization)
            localizer.localize(state.user.localization).then(() => {
                callback && callback()
            })
        },

        setLocalization(state, {localization, localeCallback}) {
            for (let key in localization) {
                if (key == 'locale' && localization.locale != state.user.localization.locale) {
                    state.user.localization._ts = 0
                }
                state.user.localization[key] = localization[key]
            }

            setDefaultServiceLocalizationHeader(state.user.localization)
            localizationCookieStore.store(state.user.localization)
            localizer.localize(state.user.localization).then(() => {
                localeCallback && localeCallback()
            })
            numberFormatHelper.localize(state.user.localization)
        },

        unsetUser(state) {
            if (state.user && state.user.localization) {
                state.user = {
                    localization: state.user.localization,
                }
            } else {
                let storedLocalization = localizationCookieStore.retrieve()
                state.user = {
                    localization: storedLocalization ? storedLocalization : DEFAULT_LOCALIZATION,
                }
            }

            state.user.localization._from_app = true
            state.user.localization._ts = 0
            setDefaultServiceLocalizationHeader(state.user.localization)
            localizationCookieStore.store(state.user.localization)

            callbackWaiter.remove('account_current')
        },
    },
    actions: {
        storePassport({state}) {
            passportCookieStore.store(state.passport)
        },

        anonymous({commit, state}, {callback}) {
            if (state.isLoggedIn) return

            let storedLocalization = localizationCookieStore.retrieve()
            commit('setUser', {
                user: {
                    localization: storedLocalization ? storedLocalization : DEFAULT_LOCALIZATION,
                },
                localeCallback: callback,
            })
        },

        reload({dispatch}, {doneCallback, errorCallback}) {
            callbackWaiter.remove('account_current')
            dispatch('current', {
                doneCallback: doneCallback,
                errorCallback: errorCallback,
            })
        },

        current({commit, state}, {login, doneCallback, errorCallback}) {
            if (!state.user || !state.user.id || login) {
                callbackWaiter.remove('account_current')
            }
            callbackWaiter.call('account_current', () => { // tricky cache
                log.write('get account', 'store')

                accountService().current(login, (data) => {
                    commit('setUser', {
                        user: data.user,
                        localeCallback: doneCallback,
                    })
                }, errorCallback)
            }, 10, () => {
                doneCallback({user: state.user})
            })
        },

        updateLocalization({commit, state}, {params, doneCallback, errorCallback}) {
            if (state.isLoggedIn) {
                accountService().updateLocalization(params, (data) => {
                    commit('setUser', {
                        user: data.user,
                        localeCallback: doneCallback,
                    })
                }, errorCallback)
            } else {
                commit('setLocalization', {
                    localization: params,
                    localeCallback: doneCallback,
                })
            }
        },

        updateLocale({commit, state}, {locale, doneCallback, errorCallback}) {
            if (state.isLoggedIn) {
                accountService().updateLocale(locale, (data) => {
                    commit('setUser', {
                        user: data.user,
                        localeCallback: doneCallback,
                    })
                }, errorCallback)
            } else {
                commit('setLocale', {
                    locale: locale,
                    callback: doneCallback,
                })
            }
        },

        refreshToken({commit}, {refreshToken, doneCallback, errorCallback}) {
            authService().refreshToken(refreshToken, (data) => {
                commit('setAuth', passportCookieStore.convert(data))
                doneCallback()
            }, errorCallback)
        },

        logout({commit}, {alwaysCallback}) {
            authService().logout(null, null, () => {
                commit('unsetAuth')
                commit('unsetUser')

                alwaysCallback()
            })
        },

        login({commit, dispatch}, {email, password, token, doneCallback, errorCallback}) {
            let done = (data) => {
                commit('setAuth', passportCookieStore.convert(data))
                dispatch('current', {
                    login: true,
                    doneCallback: doneCallback,
                    errorCallback: errorCallback,
                })
            }
            if (token) {
                authService().loginWithToken(email, done, errorCallback)
            } else {
                authService().login(email, password, done, errorCallback)
            }
        },

        loginWithFacebook({commit, dispatch}, {id, doneCallback, errorCallback}) {
            authService().loginWithFacebook(id, (data) => {
                commit('setAuth', passportCookieStore.convert(data))
                dispatch('current', {
                    doneCallback: doneCallback,
                    errorCallback: errorCallback,
                })
            }, errorCallback)
        },

        loginWithGoogle({commit, dispatch}, {id, doneCallback, errorCallback}) {
            authService().loginWithGoogle(id, (data) => {
                commit('setAuth', passportCookieStore.convert(data))
                dispatch('current', {
                    doneCallback: doneCallback,
                    errorCallback: errorCallback,
                })
            }, errorCallback)
        },

        loginWithMicrosoft({commit, dispatch}, {id, doneCallback, errorCallback}) {
            authService().loginWithMicrosoft(id, (data) => {
                commit('setAuth', passportCookieStore.convert(data))
                dispatch('current', {
                    doneCallback: doneCallback,
                    errorCallback: errorCallback,
                })
            }, errorCallback)
        },

        register({dispatch}, {
            displayName, email, password, passwordConfirmation, urlAvatar,
            provider, providerId,
            doneCallback, errorCallback,
        }) {
            authService().register({
                display_name: displayName,
                email: email,
                password: password,
                password_confirmation: passwordConfirmation,
                url_avatar: urlAvatar,
                provider: provider,
                provider_id: providerId,
            }, () => {
                dispatch('login', {
                    email: email,
                    password: password,
                    doneCallback: doneCallback,
                    errorCallback: errorCallback,
                })
            }, errorCallback)
        },

        forgotPassword(store, {email, doneCallback, errorCallback}) {
            authService().forgotPassword(email, doneCallback, errorCallback)
        },

        getResetPassword(store, {email, token, doneCallback, errorCallback}) {
            authService().getResetPassword({
                email: email,
                token: token,
            }, doneCallback, errorCallback)
        },

        resetPassword(store, {email, token, password, passwordConfirmation, doneCallback, errorCallback}) {
            authService().resetPassword({
                email: email,
                token: token,
                password: password,
                password_confirmation: passwordConfirmation,
            }, doneCallback, errorCallback)
        },
    },
}
