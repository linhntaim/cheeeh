<template lang="pug">
    .card
        .card-body.text-center
            h1 404
            h4 {{ $t('error.not_found._') }}
            .my-3 {{ $t('error.not_found.desc') }}
            router-link.btn.btn-primary(:to="{path: '/'}")
                i.fas.fa-long-arrow-alt-left
                | &nbsp;&nbsp;{{ $t('error.back_to_root') }}
</template>

<script>
    export default {
        name: 'NotFound',
    }
</script>
