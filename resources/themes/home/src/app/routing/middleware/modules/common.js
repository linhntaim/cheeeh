import {app} from '../../../utils/app'
import {intervalCaller} from '../../../utils/interval_caller'
import {log} from '../../../utils/log'
import {session} from '../../../utils/session'
import {timeoutCaller} from '../../../utils/timeout_caller'
import {ui} from '../../../utils/ui'
import {Middleware} from '../../../../plugins/middleware'

class CommonMiddleware extends Middleware {
    constructor() {
        super()

        this.initializing = true
    }

    handle($middlewareManager) {
        log.write('common', 'middleware')

        app.get().then(appInstance => {
            if ($middlewareManager.before) {
                if (this.initializing) {
                    ui.removeInitializing()
                }
                appInstance.$bus.emit('page.loading')
                session.start()
                timeoutCaller.clear()
                intervalCaller.clear()
                $middlewareManager.store.dispatch('account/anonymous', {
                    callback: () => {
                        super.handle($middlewareManager)
                    },
                })
            } else if ($middlewareManager.after) {
                appInstance.$bus.emit('page.loaded')
                ui.scrollToTop()

                super.handle($middlewareManager)
            }
        })
    }
}

export default new CommonMiddleware()
