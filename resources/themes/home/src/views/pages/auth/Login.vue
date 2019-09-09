<template lang="pug">
    .card.border-none
        .card-body
            form(@submit.prevent="onLoginSubmitted()")
                .form-group
                    input#inputEmail.form-control(v-model="email" :placeholder="$t('pages.email_address')" type="text" required)
                .form-group
                    input#inputPassword.form-control(v-model="password" :placeholder="$t('pages.password')" :required="!token" :disabled="token" type="password")
                .form-group
                    button.btn.btn-base-pink(:disabled="loading || disabled" type="submit") {{ $t('actions.login') }}
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

    export default {
        name: 'Login',
        data() {
            return {
                loading: false,

                token: false,

                email: '',
                password: '',

                microsoftEnabled: this.$server.microsoft_enable,
                googleEnabled: this.$server.google_enable,
                facebookEnabled: this.$server.facebook_enable,
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
                this.loading = true
                this.accountLogin({
                    email: this.email,
                    password: this.password,
                    token: this.token,
                    doneCallback: () => {
                        this.loading = false

                        this.afterLogin()
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.$bus.emit('error', {messages: err.getMessages(), extra: err.getExtra()})
                    },
                })
            },

            onMicrosoftLoginClicked() {
                this.loading = true
                this.microsoftLogin({
                    doneCallback: () => {
                        log.write(this.microsoftMe)

                        this.accountLoginWithMicrosoft({
                            id: this.microsoftMe.id,
                            doneCallback: () => {
                                this.loading = false

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                this.loading = false

                                this.afterLoginSociallyFailed(err, 'microsoft')
                            },
                        })
                    },
                    errorCallback: err => {
                        this.loading = false

                        this.afterTryLoginSociallyFailed(err, 'Sorry, you did not successfully login with Microsoft')
                    },
                })
            },

            onGoogleLoginClicked() {
                this.loading = true
                this.googleLogin({
                    doneCallback: () => {
                        log.write(this.googleMe)

                        this.accountLoginWithGoogle({
                            id: this.googleMe.id,
                            doneCallback: () => {
                                this.loading = false

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                log.write(err)

                                this.loading = false

                                this.afterLoginSociallyFailed(err, 'google')
                            },
                        })
                    },
                    errorCallback: err => {
                        this.loading = false

                        this.afterTryLoginSociallyFailed(err, 'Sorry, you did not successfully login with Google')
                    },
                })
            },

            onFacebookLoginClicked() {
                this.loading = true
                this.facebookLogin({
                    doneCallback: () => {
                        log.write(this.facebookMe)

                        this.accountLoginWithFacebook({
                            id: this.facebookMe.id,
                            doneCallback: () => {
                                this.loading = false

                                this.afterLogin()
                            },
                            errorCallback: err => {
                                this.loading = false

                                this.afterLoginSociallyFailed(err, 'facebook')
                            },
                        })
                    },
                    errorCallback: err => {
                        this.loading = false

                        this.afterTryLoginSociallyFailed(err, 'Sorry, you did not successfully login with Facebook')
                    },
                })
            },

            afterTryLoginSociallyFailed(err, message) {
                log.write(err)

                this.$bus.emit('error', {
                    messages: [message],
                })
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
        &:focus {
            border-color: $color-base-pink-lighter;
            box-shadow: 0 0 0 0.2rem $color-base-pink-lighter-o;
        }
    }
</style>
