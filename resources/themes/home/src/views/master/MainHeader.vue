<template lang="pug">
    .container.main-header
        nav.navbar.navbar-expand-sm.navbar-dark.py-0
            router-link.navbar-brand.py-0(:to="{name: 'home'}")
                img.h8x6(:src="logoUrl")
                | {{ appName }}
            button.navbar-toggler(type="button", data-toggle="collapse", data-target="#navbarHeader", aria-controls="navbarNav", aria-expanded="false", aria-label="Toggle navigation")
                i.fas.fa-bars
            #navbarHeader.collapse.navbar-collapse
                ul.navbar-nav
                    li.nav-item
                        a.nav-link(href="#")
                            | What's new?
                    li.nav-item.active
                        router-link.btn(:class="{'btn-base-pink': actionRouteName === 'login', 'btn-base-red': actionRouteName === 'register'}" :to="{name: actionRouteName}") {{ actionName }}
</template>

<script>
    import {APP_LOGO_URL, APP_NAME, APP_URL} from '../../config'
    import routeHelper from '../../app/utils/route_helper'

    export default {
        name: 'MainHeader',
        data() {
            return {
                appName: APP_NAME,
                logoUrl: APP_LOGO_URL.s128,

                actionRouteName: 'login',
                actionName: 'Login',
            }
        },
        watch: {
            '$route'() {
                if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                    this.actionRouteName = 'login'
                    this.actionName = 'Login'
                } else {
                    this.actionRouteName = 'register'
                    this.actionName = 'Register'
                }
            },
        },
        created() {
            if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                this.actionRouteName = 'login'
                this.actionName = 'Login'
            } else {
                this.actionRouteName = 'register'
                this.actionName = 'Register'
            }
        },
    }
</script>

<style scoped>

</style>
