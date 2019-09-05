const install = (Vue, {router, session}) => {
    router.softReplace = function (location, onComplete = null, onAbort = null) {
        session.skip()
        this.replace(location, onComplete, onAbort)
    }
}

export default install
