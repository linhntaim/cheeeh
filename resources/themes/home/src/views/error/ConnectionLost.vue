<template lang="pug">
    header.masthead
        .container.d-flex.h-100.align-items-center
            .mx-auto.text-center
                h1.mx-auto.my-0.text-uppercase 777
                h2.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.connection_lost._') }}
                h3.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.connection_lost.desc') }}
                button.btn.btn-primary(@click="onRefreshClicked()")
                    | {{ $t('actions.refresh') }}
</template>

<script>
    import {ui} from '../../app/utils/ui'

    export default {
        name: 'ConnectionLost',
        methods: {
            onRefreshClicked() {
                ui.reloadPage()
            }
        }
    }
</script>
