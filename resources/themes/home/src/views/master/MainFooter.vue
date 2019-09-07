<template lang="pug">
    .container.main-footer
        nav.navbar.navbar-expand-sm.navbar-dark
            .navbar-collapse
                ul.navbar-nav
                    li.nav-item
                        a.nav-link(href="#")
                            | About
                    li.nav-item
                        a.nav-link(href="#")
                            | Blog
                    li.nav-item
                        a.nav-link(href="#")
                            | Privacy
                    li.nav-item
                        a.nav-link(href="#")
                            | Terms
                    li.nav-item
                        a.nav-link(href="#")
                            | Help
            ul.navbar-nav.navbar-nav-right
                li.nav-item
                    a.nav-link(href="#")
                        | Language
</template>

<script>
    export default {
        name: 'MainFooter',
        data() {
            return {}
        },
    }
</script>

<style scoped>

</style>
