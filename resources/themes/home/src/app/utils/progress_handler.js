import {ui} from './ui'

export default class ProgressHandler {
    constructor() {
        this.reset()
    }

    reset() {
        this.limit = 0
        this.current = 0
        this.percentage = 0
        this.percentageText = '0%'
        this.completed = true
    }

    increaseLimit(value) {
        if (value) {
            this.limit += value
            this.updateState()
        }

        return this
    }

    increaseCurrent(value) {
        if (value) {
            this.current += value
            this.updateState()
        }

        return this
    }

    run(executor, value = 1, increasedLimitValue = 0) {
        increasedLimitValue && this.increaseLimit(increasedLimitValue)

        new Promise(executor).then(() => {
            this.increaseCurrent(value)
        }).catch(() => {
            this.increaseLimit(-value)
        })

        return this
    }

    updatePercentage() {
        this.percentage = this.limit === 0 ? 0 : Math.round(this.current / this.limit * 100)
        return this
    }

    updatePercentageText() {
        this.percentageText = this.percentage + '%'
        return this
    }

    updateCompleted() {
        this.completed = this.current === this.limit
        return this
    }

    updateState() {
        ui.waitRendering(() => {
            this.updatePercentage()
                .updatePercentageText()
                .updateCompleted()
        })
        return this
    }
}
