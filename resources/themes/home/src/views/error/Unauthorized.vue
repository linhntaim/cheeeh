<template lang="pug">
    .card
        .card-body.text-center
            h1 403
            h4 {{ $t('error.unauthorized._') }}
            .my-3 {{ $t('error.unauthorized.desc') }}
            div(:class="{'mb-5': enabled}")
                router-link(:class="{'btn btn-primary': !enabled, 'link-icon': enabled}" :to="{path: '/'}")
                    i.fas.fa-long-arrow-alt-left
                    | &nbsp;&nbsp;
                    span {{ $t('error.back_to_root') }}
            .mb-2.text-small.text-danger(v-if="enabled") {{ $t('error.clear_cache_help') }}
            clear-cache-button(:enabled="enabled")
</template>

<script>
    import ClearCacheButton from '../components/ClearCacheButton'

    export default {
        name: 'Unauthorized',
        components: {ClearCacheButton},
        data() {
            return {
                enabled: false,
            }
        },
        watch: {
            '$route'() {
                this.initUi()
            }
        },
        mounted() {
            this.initUi()
        },
        methods: {
            initUi() {
                if (this.$route.query.time) {
                    this.enabled = true
                }
            }
        }
    }
</script>
