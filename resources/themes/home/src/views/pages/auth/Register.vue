<template lang="pug">
    .card.border-none
        .card-body
            h4.font-weight-bold.text-base-red.mb-3 {{ $t('pages._auth._register._') }}
            .form-group
                .btn-group
                    button.btn.btn-microsoft.hide-not-over(v-if="microsoftEnabled" :disabled="loading" @click="onMicrosoftLoginClicked()" type="button")
                        i.fas.fa-circle-notch.fa-spin(v-if="loading")
                        span(v-else)
                            i.fab.fa-microsoft.fa-fw.fa-sm
                            span.hide-target.ml-2 {{ $t('pages._auth._register.register_with', {provider: 'Microsoft'}) }}
                    button.btn.btn-google.hide-not-over(v-if="googleEnabled" :disabled="loading" @click="onGoogleLoginClicked()" type="button")
                        i.fas.fa-circle-notch.fa-spin(v-if="loading")
                        span(v-else)
                            i.fab.fa-google.fa-fw.fa-sm
                            span.hide-target.ml-2 {{ $t('pages._auth._register.register_with', {provider: 'Google'}) }}
                    button.btn.btn-facebook.hide-not-over(v-if="facebookEnabled" :disabled="loading" @click="onFacebookLoginClicked()" type="button")
                        i.fas.fa-circle-notch.fa-spin(v-if="loading")
                        span(v-else)
                            i.fab.fa-facebook-f.fa-fw.fa-sm
                            span.hide-target.ml-2 {{ $t('pages._auth._register.register_with', {provider: 'Facebook'}) }}
            .mb-3(v-if="registerWith")
                .badge.badge-register.badge-microsoft.mb-0(v-if="provider === 'microsoft'")
                    i.fab.fa-microsoft.fa-sm
                    | &nbsp;&nbsp;{{ $t('pages._auth._register.registering_with', {provider: 'Microsoft'}) }}
                .badge.badge-register.badge-google.mb-0(v-else-if="provider === 'google'")
                    i.fab.fa-google.fa-sm
                    | &nbsp;&nbsp;{{ $t('pages._auth._register.registering_with', {provider: 'Google'}) }}
                .badge.badge-register.badge-facebook.mb-0(v-else-if="provider === 'facebook'")
                    i.fab.fa-facebook-f.fa-sm
                    | &nbsp;&nbsp;{{ $t('pages._auth._register.registering_with', {provider: 'Facebook'}) }}
            error-box.alert-base-red(:error="error")
            form(@submit.prevent="onRegisterSubmitted()")
                .form-group.text-center
                    img.rounded-circle.w-50(v-if="urlAvatar" :src="urlAvatar")
                .form-group
                    input#inputDisplayName.form-control(ref="inputDisplayName" v-model="displayName" :placeholder="$t('pages.display_name')" type="text" required)
                .form-group
                    input#inputEmail.form-control(v-model="email" :placeholder="$t('pages.email_address')" type="email" required)
                .form-group
                    input#inputPassword.form-control(ref="inputPassword" v-model="password" :placeholder="$t('pages.password')" type="password" required)
                .form-group
                    input#inputRepeatPassword.form-control(v-model="passwordConfirmation" :placeholder="$t('actions.retype_what', {what: $t('pages.password_lc')})" type="password" required)
                .form-group
                    button.btn.btn-base-red(:disabled="loading || disabled" type="submit")
                        text-with-loading(:loading="loading" :text="$t('actions.register')")
            nav
                .mt-2
                    router-link.link-base-red(:to="{name: 'login'}") {{ $t('pages._auth.has_account') }}
</template>

