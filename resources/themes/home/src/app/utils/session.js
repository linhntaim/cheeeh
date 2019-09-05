export class Session {
    constructor() {
        this.accessTime = 0
        this.nextAccessTime = 1

        this.data = {}
    }

    start() {
        this.nextAccessTime = ++this.accessTime + 1
    }

    skip() {
        this.nextAccessTime = this.accessTime
    }

    restart() {
        this.accessTime = 0
        this.nextAccessTime = 1
    }

    skipping() {
        return this.nextAccessTime <= this.accessTime
    }

    abortSkipping() {
        this.nextAccessTime = this.accessTime + 1
    }

    isFresh() {
        return this.accessTime === 1
    }

    isNotFresh() {
        return this.accessTime > 1
    }

    store(key, value, flash = false) {
        this.data[key] = {
            value: value,
            flash: flash,
        }
    }

    retrieve(key) {
        if (!this.data.hasOwnProperty(key)) return null

        let data = this.data[key]
        if (data.flash) {
            delete this.data[key]
        }
        return data.value
    }
}

export const session = new Session()
