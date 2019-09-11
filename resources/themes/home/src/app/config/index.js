export const APP_ENV = process.env.VUE_APP_ENV
export const APP_DEBUG = process.env.VUE_APP_DEBUG
export const APP_LOG_ONLY = process.env.VUE_APP_LOG_ONLY ? process.env.VUE_APP_LOG_ONLY.split(',') : []
export const APP_TYPE = process.env.VUE_APP_TYPE
export const APP_TYPE_ADMIN = 'admin'
export const APP_TYPE_HOME = 'home'
export const APP_HOST = process.env.VUE_APP_HOST
export const APP_HOST_SELF = 'self'
export const APP_HOST_SUB = 'sub'
export const APP_HOST_SUB_PATH = process.env.VUE_APP_HOST_SUB_PATH
export const APP_NAME = process.env.VUE_APP_NAME
export const APP_URL = window.location.origin + (APP_HOST === APP_HOST_SUB ? APP_HOST_SUB_PATH : '')
export const APP_ADMIN_URL = APP_TYPE === APP_TYPE_ADMIN ? APP_URL : process.env.VUE_APP_ADMIN_URL
export const APP_HOME_URL = APP_TYPE === APP_TYPE_HOME ? APP_URL : process.env.VUE_APP_HOME_URL
export const APP_STATIC_URL = process.env.VUE_APP_STATIC_URL
export const APP_LOGO_URL = {
    original: APP_STATIC_URL + '/sites/logos/logo.png',
    s32: APP_STATIC_URL + '/sites/logos/logo_32x32.png',
    s128: APP_STATIC_URL + '/sites/logos/logo_128x128.png',
    s256: APP_STATIC_URL + '/sites/logos/logo_256x256.png',
    s512: APP_STATIC_URL + '/sites/logos/logo_512x512.png',
    black_original: APP_STATIC_URL + '/sites/logos/logo_black.png',
    black_s32: APP_STATIC_URL + '/sites/logos/logo_black_32x32.png',
    black_s128: APP_STATIC_URL + '/sites/logos/logo_black_128x128.png',
    black_s256: APP_STATIC_URL + '/sites/logos/logo_black_256x256.png',
    black_s512: APP_STATIC_URL + '/sites/logos/logo_black_512x512.png',
    white_original: APP_STATIC_URL + '/sites/logos/logo_white.png',
    white_s32: APP_STATIC_URL + '/sites/logos/logo_white_32x32.png',
    white_s128: APP_STATIC_URL + '/sites/logos/logo_white_128x128.png',
    white_s256: APP_STATIC_URL + '/sites/logos/logo_white_256x256.png',
    white_s512: APP_STATIC_URL + '/sites/logos/logo_white_512x512.png',
}
export const APP_DEFAULT_SERVICE = {
    base_url: process.env.VUE_APP_SERVICE_URL,
    client_id: process.env.VUE_APP_SERVICE_CLIENT_ID,
    client_secret: process.env.VUE_APP_SERVICE_CLIENT_SECRET,
    headers: {
        application: process.env.VUE_APP_SERVICE_HEADER_APPLICATION_NAME,
        localization: process.env.VUE_APP_SERVICE_HEADER_LOCALIZATION_NAME,
        token_authorization: process.env.VUE_APP_SERVICE_HEADER_TOKEN_AUTHORIZATION_NAME,
        basic_authorization: process.env.VUE_APP_SERVICE_HEADER_BASIC_AUTHORIZATION_NAME,
    },
    basic_auth: process.env.VUE_APP_SERVICE_HEADER_BASIC_AUTHORIZATION,
}
export const APP_COOKIE = {
    names: {
        default: process.env.VUE_APP_COOKIE_DEFAULT_NAME,
        device: process.env.VUE_APP_COOKIE_DEVICE_NAME,
        localization: process.env.VUE_APP_COOKIE_LOCALIZATION_NAME,
    },
    disabled: {
        device: process.env.VUE_APP_COOKIE_DISABLE_DEVICE,
        localization: process.env.VUE_APP_COOKIE_DISABLE_LOCALIZATION,
    },
    secret: process.env.VUE_APP_COOKIE_SECRET,
    domain: process.env.VUE_APP_COOKIE_DOMAIN,
}
export const APP_ROUTE = {
    redirect_path_if_authenticated: 'home',
    redirect_path_if_unauthenticated: 'home',
    redirect_path_after_login: 'home',
    redirect_path_after_logout: 'home',
    redirect_path_after_register: 'home',
    bad_request: 'bad_request',
    not_found: 'not_found',
    unauthenticated: 'unauthenticated',
    unauthorized: 'unauthorized',
    reset_password: 'reset_password',
    verify_email: 'verify_email',
}
export const DEFAULT_PREREQUISITE_LIFETIME = 31622400
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
    none: 0,
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
export * from './other'
