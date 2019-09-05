import Vue from 'vue'
import Server from './plugins/server'
import store from './app/store'
import {app} from './app/utils/app'
import {log} from './app/utils/log'
import {ui} from './app/utils/ui'

Vue.config.productionTip = true

ui.startPageLoading()

Vue.use(Server, {
    store,
    doneCallback: () => {
        app.create()
    },
    errorCallback: err => {
        log.write(err, 'main')

        app.create(false)
    }
})


