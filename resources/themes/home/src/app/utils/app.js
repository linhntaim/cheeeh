import Vue from 'vue'
import VueHead from 'vue-head'
import VueCookie from 'vue-cookie'
import EventBus from '../../plugins/event_bus'
import Middleware from '../../plugins/middleware'
import SoftReplaceRouter from '../../plugins/soft_replace_router'
import {session} from './session'
import {log} from './log'
import router from '../routing/router'
import store from '../store'
import App from '../../views/App'
import AppFailed from '../../views/AppFailed'
import localeReady from '../../app/locales'

export class Application {
    constructor() {
        this.instance = null
    }

    get() {
        return new Promise((resolve, reject) => {
            let tried = 0, instance = () => {
                if (this.instance) {
                    resolve(this.instance)
                    return
                }

                if (++tried > 10) {
                    reject()
                    return
                }

                setTimeout(() => {
                    instance()
                }, 200)
            }

            instance()
        })
    }

    createDefault() {
        localeReady(i18n => {
            this.instance = new Vue({
                i18n,
                router,
                store,
                render: h => h(AppFailed)
            }).$mount('#app')
        })
    }

    create(alright = true) {
        if (!alright) return this.createDefault()

        Vue.use(VueHead)
        Vue.use(VueCookie)
        Vue.use(EventBus)
        Vue.use(SoftReplaceRouter, {router, session})
        Vue.use(Middleware, {
            router,
            store,
            beforeEnable: () => {
                return !session.skipping()
            },
            afterEnable: () => {
                return !session.skipping()
            },
            beforeRouting: () => {
                log.write('start', 'routing')
            },
            afterRouting: () => {
                if (session.skipping()) session.abortSkipping()
                log.write('end', 'routing')
            },
        })

        localeReady(i18n => {
            this.instance = new Vue({
                i18n,
                router,
                store,
                render: h => h(App)
            }).$mount('#app')
        })
    }
}

export const app = new Application()
