<template lang="pug">
    button.btn.btn-danger(v-if="enabled" @click="onClearCacheClicked()") {{ $t('actions.clear_cache') }}
</template>

<script>
    import {mapMutations} from 'vuex'

    export default {
        name: 'ClearCacheButton',
        props: {
            enabled: Boolean,
        },
        methods: {
            ...mapMutations({
                accountUnsetAuth: 'account/unsetAuth',
                accountUnsetUser: 'account/unsetUser',
            }),
            onClearCacheClicked() {
                this.accountUnsetAuth()
                this.accountUnsetUser()

                this.$router.push({name: 'home'})
            },
        },
    }
</script>
