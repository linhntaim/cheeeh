<template lang="pug">
    span
        i.fas.fa-circle-notch.fa-spin(v-if="loading || both")
        span(v-if="both") &nbsp;&nbsp;
        span(v-if="!loading || both" v-html="text")
</template>

<script>
    export default {
        name: 'TextWithLoading',
        props: {
            text: String,
            loading: {
                type: Boolean,
                default: false,
            },
            both: {
                type: Boolean,
                default: false,
            },
        },
    }
</script>
