import device from './modules/device'
import account from './modules/account'
import prerequisite from './modules/prerequisite'
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        prerequisite,
        device,
        account,
    },
})
