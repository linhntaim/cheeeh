export class IntervalCaller {
    constructor() {
        this.calls = []
    }

    register(handler, timeout = 1000) {
        let s = setInterval(handler, timeout)
        this.calls.push(s)
        return s
    }

    clear(s = null) {
        if (s) {
            clearInterval(s)

            let i = this.calls.indexOf(s)
            if (i !== -1) this.calls.splice(i, 1)
            return
        }

        while (this.calls.length > 0) {
            clearInterval(this.calls.shift())
        }
    }
}

export const intervalCaller = new IntervalCaller()
