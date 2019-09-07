<template lang="pug">
    .featured-image-slider
        img.featured-image.image-next(v-if="nextUrl" :src="nextUrl" @load="onNextImageLoaded")
        .featured-image.image-current(v-if="currentUrl" :style="{'background-image': 'url(' + currentUrl + ')'}")
        .featured-image.image-loading(v-else)
            i.fas.fa-circle-notch.fa-spin(v-if="loaderEnabled")
</template>

<script>
    import {timeoutCaller} from '../../app/utils/timeout_caller'

    const SLIDER_TIMEOUT = 5000 // 5s

    export default {
        name: 'FeaturedImageSlider',
        props: {
            timeout: {
                type: Number,
                default: SLIDER_TIMEOUT,
            },
            loaderEnabled: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                currentUrl: null,
                nextUrl: null,

                urls: [
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/comfortablydumb_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/arches_within_arches_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/parent_and_child_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/emotions_in_camargue_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/a_magical_dawn_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/crazy_sky_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/eyetoeye_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/wintertimehappiness_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/breathe_small.jpg',
                    'https://combo.staticflickr.com/ap/build/images/sohp/2018-top-25/burrowing_owl_juvenile_salton_sea_small.jpg',
                ],
                urlIndex: -1,
            }
        },
        mounted() {
            this.next()
        },
        methods: {
            restart() {
                timeoutCaller.register(() => {
                    this.next()
                }, this.timeout)
            },

            next() {
                ++this.urlIndex
                if (this.urlIndex >= this.urls.length) {
                    this.urlIndex = 0
                }

                this.nextUrl = this.urls[this.urlIndex]
            },

            onNextImageLoaded() {
                this.currentUrl = this.nextUrl
                this.restart()
            },
        },
    }
</script>

<style lang="scss" scoped>
    .featured-image-slider {
        position: relative;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    .featured-image {
        position: absolute;
        left: 0;
        top: 0;

        &.image-current {
            width: 100%;
            height: 100%;
            background-color: #fff;
            background-position: 50%;
            background-size: cover;
            background-repeat: no-repeat;

            z-index: 1;
        }

        &.image-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background-color: #000;
            color: rgba(255, 255, 255, .5);

            z-index: 1;
        }
    }
</style>
