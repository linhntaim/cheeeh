<template lang="pug">
    .card
        .card-body.text-center
            h1 400
            h4 {{ $t('error.bad_request._') }}
            .my-3 {{ $t('error.bad_request.desc') }}
            router-link.btn.btn-primary(:to="{path: '/'}")
                i.fas.fa-long-arrow-alt-left
                | &nbsp;&nbsp;{{ $t('error.back_to_root') }}
</template>

<script>
    export default {
        name: 'BadRequest',
    }
</script>
