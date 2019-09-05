import CryptoJS from 'crypto-js'

export class Crypto {
    encrypt(text, secret) {
        return CryptoJS.AES.encrypt(text, secret).toString()
    }

    decrypt(text, secret) {
        return CryptoJS.AES.decrypt(text, secret).toString(CryptoJS.enc.Utf8)
    }

    encryptJson(data, secret) {
        return this.encrypt(JSON.stringify(data), secret)
    }

    decryptJson(text, secret) {
        return JSON.parse(this.decrypt(text, secret))
    }
}

export const crypto = new Crypto()
