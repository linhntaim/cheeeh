<?php

namespace App\V1\Http\Controllers\Api\Account;

use App\V1\Http\Controllers\ApiController;
use App\V1\Http\Requests\Request;
use App\V1\ModelRepositories\UserEmailRepository;
use App\V1\ModelRepositories\UserRepository;
use App\V1\ModelTransformers\AccountTransformer;
use App\V1\ModelTransformers\UserEmailTransformer;
use App\V1\Rules\CurrentPasswordRule;
use App\V1\Utils\ConfigHelper;
use App\V1\Utils\DateTimeHelper;
use App\V1\Utils\LocalizationHelper;
use Illuminate\Validation\Rule;

class AccountController extends ApiController
{
    protected $userEmailRepository;

    public function __construct()
    {
        parent::__construct();

        $this->modelRepository = new UserRepository();
        $this->modelTransformerClass = AccountTransformer::class;

        $this->userEmailRepository = new UserEmailRepository();
    }

    public function index(Request $request)
    {
        return $this->responseModel($request->user());
    }

    public function store(Request $request)
    {
        $this->modelRepository->model($request->user());

        if ($request->has('_avatar')) {
            return $this->updateAvatar($request);
        }
        if ($request->has('_information')) {
            return $this->updateInformation($request);
        }
        if ($request->has('_password')) {
            return $this->updatePassword($request);
        }
        if ($request->has('_localization')) {
            return $this->localizationUpdate($request);
        }
        if ($request->has('_locale')) {
            return $this->localizationUpdateLocale($request);
        }

        return $this->responseFail();
    }

    private function updateAvatar(Request $request)
    {
        $this->validated($request, [
            'image' => 'required|image|dimensions:min_width=512,min_height=512',
        ]);

        return $this->responseModel(
            $this->modelRepository->updateAvatar($request->file('image'))
        );
    }

    private function updateInformation(Request $request)
    {
        $this->validated($request, [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9a-zA-Z_\-\.]+$/',
                Rule::unique('users', 'name')->ignore($request->user()->id),
            ],
            'display_name' => 'required|string|max:255',
        ]);

        return $this->responseModel(
            $this->modelRepository->update(
                [
                    'name' => $request->input('name'),
                    'display_name' => $request->input('display_name'),
                ],
                '',
                true,
                '',
                false
            )
        );
    }

    private function updatePassword(Request $request)
    {
        $this->validated($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'current_password' => ['required', new CurrentPasswordRule()],
        ]);

        return $this->responseModel(
            $this->modelRepository->update(
                [
                    'password' => $request->input('password'),
                ],
                '',
                true,
                '',
                false
            )
        );
    }

    private function localizationUpdate(Request $request)
    {
        $this->validated($request, [
            'locale' => 'required|in:' . implode(',', ConfigHelper::getLocaleCodes()),
            'country' => 'required|in:' . implode(',', ConfigHelper::getCountryCodes()),
            'timezone' => 'required|in:' . implode(',', DateTimeHelper::getTimezoneValues()),
            'currency' => 'required|in:' . implode(',', ConfigHelper::getCurrencyCodes()),
            'number_format' => 'required|in:' . implode(',', ConfigHelper::getNumberFormats()),
            'first_day_of_week' => 'required|in:' . implode(',', DateTimeHelper::getDaysOfWeekValues()),
            'long_date_format' => 'required|in:' . implode(',', DateTimeHelper::getLongDateFormatValues()),
            'short_date_format' => 'required|in:' . implode(',', DateTimeHelper::getShortDateFormatValues()),
            'long_time_format' => 'required|in:' . implode(',', DateTimeHelper::getLongTimeFormatValues()),
            'short_time_format' => 'required|in:' . implode(',', DateTimeHelper::getShortTimeFormatValues()),
        ]);

        $currentUser = $this->modelRepository->updateLocalization([
            'locale' => $request->input('locale'),
            'country' => $request->input('country'),
            'timezone' => $request->input('timezone'),
            'currency' => $request->input('currency'),
            'number_format' => $request->input('number_format'),
            'first_day_of_week' => $request->input('first_day_of_week'),
            'long_date_format' => $request->input('long_date_format'),
            'short_date_format' => $request->input('short_date_format'),
            'long_time_format' => $request->input('long_time_format'),
            'short_time_format' => $request->input('short_time_format'),
        ]);

        LocalizationHelper::getInstance()->fetchFromUser($currentUser);

        return $this->responseModel($currentUser);
    }

    private function localizationUpdateLocale(Request $request)
    {
        $this->validated($request, [
            'locale' => 'required|in:' . implode(',', ConfigHelper::getLocaleCodes()),
        ]);

        $currentUser = $this->modelRepository->updateLocalization([
            'locale' => $request->input('locale'),
        ]);

        LocalizationHelper::getInstance()->fetchFromUser($currentUser);

        return $this->responseModel($currentUser);
    }

    public function mainEmailUpdate(Request $request)
    {
        if ($request->has('_email')) {
            return $this->mainEmailUpdateAddress($request);
        }
        if ($request->has('_email_with_password')) {
            return $this->mainEmailUpdateAddressWithPassword($request);
        }
        if ($request->has('_verification')) {
            return $this->mainEmailUpdateVerification($request);
        }

        return $this->responseFail();
    }

    public function mainEmailUpdateAddress(Request $request)
    {
        $currentUser = $request->user();
        $this->userEmailRepository->model($currentUser->email);

        $this->validated($request, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($currentUser->email->id),
            ],
        ]);

        $currentUser->memorizeEmail($this->userEmailRepository->updateEmail($request->input('email'), $request->input('app_verify_email_path')));
        return $this->responseModel($currentUser);
    }

    public function mainEmailUpdateAddressWithPassword(Request $request)
    {
        $currentUser = $request->user();
        $this->userEmailRepository->model($currentUser->email);

        $this->validated($request, [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('user_emails', 'email')->ignore($currentUser->email->id),
            ],
            'current_password' => ['required', new CurrentPasswordRule()],
        ]);

        $currentUser->memorizeEmail($this->userEmailRepository->updateEmail($request->input('email'), $request->input('app_verify_email_path')));
        return $this->responseModel($currentUser);
    }

    public function mainEmailUpdateVerification(Request $request)
    {
        return $this->responseCustomModel($this->modelTransform(
            UserEmailTransformer::class,
            $this->userEmailRepository->updateVerifiedAtByEmailAndCode(
                $request->input('email', ''),
                $request->input('verified_code', '')
            )
        ));
    }
}
