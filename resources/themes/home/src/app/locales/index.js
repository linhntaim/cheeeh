import {localizer} from '../utils/localizer'
import {DEFAULT_LOCALIZATION} from '../config'
import localizationCookieStore from '../utils/cookie_store/localization_cookie_store'
import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

export default callback => {
    const storedLocalization = localizationCookieStore.retrieve()
    const defaultLocale = storedLocalization ? storedLocalization.locale : DEFAULT_LOCALIZATION.locale

    import(`./lang/${defaultLocale}`).then(m => {
        localizer.loadedLocales.push(defaultLocale)
        callback(new VueI18n({
            locale: defaultLocale,
            fallbackLocale: defaultLocale,
            silentFallbackWarn: true,
            messages: (() => {
                const messages = {}
                messages[defaultLocale] = m.default
                return messages
            })(),
        }))
    })
}
