<template lang="pug">
    header.masthead
        .container.d-flex.h-100.align-items-center
            .mx-auto.text-center
                h1.mx-auto.my-0.text-uppercase 404
                h2.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.not_found._') }}
                h3.text-white-50.mx-auto.mt-2.mb-5 {{ $t('error.not_found.desc') }}
                router-link.btn.btn-primary(:to="{path: '/'}") {{ $t('error.back_to_root') }}
</template>

<script>
    export default {
        name: 'NotFound',
    }
</script>
