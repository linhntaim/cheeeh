<template lang="pug">
    .card.border-0(:class="{hidden: !allowed}")
        .card-body
            h4.font-weight-bold.text-base-pink.mb-3 {{ $t('pages._auth._reset_password._') }}
            form(@submit.prevent="onResetPasswordSubmitted()")
                error-box(:error="error")
                .form-group
                    input#inputEmail.form-control.focus-base-pink(v-model="email" :placeholder="$t('pages.email_address')" type="email" readonly)
                .form-group
                    input#inputPassword.form-control.focus-base-pink(ref="inputPassword" v-model="password" :placeholder="$t('pages.password')" type="password" required)
                .form-group
                    input#inputRetypePassword.form-control.focus-base-pink(v-model="passwordConfirmation" :placeholder="$t('actions.retype_what', {what: $t('pages.password_lc')})" type="password" required)
                .form-group
                    button.btn.btn-base-pink(:disabled="loading || disabled" type="submit")
                        text-with-loading(:loading="loading" :text="$t('actions.confirm')")
            nav
                .mt-2
                    router-link.link-base-pink(:to="{name: 'login'}")
                        | {{ $t('actions.go_where', {where: $t('pages._auth._login._')}) }}
                        span(v-if="countDown.enabled") &nbsp;({{ countDown.counter }})
</template>

<script>
    import {mapActions} from 'vuex'
    import TextWithLoading from '../../components/TextWithLoading'
    import ErrorBox from '../../components/ErrorBox'
    import {APP_ROUTE, ERROR_LEVEL_DEF} from '../../../app/config'
    import {CountDown} from '../../../app/utils/count_down'
    import {log} from '../../../app/utils/log'

    export default {
        name: 'ResetPassword',
        components: {ErrorBox, TextWithLoading},
        data() {
            return {
                loading: false,
                allowed: false,

                error: null,

                email: this.$route.params.email,
                token: this.$route.params.token,
                password: '',
                passwordConfirmation: '',

                countDown: new CountDown(),
            }
        },
        computed: {
            disabled() {
                return !this.password || !this.passwordConfirmation
            },
        },
        mounted() {
            this.getResetPassword()
        },
        methods: {
            ...mapActions({
                accountGetResetPassword: 'account/getResetPassword',
                accountResetPassword: 'account/resetPassword',
            }),

            getResetPassword() {
                this.loading = true
                this.allowed = false
                this.accountGetResetPassword({
                    email: this.email,
                    token: this.token,
                    doneCallback: () => {
                        this.loading = false
                        this.allowed = true

                        this.$refs.inputPassword.focus()
                    },
                    errorCallback: err => {
                        log.write(err)

                        this.$router.push({name: APP_ROUTE.not_found})
                    },
                })
            },

            onResetPasswordSubmitted() {
                this.error = null
                this.loading = true
                this.accountResetPassword({
                    email: this.email,
                    token: this.token,
                    password: this.password,
                    passwordConfirmation: this.passwordConfirmation,
                    doneCallback: () => {
                        this.loading = false
                        this.error = {
                            messages: [this.$t('pages._auth._reset_password.succeed')],
                            level: ERROR_LEVEL_DEF.success,
                            class: 'alert-base-yellow',
                        }

                        this.countDown.start(10, () => {
                            this.$router.push({name: 'login'})
                        })
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.error = {
                            messages: err.getMessages(),
                            extra: err.getExtra(),
                            level: ERROR_LEVEL_DEF.error,
                            class: 'alert-base-pink',
                        }
                    },
                })
            },
        },
    }
</script>
