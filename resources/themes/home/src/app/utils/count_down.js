import {intervalCaller} from './interval_caller'

export class CountDown {
    start(counter, callback, step = 1) {
        this.counter = counter
        this.realCounter = counter
        this.enabled = true
        let i = intervalCaller.register(() => {
            this.realCounter -= step
            this.counter = this.realCounter > 0 ? this.realCounter : 1
            if (this.realCounter <= 0) {
                callback()
                intervalCaller.clear(i)
            }
        }, step * 1000)
    }
}
