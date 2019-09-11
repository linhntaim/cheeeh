<?php

namespace App\V1\Utils;

use App\V1\Exceptions\AppException;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

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
     * @return static
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param bool $reset
     * @return DateTime
     * @throws Exception
     */
    public static function syncNowObject($reset = false)
    {
        if ($reset || empty(static::$now)) {
            static::$now = new DateTime('now', new DateTimeZone('UTC'));
        }
        return static::$now;
    }

    /**
     * @param string $format
     * @param bool $reset
     * @return string
     * @throws Exception
     */
    public static function syncNowFormat($format, $reset = false)
    {
        return static::syncNowObject($reset)->format($format);
    }

    /**
     * @param bool $reset
     * @return string
     * @throws Exception
     */
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

        $this->transLongDate = 'datetime.formats.long_date_' . $localizationHelper->getLongDateFormat();
        $this->transShortDate = 'datetime.formats.short_date_' . $localizationHelper->getShortDateFormat();
        $this->transShortMonth = 'datetime.formats.short_month_' . $localizationHelper->getShortDateFormat();
        $this->transLongTime = 'datetime.formats.long_time_' . $localizationHelper->getLongTimeFormat();
        $this->transShortTime = 'datetime.formats.short_time_' . $localizationHelper->getShortTimeFormat();

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

    public function fromFormat($format, $time, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from(DateTime::createFromFormat($format, static::standardizeTime($time), new DateTimeZone('UTC')), $noOffset, $diffDay, $start);
    }

    public function fromFormatToFormat($currentFormat, $time, $toFormat = null, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        if (empty($toFormat)) $toFormat = $currentFormat;
        $now = $this->fromFormat($currentFormat, $time, $noOffset, $diffDay, $start);
        return $now !== false ? $now->format($toFormat) : false;
    }

    public function fromFormatToDatabaseFormat($currentFormat, $time, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->fromFormatToFormat($currentFormat, $time, static::DATABASE_FORMAT, $noOffset, $diffDay, $start);
    }

    public function fromToFormat(DateTime $time, $toFormat, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $noOffset, $diffDay, $start)->format($toFormat);
    }

    public function fromToDatabaseFormat(DateTime $time, $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $noOffset, $diffDay, $start)->format(static::DATABASE_FORMAT);
    }

    public function fromToUtc(DateTime $time)
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
            'd' => $now->format('j'),
            'dd' => $now->format('d'),
            'sd' => trans('datetime.short_day_' . $now->format('N')),
            'ld' => trans('datetime.day_' . $now->format('N')),

            'm' => $now->format('n'),
            'mm' => $now->format('m'),
            'sm' => trans('datetime.short_month_' . $now->format('n')),
            'lm' => trans('datetime.month_' . $now->format('n')),

            'yy' => $now->format('y'),
            'yyyy' => $now->format('Y'),

            'h' => $now->format('g'),
            'hh' => $now->format('h'),
            'h2' => $now->format('G'),
            'hh2' => $now->format('H'),

            'i' => intval($now->format('i')),
            'ii' => $now->format('i'),

            's' => intval($now->format('s')),
            'ss' => $now->format('s'),

            'ut' => $now->format('A'),
            'lt' => $now->format('a'),
        ];
    }

    public function compound($func1 = BaseDateTimeHelper::SHORT_DATE_FUNCTION, $separation = ' ', $func2 = BaseDateTimeHelper::SHORT_TIME_FUNCTION, $time = 'now', $noOffset = false)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        return call_user_func(array($this, $func1), $time, $noOffset)
            . $separation
            . call_user_func(array($this, $func2), $time, $noOffset);
    }

    public function compoundBags($func1 = BaseDateTimeHelper::SHORT_DATE_FUNCTION, $separation = ' ', $func2 = BaseDateTimeHelper::SHORT_TIME_FUNCTION, array $bags = [])
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        $func1 .= 'FromBags';
        $func2 .= 'FromBags';
        return call_user_func(array($this, $func1), $bags)
            . $separation
            . call_user_func(array($this, $func2), $bags);
    }

    public function shortDay($time = 'now', $no_offset = false)
    {
        return $this->getBags($time, $no_offset)['sd'];
    }

    public function longDay($time = 'now', $no_offset = false)
    {
        return $this->getBags($time, $no_offset)['ld'];
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

    public function shortDateFormat()
    {
        return $this->shortDateFromBags(static::getFormatBags());
    }

    public function shortMonthFormat()
    {
        return $this->shortMonthFromBags(static::getFormatBags());
    }

    public function longTimeFormat()
    {
        return $this->longTimeFromBags(static::getFormatBags());
    }

    public function shortTimeFormat()
    {
        return $this->shortTimeFromBags(static::getFormatBags());
    }

    public function customFormat($name)
    {
        return trans('datetime.custom_formats.' . $name, static::getFormatBags(), $this->locale);
    }
    #endregion

    #region To Format
    public function format($format, $time = 'now', $noOffset = false, &$diffDay = 0, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->getObject($time, $noOffset, $diffDay, $start)->format($format);
    }
    #endregion

    #endregion

    #region Static Methods
    protected static function getExampleBags()
    {
        return static::getInstance()->getBags(date('Y') . '-12-24 08:00:00', true);
    }

    public static function getFormatBags()
    {
        return [
            'd' => 'j',
            'dd' => 'd',
            'sd' => 'D',
            'ld' => 'l',

            'm' => 'n',
            'mm' => 'm',
            'sm' => 'M',
            'lm' => 'F',

            'yy' => 'y',
            'yyyy' => 'Y',

            'h' => 'g',
            'hh' => 'h',
            'h2' => 'G',
            'hh2' => 'H',
            'ii' => 'i',
            'ss' => 's',
            'ut' => 'A',
            'lt' => 'a',
        ];
    }

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
        switch ($start) {
            case static::DAY_TYPE_NEXT_START:
                $time->setTime(0, 0, 0)->add(new DateInterval('P1D'));
                break;
            case static::DAY_TYPE_END:
                $time->setTime(23, 59, 59);
                break;
            case static::DAY_TYPE_START:
                $time->setTime(0, 0, 0);
                break;
        }
        return $time;
    }

    public static function getUtcOffsets()
    {
        return [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
            0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14];
    }

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
        // Timezone by UTC offsets
        $utcOffsets = [];
        foreach (static::getUtcOffsets() as $offset) {
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
        // Timezone by UTC offsets
        foreach (static::getUtcOffsets() as $offset) {
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
        for ($i = 1; $i <= 7; ++$i) {
            $options[] = [
                'value' => $i,
                'name' => trans('datetime.day_' . $i),
            ];
        }
        return $options;
    }

    public static function getDaysOfWeekValues()
    {
        return range(1, 7);
    }

    public static function getLongDateFormats()
    {
        $options = [];
        for ($i = 0; $i <= 3; ++$i) {
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
        for ($i = 0; $i <= 3; ++$i) {
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

    protected static $standardizedReplaced = [];
    protected static $standardizedReplacing = [];

    public static function standardizeTime($time)
    {
        if (empty(static::$standardizedReplaced)) {
            $mappingReplaces = [
                'January' => [
                    'Tháng 1',
                ],
                'Februry' => [
                    'Tháng 2',
                ],
                'March' => [
                    'Tháng 3',
                ],
                'April' => [
                    'Tháng 4',
                ],
                'May' => [
                    'Tháng 4',
                    'Th5',
                ],
                'June' => [
                    'Tháng 6',
                ],
                'July' => [
                    'Tháng 7',
                ],
                'August' => [
                    'Tháng 8',
                ],
                'September' => [
                    'Tháng 9',
                ],
                'October' => [
                    'Tháng 10',
                ],
                'November' => [
                    'Tháng 11',
                ],
                'December' => [
                    'Tháng 12',
                ],
                'Jan' => [
                    'Th1',
                ],
                'Feb' => [
                    'Th2',
                ],
                'Mar' => [
                    'Th3',
                ],
                'Apr' => [
                    'Th4',
                ],
                'Jun' => [
                    'Th6',
                ],
                'Jul' => [
                    'Th7',
                ],
                'Aug' => [
                    'Th8',
                ],
                'Sep' => [
                    'Th9',
                ],
                'Oct' => [
                    'Th10',
                ],
                'Nov' => [
                    'Th11',
                ],
                'Dec' => [
                    'Th12',
                ],
                'Monday' => [
                    'Thứ 2',
                ],
                'Tuesday' => [
                    'Thứ 3',
                ],
                'Wednesday' => [
                    'Thứ 4',
                ],
                'Thursday' => [
                    'Thứ 5',
                ],
                'Friday' => [
                    'Thứ 6',
                ],
                'Saturday' => [
                    'Thứ 7',
                ],
                'Sunday' => [
                    'Chủ nhật',
                ],
                'Mon' => [
                    'T2',
                ],
                'Tue' => [
                    'T3',
                ],
                'Wed' => [
                    'T4',
                ],
                'Thur' => [
                    'T5',
                ],
                'Fri' => [
                    'T6',
                ],
                'Sat' => [
                    'T7',
                ],
                'Sun' => [
                    'CN',
                ],
            ];
            foreach ($mappingReplaces as $name => $maps) {
                foreach ($maps as $map) {
                    static::$standardizedReplaced[] = $map;
                    static::$standardizedReplacing[] = $name;
                }
            }
        }

        return str_replace(static::$standardizedReplaced, static::$standardizedReplacing, $time);
    }
    #endregion
}
