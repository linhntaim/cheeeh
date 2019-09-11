import {microsoftAuthenticationService} from '../../services/microsoft/microsoft_authentication_service'

export default {
    namespaced: true,
    state: {
        isLoggedIn: false,
        me: null,
    },
    getters: {
        isLoggedIn: state => state.isLoggedIn,
        me: state => state.me,
    },
    mutations: {
        setMe(state, {me}) {
            state.isLoggedIn = true
            state.me = me
        },
        unsetMe(state) {
            state.isLoggedIn = false
            state.me = null
        },
    },
    actions: {
        login({state, commit}, {doneCallback, errorCallback}) {
            if (state.isLoggedIn) {
                doneCallback()
                return
            }

            microsoftAuthenticationService.login((account) => {
                commit('setMe', {
                    me: {
                        id: account.accountIdentifier,
                        name: account.name,
                        email: account.userName,
                    }
                })
                doneCallback()
            }, errorCallback)
        },

        logout({state, commit}, {doneCallback}) {
            let done = () => {
                commit('unsetMe')
                doneCallback()
            }

            if (!state.isLoggedIn) {
                done()
                return
            }

            microsoftAuthenticationService.logout(done)
        }
    },
}
