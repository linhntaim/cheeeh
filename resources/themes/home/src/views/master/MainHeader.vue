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
                    li.nav-item(v-if="accountIsLoggedIn")
                        .dropdown.user-dropdown
                            a.dropdown-toggle.no-arrow(href="#" data-toggle="dropdown")
                                img.w8x4.rounded-circle(:src="currentUser.url_avatar")
                            .dropdown-menu.dropdown-menu-right
                                .dropdown-divider
                                router-link.dropdown-item(:to="{name: 'logout'}")
                                    i.fas.fa-sign-out-alt.mr-2.text-gray.fa-xs.fa-fw
                                    | {{ $t('actions.logout') }}
                    li.nav-item(v-else)
                        router-link.btn(:class="{'btn-base-pink': actionRouteName === 'login', 'btn-base-red': actionRouteName === 'register'}" :to="{name: actionRouteName}") {{ actionName }}
</template>

<script>
    import {APP_LOGO_URL, APP_NAME} from '../../config'
    import {mapGetters} from 'vuex'
    import routeHelper from '../../app/utils/route_helper'

    export default {
        name: 'MainHeader',
        data() {
            return {
                appName: APP_NAME,
                logoUrl: APP_LOGO_URL.s128,

                actionRouteName: 'login',
                actionName: this.$t('actions.login'),
            }
        },
        computed: {
            ...mapGetters({
                accountIsLoggedIn: 'account/isLoggedIn',
                currentUser: 'account/user',
            }),
        },
        watch: {
            '$route'() {
                if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                    this.actionRouteName = 'login'
                    this.actionName = this.$t('actions.login')
                } else {
                    this.actionRouteName = 'register'
                    this.actionName = this.$t('actions.register')
                }
            },
        },
        created() {
            if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                this.actionRouteName = 'login'
                this.actionName = this.$t('actions.login')
            } else {
                this.actionRouteName = 'register'
                this.actionName = this.$t('actions.register')
            }
        },
    }
</script>

<style lang="scss" scoped>
    @import '../../assets/css/variables';

    .user-dropdown {
        .dropdown-toggle:hover {
            img {
                box-shadow: 0 0 0 0.15rem $color-base-lighter-o2;
            }
        }
    }
</style>
