<template lang="pug">
    .card.border-0
        .card-body
            h4.font-weight-bold.text-base-pink.mb-3 {{ $t('pages._auth._forgot_password._') }}
            form(@submit.prevent="onForgotPasswordSubmitted()")
                error-box(:error="error")
                .form-group
                    input#inputEmail.form-control.focus-base-pink(ref="inputEmail" v-model="email" :placeholder="$t('pages.email_address')" type="email" required)
                .form-group
                    button.btn.btn-base-pink(:disabled="loading || disabled" type="submit")
                        text-with-loading(:loading="loading" :text="$t('pages._auth._forgot_password.submit')")
            nav
                .mt-2
                    router-link.link-base-pink(:to="{name: 'login'}") {{ $t('actions.back_where', {where: $t('pages._auth._login._')}) }}
                .mt-2
                    router-link.link-base-pink(:to="{name: 'register'}") {{ $t('pages._auth.create_account') }}
</template>

<script>
    import {mapActions} from 'vuex'
    import TextWithLoading from '../../components/TextWithLoading'
    import ErrorBox from '../../components/ErrorBox'
    import {APP_ROUTE, ERROR_LEVEL_DEF} from '../../../app/config'

    export default {
        name: 'ForgotPassword',
        components: {ErrorBox, TextWithLoading},
        data() {
            return {
                loading: false,

                error: null,

                email: '',
            }
        },
        computed: {
            disabled() {
                return !this.email
            },
        },
        mounted() {
            this.$refs.inputEmail.focus()
        },
        methods: {
            ...mapActions({
                accountForgotPassword: 'account/forgotPassword',
            }),

            onForgotPasswordSubmitted() {
                this.error = null
                this.loading = true
                this.accountForgotPassword({
                    email: this.email,
                    appResetPasswordPath: this.$router.getPathByName(APP_ROUTE.reset_password),
                    doneCallback: () => {
                        this.loading = false
                        this.error = {
                            messages: [this.$t('pages._auth._forgot_password.succeed')],
                            level: ERROR_LEVEL_DEF.success,
                            class: 'alert-base-yellow',
                        }
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
