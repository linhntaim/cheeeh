<template lang="pug">
    header.masthead
        .container.d-flex.h-100.align-items-center
            .mx-auto.text-center
                h1.mx-auto.my-0.text-uppercase 401
                h2.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.unauthenticated._') }}
                h3.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.unauthenticated.desc') }}
                div(:class="{'mb-5': enabled}")
                    router-link(:to="{path: '/'}") ← {{ $t('actions.go', {where: $t('pages._auth._login._')}) }}
                clear-cache-button(:enabled="enabled")
</template>

<script>
    import ClearCacheButton from '../components/ClearCacheButton'

    export default {
        name: 'Unauthenticated',
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
