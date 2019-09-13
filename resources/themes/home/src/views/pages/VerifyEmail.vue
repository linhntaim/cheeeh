<template lang="pug">
    .row.justify-content-center
        .col-xl-10.col-lg-12.col-md-9
            .card.border-0.shadow-sm.my-5
                .card-body.p-5.text-center
                    h4.font-weight-bold.text-base-red.mb-3 {{ $t('pages._auth._verify_email._') }}
                    div(v-html="$t('pages._auth._verify_email.desc', {email: shownEmail, button: $t('actions.resend')})")
                    .mt-5.mb-3.small.text-base-red {{ $t('pages._auth._verify_email.help') }}
                    error-box.d-inline-block.small(:error="error")
                    form(@submit.prevent="onResendSubmitted()")
                        .row
                            .col-md-2.col-lg-3
                            .col-md-8.col-lg-6
                                .form-group
                                    input#nputEmail.form-control.focus-base-red.text-center(v-model="email" :placeholder="$t('pages.email_address')" type="email" required)
                        button.btn.btn-base-red(:disabled="loading || disabled" type="submit")
                            text-with-loading(:loading="loading" :text="$t('actions.resend')")
</template>

<script>
    import {mapGetters, mapActions} from 'vuex'
    import TextWithLoading from '../components/TextWithLoading'
    import {APP_ROUTE, ERROR_LEVEL_DEF} from '../../app/config'
    import ErrorBox from '../components/ErrorBox'

    export default {
        name: 'VerifyEmail',
        components: {ErrorBox, TextWithLoading},
        data() {
            return {
                loading: false,

                error: null,

                email: '',
            }
        },
        computed: {
            ...mapGetters({
                currentUser: 'account/user',
            }),
            shownEmail: function () {
                return this.currentUser && this.currentUser.email ? this.currentUser.email.email : null
            },
            disabled() {
                return !this.email
            },
        },
        created() {
            this.email = this.shownEmail
        },
        methods: {
            ...mapActions({
                accountMainEmailUpdateAddress: 'account/mainEmailUpdateAddress',
            }),

            onResendSubmitted() {
                this.error = null
                this.loading = true
                this.accountMainEmailUpdateAddress({
                    email: this.email,
                    appVerifyEmailPath: this.$router.getPathByName(APP_ROUTE.verify_email),
                    doneCallback: () => {
                        this.loading = false
                        this.error = {
                            messages: [this.$t('pages._auth._verify_email.succeed')],
                            level: ERROR_LEVEL_DEF.success,
                        }
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.error = {
                            messages: err.getMessages(),
                            extra: err.getExtra(),
                            level: ERROR_LEVEL_DEF.error,
                            class: 'alert-base-red',
                        }
                    },
                })
            },
        },
    }
</script>
