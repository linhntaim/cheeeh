import {prerequisiteService} from '../../services/default/prerequisite'
import {APP_PREREQUISITE_LIFETIME} from '../../../config'

export default {
    namespaced: true,
    state() {
        return {
            metadata: {},
            expired: {},
        }
    },
    getters: {
        metadata: state => state.metadata,
    },
    mutations: {
        setMetadata(state, {data}) {
            for (let name in data) {
                state.metadata[name] = data[name]
                state.expired[name] = (new Date()).getTime() + APP_PREREQUISITE_LIFETIME
            }
        },

        resetMetadata(state) {
            state.metadata = {}
            state.expired = {}
        },
    },
    actions: {
        reset({commit}) {
            commit('resetMetadata')
        },

        require({commit, state}, {names, doneCallback = null, errorCallback = null, alwaysCallback = null}) {
            let freshNames = []
            for (let i in names) {
                let name = names[i]
                if (!(state.metadata[name]
                    && state.expired[name]
                    && state.expired[name] <= ((new Date()).getTime() + APP_PREREQUISITE_LIFETIME))) {
                    freshNames.push(name)
                }
            }

            if (freshNames.length <= 0) {
                if (doneCallback) doneCallback()
                if (alwaysCallback) alwaysCallback()
                return
            }

            prerequisiteService().require(
                freshNames,
                (data) => {
                    commit('setMetadata', {
                        data: data,
                    })

                    if (doneCallback) doneCallback()
                },
                errorCallback,
                alwaysCallback,
            )
        },
    },
}
