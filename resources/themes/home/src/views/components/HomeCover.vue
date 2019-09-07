<template lang="pug">
    .home-cover
        featured-image-slider.home-image(ref="slider")
        .home-caption
            .text-center.px-3
                h1.caption-title.font-weight-bold Find your inspiration.
                h4.caption-subtitle.mt-3.mb-5.mx-auto Join the Flickr community, home to tens of billions of photos and 2 million groups.
                router-link.btn.btn-light.btn-lg.font-weight-bold.px-5(:class="{'text-base-pink': actionRouteName === 'login', 'text-base-red': actionRouteName === 'register'}" :to="{name: actionRouteName}") {{ actionName }}
</template>

<script>
    import FeaturedImageSlider from './FeaturedImageSlider'
    import routeHelper from '../../app/utils/route_helper'

    export default {
        name: 'HomeCover',
        components: {FeaturedImageSlider},
        data() {
            return {
                actionRouteName: 'login',
                actionName: 'Login',
            }
        },
        watch: {
            '$route'() {
                if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                    this.actionRouteName = 'login'
                    this.actionName = 'Login'
                }
                else {
                    this.actionRouteName = 'register'
                    this.actionName = 'Start for free'
                }

                this.$refs.slider.restart()
            },
        },
        created() {
            if (routeHelper.isHome(this.$route) || routeHelper.isRegister(this.$route)) {
                this.actionRouteName = 'login'
                this.actionName = 'Login'
            }
            else {
                this.actionRouteName = 'register'
                this.actionName = 'Start for free'
            }
        },
    }
</script>

<style lang="scss" scoped>
    @import "../../assets/css/variables";

    .home-cover {
        position: relative;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;
    }

    .home-image {
        position: absolute;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;

        &:after {
            position: absolute;
            left: 0;
            top: 0;
            display: block;
            width: 100%;
            height: 100%;
            content: ' ';
            background-color: rgba(0, 0, 0, .25);
            z-index: 2;
        }
    }

    .home-caption {
        position: absolute;
        left: 0;
        right: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        z-index: 3;

        .caption-subtitle {
            line-height: 1.4;
            width: 62%;
        }
    }

    @media (max-width: $sm-max-width) {
        .home-caption {
            .caption-subtitle {
                width: 80%;
            }
        }
    }

    @media (min-width: $md-min-width) {
        .home-caption {
            .caption-subtitle {
                width: 62%;
            }
        }
    }

    @media (min-width: $lg-min-width) and (max-width: ($sm-max-width + $layout-main-with-left-cover-width-n * $rem)) {
        .home-caption {
            .caption-subtitle {
                width: 80%;
            }
        }
    }
</style>
