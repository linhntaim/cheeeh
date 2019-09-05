import {Middleware} from '../../../../plugins/middleware'
import {log} from '../../../utils/log'
import {app} from '../../../utils/app'
import {serverClock} from '../../../utils/server_clock'

class ServerMiddleware extends Middleware {
    handle($middlewareManager) {
        log.write('server', 'middleware')

        app.get().then(appInstance => {
            let server = appInstance.$server

            serverClock.setClock(server.c)

            super.handle($middlewareManager)
        })
    }
}

export default new ServerMiddleware()
