<template lang="pug">
    .card.border-none
        .card-body
            h4.font-weight-bold.text-base-red.mb-3 {{ $t('pages._auth._verify_email._') }}
            div(v-html="$t('pages._auth._verify_email.desc', {email: shownEmail})")
            .small.text-warning {{ $t('pages._auth._verify_email.help') }}
</template>

<script>
    import {mapGetters, mapActions} from 'vuex'

    export default {
        name: 'VerifyEmail',
        data() {
            return {
                loading: false,
                succeed: false,

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
            ...mapActions({}),

            init() {
            },
        },
    }
</script>
