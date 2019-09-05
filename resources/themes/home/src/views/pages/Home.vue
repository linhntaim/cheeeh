<template lang="pug">
    div
        // Header
        header.masthead
            .container.d-flex.h-100.align-items-center
                .mx-auto.text-center
                    h1.mx-auto.my-0.text-uppercase {{ noSpacesAppName }}
                    h2.text-white-50.mx-auto.mt-2.mb-5 A free, open source, pre-constructed framework created by Nguyen Tuan Linh.
                    .mb-5(v-if="accountIsLoggedIn")
                        p.text-white
                            | {{ $t('hello') }}, {{ currentUser.display_name }}.&nbsp;
                        p
                            a(:href="adminUrl")
                                | {{ $t('actions.go', {where: $t('pages._home.admin')}) }} →
                        p
                            router-link(:to="{path: '/auth/logout'}")
                                | {{ $t('actions.logout') }} →
                    .mb-5(v-else)
                        p
                            a(:href="adminUrl")
                                | {{ $t('actions.login') }} →
                    a.btn.btn-primary.js-scroll-trigger(href="#" data-target="#about") {{ $t('actions.get_started') }}
        // About Section
        section#about.about-section.text-center
            .container
                .row
                    .col-lg-8.mx-auto
                        h2.text-white.mb-4 {{ $t('pages._home.about_title') }}
                        p.text-white-50(v-html="$t('pages._home.about_content', {app_name: appName, author_name: 'Nguyen Tuan Linh', github_url: 'https://github.com/linhntaim/chichi'})")
        // Projects Section
        section#projects.projects-section.bg-light
            .container
                // Featured Project Row
                .row.align-items-center.no-gutters.mb-4.mb-lg-5
                    .col-xl-8.col-lg-7
                        img.img-fluid.mb-3.mb-lg-0(src="img/bg-masthead.jpg", alt="")
                    .col-xl-4.col-lg-5
                        .featured-text.text-center.text-lg-left
                            h4 API & Files
                            p.text-black-50.mb-2
                                | Chi Chi provides an API gateway so that front-end applications can make requests to it and get responses in a pre-defined JSON format.
                            p.text-black-50.mb-2
                                | Chi Chi also has a basic file handler which can manage to physically store files by date and especially response image files in a specific size.
                // Project One Row
                .row.justify-content-center.no-gutters.mb-5.mb-lg-0
                    .col-lg-6
                        img.img-fluid(src="img/demo-image-01.jpg", alt="")
                    .col-lg-6
                        .bg-black.text-center.h-100.project
                            .d-flex.h-100
                                .project-text.w-100.my-auto.text-center.text-lg-left
                                    h4.text-white Administration
                                    p.mb-0.text-white-50
                                        | Chi Chi is shipped with pre-constructed administration pages, so that you can quickly build administrative functions to manage your business.
                                    hr.d-none.d-lg-block.mb-0.ml-0
                // Project Two Row
                .row.justify-content-center.no-gutters
                    .col-lg-6
                        img.img-fluid(src="img/demo-image-02.jpg", alt="")
                    .col-lg-6.order-lg-first
                        .bg-black.text-center.h-100.project
                            .d-flex.h-100
                                .project-text.w-100.my-auto.text-center.text-lg-right
                                    h4.text-white Homepage
                                    p.mb-0.text-white-50
                                        | Chi Chi is shipped with pre-constructed samples of your homepage, so that you can easily show your work to your audiences.
                                    hr.d-none.d-lg-block.mb-0.mr-0
        // Signup Section
        section#signup.signup-section
            .container
                .row
                    .col-md-10.col-lg-8.mx-auto.text-center
                        i.far.fa-paper-plane.fa-2x.mb-2.text-white
                        h2.text-white.mb-5 {{ $t('pages._home.subscribe_to_receive_updates') }}
                        form.form-inline.d-flex
                            input#inputEmail.form-control.flex-fill.mr-0.mr-sm-2.mb-3.mb-sm-0(type="email" :placeholder="$t('pages._home.enter_email_address')")
                            button.btn.btn-primary.mx-auto(type="submit") {{ $t('actions.subscribe') }}
        // Contact Section
        section.contact-section.bg-black
            .container
                .row
                    .col-md-4.mb-3.mb-md-0
                        .card.py-4.h-100
                            .card-body.text-center
                                i.fas.fa-map-marked-alt.text-primary.mb-2
                                h4.text-uppercase.m-0 {{ $t('pages.address') }}
                                hr.my-4
                                .small.text-black-50 122/8 Pho Quang Street, Phu Nhuan District, Ho Chi Minh City, Vietnam
                    .col-md-4.mb-3.mb-md-0
                        .card.py-4.h-100
                            .card-body.text-center
                                i.fas.fa-envelope.text-primary.mb-2
                                h4.text-uppercase.m-0 {{ $t('pages.email') }}
                                hr.my-4
                                .small.text-black-50
                                    a(href="mailto:inbox@linhntaim.com") inbox@linhntaim.com
                    .col-md-4.mb-3.mb-md-0
                        .card.py-4.h-100
                            .card-body.text-center
                                i.fas.fa-mobile-alt.text-primary.mb-2
                                h4.text-uppercase.m-0 {{ $t('pages.phone') }}
                                hr.my-4
                                .small.text-black-50 (+84) 975-783-771
                .social.d-flex.justify-content-center
                    a.mx-2(href="https://linhntaim.com" target="_blank")
                        i.fas.fa-globe
                    a.mx-2(href="skype:live:linhnt.aim?chat")
                        i.fab.fa-skype
                    a.mx-2(href="https://facebook.com/linhntaim" target="_blank")
                        i.fab.fa-facebook-f
                    a.mx-2(href="https://github.com/linhntaim" target="_blank")
                        i.fab.fa-github
                .localization.text-center
                    a.mx-2(v-for="metaLocale in metadata.locales" @click.prevent="onLocaleClicked(metaLocale)" :class="{'locale-active': metaLocale.code === currentUser.localization.locale}" href="#")
                        span {{ metaLocale.name }}
                    router-link.mx-2(:to="{path: '/localization'}")
                        span {{ $t('pages._localization._') }} →
</template>

<script>
    import {mapGetters, mapActions} from 'vuex'
    import {APP_ADMIN_URL, APP_NAME} from '../../config'

    export default {
        name: 'Home',
        data() {
            return {
                loading: false,

                appName: APP_NAME,
                noSpacesAppName: APP_NAME.replace(' ', ''),
                adminUrl: APP_ADMIN_URL,
            }
        },
        computed: {
            ...mapGetters({
                accountIsLoggedIn: 'account/isLoggedIn',
                currentUser: 'account/user',
                metadata: 'prerequisite/metadata',
            }),
        },
        mounted() {
            this.init()

            this.$bus.on('localeChanged', () => {
                this.prerequisiteReset()
                this.init()
            })
        },
        methods: {
            ...mapActions({
                prerequisiteReset: 'prerequisite/reset',
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
                    errorCallback: () => {
                        this.loading = false
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

<style lang="scss" scoped>
    .about-section {
        padding-top: 8rem;

        p {
            margin-bottom: 8rem;
        }
    }

    .contact-section .localization {
        margin-top: 5rem;

        a {
            display: inline-block;
            margin: .25rem 0;
            border-bottom: 1px solid transparent;

            &:hover:not(:last-child) {
                border-color: #3c6360;
            }

            &.locale-active {
                cursor: default;
                color: #64a19d;
                border-color: #64a19d !important;
            }
        }
    }
</style>
