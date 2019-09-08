import {app} from './app'
import {log} from './log'
import {ui} from './ui'

export class Localizer {
    constructor() {
        this.loadedLocales = []
    }

    localize(localization) {
        return this.set(localization.locale)
    }

    set(locale) {
        return app.get().then(appInstance => {
            let i18n = appInstance.$i18n
            if (i18n.locale !== locale) {
                log.write('changed from ' + i18n.locale + ' to ' + locale, 'locale')

                let apply = (lang) => {
                    i18n.locale = lang
                    ui.setLang(lang)
                    return lang
                }
                if (!this.loadedLocales.includes(locale)) {
                    return import(`../locales/lang/${locale}`).then(m => {
                        i18n.setLocaleMessage(locale, m.default)
                        this.loadedLocales.push(locale)
                        return apply(locale)
                    })
                }
                return Promise.resolve(apply(locale))
            }
            return Promise.resolve(locale)
        })
    }

}

export const localizer = new Localizer()
