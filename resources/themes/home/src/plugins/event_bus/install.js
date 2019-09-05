import {EventBus} from './classes'

const install = (Vue) => {
    Vue.prototype.$bus = new EventBus()
}

export default install
