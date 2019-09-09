<template lang="pug">
    span
        i.fas.fa-circle-notch.fa-spin(v-if="loading || both")
        span(v-if="both") &nbsp;&nbsp;
        span(v-if="!loading || both") {{ text }}
</template>

<script>
    export default {
        name: 'TextWithLoading',
        props: {
            loading: Boolean,
            text: String,
            both: {
                type: Boolean,
                default: false,
            },
        },
    }
</script>
