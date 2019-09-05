import Vue from 'vue'
import VueI18n from 'vue-i18n'
import {DEFAULT_LOCALIZATION} from '../../config'

Vue.use(VueI18n)

export default (callback) => {
    let defaultLocale = DEFAULT_LOCALIZATION.locale

    import(`./lang/${defaultLocale}`).then(m => {
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
