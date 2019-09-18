<?php

namespace App\V1\Utils;

use App\V1\Exceptions\AppException;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Exception;
use Illuminate\Support\Str;

abstract class BaseDateTimeHelper
{
    use ClassTrait;

    const LONG_DATE_FUNCTION = 'longDate';
    const SHORT_DATE_FUNCTION = 'shortDate';
    const LONG_TIME_FUNCTION = 'longTime';
    const SHORT_TIME_FUNCTION = 'shortTime';
    const DATABASE_FORMAT_DATE = 'Y-m-d';
    const DATABASE_FORMAT_TIME = 'H:i:s';
    const DATABASE_FORMAT = BaseDateTimeHelper::DATABASE_FORMAT_DATE . ' ' . BaseDateTimeHelper::DATABASE_FORMAT_TIME;
    const DAY_TYPE_NONE = 0;
    const DAY_TYPE_START = -1;
    const DAY_TYPE_END = 1;
    const DAY_TYPE_NEXT_START = 2;

    protected static $instance;

    /**
     * @return static
     */
    public function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private $now;

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

        $this->dateTimeOffset = $this->parseDateTimeOffsetByTimezone($localizationHelper->getTimezone());
    }

    /**
     * @param bool $reset
     * @return Carbon
     * @throws Exception
     */
    public function syncNowObject($reset = false)
    {
        if ($reset || empty($this->now)) {
            $this->now = new Carbon();
        }
        return $this->now;
    }

    /**
     * @param string $format
     * @param bool $reset
     * @return string
     * @throws Exception
     */
    public function syncNowFormat($format, $reset = false)
    {
        return $this->syncNowObject($reset)->format($format);
    }

    /**
     * @param bool $reset
     * @return string
     * @throws Exception
     */
    public function syncNow($reset = false)
    {
        return $this->syncNowFormat(static::DATABASE_FORMAT, $reset);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getDateTimeOffset()
    {
        return $this->dateTimeOffset;
    }

    /**
     * @param $time
     * @return Carbon
     * @throws \App\V1\Exceptions\Exception
     */
    public function toCarbon($time)
    {
        if (is_string($time)) {
            try {
                return (new Carbon($time))->locale($this->locale);
            } catch (Exception $exception) {
                throw AppException::from($exception);
            }
        }

        if ($time instanceof Carbon) return $time->locale($this->locale);

        throw new AppException(self::__transErrorWithModule('time_not_supported'));
    }

    public function applyStartType(Carbon $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        switch ($start) {
            case static::DAY_TYPE_NEXT_START:
                $time->setTime(0, 0, 0)->addDay();
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

    #region From Local to UTC Time

    /**
     * @param Carbon|string $time
     * @param int $start
     * @return Carbon
     * @throws \App\V1\Exceptions\Exception
     */
    public function from($time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $time = $this->applyStartType($this->toCarbon($time), $start);
        $time->subSeconds($this->getDateTimeOffset());
        return $time;
    }

    public function fromFormat($format, $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from(Carbon::createFromFormat($format, $time), $start);
    }

    public function fromFormatToFormat($format, $time, $toFormat = null, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->fromFormat($format, $time, $start)->format($toFormat ?: $format);
    }

    public function fromFormatToDatabaseFormat($format, $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->fromFormatToFormat($format, $time, static::DATABASE_FORMAT, $start);
    }

    public function fromToFormat(Carbon $time, $toFormat, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $start)->format($toFormat);
    }

    public function fromToDatabaseFormat(Carbon $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->from($time, $start)->format(static::DATABASE_FORMAT);
    }

    #endregion

    #region From UTC to Local Time
    /**
     * @param Carbon|string $time
     * @param int $start
     * @return Carbon
     * @throws \App\V1\Exceptions\Exception
     */
    public function getObject($time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $time = $this->toCarbon($time);
        $time->addSeconds($this->getDateTimeOffset());
        return $this->applyStartType($time, $start);
    }

    protected function getBags($time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        $time = $this->getObject($time, $start);
        return [
            'd' => $time->format('j'),
            'dd' => $time->format('d'),
            'sd' => trans('datetime.short_day_' . $time->format('N')),
            'ld' => trans('datetime.day_' . $time->format('N')),
            'm' => $time->format('n'),
            'mm' => $time->format('m'),
            'sm' => trans('datetime.short_month_' . $time->format('n')),
            'lm' => trans('datetime.month_' . $time->format('n')),
            'yy' => $time->format('y'),
            'yyyy' => $time->format('Y'),
            'h' => $time->format('g'),
            'hh' => $time->format('h'),
            'h2' => $time->format('G'),
            'hh2' => $time->format('H'),
            'ii' => $time->format('i'),
            'ss' => $time->format('s'),
            'ut' => $time->format('A'),
            'lt' => $time->format('a'),
        ];
    }

    protected function longDateFromBags(array $bags)
    {
        return trans($this->transLongDate, $bags, $this->locale);
    }

    protected function shortDateFromBags(array $bags)
    {
        return trans($this->transShortDate, $bags, $this->locale);
    }

    protected function shortMonthFromBags(array $bags)
    {
        return trans($this->transShortMonth, $bags, $this->locale);
    }

    protected function longTimeFromBags(array $bags)
    {
        return trans($this->transLongTime, $bags, $this->locale);
    }

    protected function shortTimeFromBags(array $bags)
    {
        return trans($this->transShortTime, $bags, $this->locale);
    }

    public function compound($time, $func1 = BaseDateTimeHelper::SHORT_DATE_FUNCTION, $separation = ' ', $func2 = BaseDateTimeHelper::SHORT_TIME_FUNCTION)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        return sprintf('%s%s%s', call_user_func([$this, $func1], $time), $separation, call_user_func([$this, $func2], $time));
    }

    public function longDate($time)
    {
        return $this->longDateFromBags($this->getBags($time));
    }

    public function shortDate($time)
    {
        return $this->shortDateFromBags($this->getBags($time));
    }

    public function shortMonth($time)
    {
        return $this->shortMonthFromBags($this->getBags($time));
    }

    public function longTime($time)
    {
        return $this->longTimeFromBags($this->getBags($time));
    }

    public function shortTime($time)
    {
        return $this->shortTimeFromBags($this->getBags($time));
    }

    #region Get format
    public function compoundFormat($func1, $separation, $func2)
    {
        $allowedFunctions = [static::LONG_DATE_FUNCTION, static::LONG_TIME_FUNCTION, static::SHORT_DATE_FUNCTION, static::SHORT_TIME_FUNCTION];
        if (!in_array($func1, $allowedFunctions) || !in_array($func2, $allowedFunctions)) {
            return null;
        }
        return sprintf('%s%s%s', call_user_func([$this, $func1 . 'Format']), $separation, call_user_func([$this, $func2 . 'Format']));
    }

    public function longDateFormat()
    {
        return $this->longDateFromBags($this->getFormatBags());
    }

    public function shortDateFormat()
    {
        return $this->shortDateFromBags($this->getFormatBags());
    }

    public function shortMonthFormat()
    {
        return $this->shortMonthFromBags($this->getFormatBags());
    }

    public function longTimeFormat()
    {
        return $this->longTimeFromBags($this->getFormatBags());
    }

    public function shortTimeFormat()
    {
        return $this->shortTimeFromBags($this->getFormatBags());
    }

    public function customFormat($name)
    {
        return trans('datetime.custom_formats.' . $name, $this->getFormatBags(), $this->locale);
    }
    #endregion

    #region To Format
    /**
     * @param string $format
     * @param Carbon|string $time
     * @param int $start
     * @return string
     * @throws \App\V1\Exceptions\Exception
     */
    public function format($format, $time, $start = BaseDateTimeHelper::DAY_TYPE_NONE)
    {
        return $this->getObject($time, $start)->format($format);
    }
    #endregion

    #endregion

    #region Static Methods
    protected function getExampleBags()
    {
        return $this->getBags($this->syncNowObject()->year . '-12-24 08:00:00', true);
    }

    protected function getFormatBags()
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

    public function parseDateTimeOffsetByTimezone($timeZone)
    {
        if (empty($timeZone)) {
            return 0;
        }
        if ($timeZone != 'UTC' && Str::startsWith($timeZone, 'UTC')) {
            return floatval(Str::substr($timeZone, 3)) * 3600;
        }
        return (new CarbonTimeZone($timeZone))->getOffset(new Carbon());
    }

    protected function getUtcOffsets()
    {
        return [-12, -11.5, -11, -10.5, -10, -9.5, -9, -8.5, -8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2.5, -2, -1.5, -1, -0.5,
            0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 5.75, 6, 6.5, 7, 7.5, 8, 8.5, 8.75, 9, 9.5, 10, 10.5, 11, 11.5, 12, 12.75, 13, 13.75, 14];
    }

    public function getTimezones()
    {
        // UTC
        $timezones = [
            [
                'name' => 'UTC',
                'timezones' => [
                    [
                        'name' => 'UTC',
                        'value' => 'UTC',
                    ],
                ],
            ],
        ];
        // Timezone by UTC offsets
        $utcOffsets = [];
        foreach ($this->getUtcOffsets() as $offset) {
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
        foreach (CarbonTimeZone::listIdentifiers() as $zone) {
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

    public function getTimezoneValues()
    {
        // UTC
        $timezones = ['UTC'];
        // Timezone by UTC offsets
        foreach ($this->getUtcOffsets() as $offset) {
            $timezones[] = 'UTC' . (0 <= $offset ? '+' . $offset : (string)$offset);
        }
        // UNIX Timezones
        foreach (CarbonTimeZone::listIdentifiers() as $zone) {
            $timezones[] = $zone;
        }
        return $timezones;
    }

    public function getDaysOfWeek()
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

    public function getDaysOfWeekValues()
    {
        return range(1, 7);
    }

    public function getLongDateFormats()
    {
        $options = [];
        for ($i = 0; $i <= 3; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_date_' . $i, $this->getExampleBags()),
            ];
        }
        return $options;
    }

    public function getLongDateFormatValues()
    {
        return range(0, 3);
    }

    public function getShortDateFormats()
    {
        $options = [];
        for ($i = 0; $i <= 3; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_date_' . $i, $this->getExampleBags()),
            ];
        }
        return $options;
    }

    public function getShortDateFormatValues()
    {
        return range(0, 3);
    }

    public function getLongTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.long_time_' . $i, $this->getExampleBags()),
            ];
        }
        return $options;
    }

    public function getLongTimeFormatValues()
    {
        return range(0, 4);
    }

    public function getShortTimeFormats()
    {
        $options = [];
        for ($i = 0; $i <= 4; ++$i) {
            $options[] = [
                'value' => $i,
                'example' => trans('datetime.short_time_' . $i, $this->getExampleBags()),
            ];
        }
        return $options;
    }

    public function getShortTimeFormatValues()
    {
        return range(0, 4);
    }

    protected $standardizedReplaced = [];
    protected $standardizedReplacing = [];

    public function standardizeTime($time)
    {
        if (empty($this->standardizedReplaced)) {
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
                    $this->standardizedReplaced[] = $map;
                    $this->standardizedReplacing[] = $name;
                }
            }
        }

        return str_replace($this->standardizedReplaced, $this->standardizedReplacing, $time);
    }
    #endregion
}
