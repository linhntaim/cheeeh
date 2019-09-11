import {facebookService} from '../../services/facebook_service'

export default {
    namespaced: true,
    state: {
        isLoggedIn: false,
        auth: null,
        me: null,
    },
    getters: {
        isLoggedIn: state => state.isLoggedIn,
        me: state => state.me,
    },
    mutations: {
        setAuth(state, {auth}) {
            state.isLoggedIn = true
            state.auth = auth
        },
        unsetAuth(state) {
            state.isLoggedIn = false
            state.auth = null
        },
        setMe(state, {me}) {
            state.me = me
        },
        unsetMe(state) {
            state.me = null
        },
    },
    actions: {
        login({state, commit}, {doneCallback, errorCallback}) {
            if (state.isLoggedIn) {
                doneCallback()
                return
            }

            let meCallback = (auth) => {
                commit('setAuth', {auth})
                facebookService.me((me) => {
                    commit('setMe', {me})
                    doneCallback()
                })
            }
            let notConnectedCallback = () => {
                facebookService.login(meCallback, errorCallback)
            }
            facebookService.getLoginStatus(meCallback, notConnectedCallback, notConnectedCallback)
        },

        logout({state, commit}, {doneCallback}) {
            let done = () => {
                commit('unsetMe')
                commit('unsetAuth')
                doneCallback()
            }

            if (!state.isLoggedIn) {
                done()
                return
            }

            facebookService.logout(done)
        }
    },
}
