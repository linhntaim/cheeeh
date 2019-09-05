<template lang="pug">
    #app
        connection-lost
</template>

<script>
    import ConnectionLost from './error/ConnectionLost'
    import {ui} from '../app/utils/ui'

    export default {
        name: 'AppFailed',
        components: {ConnectionLost},
        mounted() {
            ui.stopPageLoading()
        },
    }
</script>
