<template lang="pug">
    .row
        .col-lg-4.pr-lg-0.mt-3.mb-lg-3
            .card.border-0.shadow-sm
                .card-body.p-3
                    | Sidebar
            .mt-3
                router-link.btn.btn-base-pink.btn-block(:to="{name: 'upload'}")
                    i.fas.fa-plus
                    | &nbsp;&nbsp;{{ $t('actions.upload') }}
        .col-lg-8.my-3
            .card.border-0.shadow-sm
                .card-body.p-3
                    | Hello
</template>

<script>
    export default {
        name: 'Home',
    }
</script>
