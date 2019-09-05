export class ImageSizer {
    get(url, width, height) {
        let parts = url.split('.')
        parts.push('s' + width + 'x' + height, parts.pop())
        return parts.join('.')
    }
}

export const imageSizer = new ImageSizer()
