<template lang="pug">
    h4.text-base-red.mt-5
        text-with-loading(:loading="true" :text="$t('actions.logging_out')" :both="true")
</template>

<script>
    import {mapActions} from 'vuex'
    import {notAuthRoutes} from '../../../app/routing/router/routes'
    import {session} from '../../../app/utils/session'
    import TextWithLoading from '../../components/TextWithLoading'

    export default {
        name: 'Logout',
        components: {TextWithLoading},
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
                        this.$router.replaceRoutes(notAuthRoutes).push({name: 'home'})
                    },
                })
            },
        },
    }
</script>
