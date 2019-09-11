import account from './modules/account'
import device from './modules/device'
import facebook from './modules/facebook'
import google from './modules/google'
import microsoft from './modules/microsoft'
import prerequisite from './modules/prerequisite'
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        account,
        device,
        facebook,
        google,
        microsoft,
        prerequisite,
    },
})
