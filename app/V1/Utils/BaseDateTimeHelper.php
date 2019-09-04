<?php

namespace App\V1\Utils;

use App\V1\Exceptions\AppException;
use DateInterval;
use DateTime;
use DateTimeZone;

abstract class BaseDateTimeHelper
{
    const LONG_DATE_FUNCTION = 'longDate';
    const SHORT_DATE_FUNCTION = 'shortDate';
    const LONG_TIME_FUNCTION = 'longTime';
    const SHORT_TIME_FUNCTION = 'shortTime';
    const DATABASE_FORMAT = 'Y-m-d H:i:s';
    const DATABASE_FORMAT_DATE = 'Y-m-d';
    const DATABASE_FORMAT_TIME = 'H:i:s';
    const DAY_TYPE_NONE = 0;
    const DAY_TYPE_START = -1;
    const DAY_TYPE_END = 1;
    const DAY_TYPE_NEXT_START = 2;

    protected static $instance;
    protected static $now;

    /**
     * @return BaseDateTimeHelper
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public static function syncNowObject($reset = false)
    {
        if ($reset || empty(static::$now)) {
            static::$now = new DateTime('now', new DateTimeZone('UTC'));
        }
        return static::$now;
    }

    public static function syncNowFormat($format, $reset = false)
    {
        return static::syncNowObject($reset)->format($format);
    }

    public static function syncNow($reset = false)
    {
        return static::syncNowFormat(static::DATABASE_FORMAT, $reset);
    }

    private $locale;
    private $transLongDate;
    private $transShortDate;
    private $transShortMonth;
    private $transLongTime;
    private $transShortTime;

    /**
     * Seconds
     *
     * @var float|int
     */
    private $dateTimeOffset;

    public function __construct(LocalizationHelper $localizationHelper = null)
    {
        if ($localizationHelper == null) {
            $localizationHelper = LocalizationHelper::getInstance();
        }

        $this->locale = $localizationHelper->getLocale();

        $this->transLongDate = 'datetime.long_date_' . $localizationHelper->getLongDateFormat();
        $this->transShortDate = 'datetime.short_date_' . $localizationHelper->getShortDateFormat();
        $this->transShortMonth = 'datetime.short_month_' . $localizationHelper->getShortDateFormat();
        $this->transLongTime = 'datetime.long_time_' . $localizationHelper->getLongTimeFormat();
        $this->transShortTime = 'datetime.short_time_' . $localizationHelper->getShortTimeFormat();

        $this->dateTimeOffset = static::parseDateTimeOffsetByTimezone($localizationHelper->getTimezone());
    }

    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getDateTimeOffset()
    {
        return $this->dateTimeOffset;
    }

    #region From Local Time to UTC
    public function from(DateTime $time, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $time = static::applyStartType($time, $start);
        if (!$noOffset) {
            $offset = $this->getDateTimeOffset();
            if ($offset > 0) {
                $day = $time->format('d');
                $time->sub(new DateInterval('PT' . $offset . 'S'));
                if ($time->format('d') != $day) $diffDay = -1;
            } elseif ($offset < 0) {
                $day = $time->format('d');
                $time->add(new DateInterval('PT' . abs($offset) . 'S'));
                if ($time->format('d') != $day) $diffDay = 1;
            }
        }
        return $time;
    }

    public function fromFormat($format, $inputString, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from(DateTime::createFromFormat($format, $inputString, new DateTimeZone('UTC')), $noOffset, $diffDay, $start);
    }

    public function fromFormatToFormat($currentFormat, $inputString, $toFormat = null, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        if (empty($toFormat)) $toFormat = $currentFormat;
        $now = $this->fromFormat($currentFormat, $inputString, $noOffset, $diffDay, $start);
        return $now !== false ? $now->format($toFormat) : false;
    }

    public function fromFormatToDatabaseFormat($currentFormat, $inputString, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->fromFormatToFormat($currentFormat, $inputString, static::DATABASE_FORMAT, $noOffset, $diffDay, $start);
    }

