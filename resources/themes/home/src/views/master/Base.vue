<template lang="pug">
    .layout
        header.layout-header
            main-header
        .layout-body(:class="{'align-items-center': center}")
            .container
                .px-sm-3
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
        data() {
            return {
                center: false,
            }
        },
        created() {
            this.$bus.on('baseCentered', () => {
                this.center = true
            })
            this.$bus.on('notBaseCentered', () => {
                this.center = false
            })
        },
    }
</script>
