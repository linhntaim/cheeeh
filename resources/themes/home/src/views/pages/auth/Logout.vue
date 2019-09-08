<template lang="pug">
    h4.text-base-red.mt-5
        i.fas.fa-circle-notch.fa-spin
        | &nbsp;&nbsp;{{ $t('actions.logging_out') }}
</template>

<script>
    import {mapActions} from 'vuex'
    import {session} from '../../../app/utils/session'
    import {log} from '../../../app/utils/log'

    export default {
        name: 'Logout',
        data() {
            return {
                loading: false,
            }
        },
        mounted() {
            this.init()
        },
        methods: {
            ...mapActions({
                accountLogout: 'account/logout',
            }),
            init() {
                this.loading = true
                this.accountLogout({
                    alwaysCallback: () => {
                        this.loading = false
                        session.restart()
                        this.$router.push({name: 'home'})
                    },
                })
            },
        },
    }
</script>
