import {app} from './app/utils/app'
import {log} from './app/utils/log'
import store from './app/store'
import Server from './plugins/server'
import Vue from 'vue'

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


