<template lang="pug">
    .card
        .card-body.text-center
            h1 777
            h4.font-weight-bold {{ $t('error.connection_lost._') }}
            .my-3 {{ $t('error.connection_lost.desc') }}
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
