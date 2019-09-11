<template lang="pug">
    .card.border-none
        .card-body
            error-box.alert-base-pink(:error="error")
            form(@submit.prevent="onLoginSubmitted()")
                .form-group
                    input#inputEmail.form-control(ref="inputEmail" v-model="email" :placeholder="$t('pages.email_address')" type="text" required)
                .form-group
                    input#inputPassword.form-control(v-model="password" :placeholder="$t('pages.password')" :required="!token" :disabled="token" type="password")
                .form-group
                    button.btn.btn-base-pink(:disabled="loading || disabled" type="submit")
                        text-with-loading(:loading="loading" :text="$t('actions.login')")
                .form-group
                    .btn-group
                        button.btn.btn-microsoft.hide-not-over(v-if="microsoftEnabled" :disabled="loading || token" @click="onMicrosoftLoginClicked()" type="button")
                            i.fas.fa-circle-notch.fa-spin(v-if="loading")
                            span(v-else)
                                i.fab.fa-microsoft.fa-fw.fa-sm
                                span.hide-target.ml-2 {{ $t('pages._auth._login.login_with', {provider: 'Microsoft'}) }}
                        button.btn.btn-google.hide-not-over(v-if="googleEnabled" :disabled="loading || token" @click="onGoogleLoginClicked()" type="button")
                            i.fas.fa-circle-notch.fa-spin(v-if="loading")
                            span(v-else)
                                i.fab.fa-google.fa-fw.fa-sm
                                span.hide-target.ml-2 {{ $t('pages._auth._login.login_with', {provider: 'Google'}) }}
                        button.btn.btn-facebook.hide-not-over(v-if="facebookEnabled" :disabled="loading || token" @click="onFacebookLoginClicked()" type="button")
                            i.fas.fa-circle-notch.fa-spin(v-if="loading")
                            span(v-else)
                                i.fab.fa-facebook-f.fa-fw.fa-sm
                                span.hide-target.ml-2 {{ $t('pages._auth._login.login_with', {provider: 'Facebook'}) }}
            nav
                .mt-2
                    router-link.link-base-pink(:to="{name: 'forgot_password'}") {{ $t('pages._auth.forgot_password') }}
                .mt-2
                    router-link.link-base-pink(:to="{name: 'register'}") {{ $t('pages._auth.create_account') }}
</template>

<script>
    import {authRoutes} from '../../../app/routing/router/routes'
    import {log} from '../../../app/utils/log'
    import {mapActions, mapGetters} from 'vuex'
    import {session} from '../../../app/utils/session'
    import TextWithLoading from '../../components/TextWithLoading'
    import ErrorBox from '../../components/ErrorBox'
    import {ERROR_LEVEL_DEF} from '../../../app/config'

    export default {
        name: 'Login',
        components: {ErrorBox, TextWithLoading},
        data() {
            return {
                loading: false,

                error: null,

                token: false,

                email: '',
                password: '',

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
                return !this.token && (!this.email || !this.password)
            },
        },
        created() {
            if (this.$route.query.token) {
                this.email = this.$route.query.token
                this.token = true
            }
        },
        mounted() {
            this.$refs.inputEmail.focus()
        },
        methods: {
            ...mapActions({
                accountLogin: 'account/login',
                accountLoginWithMicrosoft: 'account/loginWithMicrosoft',
                accountLoginWithGoogle: 'account/loginWithGoogle',
                accountLoginWithFacebook: 'account/loginWithFacebook',
                microsoftLogin: 'microsoft/login',
                googleLogin: 'google/login',
                facebookLogin: 'facebook/login',
            }),

            onLoginSubmitted() {
                this.error = null
                this.loading = true
                this.accountLogin({
                    email: this.email,
                    password: this.password,
                    token: this.token,
                    doneCallback: () => {
                        this.afterLogin()
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

            onMicrosoftLoginClicked() {
                this.$bus.emit('page.loading')
                this.microsoftLogin({
                    doneCallback: () => {
                        log.write(this.microsoftMe)

                        this.accountLoginWithMicrosoft({
                            id: this.microsoftMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                this.$bus.emit('page.loaded')

                                this.afterLoginSociallyFailed(err, 'microsoft')
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
                this.$bus.emit('page.loading')
                this.googleLogin({
                    doneCallback: () => {
                        log.write(this.googleMe)

                        this.accountLoginWithGoogle({
                            id: this.googleMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                this.$bus.emit('page.loaded')

                                this.afterLoginSociallyFailed(err, 'google')
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
                this.$bus.emit('page.loading')
                this.facebookLogin({
                    doneCallback: () => {
                        log.write(this.facebookMe)

                        this.accountLoginWithFacebook({
                            id: this.facebookMe.id,
                            doneCallback: () => {
                                this.$bus.emit('page.loaded')

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                this.$bus.emit('page.loaded')

                                this.afterLoginSociallyFailed(err, 'facebook')
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
            },

            afterLoginSociallyFailed(err, providerName) {
                log.write(err)

                session.store(providerName + '_login', true, true)
                this.$router.push({name: 'register'})
            },

            afterLogin() {
                session.restart()

                this.$router.replaceRoutes(authRoutes).push({name: 'home'})
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import "../../../assets/css/variables";

    .form-control {
        &:focus, &.focus {
            box-shadow: 0 0 0 0.2rem $color-base-pink-lighter-o;
        }
    }
</style>
