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
                        a.nav-link(href="#") What's new
                    li.nav-item.user-nav-item(v-if="accountIsLoggedIn")
                        .dropdown
                            a.nav-link.dropdown-toggle.no-arrow(href="#" data-toggle="dropdown")
                                img.w8x4.rounded-circle.va-middle(:src="currentUser.url_avatar")
                            .dropdown-menu.dropdown-menu-right
                                router-link.dropdown-item(:to="{name: 'account'}")
                                    i.fas.fa-user.mr-2.text-gray.fa-xs.fa-fw
                                    | My account
                                .dropdown-divider
                                router-link.dropdown-item(:to="{name: 'logout'}")
                                    i.fas.fa-sign-out-alt.mr-2.text-gray.fa-xs.fa-fw
                                    | {{ $t('actions.logout') }}
                    li.nav-item(v-else)
                        router-link.btn(:class="{'btn-base-pink': actionRouteName === 'login', 'btn-base-red': actionRouteName === 'register'}" :to="{name: actionRouteName}") {{ actionName }}
</template>

<script>
    import {APP_LOGO_URL, APP_NAME} from '../../app/config'
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
                this.initUi()
            },
        },
        created() {
            this.initUi()

            this.$bus.on('localeChanged', () => {
                this.initUi()
            })
        },
        methods: {
            initUi() {
                if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                    this.actionRouteName = 'login'
                    this.actionName = this.$t('actions.login')
                } else {
                    this.actionRouteName = 'register'
                    this.actionName = this.$t('actions.register')
                }
            },
        },
    }
</script>
