<template lang="pug">
    .card.border-dark
        .card-body.text-center
            safe-image.mt-n4(:src="logoUrl")
            h1.font-weight-bold 404
            h4.font-weight-bold {{ $t('error.not_found._') }}
            .my-3 {{ $t('error.not_found.desc') }}
            router-link.btn.btn-dark(:to="{name: 'home'}")
                i.fas.fa-long-arrow-alt-left
                | &nbsp;&nbsp;{{ $t('error.back_to_root') }}
</template>

<script>
    import {APP_LOGO_URL} from '../../app/config'
    import SafeImage from '../components/SafeImage'

    export default {
        name: 'NotFound',
        components: {SafeImage},
        data() {
            return {
                logoUrl: APP_LOGO_URL.black_s128,
            }
        },
    }
</script>
