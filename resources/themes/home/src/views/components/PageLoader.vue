<template lang="pug">
    .page-popping.dark(v-if="enabled")
        i.fas.fa-circle-notch.fa-spin
</template>

<script>
    import {$ui} from '../../app/utils/ui'
    import {log} from '../../app/utils/log'

    export default {
        name: 'PageLoader',
        data() {
            return {
                enabled: false,
            }
        },
        mounted() {
            const $body = $ui('body')
            this.$bus.on('page.loading', () => {
                log.write('loading', 'page')

                this.enabled = true
                $body.addClass('has-page-popping')
            })
            this.$bus.on('page.loaded', () => {
                log.write('loaded', 'page')

                this.enabled = false
                $body.removeClass('has-page-popping')
            })
        },
    }
</script>
