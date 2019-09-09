<template lang="pug">
    .layout
        header.layout-header
            main-header
        .layout-body
            router-view
        footer.layout-footer
            main-footer
</template>

<script>
    import MainHeader from './MainHeader'
    import MainFooter from './MainFooter'

    export default {
        name: 'Base',
        components: {MainFooter, MainHeader},
    }
</script>
