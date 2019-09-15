<template lang="pug">
    div
        .controls.py15x2
            .actions
                .action-upload.custom-file(:class="{'w-auto float-left': files.length}")
                    input#inputFile.custom-file-input.as-btn.btn-base-pink(@change="onFileChanged" name="files[]" type="file" multiple)
                    label.custom-file-label(for="inputFile")
                        i.fas.fa-upload.fa-fw.mr-2
                        | {{ $t('actions.upload') }}
                .action-other(v-if="files.length")
                    button.btn.btn-warning.text-white(@click="onClearClicked")
                        i.far.fa-trash-alt.fa-fw.mr-2
                        | {{ $t('actions.remove_all') }}
            .progress.mt15x2.h8(v-if="files.length && !progress.completed")
                .progress-bar.progress-bar-striped.progress-bar-animated.bg-base-dark(:style="{width: progress.percentageText}" role="progressbar")
        .row.square-row.mt15xn.mb15x(v-if="files.length")
            .position-relative(v-for="file in files" :class="{'col-xs-12 col-sm-6 col-md-4 col-lg-3': files.length > 3, 'col-xs-12 col-sm-6 col-md-4': files.length === 3, 'col-sm-6': files.length === 2, 'col-xs-12': files.length === 1}")
                .filled.p15x
                    .inner-filled.d-flex-center.bg-base-dark-lighter.border.border-dark-o2
                        img.img-max(v-if="file.preview" :src="file.preview")
                        i.fas.fa-circle-notch.fa-spin.text-base-dark(v-else)
</template>

<script>
    import {FilesUploader} from '../../app/utils/files_uploader'
    import helpers from '../../app/utils/helpers'
    import {accountUploadService} from '../../app/services/default/account_upload'

    const CHUNK_SIZE = 1024 * 1024 * 2 // 2MB

    export default {
        name: 'Upload',
        data() {
            return {
                filesUploader: new FilesUploader(),
            }
        },
        computed: {
            files() {
                return this.filesUploader.files
            },
            progress() {
                return this.filesUploader.progress
            },
        },
        created() {
            this.$bus.emit('baseCentered')
            this.$bus.emit('baseDarkener')
        },
        methods: {
            onFileChanged($event) {
                this.filesUploader.processFiles($event.target.files, (file) => {
                    return helpers.string.startWith(file.type, 'image/')
                }).then(() => {
                    this.filesUploader.processPreview().then(() => {
                        const service = accountUploadService()

                        this.filesUploader.processChunks(CHUNK_SIZE, (chunkData, chunkIndex, chunkTotal, doneCallback, errorCallback, file, data) => {
                            const fileId = data
                            service.chunkUpload(
                                fileId,
                                chunkIndex,
                                chunkTotal,
                                chunkData,
                                data => {
                                    doneCallback()
                                },
                                err => {
                                    errorCallback()
                                },
                            )
                        }, file => {
                            console.log('done')
                        }, file => {
                            console.log('error')
                        }, () => new Promise((resolve, reject) => {
                            service.chunkUploadInit(data => {
                                resolve(data.model.file_id)
                            }, err => {
                                reject()
                            })
                        }))
                    })
                })
                $event.target.value = ''
            },

            onClearClicked() {
                this.filesUploader.clear()
            },
        },
    }
</script>

<style lang="scss" scoped>
    @import '../../assets/css/variables';

    .controls {
        position: sticky;
        top: 0;
        left: 0;
        right: 0;
        background-color: $color-base-dark;
        z-index: 999;

        .actions {
            display: flex;
            justify-content: space-between;

            .action-other {
                flex-shrink: 0;
                margin-left: $d15x;
            }
        }
    }
</style>
