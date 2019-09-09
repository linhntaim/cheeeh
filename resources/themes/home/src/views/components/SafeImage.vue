<template lang="pug">
    img(v-if="source" :src="source" @error="onError")
</template>

<script>
    import {log} from '../../app/utils/log'

    export default {
        name: 'SafeImage',
        props: {
            src: {
                type: String,
                default: null,
            },
        },
        data() {
            return {
                source: this.src,
            }
        },
        methods: {
            onError() {
                log.write('failed', 'safe_image')

                this.source = null
            },
        },
    }
</script>
