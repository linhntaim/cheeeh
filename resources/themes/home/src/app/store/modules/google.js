import {googleService} from '../../services/google_service'

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

            googleService.login((user) => {
                let basicProfile = user.getBasicProfile()
                commit('setMe', {
                    me: {
                        id: user.getId(),
                        basicProfile: {
                            id: basicProfile.getId(),
                            name: basicProfile.getName(),
                            givenName: basicProfile.getGivenName(),
                            familyName: basicProfile.getFamilyName(),
                            imageUrl: basicProfile.getImageUrl().replace('s96-c', 's512-c'),
                            email: basicProfile.getEmail(),
                        },
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

            googleService.logout(done)
        }
    },
}
