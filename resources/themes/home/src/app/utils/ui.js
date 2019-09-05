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

    setLang(lang) {
        document.querySelector('html').setAttribute('lang', lang)
    }

    startPageLoading() {
        let $body = $ui('body')
        if (!$body.hasClass('initializing')) {
            $ui('#page-pop-loading').removeClass('hide')
            $body.addClass('has-page-popping')
        }
    }

    openWindow(url = null, target = null, features = null, replace = null) {
        return window.open(url, target, features, replace)
    }

    stopPageLoading() {
        $ui('body').removeClass('has-page-popping').removeClass('initializing')
        $ui('#page-pop-loading').addClass('hide')
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
