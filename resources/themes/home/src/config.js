export const APP_DEBUG = process.env.VUE_APP_DEBUG
export const APP_NAME = process.env.VUE_APP_NAME
export const APP_URL = window.location.origin
export const APP_ADMIN_URL = process.env.VUE_APP_ADMIN_URL
export const APP_STATIC_URL = process.env.VUE_APP_STATIC_URL
export const APP_LOGO_URL = {
    original: APP_STATIC_URL + '/sites/logos/logo.png',
    s32: APP_STATIC_URL + '/sites/logos/logo_32x32.png',
    s128: APP_STATIC_URL + '/sites/logos/logo_128x128.png',
}
export const APP_PASSPORT_PW_CLIENT_ID = process.env.VUE_APP_PASSPORT_PW_CLIENT_ID
export const APP_PASSPORT_PW_CLIENT_SECRET = process.env.VUE_APP_PASSPORT_PW_CLIENT_SECRET
export const APP_COOKIE = {
    names: {
        default: process.env.VUE_APP_COOKIE_DEFAULT_NAME,
        device: process.env.VUE_APP_COOKIE_DEVICE_NAME,
        localization: process.env.VUE_APP_COOKIE_LOCALIZATION_NAME,
    },
    disabled: {
        localization: process.env.VUE_APP_COOKIE_DISABLE_LOCALIZATION,
    },
    secret: process.env.VUE_APP_COOKIE_SECRET,
    domain: process.env.VUE_APP_COOKIE_DOMAIN,
}
export const APP_DEFAULT_SERVICE = {
    base_url: process.env.VUE_APP_SERVICE_URL,
    basic_auth: process.env.VUE_APP_SERVICE_HEADER_BASIC_AUTHORIZATION,
    headers: {
        application: process.env.VUE_APP_SERVICE_HEADER_APPLICATION_NAME,
        localization: process.env.VUE_APP_SERVICE_HEADER_LOCALIZATION_NAME,
        token_authorization: process.env.VUE_APP_SERVICE_HEADER_TOKEN_AUTHORIZATION_NAME,
        basic_authorization: process.env.VUE_APP_SERVICE_HEADER_BASIC_AUTHORIZATION_NAME,
    },
}
export const APP_LOG_ONLY = []
export const APP_PREREQUISITE_LIFETIME = 31622400
export const APP_PATH = {
    verify_email: 'auth/verify-email',
    reset_password: 'auth/reset-password',
}
export const DEFAULT_LOCALIZATION = {
    _from_app: true,
    _ts: 0,
    locale: process.env.VUE_APP_LOCALE,
    country: process.env.VUE_APP_COUNTRY,
    timezone: process.env.VUE_APP_TIMEZONE,
    currency: process.env.VUE_APP_CURRENCY,
    number_format: process.env.VUE_APP_NUMBER_FORMAT,
    first_day_of_week: 0,
    long_date_format: 0,
    short_date_format: 0,
    long_time_format: 0,
    short_time_format: 0,
    long_date_js_format: 'MMMM DD, YYYY',
    long_date_picker_js_format: 'MM dd, yyyy',
    long_time_ps_format: 'HH:mm:ss',
    short_date_js_format: 'YYYY-MM-DD',
    short_date_picker_js_format: 'yyyy-mm-dd',
    short_time_ps_format: 'HH:mm',
    time_offset: 0,
}
export const DEFAULT_DEVICE = {
    provider: 'browser',
    secret: null,
}
export const ITEMS_PER_PAGE_LIST = [10, 20, 50, 100]
export const TOAST_DEF = {
    primary: 'primary',
    success: 'success',
    info: 'info',
    warning: 'warning',
    danger: 'danger',
    secondary: 'secondary',
    default: 'default',
}
export const ERROR_LEVEL = {
    1: {text: 'info'},
    2: {text: 'warning'},
    3: {text: 'error'},
}
export const ERROR_LEVEL_DEF = {
    info: 1,
    warning: 2,
    error: 3,
}
export const ERROR_MESSAGE_LEVEL = {
    1: {text: 'user_failed'},
    2: {text: 'database_failed'},
    3: {text: 'application_failed'},
    4: {text: 'unhandled_error'},
}
export const CLOCK_BLOCK_RANGE = 30
export const CLOCK_BLOCK_KEYS = [
    'u&9zBJT4ztfLQM?Mp22r7ApPx$F3=jGkVMPGzhuxubrG^JawRe9haGpJrL^CaL8X',
    'FazYe6ghQ?n86GTDpFYYt!4AZM%%*Ye48!7w^MRqe?w#yjPLE-Lgq*Uy@jbq+7r*',
    'VMnJMTquv?vvux33G!?D6!jxeKDt?Yfqjx+*e7B6+C@7UU=qe3WsS*QPYxexHjkB',
    'm_dbQ7RH4bXynUr&9H%kVQcZp8gdHz3gqES-QN5nJH8p%yN@Gs@Vmz9Lv5*u6T+P',
    'Q-YfkLfu#8Dg2ZFAQH%ttbemgKudsx&#cWtr6uRW5&bNLNDRvmaD-mc!thtXGQ!9',
    'f^j&3cDj&J4$*-*yw3meFfC_Qc_r^4G+*td87YB58xF2JPUrQ!N68JDWvN*aC!AZ',
    'y?M3x_=tB5S!Dn!^yvKPEPdHs5$7!t^@rvUM2Yd%2gKbS$D&BVw5+LWzLCUJB+S?',
    'R^dANH-e^*?h6UK@uCR_a?dSX%aj7L%!^mM=#xzFY9E*=x3aF9uaLwvHBj4VHCVH',
]
