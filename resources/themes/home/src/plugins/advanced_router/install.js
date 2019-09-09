import Router from 'vue-router'

const install = (Vue, {router, session}) => {
    router.replaceRoutes = function (routes, mode = 'history', base = null) {
        this.options.routes = routes
        this.matcher = (new Router({
            mode: mode,
            base: base ? base : process.env.BASE_URL,
            routes: routes,
        })).matcher

        return this
    }

    router.softReplace = function (location, onComplete = null, onAbort = null) {
        session.skip()
        this.replace(location, onComplete, onAbort)
    }
}

export default install
