import Vue from 'vue'
import Server from './plugins/server'
import store from './app/store'
import {app} from './app/utils/app'
import {log} from './app/utils/log'

Vue.config.productionTip = true

Vue.use(Server, {
    store,
    doneCallback: () => {
        app.create()
    },
    errorCallback: err => {
        log.write(err, 'main')

        app.create(false)
    },
})


