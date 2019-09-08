<template lang="pug">
    .card.border-none
        .card-body
            h4.font-weight-bold.text-base-pink.mb-3 {{ $t('pages._auth._forgot_password._') }}
            form(@submit.prevent="onLoginSubmitted()")
                .alert.alert-success.small(v-if="succeed") {{ $t('pages._auth._forgot_password.succeed') }}
                .form-group
                    input#inputEmail.form-control.form-control-user(v-model="email" :readonly="succeed" :placeholder="$t('pages.email_address')" type="email" required)
                .form-group
                    button.btn.btn-base-pink(:disabled="loading || disabled" type="submit") {{ $t('pages._auth._forgot_password.submit') }}
            nav
                .mt-2
                    router-link.link-base-pink(:to="{name: 'login'}") {{ $t('actions.back_where', {where: $t('pages._auth._login._')}) }}
                .mt-2
                    router-link.link-base-pink(:to="{name: 'register'}") {{ $t('pages._auth.create_account') }}
</template>

<script>
    import {mapActions} from 'vuex'

    export default {
        name: 'ForgotPassword',
        data() {
            return {
                loading: false,
                succeed: false,

                email: '',
            }
        },
        computed: {
            disabled() {
                return !this.email
            },
        },
        methods: {
            ...mapActions({
                accountForgotPassword: 'account/forgotPassword',
            }),

            onSubmitted() {
                this.succeed = false
                this.loading = true
                this.accountForgotPassword({
                    email: this.email,
                    doneCallback: () => {
                        this.loading = false
                        this.succeed = true
                    },
                    errorCallback: err => {
                        this.loading = false
                        this.$bus.emit('error', {messages: err.getMessages(), extra: err.getExtra()})
                    },
                })
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
