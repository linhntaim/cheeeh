<template lang="pug">
    .layout
        header.layout-header
            main-header
        .layout-body.with-left-cover
            aside.layout-cover
                home-cover
            section.layout-main.layout-auth
                .container
                    h2.font-weight-bold {{ $t('master.auth.title_1') }}
                    h2.font-weight-bold.mb-0 {{ $t('master.auth.title_2') }}
                    .my-4(v-html="$t('master.auth.desc')")
                    router-view
        footer.layout-footer
            main-footer
</template>

<script>
    import HomeCover from '../components/HomeCover'
    import MainHeader from './MainHeader'
    import MainFooter from './MainFooter'

    export default {
        name: 'Auth',
        components: {HomeCover, MainFooter, MainHeader},
    }
</script>

<style lang="scss" scoped>
    @import "../../assets/css/variables";

    .layout-auth {
        display: flex;
        align-items: center;
        padding-top: 2rem;
        padding-bottom: 2rem;

        .container {
            padding-left: 2rem;
            padding-right: 2rem;
        }
    }

    @media (max-width: $md-max-width) {
        .layout-auth {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
    }

    @media (max-width: $xs-max-width) {
        .layout-auth {
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    }
</style>
