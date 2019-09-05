const install = (Vue, {store, doneCallback, errorCallback}) => {
    store.dispatch('prerequisite/require', {
        names: ['server'],
        doneCallback: () => {
            Vue.prototype.$server = store.getters['prerequisite/metadata'].server

            doneCallback()
        },
        errorCallback
    })
}

export default install
