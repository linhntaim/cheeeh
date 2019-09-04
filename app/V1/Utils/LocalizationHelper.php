<?php

namespace App\V1\Utils;

use App\V1\Models\User;

class LocalizationHelper extends BaseLocalizationHelper
{
    public function autoFetch()
    {
        $currentUser = request()->user();

        if (!$this->fetchFromRequestHeader()->fetched()
            || ($this->fromApp && !empty($currentUser))
            || (!$this->fromApp && !empty($currentUser)
                && (new LocalizationHelper())->fetchFromUser($currentUser)->getTimestamp() != $this->getTimestamp()
                && $this->getTimestamp() != 0)) {
            return $this->fetchFromUser($currentUser)->apply();
        }

        return $this->storeWithUser($currentUser)
            ->apply();
    }

    /**
     * @param integer|User|null $userId
     * @return User|null
     */
    protected function getUser($userId = null)
    {
        if (!empty($userId)) {
            return $userId instanceof User ? $userId : User::find($userId);
        }
        $currentUser = request()->user();
        return empty($currentUser) ? null : $currentUser;
    }

    /**
     * @param integer|User|null $userId
     * @return LocalizationHelper
     */
    public function fetchFromUser($userId = null)
    {
        $user = $this->getUser($userId);
        if (empty($user)) return $this;

        $userLocalization = $user->localization;
        if (empty($userLocalization)) return $this;

        $this->fromApp = false;
        $this->setTimestamp($userLocalization->timestampedUpdatedAt);
        $this->setLocale($userLocalization->locale);
        $this->setCountry($userLocalization->country);
        $this->setTimezone($userLocalization->timezone);
        $this->setCurrency($userLocalization->currency);
        $this->setNumberFormat($userLocalization->number_format);
        $this->setFirstDayOfWeek($userLocalization->first_day_of_week);
        $this->setLongDateFormat($userLocalization->long_date_format);
        $this->setShortDateFormat($userLocalization->short_date_format);
        $this->setLongTimeFormat($userLocalization->long_time_format);
        $this->setShortTimeFormat($userLocalization->short_time_format);

        $this->fetched = true;

        return $this;
    }

    /**
     * @param integer|User|null $userId
     * @return LocalizationHelper
     */
    public function storeWithUser($userId = null)
    {
        $user = $this->getUser($userId);
        if (empty($user)) return $this;

        $userLocalization = $user->localization;
        $attributes = [];
        if ($this->locale != $userLocalization->locale) {
            $attributes['locale'] = $this->locale;
        }
        if ($this->country != $userLocalization->country) {
            $attributes['country'] = $this->country;
        }
        if ($this->timezone != $userLocalization->timezone) {
            $attributes['timezone'] = $this->timezone;
        }
        if ($this->currency != $userLocalization->currency) {
            $attributes['currency'] = $this->currency;
        }
        if ($this->numberFormat != $userLocalization->number_format) {
            $attributes['number_format'] = $this->numberFormat;
        }
        if ($this->firstDayOfWeek != $userLocalization->first_day_of_week) {
            $attributes['first_day_of_week'] = $this->firstDayOfWeek;
        }
        if ($this->longDateFormat != $userLocalization->long_date_format) {
            $attributes['long_date_format'] = $this->longDateFormat;
        }
        if ($this->shortDateFormat != $userLocalization->short_date_format) {
            $attributes['short_date_format'] = $this->shortDateFormat;
        }
        if ($this->longTimeFormat != $userLocalization->long_time_format) {
            $attributes['long_time_format'] = $this->longTimeFormat;
        }
        if ($this->shortTimeFormat != $userLocalization->short_time_format) {
            $attributes['short_time_format'] = $this->shortTimeFormat;
        }

        if (count($attributes) <= 0) return $this;

        $userLocalization->update($attributes);

        return $this;
    }
}
