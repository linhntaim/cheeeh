<template lang="pug">
    #app
        .container.pt-5
            connection-lost
</template>

<script>
    import ConnectionLost from './error/ConnectionLost'
    import {ui} from '../app/utils/ui'

    export default {
        name: 'AppFailed',
        components: {ConnectionLost},
        mounted() {
            ui.removeInitializing()
        },
    }
</script>
