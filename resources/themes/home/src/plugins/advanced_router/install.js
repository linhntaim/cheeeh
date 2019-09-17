import Router from 'vue-router'

const install = (Vue, {router, session}) => {
    router.routePathByNames = {}

    const parseRoute = (route, parentPath = '') => {
        const routePath = '/' + (parentPath + '/' + route.path).replace(/\/+/g, '/').replace(/^\/|\/$/g, '')
        if (route.name) {
            router.routePathByNames[route.name] = routePath
        }
        route.children && route.children.length && route.children.forEach(childRoute => {
            parseRoute(childRoute, routePath)
        })
    }

    const parseRoutes = () => {
        router.routePathByNames = {}
        router.options.routes.forEach(route => {
            parseRoute(route)
        })
    }

    parseRoutes()

    router.getPathByName = function (name) {
        return this.routePathByNames.hasOwnProperty(name) ? this.routePathByNames[name] : null
    }

    router.replaceRoutes = function (routes, mode = 'history', base = null) {
        this.options.routes = routes
        this.matcher = (new Router({
            mode: mode,
            base: base ? base : process.env.BASE_URL,
            routes: routes,
        })).matcher

        parseRoutes()

        return this
    }

    router.softReplace = function (location, onComplete = null, onAbort = null) {
        session.skip()
        this.replace(location, onComplete, () => {
            onAbort && onAbort()
        })
    }

    router.catchSoftReplace = function (location, onComplete = null, onAbort = null) {
        session.skip()
        return this.replace(location, onComplete, onAbort)
    }
}

export default install
