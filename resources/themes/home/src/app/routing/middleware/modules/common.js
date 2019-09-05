import {Middleware} from '../../../../plugins/middleware'
import {intervalCaller} from '../../../utils/interval_caller'
import {timeoutCaller} from '../../../utils/timeout_caller'
import {ui} from '../../../utils/ui'
import {session} from '../../../utils/session'
import {log} from '../../../utils/log'

class CommonMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('common', 'middleware')

        if ($middlewareManager.before) {
            session.start()
            timeoutCaller.clear()
            intervalCaller.clear()
            ui.startPageLoading()
            $middlewareManager.store.dispatch('account/anonymous')
        } else if ($middlewareManager.after) {
            ui.stopPageLoading()
            ui.scrollToTop()
        }

        super.handle($middlewareManager)
    }
}

export default new CommonMiddleware()
