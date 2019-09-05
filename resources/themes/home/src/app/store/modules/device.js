import {deviceService} from '../../services/default/device'
import {accountDeviceService} from '../../services/default/account_device'
import deviceCookieStore from '../../utils/cookie_store/device_cookie_store'

export default {
    namespaced: true,
    state: {
        device: null,
        failed: false,
    },
    getters: {
        device: state => state.device,
        existed: state => state.device != null,
        failed: state => state.failed,
    },
    mutations: {
        setDevice(state, {device}) {
            state.device = device

            deviceCookieStore.store(state.device)
        },
    },
    actions: {
        current({commit}, {device, isLoggedIn, doneCallback, errorCallback}) {
            (isLoggedIn ? accountDeviceService() : deviceService()).currentSave(
                device.provider,
                device.secret,
                (data) => {
                    commit('setDevice', {
                        device: data.device,
                    })
                    doneCallback(data.device)
                },
                errorCallback,
            )
        },

        fails({state}) {
            state.failed = true
        },
    },
}
