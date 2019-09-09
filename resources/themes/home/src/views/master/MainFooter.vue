<template lang="pug">
    .container.main-footer
        nav.navbar.navbar-expand-sm.navbar-dark
            .navbar-collapse
                ul.navbar-nav
                    li.nav-item
                        a.nav-link(href="#")
                            | {{ $t('master.main_footer.about') }}
                    li.nav-item
                        a.nav-link(href="#")
                            | {{ $t('master.main_footer.blog') }}
                    li.nav-item
                        a.nav-link(href="#")
                            | {{ $t('master.main_footer.privacy') }}
                    li.nav-item
                        a.nav-link(href="#")
                            | {{ $t('master.main_footer.terms') }}
                    li.nav-item
                        a.nav-link(href="#")
                            | {{ $t('master.main_footer.help') }}
            ul.navbar-nav.navbar-nav-right
                li.nav-item
                    a.nav-link(href="#")
                        | {{ $t('master.main_footer.language') }}
</template>

<script>
    export default {
        name: 'MainFooter',
        data() {
            return {}
        },
    }
</script>
