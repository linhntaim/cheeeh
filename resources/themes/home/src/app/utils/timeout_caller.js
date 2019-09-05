export class TimeoutCaller {
    constructor() {
        this.calls = []
    }

    register(handler, timeout = 200) {
        let s = setTimeout(handler, timeout)
        this.calls.push(s)
        return s
    }

    clear(s = null) {
        if (s) {
            clearTimeout(s)

            let i = this.calls.indexOf(s)
            if (i !== -1) this.calls.splice(i, 1)
            return
        }

        while (this.calls.length > 0) {
            clearTimeout(this.calls.shift())
        }
    }
}

export const timeoutCaller = new TimeoutCaller()
