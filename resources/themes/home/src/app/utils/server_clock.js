import {CLOCK_BLOCK_KEYS, CLOCK_BLOCK_RANGE} from '../config'

class ServerClock {
    setClock(clock) {
        this.d = this.localClock() - clock
    }

    localClock() {
        return Math.floor((new Date()).getTime() / 1000)
    }

    clock() {
        return this.localClock() - this.d
    }

    block(secondRange = CLOCK_BLOCK_RANGE) {
        return Math.floor(this.clock() / secondRange)
    }

    blockKey(callback = null, secondRange = CLOCK_BLOCK_RANGE) {
        let key = CLOCK_BLOCK_KEYS[this.block(secondRange) % CLOCK_BLOCK_KEYS.length]
        return callback ? callback(key) : key
    }
}

export const serverClock = new ServerClock()
