<template lang="pug">
    .card.border-dark
        .card-body.text-center
            safe-image.mt-n4(:src="logoUrl")
            h1.font-weight-bold 403
            h4.font-weight-bold {{ $t('error.unauthorized._') }}
            .my-3 {{ $t('error.unauthorized.desc') }}
            div(:class="{'mb-5': enabled}")
                router-link.btn.btn-dark(:to="{name: 'home'}")
                    i.fas.fa-long-arrow-alt-left
                    | &nbsp;&nbsp;
                    span {{ $t('error.back_to_root') }}
            .mb-2.text-small.text-danger(v-if="enabled") {{ $t('error.clear_cache_help') }}
            clear-cache-button(:enabled="enabled")
</template>

<script>
    import {APP_LOGO_URL} from '../../app/config'
    import ClearCacheButton from '../components/ClearCacheButton'
    import SafeImage from '../components/SafeImage'

    export default {
        name: 'Unauthorized',
        components: {SafeImage, ClearCacheButton},
        data() {
            return {
                logoUrl: APP_LOGO_URL.black_s128,
                enabled: false,
            }
        },
        watch: {
            '$route'() {
                this.initUi()
            },
        },
        mounted() {
            this.initUi()
        },
        methods: {
            initUi() {
                if (this.$route.query.time) {
                    this.enabled = true
                }
            },
        },
    }
</script>
