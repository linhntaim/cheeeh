<template lang="pug">
    .layout
        header.layout-header
            main-header
        .layout-body(:class="{'align-items-center': center, 'bg-base-dark-lighter': darken, 'bg-base-dark': darkener}")
            .container
                .px-md-3
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
                darken: false,
                darkener: false,
            }
        },
        created() {
            this.$bus.on('baseCentered', () => {
                this.center = true
            })
            this.$bus.on('baseDarken', () => {
                this.darken = true
            })
            this.$bus.on('baseDarkener', () => {
                this.darkener = true
            })
        },
    }
</script>
