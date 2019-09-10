<template lang="pug">
    .alert(v-if="error" :class="alertHtmlClass")
        div(v-for="message in messages" v-html="message")
</template>

<script>
    import {ERROR_LEVEL_DEF} from '../../app/config'

    export default {
        name: 'ErrorBox',
        props: {
            error: Object,
        },
        data() {
            let alertHtmlClasses = {}
            alertHtmlClasses[ERROR_LEVEL_DEF.info] = 'alert-primary'
            alertHtmlClasses[ERROR_LEVEL_DEF.warning] = 'alert-warning'
            alertHtmlClasses[ERROR_LEVEL_DEF.error] = 'alert-danger'
            return {
                alertHtmlClasses: alertHtmlClasses,
            }
        },
        computed: {
            messages() {
                return this.error ? this.error.messages : []
            },
            level() {
                return this.error ? this.error.level : ERROR_LEVEL_DEF.info
            },
            extra() {
                return this.error ? this.error.extra : null
            },
            alertHtmlClass() {
                const level = this.level
                return this.alertHtmlClasses.hasOwnProperty(level) ? this.alertHtmlClasses[level] : ''
            },
        },
    }
</script>
