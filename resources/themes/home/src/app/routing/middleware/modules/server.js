import {app} from '../../../utils/app'
import {log} from '../../../utils/log'
import {serverClock} from '../../../utils/server_clock'
import {Middleware} from '../../../../plugins/middleware'

class ServerMiddleware extends Middleware {
    constructor() {
        super()

        this.server = {}
    }

    handle($middlewareManager) {
        log.write('server', 'middleware')

        app.get().then(appInstance => {
            this.server = appInstance.$server
            this.handleClock()
            this.handleOthers()
            super.handle($middlewareManager)
        })
    }

    handleClock() {
        serverClock.setClock(this.server.c)
    }

    handleOthers() {
        // TODO: Handle others by server configuration
    }
}

export default new ServerMiddleware()
