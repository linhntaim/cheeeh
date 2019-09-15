export default class FileSplitter {
    chunks = []

    /**
     *
     * @param {File} file
     * @param {Number} chunkSize
     */
    constructor(file, chunkSize = 1024 * 1024 * 2) {
        const numChunks = Math.ceil(file.size / chunkSize)
        for (let i = 0; i < numChunks; ++i) {
            this.chunks.push(file.slice(i * chunkSize, (i + 1) * chunkSize))
        }
    }

    every(callback) {
        this.chunks.forEach((chunk, index) => {
            callback(chunk, index, this.length())
        })
    }

    length() {
        return this.chunks.length
    }
}
