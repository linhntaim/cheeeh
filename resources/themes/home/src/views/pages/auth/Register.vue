<template lang="pug">
    .card.border-none
        .card-body
            h4.font-weight-bold.text-base-red.mb-3 {{ $t('pages._auth._register._') }}
            form(@submit.prevent="onRegisterSubmitted()")
                .form-group.text-center
                    img.rounded-circle.w-50(v-if="urlAvatar" :src="urlAvatar")
                .form-group
                    input#inputDisplayName.form-control(v-model="displayName" :placeholder="$t('pages.display_name')" type="text" required)
                .form-group
                    input#inputEmail.form-control(v-model="email" :placeholder="$t('pages.email_address')" type="email" required)
                .form-group
                    input#inputPassword.form-control(v-model="password" :placeholder="$t('pages.password')" type="password" required)
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
    import {mapActions, mapGetters} from 'vuex'
    import {session} from '../../../app/utils/session'
    import TextWithLoading from '../../components/TextWithLoading'

    export default {
        name: 'Register',
        components: {TextWithLoading},
        data() {
            return {
                loading: false,

                displayName: '',
                email: '',
                password: '',
                passwordConfirmation: '',

                urlAvatar: null,
                provider: null,
                providerId: null,
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
            }
        },
        methods: {
            ...mapActions({
                accountRegister: 'account/register',
            }),

            initMicrosoftLogin() {
                this.provider = 'microsoft'
                this.providerId = this.microsoftMe.id
                this.displayName = this.microsoftMe.name
                this.email = this.microsoftMe.email
            },

            initGoogleLogin() {
                this.provider = 'google'
                this.providerId = this.googleMe.basicProfile.id
                this.displayName = this.googleMe.basicProfile.name
                this.email = this.googleMe.basicProfile.email
                this.urlAvatar = this.googleMe.basicProfile.imageUrl
            },

            initFacebookLogin() {
                this.provider = 'facebook'
                this.providerId = this.facebookMe.id
                this.displayName = this.facebookMe.name
                this.email = this.facebookMe.email
                this.urlAvatar = this.facebookMe.picture
            },

            onRegisterSubmitted() {
                this.loading = true
                this.accountRegister({
                    displayName: this.displayName,
                    email: this.email,
                    password: this.password,
                    passwordConfirmation: this.passwordConfirmation,
                    provider: this.provider,
                    providerId: this.providerId,
                    urlAvatar: this.urlAvatar,
                    doneCallback: () => {
                        this.loading = false

                        this.afterRegister()
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.$bus.emit('error', {messages: err.getMessages(), extra: err.getExtra()})
                    },
                })
            },

            afterRegister() {
                this.$router.push({name: 'home'})
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import "../../../assets/css/variables";

    .form-control {
        &:focus {
            border-color: $color-base-red-lighter;
            box-shadow: 0 0 0 0.2rem $color-base-red-lighter-o;
        }
    }
</style>
