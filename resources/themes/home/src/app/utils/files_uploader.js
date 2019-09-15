import ProgressHandler from './progress_handler'
import {ui} from './ui'
import FileSplitter from './file_splitter'

export class FilesUploader {
    files = []
    progress = new ProgressHandler()

    /**
     *
     * @param {FileList} files
     * @param {function | null} filteredCallback
     * @return {Promise}
     */
    processFiles(files, filteredCallback = null) {
        return new Promise(resolve => {
            let remaining = files.length
            const left = () => {
                --remaining
                if (remaining === 0) {
                    resolve()
                }
            }

            this.progress.increaseLimit(remaining)
            Array.from(files).forEach(file => {
                this.progress.run((progressResolve, progressReject) => {
                    if (!filteredCallback || filteredCallback(file)) {
                        ui.waitRendering(() => {
                            this.files.push({
                                original: file,
                                progress: new ProgressHandler(),
                                preview: null,
                            })

                            progressResolve()
                            left()
                        })
                        return
                    }

                    progressReject()
                    left()
                })
            })
        })
    }

    length() {
        return this.files.length
    }

    clear() {
        this.files = []
        this.progress.reset()
    }

    processPreview() {
        return new Promise(resolve => {
            let remaining = this.files.length
            const left = () => {
                --remaining
                if (remaining === 0) {
                    resolve()
                }
            }

            this.progress.increaseLimit(remaining)
            this.files.forEach(file => {
                this.progress.run((progressResolve, progressReject) => {
                    this.processFilePreview(file, () => {
                        progressResolve()
                        left()
                    }, () => {
                        progressReject()
                        left()
                    })
                })
            })
        })
    }

    processFilePreview(file, doneCallback = null, errorCallback = null) {
        const reader = new FileReader()
        reader.onerror = () => {
            errorCallback && errorCallback()
        }
        reader.onabort = () => {
            errorCallback && errorCallback()
        }
        reader.onload = e => {
            ui.waitRendering(() => {
                file.preview = e.target.result

                doneCallback && doneCallback()
            })
        }
        ui.waitRendering(() => reader.readAsDataURL(file.original))

        return this
    }

    processChunks(chunkSize, everyChunkCallback, everyDoneCallback = null, everyErrorCallback = null, everyPromisedBeforeCallback = null) {
        return new Promise(resolve => {
            let remaining = this.files.length
            const left = () => {
                --remaining
                if (remaining === 0) {
                    resolve()
                }
            }

            this.progress.increaseLimit(remaining)
            this.files.forEach(file => {
                const run = (data = null) => {
                    this.progress.run((progressResolve, progressReject) => {
                        this.processFileChunks(file, chunkSize, everyChunkCallback, data).then(() => {
                            progressResolve()
                            everyDoneCallback && everyDoneCallback(file)
                            left()
                        }).catch(() => {
                            progressReject()
                            everyErrorCallback && everyErrorCallback(file)
                            left()
                        })
                    })
                }

                (everyPromisedBeforeCallback && everyPromisedBeforeCallback(file).then(run).catch(left)) || run()
            })
        })
    }

    processFileChunks(file, chunkSize, chunkCallback, data = null) {
        const fileSplitter = new FileSplitter(file.original, chunkSize)

        return new Promise((resolve, reject) => {
            let remaining = fileSplitter.length()
            const left = () => {
                --remaining
                if (remaining === 0) {
                    resolve()
                }
            }

            file.progress.increaseLimit(remaining)
            fileSplitter.every((chunkData, chunkIndex, chunkTotal) => {
                file.progress.run((progressResolve, progressReject) => {
                    chunkCallback(chunkData, chunkIndex, chunkTotal, () => {
                        progressResolve()
                        left()
                    }, () => {
                        progressReject()
                        reject()
                    }, file, data)
                })
            })
        })
    }
}
