<?php

namespace App\V1\ModelTransformers;

use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\LocalizationHelper;

class AccountTransformer extends ModelTransformer
{
    use TransformTrait;

    public function toArray()
    {
        $user = $this->getModel();

        $localizationHelper = LocalizationHelper::getInstance();
        $dateTimeHelper = DateTimeHelper::getInstance();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'display_name' => $user->display_name,
            'url_avatar' => $user->url_avatar,
            'email' => $this->safeObject($user->email, function ($userEmail) {
                return $this->transform(UserEmailTransformer::class, $userEmail);
            }),
            'role_names' => $this->safeObject($user->roleNames),
            'permission_names' => $this->safeObject($user->permissionNames),

            'localization' => [
                '_ts' => $localizationHelper->getTimestamp(),

                'locale' => $localizationHelper->getLocale(),
                'country' => $localizationHelper->getCountry(),
                'timezone' => $localizationHelper->getTimezone(),
                'currency' => $localizationHelper->getCurrency(),
                'number_format' => $localizationHelper->getNumberFormat(),
                'first_day_of_week' => $localizationHelper->getFirstDayOfWeek(),
                'long_date_format' => $localizationHelper->getLongDateFormat(),
                'short_date_format' => $localizationHelper->getShortDateFormat(),
                'long_time_format' => $localizationHelper->getLongTimeFormat(),
                'short_time_format' => $localizationHelper->getShortTimeFormat(),

                'time_offset' => $dateTimeHelper->getDateTimeOffset(), // seconds
                'short_date_js_format' => $dateTimeHelper->shortDateJsFormat(),
                'short_date_picker_js_format' => $dateTimeHelper->shortDatePickerJsFormat(),
                'long_date_js_format' => $dateTimeHelper->longDateJsFormat(),
                'long_date_picker_js_format' => $dateTimeHelper->longDatePickerJsFormat(),
                'short_time_js_format' => $dateTimeHelper->shortTimeJsFormat(),
                'long_time_js_format' => $dateTimeHelper->longTimeJsFormat(),
                'short_date_android_format' => $dateTimeHelper->shortDateAndroidFormat(),
                'long_date_android_format' => $dateTimeHelper->longDateAndroidFormat(),
                'short_time_android_format' => $dateTimeHelper->shortTimeAndroidFormat(),
                'long_time_android_format' => $dateTimeHelper->longTimeAndroidFormat(),
            ],
        ];
    }
}
