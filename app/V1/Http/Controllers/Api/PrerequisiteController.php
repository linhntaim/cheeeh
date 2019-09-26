<?php

namespace App\V1\Http\Controllers\Api;

use App\V1\Configuration;
use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\PermissionRepository;
use App\V1\ModelRepositories\RoleRepository;
use App\V1\ModelTransformers\PermissionTransformer;
use App\V1\ModelTransformers\RoleTransformer;
use App\V1\ModelTransformers\ModelTransformTrait;
use App\V1\Utils\ConfigHelper;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\Files\FileHelper;
use App\V1\Utils\NumberFormatHelper;

class PrerequisiteController extends ApiController
{
    use ModelTransformTrait;

    private $dataset;

    public function __construct()
    {
        parent::__construct();

        $this->dataset = [];
    }

    public function index(Request $request)
    {
        $this->server($request);
        $this->roles($request);
        $this->permissions($request);
        $this->locales($request);
        $this->countries($request);
        $this->timezones($request);
        $this->currencies($request);
        $this->numberFormats($request);
        $this->daysOfWeek($request);
        $this->longDateFormats($request);
        $this->longTimeFormats($request);
        $this->shortDateFormats($request);
        $this->shortTimeFormats($request);
        return $this->responseSuccess($this->dataset);
    }

    private function server(Request $request)
    {
        if ($request->has('server')) {
            $this->dataset['server'] = [
                'c' => time(),
                'throttle_request' => [
                    'max_attempts' => Configuration::THROTTLE_REQUEST_MAX_ATTEMPTS,
                    'decay_minutes' => Configuration::THROTTLE_REQUEST_DECAY_MINUTES,
                ],
                'max_upload_file_size' => FileHelper::maxUploadFileSize(),
                'facebook_enabled' => true,
                'google_enabled' => true,
                'microsoft_enabled' => true,
            ];
        }
    }

    private function roles(Request $request)
    {
        if ($request->has('roles')) {
            $this->dataset['roles'] = $this->modelTransform(
                RoleTransformer::class,
                (new RoleRepository())->getNoneProtected()
            );
        }
    }

    private function permissions(Request $request)
    {
        if ($request->has('permissions')) {
            $this->dataset['permissions'] = $this->modelTransform(
                PermissionTransformer::class,
                (new PermissionRepository())->getNoneProtected()
            );
        }
    }

    private function locales(Request $request)
    {
        if ($request->has('locales')) {
            $locales = [];
            foreach (ConfigHelper::getLocaleCodes() as $code) {
                $locales[] = [
                    'code' => $code,
                    'name' => trans('locale.' . $code),
                ];
            }
            $this->dataset['locales'] = $locales;
        }
    }

    private function countries(Request $request)
    {
        if ($request->has('countries')) {
            $countries = [];
            foreach (ConfigHelper::getCountryCodes() as $code) {
                $countries[] = [
                    'code' => $code,
                    'name' => trans('country.' . $code),
                ];
            }
            $this->dataset['countries'] = $countries;
        }
    }

    private function timezones(Request $request)
    {
        if ($request->has('timezones')) {
            $this->dataset['timezones'] = DateTimeHelper::getInstance()->getTimezones();
        }
    }

    private function daysOfWeek(Request $request)
    {
        if ($request->has('days_of_week')) {
            $this->dataset['days_of_week'] = DateTimeHelper::getInstance()->getDaysOfWeek();
        }
    }

    private function longDateFormats(Request $request)
    {
        if ($request->has('long_date_formats')) {
            $this->dataset['long_date_formats'] = DateTimeHelper::getInstance()->getLongDateFormats();
        }
    }

    private function shortDateFormats(Request $request)
    {
        if ($request->has('short_date_formats')) {
            $this->dataset['short_date_formats'] = DateTimeHelper::getInstance()->getShortDateFormats();
        }
    }

    private function longTimeFormats(Request $request)
    {
        if ($request->has('long_time_formats')) {
            $this->dataset['long_time_formats'] = DateTimeHelper::getInstance()->getLongTimeFormats();
        }
    }

    private function shortTimeFormats(Request $request)
    {
        if ($request->has('short_time_formats')) {
            $this->dataset['short_time_formats'] = DateTimeHelper::getInstance()->getShortTimeFormats();
        }
    }

    private function currencies(Request $request)
    {
        if ($request->has('currencies')) {
            $currencies = [];
            foreach (ConfigHelper::getCurrencies() as $code => $currency) {
                $currencies[] = [
                    'code' => $code,
                    'name' => sprintf('%s (%s)', trans('currency.' . $code), $currency['symbol']),
                ];
            }
            $this->dataset['currencies'] = $currencies;
        }
    }

    private function numberFormats(Request $request)
    {
        if ($request->has('number_formats')) {
            $numberFormats = [];
            foreach (ConfigHelper::getNumberFormats() as $numberFormat) {
                $numberFormats[] = [
                    'code' => $numberFormat,
                    'example' => NumberFormatHelper::doFormat(12345.67, $numberFormat),
                ];
            }
            $this->dataset['number_formats'] = $numberFormats;
        }
    }
}
