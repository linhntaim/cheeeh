<template lang="pug">
    #app
        router-view
        page-loader
</template>

<script>
    import PageLoader from './components/PageLoader'

    export default {
        name: 'App',
        components: {PageLoader},
    }
</script>
