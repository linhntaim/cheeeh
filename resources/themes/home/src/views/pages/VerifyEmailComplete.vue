<template lang="pug">
    .row.justify-content-center
        .col-xl-10.col-lg-12.col-md-9
            .card.border-0.shadow-sm.my-3.my-md-5
                .card-body.text-center.p-5
                    h4.font-weight-bold.text-base-red.mb-3 {{ $t('pages._auth._verify_email._') }}
                    div(v-if="!verified")
                        .mb-3(v-if="loading" v-html="$t('pages._auth._verify_email.waiting', {email: email})")
                        error-box.alert-base-red(:error="error")
                        i.fas.fa-circle-notch.fa-spin.text-base-red(v-if="loading")
                        form(v-else @submit.prevent="onVerifySubmitted()")
                            button.btn.btn-base-red(type="submit") {{ $t('actions.verify') }}
                    div(v-else)
                        .mb-5(v-html="$t('pages._auth._verify_email.done', {email: email})")
                        router-link.link-base-red(v-if="accountIsLoggedIn" :to="{name: 'home'}")
                            | {{ $t('actions.go_where', {where: $t('pages._home._')}) }}
                            span(v-if="countDown.enabled") &nbsp;({{ countDown.counter }})
                        router-link.link-base-pink(v-else :to="{name: 'login'}")
                            | {{ $t('actions.go_where', {where: $t('pages._auth._login._')}) }}
                            span(v-if="countDown.enabled") &nbsp;({{ countDown.counter }})
</template>

<script>
    import {mapActions, mapGetters} from 'vuex'
    import {ui} from '../../app/utils/ui'
    import {CountDown} from '../../app/utils/count_down'
    import {ERROR_LEVEL_DEF} from '../../app/config'
    import ErrorBox from '../components/ErrorBox'

    export default {
        name: 'DoVerifyEmail',
        components: {ErrorBox},
        data() {
            return {
                loading: true,

                error: null,

                verified: false,

                email: this.$route.params.email,
                verifiedCode: this.$route.params.verified_code,

                countDown: new CountDown(),
            }
        },
        computed: {
            ...mapGetters({
                accountIsLoggedIn: 'account/isLoggedIn',
            }),
        },
        mounted() {
            this.onVerifySubmitted()
        },
        methods: {
            ...mapActions({
                accountVerifyEmail: 'account/verifyEmail',
                accountReload: 'account/reload',
            }),

            onVerifySubmitted() {
                this.loading = true
                this.error = null
                this.accountVerifyEmail({
                    email: this.email,
                    verifiedCode: this.verifiedCode,
                    doneCallback: () => {
                        this.verified = true

                        if (this.accountIsLoggedIn) {
                            this.accountReload({
                                doneCallback: () => {
                                    this.loading = false

                                    this.countDown.start(10, () => {
                                        this.$router.push({name: 'home'})
                                    })
                                },
                                errorCallback: () => {
                                    ui.reloadPage()
                                },
                            })
                        } else {
                            this.loading = false

                            this.countDown.start(10, () => {
                                this.$router.push({name: 'login'})
                            })
                        }
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
        },
    }
</script>
