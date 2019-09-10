<template lang="pug">
    .card.border-dark
        .card-body.text-center
            safe-image.mt-n4(:src="logoUrl")
            h1.font-weight-bold 400
            h4.font-weight-bold {{ $t('error.bad_request._') }}
            .my-3 {{ $t('error.bad_request.desc') }}
            router-link.btn.btn-dark(:to="{name: 'home'}")
                i.fas.fa-long-arrow-alt-left
                | &nbsp;&nbsp;{{ $t('error.back_to_root') }}
</template>

<script>
    import {APP_LOGO_URL} from '../../app/config'
    import SafeImage from '../components/SafeImage'

    export default {
        name: 'BadRequest',
        components: {SafeImage},
        data() {
            return {
                logoUrl: APP_LOGO_URL.black_s128,
            }
        },
    }
</script>
