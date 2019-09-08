<template lang="pug">
    .card.border-dark.pb-4
        .card-body.text-center
            img(:src="logoUrl")
            h1.font-weight-bold 777
            h4.font-weight-bold {{ $t('error.connection_lost._') }}
            .my-3 {{ $t('error.connection_lost.desc') }}
            button.btn.btn-dark(@click="onRefreshClicked()")
                | {{ $t('actions.refresh') }}
</template>

<script>
    import {ui} from '../../app/utils/ui'
    import {APP_LOGO_URL} from '../../config'

    export default {
        name: 'ConnectionLost',
        data() {
            return {
                logoUrl: APP_LOGO_URL.black_s128,
            }
        },
        methods: {
            onRefreshClicked() {
                ui.reloadPage()
            }
        }
    }
</script>
