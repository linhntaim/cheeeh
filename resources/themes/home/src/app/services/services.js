import {APP_DEFAULT_SERVICE, APP_NAME, APP_URL} from '../../config'
import axios from 'axios'

export const defaultService = 'axios'

export const services = {
    axios: {
        instance: axios.create({
            baseURL: APP_DEFAULT_SERVICE.base_url,
            headers: (() => {
                const headers = {}
                headers[APP_DEFAULT_SERVICE.headers.application] = JSON.stringify({
                    name: APP_NAME,
                    url: APP_URL,
                })
                if (APP_DEFAULT_SERVICE.basic_auth) {
                    headers[APP_DEFAULT_SERVICE.headers.basic_authorization] = 'Basic ' + btoa(APP_DEFAULT_SERVICE.basic_auth)
                }
                return headers
            })(),
        }),
        instanceCallback: null,
        paramsCallback: {},
    },
}
