<template lang="pug">
    .container.main-footer
        nav.navbar.navbar-expand-md.navbar-dark
            .navbar-collapse
                ul.navbar-nav
                    li.nav-item
                        a.nav-link.active {{ appName }} &copy; {{ year }}
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
                    .dropdown.dropup.locale-dropdown
                        a.nav-link.dropdown-toggle.no-arrow(href="#" data-toggle="dropdown")
                            | {{ $t('master.main_footer.language') }}
                        .dropdown-menu.dropdown-menu-right
                            a.dropdown-item(v-for="metaLocale in metadata.locales" @click.prevent="onLocaleClicked(metaLocale)" :class="{'active': metaLocale.code === currentUser.localization.locale}" href="#")
                                i.fas.fa-check.fa-fw.fa-sm.ml-n3(v-if="metaLocale.code === currentUser.localization.locale")
                                span.font-weight-bold.small.ml-1 {{ metaLocale.name }}
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'
    import {log} from '../../app/utils/log'
    import {APP_NAME} from '../../app/config'

    export default {
        name: 'MainFooter',
        data() {
            return {
                loading: false,

                appName: APP_NAME,
                year: (new Date()).getFullYear(),
            }
        },
        computed: {
            ...mapGetters({
                metadata: 'prerequisite/metadata',
                currentUser: 'account/user',
            }),
        },
        mounted() {
            this.init()
        },
        methods: {
            ...mapActions({
                require: 'prerequisite/require',
                accountUpdateLocale: 'account/updateLocale',
            }),
            init() {
                this.loading = true
                this.require({
                    names: ['locales'],
                    doneCallback: () => {
                        this.loading = false

                        this.$forceUpdate()
                    },
                    errorCallback: err => {
                        this.loading = false

                        log.write(err, 'main_footer')
                    },
                })
            },
            onLocaleClicked(locale) {
                if (locale.code === this.currentUser.localization.locale) return

                this.loading = true
                this.accountUpdateLocale({
                    locale: locale.code,
                    doneCallback: () => {
                        this.loading = false

                        this.$bus.emit('localeChanged')
                    },
                    errorCallback: () => {
                        this.loading = false
                    },
                })
            },
        },
    }
</script>
