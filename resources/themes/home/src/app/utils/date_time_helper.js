import {DEFAULT_LOCALIZATION} from '../config'

export class DateTimeHelper {
    constructor() {
        this.compiler = null
        this.localize(DEFAULT_LOCALIZATION)
    }

    withCompiler(compiler) {
        this.compiler = compiler
        return this
    }

    localize(localization) {
        this.longDateFormat = 'def.datetime.long_date_' + localization.long_date_format
        this.shortDateFormat = 'def.datetime.short_date_' + localization.short_date_format
        this.longTimeFormat = 'def.datetime.long_time_' + localization.long_time_format
        this.shortTimeFormat = 'def.datetime.short_time_' + localization.short_time_format
        return this
    }

    getBags() {
        return {
            d: 'D',
            dd: 'DD',
            sd: 'ddd',
            ld: 'dddd',

            m: 'M',
            mm: 'MM',
            sm: 'MMM',
            lm: 'MMMM',

            yy: 'YY',
            yyyy: 'YYYY',

            h: 'h',
            hh: 'hh',
            h2: 'H',
            hh2: 'HH',
            ii: 'mm',
            ss: 'ss',

            lt: 'a',
            ut: 'A',
        }
    }

    getFormat(format, bags = null) {
        return this.compiler ? this.compiler(format, bags ? bags : this.getBags()) : null
    }

    getLongDateFormat(bags = null) {
        return this.getFormat(this.longDateFormat, bags)
    }

    getShortDateFormat(bags = null) {
        return this.getFormat(this.shortDateFormat, bags)
    }

    getLongTimeFormat(bags = null) {
        return this.getFormat(this.longTimeFormat, bags)
    }

    getShortTimeFormat(bags = null) {
        return this.getFormat(this.shortTimeFormat, bags)
    }
}

export const dateTimeHelper = new DateTimeHelper()
