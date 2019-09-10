<template lang="pug">
    .card.border-dark
        .card-body.text-center
            safe-image.mt-n4(:src="logoUrl")
            h1.font-weight-bold 777
            h4.font-weight-bold {{ $t('error.connection_lost._') }}
            .my-3 {{ $t('error.connection_lost.desc') }}
            button.btn.btn-dark(@click="onRefreshClicked()")
                | {{ $t('actions.refresh') }}
</template>

<script>
    import {APP_LOGO_URL} from '../../app/config'
    import {ui} from '../../app/utils/ui'
    import SafeImage from '../components/SafeImage'

    export default {
        name: 'ConnectionLost',
        components: {SafeImage},
        data() {
            return {
                logoUrl: APP_LOGO_URL.black_s128,
            }
        },
        methods: {
            onRefreshClicked() {
                ui.reloadPage()
            },
        },
    }
</script>
