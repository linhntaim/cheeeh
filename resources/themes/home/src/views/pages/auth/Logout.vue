<template lang="pug">
    div
</template>

<script>
    import {mapActions} from 'vuex'
    import {session} from '../../../app/utils/session'

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
                        this.$router.push('/')
                    },
                })
            },
        },
    }
</script>
