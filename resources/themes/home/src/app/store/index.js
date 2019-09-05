import Vue from 'vue'
import Vuex from 'vuex'
import prerequisite from './modules/prerequisite'
import device from './modules/device'
import account from './modules/account'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        prerequisite,
        device,
        account,
    }
})