    public function fromToFormat(DateTime $time, $toFormat, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $noOffset, $diffDay, $start)
            ->format($toFormat);
    }

    public function fromToDatabaseFormat(DateTime $time, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $noOffset, $diffDay, $start)
            ->format(static::DATABASE_FORMAT);
    }

    public function convertToUTC(DateTime $time)
    {
        $offset = $this->getDateTimeOffset();
        if ($offset > 0) {
            $time->sub(new DateInterval('PT' . $offset . 'S'));
        } elseif ($offset < 0) {
            $time->add(new DateInterval('PT' . abs($offset) . 'S'));
        }
        return $time;
    }
    #endregion

    #region From UTC to Local Time
    public function getObject($time = 'now', $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $now = $time instanceof DateTime ? $time : new DateTime($time, new DateTimeZone('UTC'));
        if (!$noOffset) {
            $offset = $this->getDateTimeOffset();
            if ($offset > 0) {
                $day = $now->format('d');
                $now->add(new DateInterval('PT' . $offset . 'S'));
                if ($now->format('d') != $day) $diffDay = 1;
            } elseif ($offset < 0) {
                $day = $now->format('d');
                $now->sub(new DateInterval('PT' . abs($offset) . 'S'));
                if ($now->format('d') != $day) $diffDay = -1;
            }
        }
        return static::applyStartType($now, $start);
    }

    public function getBags($time = 'now', $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $now = $this->getObject($time, $noOffset, $diffDay, $start);
        return [
            'ld' => $now->format('l'),
            'sd' => $now->format('D'),
            '1d' => $now->format('j'),
            '2d' => $now->format('d'),
            'sm' => $now->format('M'),
            'lm' => $now->format('F'),
            '2m' => $now->format('m'),
            '2y' => $now->format('y'),
            '4y' => $now->format('Y'),
            '1h' => $now->format('g'),
            '1hf' => $now->format('h'),
            '2h' => $now->format('H'),
            '2i' => $now->format('i'),
            '2s' => $now->format('s'),
            'ut' => $now->format('A'),
            'lt' => $now->format('a'),
            'lw' => static::transPhpDayOfWeek($now->format('w')),
            'sw' => static::transShortPhpDayOfWeek($now->format('w')),
        ];
    }

    public function compound($func_1 = BaseDateTimeHelper::SHORT_DATE_FUNCTION, $separation = ' ', $func_2 = BaseDateTimeHelper::SHORT_TIME_FUNCTION, $time = 'now', $no_offset = false)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func_1, $allowedFunctions) || !in_array($func_2, $allowedFunctions)) {
            throw new AppException('Not allowed methods');
        }
        return call_user_func(array($this, $func_1), $time, $no_offset)
            . $separation
            . call_user_func(array($this, $func_2), $time, $no_offset);
    }

    public function compoundBags($func_1 = BaseDateTimeHelper::SHORT_DATE_FUNCTION, $separation = ' ', $func_2 = BaseDateTimeHelper::SHORT_TIME_FUNCTION, array $bags = [])
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func_1, $allowedFunctions) || !in_array($func_2, $allowedFunctions)) {
            throw new AppException('Not allowed methods');
        }
        $func_1 .= 'FromBags';
        $func_2 .= 'FromBags';
        return call_user_func(array($this, $func_1), $bags)
            . $separation
            . call_user_func(array($this, $func_2), $bags);
    }

    public function longDayOfWeek($time = 'now', $no_offset = false)
    {
        return $this->getBags($time, $no_offset)['lw'];
    }

    public function shortDayOfWeek($time = 'now', $no_offset = false)
    {
        return $this->getBags($time, $no_offset)['sw'];
    }

    public function longDate($time = 'now', $no_offset = false)
    {
        return $this->longDateFromBags($this->getBags($time, $no_offset));
    }

    public function shortDate($time = 'now', $no_offset = false)
    {
        return $this->shortDateFromBags($this->getBags($time, $no_offset));
    }

    public function shortMonth($time = 'now', $no_offset = false)
    {
        return $this->shortMonthFromBags($this->getBags($time, $no_offset));
    }

    public function longTime($time = 'now', $no_offset = false)
    {
        return $this->longTimeFromBags($this->getBags($time, $no_offset));
    }

    public function shortTime($time = 'now', $no_offset = false)
    {
        return $this->shortTimeFromBags($this->getBags($time, $no_offset));
    }

    public function longDateFromBags(array $bags)
    {
        return trans($this->transLongDate, $bags, $this->locale);
    }

    public function shortDateFromBags(array $bags)
    {
        return trans($this->transShortDate, $bags, $this->locale);
    }

    public function shortMonthFromBags(array $bags)
    {
        return trans($this->transShortMonth, $bags, $this->locale);
    }

    public function longTimeFromBags(array $bags)
    {
        return trans($this->transLongTime, $bags, $this->locale);
    }

    public function shortTimeFromBags(array $bags)
    {
        return trans($this->transShortTime, $bags, $this->locale);
    }

    #region Get format
    public function customFormat($name)
    {
        return trans('datetime.custom.' . $name, static::getFormatBags(), $this->locale);
    }

    public function compoundFormat($func1, $separation, $func2)
    {
        return call_user_func(array($this, $func1 . 'Format'))
            . $separation
            . call_user_func(array($this, $func2 . 'Format'));
    }

    public function longDateFormat()
    {
        return $this->longDateFromBags(static::getFormatBags());
    }

    public function longDateJsFormat()
    {
        return $this->longDateFromBags(static::getMomentJsFormatBags());
    }

    public function longDatePickerJsFormat()
    {
        return $this->longDateFromBags(static::getDatePickerJsFormatBags());
    }

    public function shortDateFormat()
    {
        return $this->shortDateFromBags(static::getFormatBags());
    }

    public function shortMonthFormat()
    {
        return $this->shortMonthFromBags(static::getFormatBags());
    }

    public function shortDateJsFormat()
    {
        return $this->shortDateFromBags(static::getMomentJsFormatBags());
    }

    public function shortDatePickerJsFormat()
    {
        return $this->shortDateFromBags(static::getDatePickerJsFormatBags());
    }

    public function shortMonthPickerJsFormat()
    {
        return $this->shortMonthFromBags(static::getDatePickerJsFormatBags());
    }

    public function longTimeFormat()
    {
        return $this->longTimeFromBags(static::getFormatBags());
    }

    public function longTimeJsFormat()
    {
        return $this->longTimeFromBags(static::getMomentJsFormatBags());
    }

    public function shortTimeFormat()
    {
        return $this->shortTimeFromBags(static::getFormatBags());
    }

    public function shortTimeJsFormat()
    {
        return $this->shortTimeFromBags(static::getMomentJsFormatBags());
    }

    public function shortDateAndroidFormat()
    {
        return $this->shortDateFromBags(static::getAndroidFormatBags());
    }

    public function longDateAndroidFormat()
    {
        return $this->longDateFromBags(static::getAndroidFormatBags());
    }

    public function shortTimeAndroidFormat()
    {
        return $this->shortTimeFromBags(static::getAndroidFormatBags());
    }

    public function longTimeAndroidFormat()
    {
        return $this->longTimeFromBags(static::getAndroidFormatBags());
    }
    #endregion

    #region To Format
    public function format($format, $time = 'now', $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->getObject($time, $noOffset, $diffDay, $start)
            ->format($format);
    }
    #endregion

    #endregion

    #region Static Methods
    public static function parseDateTimeOffsetByTimezone($timeZone)
    {
        if (empty($timeZone)) {
            return 0;
        }
        if ($timeZone != 'UTC' && strpos($timeZone, 'UTC') === 0) {
            return floatval(str_replace('UTC', '', $timeZone)) * 3600;
        }
        $currentTimeZone = new DateTimeZone($timeZone);
        return $currentTimeZone->getOffset(new DateTime('now', new DateTimeZone('UTC')));
    }

    public static function applyStartType(DateTime $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        if ($start == static::DAY_TYPE_NEXT_START) {
            $time->setTime(0, 0, 0)->add(new DateInterval('P1D'));
        } elseif ($start == static::DAY_TYPE_END) {
            $time->setTime(23, 59, 59);
        } elseif ($start == static::DAY_TYPE_START) {
            $time->setTime(0, 0, 0);
        }
        return $time;
    }

    public static function dayOfWeek($phpDayOfWeek)
    {
        return $phpDayOfWeek == 0 ? 6 : ($phpDayOfWeek - 1);
    }

    public static function phpDayOfWeek($dayOfWeek)
    {
        return $dayOfWeek == 6 ? 0 : ($dayOfWeek + 1);
    }

    public static function transPossiblePhpTexts($formatted, $locale = null)
    {
        $possibleTexts = [
            'Jan', 'January',
            'Feb', 'February',
            'Mar', 'March',
            'Apr', 'April',
            'May',
            'Jun', 'Jun',
            'July', 'Jul',
            'Aug', 'August',
            'Sep', 'September',
            'Oct', 'October',
            'Nov', 'November',
            'Dec', 'December',
            'Mon', 'Monday',
            'Tue', 'Tuesday',
            'Wed', 'Wednesday',
            'Thu', 'Thursday',
            'Fri', 'Friday',
            'Sat', 'Saturday',
            'Sun', 'Sunday',
        ];
        return str_replace($possibleTexts, array_map(function ($text) use ($locale) {
            return trans('datetime.texts.' . $text, [], $locale);
        }, $possibleTexts), $formatted);
    }

    public static function transDayOfWeek($dayOfWeek, $locale = null)
    {
        return trans('datetime.day_' . $dayOfWeek, [], $locale);
    }

    public static function transPhpDayOfWeek($phpDayOfWeek, $locale = null)
    {
        return trans('datetime.day_' . static::dayOfWeek($phpDayOfWeek), [], $locale);
    }

    public static function transShortDayOfWeek($dayOfWeek, $locale = null)
    {
        return trans('datetime.short_day_' . $dayOfWeek, [], $locale);
    }

    public static function transShortPhpDayOfWeek($phpDayOfWeek, $locale = null)
    {
        return trans('datetime.short_day_' . static::dayOfWeek($phpDayOfWeek), [], $locale);
    }

    /**
     * Get list timezone
     * @return array
     */
    public static function getTimezones()
    {
        // UTC
        $timezones = [
            [
                'name' => 'UTC',
                'timezones' => [
                    [
                        'name' => 'UTC',
                        'value' => 'UTC',
                    ]
                ]
            ],
        ];
        // UTC offsets
        $utcOffsets = [];
        $offsetRange = [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
            0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14];
        foreach ($offsetRange as $offset) {
            $offsetValue = 'UTC' . (0 <= $offset ? '+' . $offset : (string)$offset);
            $offsetName = str_replace(['.25', '.5', '.75'], [':15', ':30', ':45'], $offsetValue);
            $utcOffsets[] = [
                'name' => $offsetName,
                'value' => $offsetValue,
            ];
        }
        $timezones[] = [
            'name' => trans('datetime.utc_offsets'),
            'timezones' => $utcOffsets,
        ];
        // UNIX Timezones
        $unixTimezones = [];
        $currentContinent = null;
        foreach (DateTimeZone::listIdentifiers() as $zone) {
            $zonePart = explode('/', $zone);
            $continent = $zonePart[0];

            if ($continent == 'UTC') continue;

            if (!empty($currentContinent) && $continent != $currentContinent) {
                $timezones[] = [
                    'name' => $currentContinent,
                    'timezones' => $unixTimezones,
                ];
                $unixTimezones = [];
            }
            $currentContinent = $continent;
            $city = isset($zonePart[1]) ? $zonePart[1] : '';
            $subCity = isset($zonePart[2]) ? $zonePart[2] : '';
            $unixTimezones[] = [
                'name' => str_replace('_', ' ', $city) . (empty($subCity) ? '' : ' - ' . str_replace('_', ' ', $subCity)),
                'value' => $zone,
            ];
        }
        $timezones[] = [
            'name' => $currentContinent,
            'timezones' => $unixTimezones,
        ];
        return $timezones;
    }

    public static function getTimezoneValues()
    {
        // UTC
        $timezones = ['UTC'];
        // UTC offsets
        $offsetRange = [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
            0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14];
        foreach ($offsetRange as $offset) {
            $timezones[] = 'UTC' . (0 <= $offset ? '+' . $offset : (string)$offset);
        }
        // UNIX Timezones
        foreach (DateTimeZone::listIdentifiers() as $zone) {
            $timezones[] = $zone;
        }
        return $timezones;
    }

    public static function getDaysOfWeek()
    {
        $options = [];
        for ($i = 0; $i < 7; ++$i) {
            $options[] = [
                'value' => $i,
                'name' => trans('datetime.day_' . $i),
            ];
        }
        return $options;
    }

    public static function getDaysOfWeekValues()
    {
        return range(0, 6);
    }

    protected static function getExampleBags()
    {
        return static::getInstance()->getBags(date('Y') . '-12-24 08:00:00', true);
    }

    public static function getLongDateFormats()
    {
        $options = [];
        for ($i = 0; $i < 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_date_' . $i, static::getExampleBags()),
            ];
        }
        return $options;
    }

    public static function getLongDateFormatValues()
    {
        return range(0, 3);
    }

    public static function getShortDateFormats()
    {
        $options = [];
        for ($i = 0; $i < 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_date_' . $i, static::getExampleBags()),
            ];
        }
        return $options;
    }

    public static function getShortDateFormatValues()
    {
        return range(0, 3);
    }

    public static function getLongTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_time_' . $i, static::getExampleBags()),
            ];
        }
        return $options;
    }

    public static function getLongTimeFormatValues()
    {
        return range(0, 4);
    }

    public static function getShortTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_time_' . $i, static::getExampleBags()),
            ];
        }
        return $options;
    }

    public static function getShortTimeFormatValues()
    {
        return range(0, 4);
    }

    public static function getFormatBags()
    {
        return [
            'ld' => 'l',
            'sd' => 'D',
            '1d' => 'j',
            '2d' => 'd',
            'sm' => 'M',
            'lm' => 'F',
            '1m' => 'n',
            '2m' => 'm',
            '2y' => 'y',
            '4y' => 'Y',
            '1h' => 'g',
            '1hf' => 'h',
            '2h' => 'H',
            '2i' => 'i',
            '2s' => 's',
            'ut' => 'A',
            'lt' => 'a',
        ];
    }

    public static function getDatePickerJsFormatBags()
    {
        return [
            'ld' => 'DD',
            'sd' => 'D',
            '1d' => 'd',
            '2d' => 'dd',
            'sm' => 'M',
            'lm' => 'MM',
            '1m' => 'm',
            '2m' => 'mm',
            '2y' => 'yy',
            '4y' => 'yyyy'
        ];
    }

    public static function getMomentJsFormatBags()
    {
        return [
            'ld' => 'dddd',
            'sd' => 'ddd',
            '1d' => 'D',
            '2d' => 'DD',
            'sm' => 'MMM',
            'lm' => 'MMMM',
            '1m' => 'M',
            '2m' => 'MM',
            '2y' => 'YY',
            '4y' => 'YYYY',
            '1h' => 'h',
            '1hf' => 'hh',
            '2h' => 'HH',
            '2i' => 'mm',
            '2s' => 'ss',
            'ut' => 'A',
            'lt' => 'a',
        ];
    }

    public static function getAndroidFormatBags()
    {
        return [
            'ld' => 'EEEE',
            'sd' => 'EEE',
            '1d' => 'd',
            '2d' => 'dd',
            'sm' => 'MMM',
            'lm' => 'MMMM',
            '1m' => 'M',
            '2m' => 'MM',
            '2y' => 'yy',
            '4y' => 'yyyy',
            '1h' => 'h',
            '1hf' => 'hh',
            '2h' => 'HH',
            '2i' => 'mm',
            '2s' => 'ss',
            'ut' => 'a',
            'lt' => 'a',
        ];
    }

    public static function currentCompoundFormat($func1, $separation, $func2)
    {
        return static::getInstance()->compoundFormat($func1, $separation, $func2);
    }

    public static function currentLongDateFormat()
    {
        return static::getInstance()->longDateFormat();
    }

    public static function currentShortDateFormat()
    {
        return static::getInstance()->shortDateFormat();
    }

    public static function currentShortMonthFormat()
    {
        return static::getInstance()->shortMonthFormat();
    }

    public static function currentLongTimeFormat()
    {
        return static::getInstance()->longTimeFormat();
    }

    public static function currentShortTimeFormat()
    {
        return static::getInstance()->shortTimeFormat();
    }

    public static function currentLongDateJsFormat()
    {
        return static::getInstance()->longDateJsFormat();
    }

    public static function currentLongDatePickerJsFormat()
    {
        return static::getInstance()->longDatePickerJsFormat();
    }

    public static function currentShortDateJsFormat()
    {
        return static::getInstance()->shortDateJsFormat();
    }

    public static function currentShortDatePickerJsFormat()
    {
        return static::getInstance()->shortDatePickerJsFormat();
    }

    public static function currentShortMonthPickerJsFormat()
    {
        return static::getInstance()->shortMonthPickerJsFormat();
    }

    public static function currentLongTimeJsFormat()
    {
        return static::getInstance()->longTimeJsFormat();
    }

    public static function currentShortTimeJsFormat()
    {
        return static::getInstance()->shortTimeJsFormat();
    }

    public static function currentShortDateAndroidFormat()
    {
        return static::getInstance()->shortDateAndroidFormat();
    }

    public static function currentLongDateAndroidFormat()
    {
        return static::getInstance()->longDateAndroidFormat();
    }

    public static function currentShortTimeAndroidFormat()
    {
        return static::getInstance()->shortTimeAndroidFormat();
    }

    public static function currentLongTimeAndroidFormat()
    {
        return static::getInstance()->longTimeAndroidFormat();
    }
    #endregion
}