<script>
    import {authRoutes} from '../../../app/routing/router/routes'
    import {log} from '../../../app/utils/log'
    import {mapActions, mapGetters} from 'vuex'
    import {session} from '../../../app/utils/session'
    import {APP_ROUTE, ERROR_LEVEL_DEF} from '../../../app/config'
    import ErrorBox from '../../components/ErrorBox'
    import TextWithLoading from '../../components/TextWithLoading'

    export default {
        name: 'Register',
        components: {ErrorBox, TextWithLoading},
        data() {
            return {
                loading: false,

                error: null,

                registerWith: false,

                displayName: '',
                email: '',
                password: '',
                passwordConfirmation: '',

                urlAvatar: null,
                provider: null,
                providerId: null,

                microsoftEnabled: this.$server.microsoft_enabled,
                googleEnabled: this.$server.google_enabled,
                facebookEnabled: this.$server.facebook_enabled,
            }
        },
        computed: {
            ...mapGetters({
                microsoftMe: 'microsoft/me',
                googleMe: 'google/me',
                facebookMe: 'facebook/me',
            }),
            disabled() {
                return !this.displayName || !this.email || !this.password || !this.passwordConfirmation
            },
        },
        mounted() {
            if (session.retrieve('microsoft_login')) {
                this.initMicrosoftLogin()
            } else if (session.retrieve('google_login')) {
                this.initGoogleLogin()
            } else if (session.retrieve('facebook_login')) {
                this.initFacebookLogin()
            } else {
                this.$refs.inputDisplayName.focus()
            }
        },
        methods: {
            ...mapActions({
                accountRegister: 'account/register',
                accountLoginWithMicrosoft: 'account/loginWithMicrosoft',
                accountLoginWithGoogle: 'account/loginWithGoogle',
                accountLoginWithFacebook: 'account/loginWithFacebook',
                microsoftLogin: 'microsoft/login',
                googleLogin: 'google/login',
                facebookLogin: 'facebook/login',
            }),

            reset() {
                this.registerWith = false

                this.urlAvatar = null
                this.provider = null
                this.providerId = null

                this.displayName = ''
                this.email = ''
                this.password = ''
                this.passwordConfirmation = ''
            },

            initMicrosoftLogin() {
                this.registerWith = true
                this.provider = 'microsoft'
                this.providerId = this.microsoftMe.id
                this.displayName = this.microsoftMe.name
                this.email = this.microsoftMe.email
                this.urlAvatar = null

                this.$refs.inputPassword.focus()
            },

            initGoogleLogin() {
                this.registerWith = true
                this.provider = 'google'
                this.providerId = this.googleMe.basicProfile.id
                this.displayName = this.googleMe.basicProfile.name
                this.email = this.googleMe.basicProfile.email
                this.urlAvatar = this.googleMe.basicProfile.imageUrl

                this.$refs.inputPassword.focus()
            },

            initFacebookLogin() {
                this.registerWith = true
                this.provider = 'facebook'
                this.providerId = this.facebookMe.id
                this.displayName = this.facebookMe.name
                this.email = this.facebookMe.email
                this.urlAvatar = this.facebookMe.picture

                this.$refs.inputPassword.focus()
            },

            onRegisterSubmitted() {
                this.error = null
                this.loading = true
                this.accountRegister({
                    params: {
                        display_name: this.displayName,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.passwordConfirmation,
                        url_avatar: this.urlAvatar,
                        provider: this.provider,
                        provider_id: this.providerId,
                        app_verify_email_path: this.$router.getPathByName(APP_ROUTE.verify_email),
                    },
                    doneCallback: () => {
                        this.loading = false

                        this.afterRegister()
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.error = {
                            messages: err.getMessages(),
                            extra: err.getExtra(),
                            level: ERROR_LEVEL_DEF.none,
                        }
                    },
                })
            },

            afterRegister() {
                session.restart()

                this.$router.replaceRoutes(authRoutes).push({name: 'home'})
            },

            onMicrosoftLoginClicked() {
                this.error = null
                this.$bus.emit('page.loading')
                this.microsoftLogin({
                    doneCallback: () => {
                        log.write(this.microsoftMe)

                        this.accountLoginWithMicrosoft({
                            id: this.microsoftMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterRegister()
                            },
                            errorCallback: err => {
                                log.write(err)

                                this.$bus.emit('page.loaded')

                                this.initMicrosoftLogin()
                            },
                        })
                    },
                    errorCallback: err => {
                        this.$bus.emit('page.loaded')

                        this.afterTryLoginSociallyFailed(err, this.$t('pages._auth._login.login_failed_with', {provider: 'Microsoft'}))
                    },
                })
            },

            onGoogleLoginClicked() {
                this.error = null
                this.$bus.emit('page.loading')
                this.googleLogin({
                    doneCallback: () => {
                        log.write(this.googleMe)

                        this.accountLoginWithGoogle({
                            id: this.googleMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterRegister()
                            },
                            errorCallback: err => {
                                log.write(err)

                                this.$bus.emit('page.loaded')

                                this.initGoogleLogin()
                            },
                        })
                    },
                    errorCallback: err => {
                        this.$bus.emit('page.loaded')

                        this.afterTryLoginSociallyFailed(err, this.$t('pages._auth._login.login_failed_with', {provider: 'Google'}))
                    },
                })
            },

            onFacebookLoginClicked() {
                this.error = null
                this.$bus.emit('page.loading')
                this.facebookLogin({
                    doneCallback: () => {
                        log.write(this.facebookMe)

                        this.accountLoginWithFacebook({
                            id: this.facebookMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterRegister()
                            },
                            errorCallback: err => {
                                log.write(err)

                                this.$bus.emit('page.loaded')

                                this.initFacebookLogin()
                            },
                        })
                    },
                    errorCallback: err => {
                        this.$bus.emit('page.loaded')

                        this.afterTryLoginSociallyFailed(err, this.$t('pages._auth._login.login_failed_with', {provider: 'Facebook'}))
                    },
                })
            },

            afterTryLoginSociallyFailed(err, message) {
                log.write(err)

                this.error = {
                    messages: [message],
                    level: ERROR_LEVEL_DEF.none,
                }

                this.reset()
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import "../../../assets/css/variables";

    .badge-register {
        line-height: inherit;
        padding-left: .6rem;
        padding-right: .6rem;
    }

    .form-control {
        &:focus {
            box-shadow: 0 0 0 0.2rem $color-base-red-lighter-o;
        }
    }
</style>
