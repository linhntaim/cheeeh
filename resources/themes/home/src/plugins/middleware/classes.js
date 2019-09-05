class MiddlewareManager {
    constructor(groups, store) {
        this.groups = groups
        this.store = store
        this.index = -1
    }

    run() {
        return this
    }

    handle() {
        return this
    }

    skip() {
        this.index = this.groups.length
        return this
    }
}

export class BeforeMiddlewareManager extends MiddlewareManager {
    constructor(groups, store) {
        super(groups, store)
        this.before = true
    }

    run(to, from, next) {
        this.to = to
        this.from = from
        this.next = next
        this.index = -1

        return this.handle()
    }

    handle() {
        if (this.groups.length === 0) {
            this.next()
            return
        }
        ++this.index
        if (this.index == this.groups.length) {
            this.next()
            return
        }
        this.groups[this.index].handle(this)

        return super.handle()
    }
}

export class AfterMiddlewareManager extends MiddlewareManager {
    constructor(groups, store) {
        super(groups, store)
        this.after = true
    }

    run(to, from) {
        this.to = to
        this.from = from
        this.index = -1

        return this.handle()
    }

    handle() {
        if (this.groups.length === 0) {
            return
        }
        ++this.index
        if (this.index == this.groups.length) {
            return
        }
        this.groups[this.index].handle(this)

        return super.handle()
    }
}

export class Middleware {
    handle($middlewareManager) {
        $middlewareManager.handle()
    }

    redirect($middlewareManager, path) {
        $middlewareManager.next({
            path: path,
            query: {
                time: new Date().getTime(),
            }
        })
    }
}

