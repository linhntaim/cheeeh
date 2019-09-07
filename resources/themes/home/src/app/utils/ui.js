export class UI {
    constructor() {
        this.selectElement = document.body.createTextRange ? element => {
            let range = document.body.createTextRange()
            range.moveToElementText(element)
            range.select()
        } : (window.getSelection ? element => {
            let selection = window.getSelection()
            let range = document.createRange()
            range.selectNodeContents(element)
            selection.removeAllRanges()
            selection.addRange(range)
        } : () => {
        })
    }

    removeInitializing() {
        $ui('body').removeClass('initializing')
        $ui('#app-initializing').remove()
    }

    setLang(lang) {
        document.querySelector('html').setAttribute('lang', lang)
    }

    openWindow(url = null, target = null, features = null, replace = null) {
        return window.open(url, target, features, replace)
    }

    scrollToTop() {
        $ui('html, body').stop().animate({
            scrollTop: 0,
        }, 500)
    }

    scrollToBottom() {
        $ui('html, body').stop().animate({
            scrollTop: $ui(document).height(),
        }, 500)
    }

    reloadPage() {
        window.location.reload()
    }
}

export const ui = new UI()

export const $ui = (selector) => {
    return window.$(selector)
}
