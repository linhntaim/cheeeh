import {APP_COOKIE} from '../../config'
import CookieStore from './cookie_store'

class LocalizationCookieStore extends CookieStore {
    constructor() {
        super(APP_COOKIE.names.localization)
    }

    retrieve() {
        if (APP_COOKIE.disabled.localization) return null
        return super.retrieve()
    }

    store(localization) {
        if (APP_COOKIE.disabled.localization) return null
        let storedLocalization = {
            _ts: localization._ts,
            locale: localization.locale,
            country: localization.country,
            timezone: localization.timezone,
            currency: localization.currency,
            number_format: localization.number_format,
            first_day_of_week: localization.first_day_of_week,
            long_date_format: localization.long_date_format,
            short_date_format: localization.short_date_format,
            long_time_format: localization.long_time_format,
            short_time_format: localization.short_time_format,
            long_date_js_format: localization.long_date_js_format,
            long_date_picker_js_format: localization.long_date_picker_js_format,
            long_time_ps_format: localization.long_time_ps_format,
            short_date_js_format: localization.short_date_js_format,
            short_date_picker_js_format: localization.short_date_picker_js_format,
            short_time_ps_format: localization.short_time_ps_format,
            time_offset: localization.time_offset,
        }
        if (localization._from_app) {
            storedLocalization._from_app = localization._from_app
        }
        return super.store(storedLocalization)
    }
}

export default new LocalizationCookieStore()
